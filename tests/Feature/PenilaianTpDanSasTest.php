<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\LingkupMateri;
use App\Models\TujuanPembelajaran;
use App\Models\NilaiSumatifTp;
use App\Models\NilaiSas;
use App\Models\Rapor;
use App\Models\RaporDetail;
use Livewire\Livewire;
use App\Livewire\Guru\InputNilaiSumatif;
use App\Livewire\Murid\RaporNilai;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PenilaianTpDanSasTest extends TestCase
{
    use RefreshDatabase;

    protected User $guruUser;
    protected Guru $guru;
    protected User $muridUser;
    protected Siswa $siswa;
    protected Kelas $kelas;
    protected MataPelajaran $mapel;
    protected TahunAjaran $tahunAjaran;
    protected Semester $semester;
    protected LingkupMateri $lingkupMateri;
    protected TujuanPembelajaran $tp1;
    protected TujuanPembelajaran $tp2;

    protected function setUp(): void
    {
        parent::setUp();

        $roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);
        $roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);

        $this->guruUser = User::factory()->create([
            'username' => 'guru_merdeka_' . uniqid(),
            'nama' => 'Budi Santoso, S.Pd.',
            'role_id' => $roleGuru->id,
            'status' => 'aktif',
        ]);

        $this->guru = Guru::create([
            'user_id' => $this->guruUser->id,
            'nip' => '198701012020011001',
            'jenis_guru' => 'umum',
            'status_kepegawaian' => 'tetap_yayasan',
            'tanggal_masuk' => '2020-01-01',
        ]);

        $this->tahunAjaran = TahunAjaran::create([
            'nama' => '2026/2027',
            'status_aktif' => true,
        ]);

        $this->semester = Semester::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'semester' => 'Ganjil',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
            'status_aktif' => true,
        ]);

        $this->kelas = Kelas::create([
            'nama_kelas' => '7A',
            'jenis_kelas' => 'umum',
            'tingkat' => 7,
            'guru_umum_id' => $this->guru->id,
            'semester_id' => $this->semester->id,
        ]);

        $this->muridUser = User::factory()->create([
            'username' => 'siswa_' . uniqid(),
            'role_id' => $roleMurid->id,
            'nama' => 'Ahmad Dahlan',
            'status' => 'aktif',
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $this->muridUser->id,
            'kelas_id' => $this->kelas->id,
            'nis' => '1001',
            'nisn' => '0000001001',
            'jenis_kelamin' => 'L',
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
            'saldo_deposit' => 0,
        ]);

        $this->mapel = MataPelajaran::create([
            'nama_mapel' => 'Matematika',
            'jenis' => 'umum',
        ]);

        $this->lingkupMateri = LingkupMateri::create([
            'mapel_id' => $this->mapel->id,
            'nama_lingkup_materi' => 'Bilangan Bulat dan Pecahan',
            'urutan' => 1,
        ]);

        $this->tp1 = TujuanPembelajaran::create([
            'lingkup_materi_id' => $this->lingkupMateri->id,
            'deskripsi_tp' => 'Memahami operasi hitung bilangan bulat',
            'urutan' => 1,
        ]);

        $this->tp2 = TujuanPembelajaran::create([
            'lingkup_materi_id' => $this->lingkupMateri->id,
            'deskripsi_tp' => 'Menyelesaikan masalah terkait pecahan',
            'urutan' => 2,
        ]);
    }

    public function test_penilaian_only_from_tp_and_sas_without_bobot_and_formula(): void
    {
        $this->actingAs($this->guruUser);

        // Input TP1 = 80, TP2 = 90 (Avg TP = 85), and SAS = 95
        // Final score should be: (85 + 95) / 2 = 90 (Predikat A)
        Livewire::test(InputNilaiSumatif::class)
            ->set('kelas_id', $this->kelas->id)
            ->set('mapel_id', $this->mapel->id)
            ->set('semester_id', $this->semester->id)
            ->set("nilaiTpMatrix.{$this->siswa->id}.{$this->tp1->id}", 80)
            ->set("nilaiTpMatrix.{$this->siswa->id}.{$this->tp2->id}", 90)
            ->set("nilaiSasMatrix.{$this->siswa->id}", 95)
            ->call('saveMatrix')
            ->assertHasNoErrors()
            ->assertSee('Matriks Nilai Sumatif TP & SAS berhasil disimpan');

        // Check NilaiSumatifTp
        $this->assertDatabaseHas('nilai_sumatif_tp', [
            'siswa_id' => $this->siswa->id,
            'tp_id' => $this->tp1->id,
            'nilai' => 80,
        ]);
        $this->assertDatabaseHas('nilai_sumatif_tp', [
            'siswa_id' => $this->siswa->id,
            'tp_id' => $this->tp2->id,
            'nilai' => 90,
        ]);

        // Check NilaiSas
        $this->assertDatabaseHas('nilai_sas', [
            'siswa_id' => $this->siswa->id,
            'mapel_id' => $this->mapel->id,
            'nilai_sas' => 95,
        ]);

        // Check RaporDetail has final score 90.00 and predikat A
        $rapor = Rapor::where('siswa_id', $this->siswa->id)
            ->where('semester_id', $this->semester->id)
            ->first();

        $this->assertNotNull($rapor);

        $detail = RaporDetail::where('rapor_id', $rapor->id)
            ->where('mapel_id', $this->mapel->id)
            ->first();

        $this->assertNotNull($detail);
        $this->assertEquals(90.00, floatval($detail->nilai_akhir));
        $this->assertEquals('A', $detail->predikat);
        $this->assertNotEmpty($detail->deskripsi_tertinggi);
        $this->assertNotEmpty($detail->deskripsi_terendah);
    }

    public function test_penilaian_with_only_tp_when_sas_not_yet_conducted(): void
    {
        $this->actingAs($this->guruUser);

        // Input TP1 = 82, TP2 = 86 (Avg TP = 84), SAS not yet inputted
        // Final score should be equal to avg TP: 84 (Predikat B)
        Livewire::test(InputNilaiSumatif::class)
            ->set('kelas_id', $this->kelas->id)
            ->set('mapel_id', $this->mapel->id)
            ->set('semester_id', $this->semester->id)
            ->set("nilaiTpMatrix.{$this->siswa->id}.{$this->tp1->id}", 82)
            ->set("nilaiTpMatrix.{$this->siswa->id}.{$this->tp2->id}", 86)
            ->call('saveMatrix')
            ->assertHasNoErrors();

        $rapor = Rapor::where('siswa_id', $this->siswa->id)
            ->where('semester_id', $this->semester->id)
            ->first();

        $detail = RaporDetail::where('rapor_id', $rapor->id)
            ->where('mapel_id', $this->mapel->id)
            ->first();

        $this->assertNotNull($detail);
        $this->assertEquals(84.00, floatval($detail->nilai_akhir));
        $this->assertEquals('B', $detail->predikat);
    }

    public function test_penilaian_with_only_sas_when_tp_not_yet_recorded(): void
    {
        $this->actingAs($this->guruUser);

        // Input SAS = 75 only
        // Final score should be 75 (Predikat C)
        Livewire::test(InputNilaiSumatif::class)
            ->set('kelas_id', $this->kelas->id)
            ->set('mapel_id', $this->mapel->id)
            ->set('semester_id', $this->semester->id)
            ->set("nilaiSasMatrix.{$this->siswa->id}", 75)
            ->call('saveMatrix')
            ->assertHasNoErrors();

        $rapor = Rapor::where('siswa_id', $this->siswa->id)
            ->where('semester_id', $this->semester->id)
            ->first();

        $detail = RaporDetail::where('rapor_id', $rapor->id)
            ->where('mapel_id', $this->mapel->id)
            ->first();

        $this->assertNotNull($detail);
        $this->assertEquals(75.00, floatval($detail->nilai_akhir));
        $this->assertEquals('C', $detail->predikat);
    }

    public function test_murid_rapor_shows_bab_and_sas_without_individual_tp_and_without_pts_or_weights(): void
    {
        // Setup scores
        NilaiSumatifTp::create([
            'siswa_id' => $this->siswa->id,
            'tp_id' => $this->tp1->id,
            'semester_id' => $this->semester->id,
            'nilai' => 85,
        ]);

        NilaiSas::create([
            'siswa_id' => $this->siswa->id,
            'mapel_id' => $this->mapel->id,
            'semester_id' => $this->semester->id,
            'nilai' => 95,
            'nilai_sas' => 95,
        ]);

        $rapor = Rapor::create([
            'siswa_id' => $this->siswa->id,
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas->id,
            'tipe_rapor' => 'akademik',
            'status' => 'terbit',
        ]);

        RaporDetail::create([
            'rapor_id' => $rapor->id,
            'mapel_id' => $this->mapel->id,
            'nilai_akhir' => 90.00,
            'predikat' => 'A',
        ]);

        $this->actingAs($this->muridUser);

        // 1. Rekap Tab
        $test = Livewire::test(RaporNilai::class)
            ->assertSee('Matematika')
            ->assertSee('Rata-rata Bab')
            ->assertSee('Nilai SAS')
            ->assertSee('Nilai Akhir')
            ->assertDontSee('>UH<', false)
            ->assertDontSee('>UTS<', false)
            ->assertDontSee('Mid Semester (STS)')
            ->assertDontSee('Bobot:');

        // 2. Bab Tab: Siswa hanya melihat skor per-Bab, BUKAN rincian TP
        $test->call('setTab', 'bab')
            ->assertSee('Rincian Capaian Nilai per-Bab')
            ->assertSee('Bilangan Bulat dan Pecahan')
            ->assertSee('85')
            // Ensure student CANNOT see TP description or TP code
            ->assertDontSee('Memahami operasi hitung bilangan bulat')
            ->assertDontSee('Menyelesaikan masalah terkait pecahan');
    }

    public function test_bobot_nilai_route_redirects_to_input_sumatif(): void
    {
        $this->actingAs($this->guruUser);

        $response = $this->get(route('guru.bobot-nilai'));
        $response->assertRedirect(route('guru.input-sumatif'));
    }
}
