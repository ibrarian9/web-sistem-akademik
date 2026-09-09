<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\CatatanPendampingan;
use App\Models\KategoriPengeluaran;
use App\Livewire\Guru\CatatanPendampinganIndex;
use App\Livewire\Finance\ArusKas;
use App\Livewire\Finance\ArusKasMasuk;
use App\Livewire\Finance\ArusKasKeluar;
use App\Livewire\Finance\TabunganSiswa;
use Livewire\Livewire;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);

    // Setup active Academic Year and Semester
    $this->ta = TahunAjaran::firstOrCreate(
        ['nama' => '2026/2027'],
        ['status_aktif' => true]
    );

    $this->semester = Semester::firstOrCreate(
        ['tahun_ajaran_id' => $this->ta->id, 'semester' => 'ganjil'],
        ['status_aktif' => true, 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2026-12-31']
    );

    // Setup Guru Pendamping
    $roleGuru = Role::where('nama', 'guru')->first();
    $this->userGuruPendamping = User::create([
        'nama' => 'Ustadzah Pendamping ABK',
        'username' => 'guru_pendamping_test',
        'email' => 'pendamping@test.com',
        'password' => bcrypt('password'),
        'role_id' => $roleGuru->id,
    ]);

    $this->guruPendamping = Guru::create([
        'user_id' => $this->userGuruPendamping->id,
        'nip' => 'GP2026001',
        'jenis_guru' => 'pendamping',
        'status_kepegawaian' => 'tetap',
        'tanggal_masuk' => '2024-01-01',
    ]);

    // Setup Student with Shadow Teacher assignment
    $this->siswa = Siswa::first();
    if ($this->siswa) {
        $this->siswa->update(['shadow_teacher_id' => $this->guruPendamping->id]);
    }

    // Setup Finance User
    $roleFinance = Role::where('nama', 'finance')->first();
    $this->userFinance = User::where('role_id', $roleFinance->id)->first();
    if (!$this->userFinance) {
        $this->userFinance = User::create([
            'nama' => 'Staff Finance Test',
            'username' => 'finance_test_user',
            'email' => 'finance_test@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleFinance->id,
        ]);
    }
});

/*
|--------------------------------------------------------------------------
| A. Guru Pendamping Model & Relationship Tests
|--------------------------------------------------------------------------
*/

test('guru model detects isGuruPendamping and relates to assigned students', function () {
    expect($this->guruPendamping->isGuruPendamping())->toBeTrue();
    expect($this->guruPendamping->siswaDidampingi()->count())->toBeGreaterThanOrEqual(1);

    $assignedStudent = $this->guruPendamping->siswaDidampingi()->first();
    expect($assignedStudent->shadowTeacher->id)->toBe($this->guruPendamping->id);
});

/*
|--------------------------------------------------------------------------
| B. Catatan Pendampingan Livewire Input & Qualitative Standardization Tests
|--------------------------------------------------------------------------
*/

test('guru pendamping can create qualitative observation record without numerical score', function () {
    $this->actingAs($this->userGuruPendamping);

    Livewire::test(CatatanPendampinganIndex::class)
        ->set('form_tanggal', '2026-09-09')
        ->set('form_siswa_id', $this->siswa->id)
        ->set('form_aspek', 'Komunikasi')
        ->set('form_hasil_perkembangan', 'BSH')
        ->set('form_catatan', 'Ananda sudah dapat menjawab salam guru dan menatap mata saat berbicara selama 10 detik.')
        ->set('form_rekomendasi', 'Lanjutkan latihan dialog bergantian saat jam istirahat.')
        ->set('form_periode', 'tengah_semester')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('catatan_pendampingan', [
        'guru_id' => $this->guruPendamping->id,
        'siswa_id' => $this->siswa->id,
        'aspek' => 'Komunikasi',
        'hasil_perkembangan' => 'BSH',
    ]);
});

test('catatan pendampingan rejects invalid qualitative scale', function () {
    $this->actingAs($this->userGuruPendamping);

    Livewire::test(CatatanPendampinganIndex::class)
        ->set('form_tanggal', '2026-09-09')
        ->set('form_siswa_id', $this->siswa->id)
        ->set('form_aspek', 'Kemandirian')
        ->set('form_hasil_perkembangan', 'ANGKA_85') // Non-qualitative value must be rejected
        ->set('form_catatan', 'Catatan observasi harian')
        ->call('save')
        ->assertHasErrors(['form_hasil_perkembangan']);
});

test('catatan pendampingan rekap tab computes scale distribution per aspect', function () {
    $this->actingAs($this->userGuruPendamping);

    // Seed 2 distinct records
    CatatanPendampingan::create([
        'siswa_id' => $this->siswa->id,
        'guru_id' => $this->guruPendamping->id,
        'semester_id' => $this->semester->id,
        'tanggal' => '2026-08-15',
        'aspek' => 'Motorik',
        'hasil_perkembangan' => 'MB',
        'catatan' => 'Mulai memegang pensil dengan posisi tripod, perlu bantuan untuk menggunting pola lurus.',
        'periode' => 'tengah_semester',
    ]);

    CatatanPendampingan::create([
        'siswa_id' => $this->siswa->id,
        'guru_id' => $this->guruPendamping->id,
        'semester_id' => $this->semester->id,
        'tanggal' => '2026-09-05',
        'aspek' => 'Motorik',
        'hasil_perkembangan' => 'BSH',
        'catatan' => 'Sudah dapat memegang gunting dan melipat kertas. Berkembang sangat pesat pada sesi terapi okupasi.',
        'periode' => 'tengah_semester',
    ]);

    Livewire::test(CatatanPendampinganIndex::class)
        ->set('tab', 'rekap')
        ->set('selectedSiswaId', $this->siswa->id)
        ->set('rekapPeriode', 'tengah_semester')
        ->assertSee('Motorik')
        ->assertSee('MB:')
        ->assertSee('BSH:')
        ->assertSee('Sudah dapat memegang gunting');
});

/*
|--------------------------------------------------------------------------
| C. PDF Report Generation & Verification
|--------------------------------------------------------------------------
*/

test('cetak laporan pendampingan endpoint streams valid official pdf', function () {
    $this->actingAs($this->userGuruPendamping);

    CatatanPendampingan::create([
        'siswa_id' => $this->siswa->id,
        'guru_id' => $this->guruPendamping->id,
        'semester_id' => $this->semester->id,
        'tanggal' => '2026-09-01',
        'aspek' => 'Konsentrasi/Fokus',
        'hasil_perkembangan' => 'BSB',
        'catatan' => 'Mampu fokus menyelesaikan tugas mewarnai selama 25 menit.',
        'rekomendasi' => 'Pertahankan rutinitas visual schedule.',
        'periode' => 'tengah_semester',
    ]);

    $response = $this->get(route('pendampingan.cetak', [
        'siswaId' => $this->siswa->id,
        'periode' => 'tengah_semester',
        'semester_id' => $this->semester->id,
    ]));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
});

/*
|--------------------------------------------------------------------------
| D. Modul Keuangan: Acceptance of Small Nominal (500) & Real-time Formatting
|--------------------------------------------------------------------------
*/

test('ArusKasMasuk accepts nominal 500 without min 1000 rejection', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(ArusKasMasuk::class)
        ->set('kategori', 'Infaq')
        ->set('jumlah', 500) // Small nominal like 500
        ->set('tanggal', date('Y-m-d'))
        ->set('keterangan', 'Infaq receh sedekah subuh Rp 500')
        ->call('saveIncome')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('pemasukan_kas', [
        'kategori' => 'Infaq',
        'jumlah' => 500.00,
        'keterangan' => 'Infaq receh sedekah subuh Rp 500',
    ]);
});

test('ArusKasKeluar accepts nominal 500 without min 1000 rejection', function () {
    $this->actingAs($this->userFinance);

    $kategori = KategoriPengeluaran::firstOrCreate(['nama' => 'Operasional ATK']);

    Livewire::test(ArusKasKeluar::class)
        ->set('kategori_pengeluaran_id', $kategori->id)
        ->set('jumlah', 500) // Small nominal like 500
        ->set('tanggal', date('Y-m-d'))
        ->set('keterangan', 'Beli klip kertas kecil Rp 500')
        ->call('saveExpense')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('pengeluaran', [
        'kategori_pengeluaran_id' => $kategori->id,
        'jumlah' => 500.00,
        'keterangan' => 'Beli klip kertas kecil Rp 500',
    ]);
});

test('TabunganSiswa accepts nominal 500 without min 1000 rejection', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(TabunganSiswa::class)
        ->set('siswa_id', $this->siswa->id)
        ->set('jenis', 'setor')
        ->set('nominal', 500)
        ->set('tanggal', date('Y-m-d'))
        ->set('keterangan', 'Setoran koin Rp 500')
        ->call('saveTransaction')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('tabungans', [
        'siswa_id' => $this->siswa->id,
        'nominal' => 500.00,
        'keterangan' => 'Setoran koin Rp 500',
    ]);
});

/*
|--------------------------------------------------------------------------
| E. Modul Keuangan: Filter Toolbar Functionality in ArusKas
|--------------------------------------------------------------------------
*/

test('ArusKas filters transactions by nominal range and payment method', function () {
    $this->actingAs($this->userFinance);

    $testComponent = Livewire::test(ArusKas::class)
        ->set('nominalMin', '1000')
        ->set('nominalMax', '5000000')
        ->set('filterMetode', 'tunai');

    expect($testComponent->get('nominalMin'))->toBe('1000');
    expect($testComponent->get('nominalMax'))->toBe('5000000');
    expect($testComponent->get('filterMetode'))->toBe('tunai');

    // Test Reset action
    $testComponent->call('resetFilters');

    expect($testComponent->get('nominalMin'))->toBeNull();
    expect($testComponent->get('nominalMax'))->toBeNull();
    expect($testComponent->get('filterMetode'))->toBe('semua');
    expect($testComponent->get('search'))->toBe('');
});
