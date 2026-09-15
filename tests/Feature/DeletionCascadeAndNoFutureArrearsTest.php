<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\GuruMapelKelas;
use App\Models\JenisTagihan;
use App\Models\Tagihan;
use App\Livewire\SuperAdmin\TataKelola\ManajemenGuru;
use App\Livewire\SuperAdmin\TataKelola\ManajemenSiswa;
use App\Livewire\TataUsaha\ManajemenKaryawan;
use App\Livewire\SuperAdmin\TataKelola\ManajemenUser;
use App\Livewire\Finance\InputPembayaran;
use App\Livewire\Finance\ManajemenTagihan;
use Livewire\Livewire;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeletionCascadeAndNoFutureArrearsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $tuUser;
    protected User $financeUser;
    protected TahunAjaran $tahunAjaran;
    protected Semester $semester;
    protected Kelas $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::firstOrCreate(['nama' => 'super_admin']);
        $roleTU = Role::firstOrCreate(['nama' => 'tata_usaha']);
        $roleFinance = Role::firstOrCreate(['nama' => 'finance']);
        Role::firstOrCreate(['nama' => 'guru']);
        Role::firstOrCreate(['nama' => 'murid']);

        $this->superAdmin = User::factory()->create([
            'role_id' => $roleAdmin->id,
            'status' => 'aktif',
        ]);

        $this->tuUser = User::factory()->create([
            'role_id' => $roleTU->id,
            'status' => 'aktif',
        ]);

        $this->financeUser = User::factory()->create([
            'role_id' => $roleFinance->id,
            'status' => 'aktif',
        ]);

        $this->tahunAjaran = TahunAjaran::firstOrCreate(
            ['nama' => '2026/2027'],
            ['status_aktif' => true, 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30']
        );

        $this->semester = Semester::firstOrCreate(
            ['tahun_ajaran_id' => $this->tahunAjaran->id, 'semester' => 'ganjil'],
            ['tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2026-12-31', 'status_aktif' => true]
        );

        $this->kelas = Kelas::firstOrCreate(
            ['nama_kelas' => '7B'],
            ['tingkat' => 7, 'kapasitas' => 30, 'semester_id' => $this->semester->id]
        );
    }

    public function test_can_delete_guru_with_wali_kelas_and_schedules_without_fk_constraint_error(): void
    {
        $guruRole = Role::where('nama', 'guru')->first();
        $guruUser = User::factory()->create([
            'nama' => 'Ustadz Fulan',
            'role_id' => $guruRole->id,
        ]);

        $guru = Guru::create([
            'user_id' => $guruUser->id,
            'nip' => '19900202',
            'tanggal_masuk' => '2020-01-01',
            'status_aktif' => true,
        ]);

        // Assign guru as wali kelas
        $this->kelas->update([
            'guru_umum_id' => $guru->id,
            'guru_tahfidz_id' => $guru->id,
        ]);

        // Create Mapel & GMK & Jadwal
        $mapel = MataPelajaran::firstOrCreate(
            ['nama_mapel' => 'Pendidikan Agama Islam'],
            ['jenis' => 'umum']
        );

        $gmk = GuruMapelKelas::create([
            'guru_id' => $guru->id,
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $mapel->id,
            'semester_id' => $this->semester->id,
        ]);

        JadwalPelajaran::create([
            'guru_mapel_kelas_id' => $gmk->id,
            'hari' => 'senin',
            'jam_mulai' => '07:30',
            'jam_selesai' => '09:00',
        ]);

        // Execute delete
        Livewire::actingAs($this->superAdmin)
            ->test(ManajemenGuru::class)
            ->call('delete', $guru->id)
            ->assertDispatched('show-alert');

        $this->assertSoftDeleted('guru', ['id' => $guru->id]);
        $this->assertSoftDeleted('users', ['id' => $guruUser->id]);

        // Wali kelas should be detached safely to null
        $this->kelas->refresh();
        $this->assertNull($this->kelas->guru_umum_id);
        $this->assertNull($this->kelas->guru_tahfidz_id);

        // GMK was cleaned up
        $this->assertEquals(0, GuruMapelKelas::where('guru_id', $guru->id)->count());

        // withTrashed() ensures $guru->user still resolves
        $guruReloaded = Guru::withTrashed()->find($guru->id);
        $this->assertNotNull($guruReloaded->user);
        $this->assertEquals('Ustadz Fulan', $guruReloaded->user->nama);
    }

    public function test_can_delete_karyawan_without_errors(): void
    {
        $tuRole = Role::where('nama', 'tata_usaha')->first();
        $staffUser = User::factory()->create([
            'nama' => 'Staff Administrasi',
            'role_id' => $tuRole->id,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManajemenKaryawan::class)
            ->call('delete', $staffUser->id)
            ->assertDispatched('show-alert');

        $this->assertSoftDeleted('users', ['id' => $staffUser->id]);
    }

    public function test_can_delete_siswa_and_unpaid_bills_are_cleaned_up_from_arrears(): void
    {
        $muridRole = Role::where('nama', 'murid')->first();
        $studentUser = User::factory()->create([
            'nama' => 'Santri Ali',
            'role_id' => $muridRole->id,
        ]);

        $siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'nis' => '8801',
            'kelas_id' => $this->kelas->id,
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
        ]);

        $jenisSpp = JenisTagihan::firstOrCreate(
            ['nama' => 'SPP'],
            ['kategori' => 'rutin', 'default_nominal' => 300000]
        );

        // Past bill
        $pastBill = Tagihan::create([
            'siswa_id' => $siswa->id,
            'jenis_tagihan_id' => $jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Agustus',
            'nominal' => 300000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->subMonth()->startOfMonth()->addDays(9)->toDateString(),
        ]);

        // Future bill (bulan-bulan berikutnya)
        $futureBill = Tagihan::create([
            'siswa_id' => $siswa->id,
            'jenis_tagihan_id' => $jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'November',
            'nominal' => 300000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->addMonths(2)->startOfMonth()->addDays(9)->toDateString(),
        ]);

        $this->assertEquals(1, Tagihan::tunggakan()->count());

        // Delete siswa
        Livewire::actingAs($this->superAdmin)
            ->test(ManajemenSiswa::class)
            ->call('delete', $siswa->id)
            ->assertDispatched('show-alert');

        $this->assertSoftDeleted('siswa', ['id' => $siswa->id]);
        $this->assertSoftDeleted('users', ['id' => $studentUser->id]);

        // Unpaid bills should be soft-deleted
        $this->assertSoftDeleted('tagihan', ['id' => $pastBill->id]);
        $this->assertSoftDeleted('tagihan', ['id' => $futureBill->id]);

        // Arrears count should now be 0
        $this->assertEquals(0, Tagihan::tunggakan()->count());

        // withTrashed() ensures $siswa->user still resolves
        $siswaReloaded = Siswa::withTrashed()->find($siswa->id);
        $this->assertNotNull($siswaReloaded->user);
        $this->assertEquals('Santri Ali', $siswaReloaded->user->nama);
    }

    public function test_future_months_bills_are_not_counted_as_arrears_in_kasir_and_tagihan_filter(): void
    {
        $muridRole = Role::where('nama', 'murid')->first();
        $studentUser = User::factory()->create([
            'nama' => 'Santri Budi',
            'role_id' => $muridRole->id,
        ]);

        $siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'nis' => '8802',
            'kelas_id' => $this->kelas->id,
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
        ]);

        $jenisSpp = JenisTagihan::firstOrCreate(
            ['nama' => 'SPP'],
            ['kategori' => 'rutin', 'default_nominal' => 300000]
        );

        // Current / past month is fully paid
        Tagihan::create([
            'siswa_id' => $siswa->id,
            'jenis_tagihan_id' => $jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'September',
            'nominal' => 300000,
            'total_dibayar' => 300000,
            'status' => 'lunas',
            'jatuh_tempo' => Carbon::now()->startOfMonth()->addDays(9)->toDateString(),
        ]);

        // Released bills for future months (3 bulan ke depan)
        $future1 = Tagihan::create([
            'siswa_id' => $siswa->id,
            'jenis_tagihan_id' => $jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Oktober',
            'nominal' => 300000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->addMonth()->startOfMonth()->addDays(9)->toDateString(),
        ]);

        $future2 = Tagihan::create([
            'siswa_id' => $siswa->id,
            'jenis_tagihan_id' => $jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'November',
            'nominal' => 300000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->addMonths(2)->startOfMonth()->addDays(9)->toDateString(),
        ]);

        // Tunggakan should be 0 because all due bills are paid
        $this->assertEquals(0, Tagihan::tunggakan()->count());
        $this->assertEquals(2, Tagihan::mendatang()->count());

        // In InputPembayaran (Kasir), Daftar Tunggakan Aktif should NOT show future bills as tunggakan
        Livewire::actingAs($this->financeUser)
            ->test(InputPembayaran::class)
            ->assertViewHas('activeTunggakan', function ($collection) {
                return $collection->total() === 0;
            });

        // In ManajemenTagihan, filtering by status 'belum_bayar' should NOT match this student
        Livewire::actingAs($this->financeUser)
            ->test(ManajemenTagihan::class)
            ->set('filterStatus', 'belum_bayar')
            ->assertDontSee('Santri Budi');
    }

    public function test_can_delete_user_and_cascades_safely_to_related_guru_and_siswa(): void
    {
        $guruRole = Role::where('nama', 'guru')->first();
        $targetUser = User::factory()->create([
            'nama' => 'Ustadz Umar',
            'role_id' => $guruRole->id,
        ]);

        $guru = Guru::create([
            'user_id' => $targetUser->id,
            'nip' => '19950505',
            'tanggal_masuk' => '2021-01-01',
            'status_aktif' => true,
        ]);

        $this->kelas->update(['guru_umum_id' => $guru->id]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManajemenUser::class)
            ->call('delete', $targetUser->id);

        $this->assertSoftDeleted('users', ['id' => $targetUser->id]);
        $this->assertSoftDeleted('guru', ['id' => $guru->id]);

        $this->kelas->refresh();
        $this->assertNull($this->kelas->guru_umum_id);
    }
}
