<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Pembayaran;
use App\Models\MataPelajaran;
use App\Models\KomponenNilai;
use App\Models\Nilai;
use App\Models\Rapor;
use App\Models\RaporDetail;
use App\Models\PengajuanKoreksiNilai;
use Livewire\Livewire;
use App\Livewire\Murid\RaporNilai;
use App\Livewire\Murid\Dashboard as MuridDashboard;
use App\Livewire\Finance\InputPembayaran;
use App\Livewire\Koordinator\ManajemenKoreksiNilai;
use App\Livewire\SuperAdmin\TataKelola\ManajemenSiswa;
use App\Livewire\TataUsaha\ManajemenKaryawan;
use App\Livewire\TataUsaha\ManajemenPiketGuru;
use App\Livewire\TataUsaha\DataAlumni;
use App\Livewire\KepalaSekolah\Dashboard as KepalaSekolahDashboard;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);
    $this->artisan('db:seed', ['--class' => 'JenisTagihanSeeder']);

    foreach (['koordinator', 'tata_usaha', 'kepala_sekolah'] as $roleName) {
        $role = Role::where('nama', $roleName)->first() ?? Role::create(['nama' => $roleName]);
        User::firstOrCreate(['username' => $roleName . '_test'], [
            'nama' => ucfirst(str_replace('_', ' ', $roleName)),
            'email' => $roleName . '@test.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);
    }

    $this->userFinance = User::whereHas('role', fn($q) => $q->where('nama', 'finance'))->first();
    $this->userKoordinator = User::whereHas('role', fn($q) => $q->where('nama', 'koordinator'))->first();
    $this->userSuperAdmin = User::whereHas('role', fn($q) => $q->where('nama', 'super_admin'))->first();
    $this->userGuru = User::whereHas('role', fn($q) => $q->where('nama', 'guru'))->first();
    $this->userMurid = User::whereHas('role', fn($q) => $q->where('nama', 'murid'))->first();
    $this->userTU = User::whereHas('role', fn($q) => $q->where('nama', 'tata_usaha'))->first();
    $this->userKepala = User::whereHas('role', fn($q) => $q->where('nama', 'kepala_sekolah'))->first();

    $this->guru = Guru::first() ?? Guru::create(['user_id' => $this->userGuru->id, 'nip' => '12345', 'jenis_guru' => 'mapel']);
    $this->siswa = Siswa::first() ?? Siswa::create(['user_id' => $this->userMurid->id, 'nis' => '9999', 'nama_wali' => 'Wali Siswa']);
});

test('1. spp lock only triggers on or after due date for unpaid blocking bills', function () {
    $this->actingAs($this->userMurid);

    $tahunAjaran = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::first() ?? TahunAjaran::create(['nama' => '2025/2026', 'status_aktif' => true]);
    $jtSpp = JenisTagihan::where('is_blocking', true)->first() ?? JenisTagihan::create(['nama' => 'SPP', 'tipe' => 'bulanan', 'is_blocking' => true, 'is_active' => true]);

    // Clear existing bills for clean testing
    Tagihan::where('siswa_id', $this->siswa->id)->delete();

    // Create a future bill (due in 5 days) -> should NOT lock portal
    $futureBill = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jtSpp->id,
        'tahun_ajaran_id' => $tahunAjaran->id,
        'bulan' => 'Juli',
        'nominal' => 500000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => Carbon::today()->addDays(5)->toDateString(),
    ]);

    $component = Livewire::test(RaporNilai::class);
    expect($component->get('hasOutstanding'))->toBeFalse();

    // Change bill due date to yesterday (overdue) -> SHOULD lock portal
    $futureBill->update([
        'jatuh_tempo' => Carbon::today()->subDay(1)->toDateString(),
    ]);

    $componentOverdue = Livewire::test(RaporNilai::class);
    expect($componentOverdue->get('hasOutstanding'))->toBeTrue();
});

test('2. koordinator approval of grade correction auto-recalculates published rapor_detail', function () {
    $this->actingAs($this->userKoordinator);

    $tahunAjaran = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::first() ?? TahunAjaran::create(['nama' => '2025/2026', 'status_aktif' => true]);
    $semester = Semester::where('status_aktif', true)->first() ?? Semester::first() ?? Semester::create(['tahun_ajaran_id' => $tahunAjaran->id, 'nama' => 'Ganjil', 'status_aktif' => true]);

    $kelas = Kelas::first() ?? Kelas::create(['nama_kelas' => '7A', 'tingkat' => '7']);
    $this->siswa->update(['kelas_id' => $kelas->id]);

    $mapel = MataPelajaran::first() ?? MataPelajaran::create(['nama_matapelajaran' => 'Matematika', 'kode' => 'MTK']);
    $komponen = KomponenNilai::first() ?? KomponenNilai::create(['nama' => 'PAS', 'bobot' => 30]);

    // Original grade = 70.00
    $nilai = Nilai::create([
        'siswa_id' => $this->siswa->id,
        'kelas_id' => $kelas->id,
        'mapel_id' => $mapel->id,
        'guru_id' => $this->guru->id,
        'semester_id' => $semester->id,
        'komponen_nilai_id' => $komponen->id,
        'tanggal' => now()->toDateString(),
        'nilai' => 70.00,
    ]);

    // Published Rapor Header & Rapor Detail snapshot (initial grade = 70.00)
    $rapor = Rapor::create([
        'siswa_id' => $this->siswa->id,
        'kelas_id' => $kelas->id,
        'semester_id' => $semester->id,
        'catatan_wali_kelas' => 'Pertahankan',
        'tanggal_terbit' => now()->toDateString(),
    ]);

    $raporDetail = RaporDetail::create([
        'rapor_id' => $rapor->id,
        'mapel_id' => $mapel->id,
        'nilai_pengetahuan' => 70.00,
        'nilai_akhir' => 70.00,
        'predikat' => 'C',
    ]);

    // Teacher requests grade correction to 90.00
    $pengajuan = PengajuanKoreksiNilai::create([
        'nilai_id' => $nilai->id,
        'nilai_baru' => 90.00,
        'alasan' => 'Salah rekap nilai ujian',
        'status' => 'pending',
        'diajukan_oleh_guru_id' => $this->guru->id,
    ]);

    // Koordinator approves the request
    Livewire::test(ManajemenKoreksiNilai::class)
        ->call('approve', $pengajuan->id)
        ->assertHasNoErrors();

    // Assert grade in 'nilai' table is updated
    $nilai->refresh();
    expect(floatval($nilai->nilai))->toEqual(90.00);

    // Assert snapshot 'rapor_detail' was auto-recalculated and updated!
    $raporDetail->refresh();
    expect(floatval($raporDetail->nilai_akhir))->toBeGreaterThan(70.00);
    expect($raporDetail->predikat)->toEqual('A');
});

test('3. input pembayaran generates no_resi, handles overpayment deposit, and uses atomic lock', function () {
    $this->actingAs($this->userFinance);

    $tahunAjaran = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::first() ?? TahunAjaran::create(['nama' => '2025/2026', 'status_aktif' => true]);
    $jtSpp = JenisTagihan::first() ?? JenisTagihan::create(['nama' => 'SPP', 'tipe' => 'bulanan', 'is_blocking' => true, 'is_active' => true]);

    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jtSpp->id,
        'tahun_ajaran_id' => $tahunAjaran->id,
        'bulan' => 'Agustus',
        'nominal' => 500000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10)->toDateString(),
    ]);

    // Parent pays Rp 600.000 for a Rp 500.000 bill (Rp 100.000 excess)
    Livewire::test(InputPembayaran::class)
        ->set('siswa_id', $this->siswa->id)
        ->set('tagihan_id', $tagihan->id)
        ->set('tanggal_bayar', now()->toDateString())
        ->set('nominal_dibayar', 600000)
        ->set('metode_bayar', 'Transfer Bank')
        ->call('savePayment')
        ->assertHasNoErrors();

    $pembayaran = Pembayaran::where('tagihan_id', $tagihan->id)->first();
    expect($pembayaran)->not->toBeNull();
    expect($pembayaran->no_resi)->toStartWith('KW-');
    expect(floatval($pembayaran->kelebihan_bayar))->toEqual(100000.00);

    // Verify student deposit increased by Rp 100.000
    $this->siswa->refresh();
    expect(floatval($this->siswa->saldo_deposit))->toEqual(100000.00);

    // Verify invoice status is lunas
    $tagihan->refresh();
    expect($tagihan->status)->toEqual('lunas');
});

test('4. changing student status to pindah or keluar automatically cancels future unpaid bills', function () {
    $this->actingAs($this->userSuperAdmin);

    $tahunAjaran = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::first() ?? TahunAjaran::create(['nama' => '2025/2026', 'status_aktif' => true]);
    $jtSpp = JenisTagihan::first() ?? JenisTagihan::create(['nama' => 'SPP', 'tipe' => 'bulanan', 'is_blocking' => true, 'is_active' => true]);

    // Create a future unpaid bill
    $futureBill = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jtSpp->id,
        'tahun_ajaran_id' => $tahunAjaran->id,
        'bulan' => 'Desember',
        'nominal' => 500000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => Carbon::today()->addDays(30)->toDateString(),
    ]);

    // Super Admin updates student status to 'pindah'
    Livewire::test(ManajemenSiswa::class)
        ->call('openEdit', $this->siswa->id)
        ->set('status', 'pindah')
        ->call('save')
        ->assertHasNoErrors();

    $this->siswa->refresh();
    expect($this->siswa->status)->toEqual('pindah');

    // Assert future unpaid bill was automatically canceled ('batal')
    $futureBill->refresh();
    expect($futureBill->status)->toEqual('batal');
});

test('5. dual role teacher supports jenis_guru keduanya', function () {
    $this->guru->update(['jenis_guru' => 'keduanya']);
    expect($this->guru->jenis_guru)->toEqual('keduanya');
});

test('6. tata usaha can view employee directory and alumni', function () {
    $this->actingAs($this->userTU);

    Livewire::test(ManajemenKaryawan::class)->assertStatus(200);
    Livewire::test(DataAlumni::class)->assertStatus(200);
});

test('7. kepala sekolah can view read-only executive monitoring dashboard', function () {
    $this->actingAs($this->userKepala);

    Livewire::test(KepalaSekolahDashboard::class)
        ->assertStatus(200)
        ->assertSee('Dashboard Pemantauan Kepala Sekolah');
});
