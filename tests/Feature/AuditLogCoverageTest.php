<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\JenisTagihan;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\GajiGuru;
use App\Models\AbsensiSiswa;
use App\Models\NilaiSumatifTp;
use App\Models\LingkupMateri;
use App\Models\TujuanPembelajaran;
use App\Models\MataPelajaran;
use App\Models\RiwayatSurat;
use Illuminate\Support\Facades\DB;

test('audit log records all activities performed by Finance, Tata Usaha, and Guru', function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

    $roleSuperAdmin = Role::where('nama', 'super_admin')->first();
    $roleFinance = Role::where('nama', 'finance')->first();
    $roleTU = Role::where('nama', 'tata_usaha')->first();
    $roleGuru = Role::where('nama', 'guru')->first();
    $roleMurid = Role::where('nama', 'murid')->first();

    $financeUser = User::create([
        'nama' => 'Finance Staff',
        'username' => 'finance_staff',
        'email' => 'finance_staff@test.com',
        'password' => bcrypt('password'),
        'role_id' => $roleFinance->id,
        'status' => 'aktif',
    ]);

    $tuUser = User::create([
        'nama' => 'TU Staff',
        'username' => 'tu_staff',
        'email' => 'tu_staff@test.com',
        'password' => bcrypt('password'),
        'role_id' => $roleTU->id,
        'status' => 'aktif',
    ]);

    $guruUser = User::create([
        'nama' => 'Guru Pengajar',
        'username' => 'guru_pengajar',
        'email' => 'guru_pengajar@test.com',
        'password' => bcrypt('password'),
        'role_id' => $roleGuru->id,
        'status' => 'aktif',
    ]);
    $guru = Guru::create([
        'user_id' => $guruUser->id,
        'nip' => '198901012015011001',
        'niy' => 'YFI-G05',
        'jenis_guru' => 'umum',
        'status_kepegawaian' => 'tetap_yayasan',
        'pendidikan' => 'S1',
        'tanggal_masuk' => date('Y-m-d'),
        'status_aktif' => true,
    ]);

    $ta = TahunAjaran::create(['nama' => '2026/2027', 'status_aktif' => true]);
    $semester = Semester::create([
        'tahun_ajaran_id' => $ta->id,
        'semester' => 'ganjil',
        'tanggal_mulai' => date('Y-m-d'),
        'tanggal_selesai' => date('Y-m-d', strtotime('+6 months')),
        'status_aktif' => true,
    ]);
    $kelas = Kelas::create(['nama_kelas' => '8A', 'tingkat' => 8, 'semester_id' => $semester->id]);
    $muridUser = User::create([
        'nama' => 'Ahmad Siswa',
        'username' => 'ahmad_siswa',
        'email' => 'ahmad@test.com',
        'password' => bcrypt('password'),
        'role_id' => $roleMurid->id,
        'status' => 'aktif',
    ]);
    $siswa = Siswa::create([
        'user_id' => $muridUser->id,
        'nis' => '2001',
        'nisn' => '0099887766',
        'kelas_id' => $kelas->id,
        'nama_wali' => 'Bapak Ahmad',
        'no_hp_wali' => '081234567890',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    // 1. Activity by FINANCE: Input Pembayaran & Gaji
    $this->actingAs($financeUser);
    $jt = JenisTagihan::create(['nama' => 'SPP', 'default_nominal' => 400000]);
    $tagihan = Tagihan::create([
        'siswa_id' => $siswa->id,
        'tahun_ajaran_id' => $ta->id,
        'jenis_tagihan_id' => $jt->id,
        'bulan' => 'Agustus',
        'nominal' => 400000,
        'total_dibayar' => 400000,
        'status' => 'lunas',
        'jatuh_tempo' => date('Y-m-d'),
    ]);
    $pembayaran = Pembayaran::create([
        'no_resi' => 'RES-TEST-001',
        'tagihan_id' => $tagihan->id,
        'tanggal_bayar' => date('Y-m-d'),
        'nominal_dibayar' => 400000,
        'metode_bayar' => 'Tunai',
        'petugas_id' => $financeUser->id,
    ]);
    $gaji = GajiGuru::create([
        'guru_id' => $guru->id,
        'bulan' => 'Agustus',
        'tahun' => 2026,
        'tanggal_bayar' => date('Y-m-d'),
        'gaji_pokok' => 2000000,
        'total_diterima' => 2000000,
        'status' => 'draft',
    ]);

    // Verify Finance Audit Logs exist
    $financeLogs = DB::table('activity_log')
        ->where('causer_id', $financeUser->id)
        ->get();
    expect($financeLogs->count())->toBeGreaterThanOrEqual(2);
    expect($financeLogs->pluck('log_name')->contains('keuangan'))->toBeTrue();

    // 2. Activity by TATA USAHA: Persuratan & Setup Kalender
    $this->actingAs($tuUser);
    $surat = RiwayatSurat::create([
        'nomor_surat' => '001/YFI/VIII/2026',
        'jenis_surat' => 'aktif_sekolah',
        'penerima_nama' => 'Ahmad Siswa',
        'tanggal_surat' => date('Y-m-d'),
        'payload_json' => ['nis' => '2001'],
        'created_by' => $tuUser->id,
    ]);

    $tuLogs = DB::table('activity_log')
        ->where('causer_id', $tuUser->id)
        ->get();
    expect($tuLogs->count())->toBeGreaterThanOrEqual(1);

    // 3. Activity by GURU: Presensi & Input Nilai
    $this->actingAs($guruUser);
    $mapel = MataPelajaran::create(['nama_mapel' => 'Matematika', 'kode_mapel' => 'MTK']);
    $lm = LingkupMateri::create(['mapel_id' => $mapel->id, 'nama_lingkup_materi' => 'Aljabar', 'kategori' => 'formatif', 'urutan' => 1]);
    $tp = TujuanPembelajaran::create(['lingkup_materi_id' => $lm->id, 'deskripsi_tp' => 'Memahami Aljabar', 'urutan' => 1]);

    $nilaiTp = NilaiSumatifTp::create([
        'siswa_id' => $siswa->id,
        'tp_id' => $tp->id,
        'semester_id' => $semester->id,
        'nilai' => 88,
    ]);

    $absensi = AbsensiSiswa::create([
        'siswa_id' => $siswa->id,
        'guru_id' => $guru->id,
        'kelas_id' => $kelas->id,
        'tanggal' => date('Y-m-d'),
        'status' => 'hadir',
    ]);

    $guruLogs = DB::table('activity_log')
        ->where('causer_id', $guruUser->id)
        ->get();
    expect($guruLogs->count())->toBeGreaterThanOrEqual(2);
    expect($guruLogs->pluck('log_name')->contains('akademik'))->toBeTrue();
});
