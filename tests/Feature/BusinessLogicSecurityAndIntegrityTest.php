<?php

namespace Tests\Feature;

use App\Models\JenisTagihan;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Rapor;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\AutoNarasiService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessLogicSecurityAndIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $muridUser;
    protected Siswa $siswa;
    protected TahunAjaran $ta;
    protected Semester $sem;
    protected Kelas $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::firstOrCreate(['nama' => 'super_admin']);
        $roleMurid = Role::firstOrCreate(['nama' => 'murid']);

        $this->superAdmin = User::factory()->create([
            'role_id' => $roleAdmin->id,
            'status' => 'aktif',
        ]);

        $this->muridUser = User::factory()->create([
            'role_id' => $roleMurid->id,
            'status' => 'aktif',
        ]);

        $this->ta = TahunAjaran::create([
            'nama' => '2026/2027',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2027-06-30',
            'status_aktif' => true,
        ]);

        $this->sem = Semester::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'ganjil',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
            'status_aktif' => true,
        ]);

        $this->kelas = Kelas::create([
            'nama_kelas' => '1-A Tahfizh',
            'tingkat' => 1,
            'jenis_kelas' => 'umum',
            'semester_id' => $this->sem->id,
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $this->muridUser->id,
            'kelas_id' => $this->kelas->id,
            'nis' => '8888',
            'nisn' => '0088888888',
            'jenis_kelamin' => 'L',
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
            'saldo_deposit' => 0,
        ]);
    }

    public function test_qr_verification_rejects_fake_or_nonexistent_ttd_code(): void
    {
        // Random fake code with TTD-RES prefix
        $response = $this->get('/verifikasi/dokumen/TTD-RES-99999-BUATAN_SENDIRI');
        $response->assertStatus(200);
        $response->assertSee('Verifikasi Gagal');
        $response->assertSee('Dokumen Tidak Valid / Palsu');
    }

    public function test_qr_verification_rejects_voided_payment(): void
    {
        $jt = JenisTagihan::create(['nama' => 'SPP', 'kategori' => 'rutin', 'default_nominal' => 350000]);
        $tagihan = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $jt->id,
            'tahun_ajaran_id' => $this->ta->id,
            'bulan' => 'Juli',
            'nominal' => 350000,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-07-10',
        ]);

        $pembayaran = Pembayaran::create([
            'no_resi' => 'KW-TEST-VOID',
            'tagihan_id' => $tagihan->id,
            'tanggal_bayar' => '2026-07-05',
            'nominal_dibayar' => 350000,
            'metode_bayar' => 'Tunai',
            'is_void' => true, // Voided / cancelled!
            'petugas_id' => $this->superAdmin->id,
        ]);

        $response = $this->get("/verifikasi/dokumen/TTD-RES-{$pembayaran->id}-DUMMYHASH");
        $response->assertStatus(200);
        $response->assertSee('Verifikasi Gagal');
    }

    public function test_rapor_pdf_preview_blocked_if_student_has_overdue_spp(): void
    {
        $jt = JenisTagihan::create([
            'nama' => 'SPP',
            'kategori' => 'rutin',
            'default_nominal' => 350000,
            'is_blocking' => true,
        ]);

        // Overdue bill (past due date)
        Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $jt->id,
            'tahun_ajaran_id' => $this->ta->id,
            'bulan' => 'Juli',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::yesterday()->toDateString(),
        ]);

        $this->actingAs($this->muridUser);

        // Attempt to access PDF preview directly
        $response = $this->get(route('rapor.pdf.preview', ['siswaId' => $this->siswa->id]));
        $response->assertStatus(403);
    }

    public function test_rapor_pdf_returns_404_if_not_published_instead_of_auto_creating(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('rapor.pdf.preview', ['siswaId' => $this->siswa->id]));
        $response->assertStatus(404);

        // Verify that NO fake rapor record was generated in database
        $this->assertEquals(0, Rapor::where('siswa_id', $this->siswa->id)->count());
    }

    public function test_auto_narasi_returns_truthful_empty_state_without_fake_high_grades(): void
    {
        $service = new AutoNarasiService();
        $res = $service->generateForTahfidz($this->siswa->id, $this->sem->id);

        $this->assertEquals(0, $res['total_juz_dihafal']);
        $this->assertEquals('-', $res['daftar_surah_lulus']);
        $this->assertEquals('Belum Dinilai', $res['predikat_tahfidz']);
        $this->assertEquals(0, $res['avg_tajwid']);
        $this->assertEquals(0, $res['avg_kelancaran']);
        $this->assertStringContainsString('belum memiliki catatan setoran hafalan', $res['narasi_tahfidz_full']);
    }
}
