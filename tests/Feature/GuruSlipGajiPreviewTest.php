<?php

use App\Models\GajiGuru;
use App\Models\Guru;
use App\Models\Role;
use App\Models\User;
use App\Livewire\Guru\SlipGajiSaya;
use App\Livewire\Finance\ManajemenGajiGuru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);
    $this->roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Finance']);

    // Setup Guru 1
    $this->userGuru1 = User::create([
        'nama' => 'Ustadz Ahmad Fauzi, S.Pd.',
        'username' => 'guru_ahmad',
        'email' => 'ahmad@test.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleGuru->id,
        'status' => 'aktif',
    ]);
    $this->guru1 = Guru::create([
        'user_id' => $this->userGuru1->id,
        'nip' => '198501012010011001',
        'nik' => '3201010101850001',
        'jenis_guru' => 'umum',
        'status_kepegawaian' => 'gtt',
        'pendidikan' => 'S1 Pendidikan',
        'tanggal_masuk' => date('Y-m-d'),
        'status_aktif' => true,
    ]);

    // Setup Guru 2
    $this->userGuru2 = User::create([
        'nama' => 'Ustadz Bambang, M.Pd.',
        'username' => 'guru_bambang',
        'email' => 'bambang@test.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleGuru->id,
        'status' => 'aktif',
    ]);
    $this->guru2 = Guru::create([
        'user_id' => $this->userGuru2->id,
        'nip' => '198702022012011002',
        'nik' => '3201010101870002',
        'jenis_guru' => 'tahfidz',
        'status_kepegawaian' => 'honorer',
        'pendidikan' => 'S2 Pendidikan',
        'tanggal_masuk' => date('Y-m-d'),
        'status_aktif' => true,
    ]);

    // Setup Finance User
    $this->userFinance = User::create([
        'nama' => 'Staff Keuangan',
        'username' => 'finance_staff',
        'email' => 'finance@test.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleFinance->id,
        'status' => 'aktif',
    ]);

    // Create Salary Records for Guru 1
    $this->salary1 = GajiGuru::create([
        'guru_id' => $this->guru1->id,
        'bulan' => 'Januari',
        'tahun' => (int) date('Y'),
        'gaji_pokok' => 3000000.00,
        'insentif_bpjs' => 200000.00,
        'insentif_maghrib_mengaji' => 300000.00,
        'potongan_peminjaman' => 500000.00,
        'potongan_lainnya' => 0.00,
        'total_diterima' => 3000000.00,
        'tanggal_bayar' => date('Y-m-d'),
        'status' => 'dibayar',
    ]);

    // Create Salary Records for Guru 2
    $this->salary2 = GajiGuru::create([
        'guru_id' => $this->guru2->id,
        'bulan' => 'Januari',
        'tahun' => (int) date('Y'),
        'gaji_pokok' => 2500000.00,
        'insentif_bpjs' => 150000.00,
        'insentif_maghrib_mengaji' => 200000.00,
        'potongan_peminjaman' => 0.00,
        'potongan_lainnya' => 0.00,
        'total_diterima' => 2850000.00,
        'tanggal_bayar' => date('Y-m-d'),
        'status' => 'dibayar',
    ]);
});

test('guru dapat mengakses menu slip gaji saya dan melihat daftar penggajian miliknya', function () {
    $this->actingAs($this->userGuru1);

    $response = $this->get(route('guru.slip-gaji'));
    $response->assertStatus(200);

    Livewire::test(SlipGajiSaya::class)
        ->assertSee('Slip Gaji')
        ->assertSee('Januari ' . date('Y'))
        ->assertSee('Rp 3.000.000')
        ->assertSee('Dibayar')
        ->assertDontSee('Ustadz Bambang');
});

test('guru dapat membuka interactive preview modal slip gaji pdf', function () {
    $this->actingAs($this->userGuru1);

    Livewire::test(SlipGajiSaya::class)
        ->assertSet('showPreviewModal', false)
        ->call('openPreview', $this->salary1->id)
        ->assertSet('showPreviewModal', true)
        ->assertSet('previewSalaryId', $this->salary1->id)
        ->assertSee('Pratinjau Slip Gaji')
        ->assertSee('Unduh PDF')
        ->call('closePreview')
        ->assertSet('showPreviewModal', false);
});

test('guru dapat mendownload dan preview pdf slip gaji miliknya sendiri', function () {
    $this->actingAs($this->userGuru1);

    // 1. Own Slip Gaji: 200 OK
    $response = $this->get(route('gaji-guru.slip', $this->salary1->id));
    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toBe('application/pdf');

    // 2. Download mode
    $downloadResponse = $this->get(route('gaji-guru.slip', ['id' => $this->salary1->id, 'download' => 1]));
    $downloadResponse->assertStatus(200);
    expect($downloadResponse->headers->get('content-type'))->toBe('application/pdf');
});

test('guru tidak dapat mengakses slip gaji guru lain (403 forbidden)', function () {
    $this->actingAs($this->userGuru1);

    // Guru 1 tries to access Guru 2's slip
    $response = $this->get(route('gaji-guru.slip', $this->salary2->id));
    $response->assertStatus(403);
});

test('finance staff dapat membuka preview modal dan mengunduh slip gaji seluruh guru', function () {
    $this->actingAs($this->userFinance);

    // Livewire ManajemenGajiGuru has preview modal
    Livewire::test(ManajemenGajiGuru::class)
        ->assertSet('showPreviewModal', false)
        ->call('openPreview', $this->salary1->id)
        ->assertSet('showPreviewModal', true)
        ->assertSet('previewSalaryId', $this->salary1->id)
        ->assertSee('Pratinjau Slip Gaji')
        ->call('closePreview')
        ->assertSet('showPreviewModal', false);

    // Finance can access any salary slip
    $res1 = $this->get(route('finance.gaji-guru.slip', $this->salary1->id));
    $res1->assertStatus(200);
    expect($res1->headers->get('content-type'))->toBe('application/pdf');

    $res2 = $this->get(route('finance.gaji-guru.slip', $this->salary2->id));
    $res2->assertStatus(200);
    expect($res2->headers->get('content-type'))->toBe('application/pdf');
});

test('template slip gaji tidak lagi menampilkan kop surat yayasan', function () {
    $this->actingAs($this->userGuru1);

    $html = view('livewire.shared.laporan.pdf-slip-gaji', [
        'gaji' => $this->salary1,
        'terbilang' => 'Tiga Juta Rupiah',
    ])->render();

    expect($html)->not->toContain('header-kop');
    expect($html)->not->toContain('YAYASAN PENDIDIKAN ISLAM');
    expect($html)->toContain('SLIP GAJI & PENGHASILAN GURU');
    expect($html)->toContain('Ustadz Ahmad Fauzi');
});

test('finance staff dapat mengunduh bulk slip gaji pdf sekaligus', function () {
    $this->actingAs($this->userFinance);

    // 1. Bulk download by month & year
    $response = $this->get(route('finance.gaji-guru.bulk-slip', [
        'bulan' => 'Januari',
        'tahun' => (int) date('Y'),
    ]));

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toBe('application/pdf');

    // 2. Bulk download by specific IDs
    $responseIds = $this->get(route('finance.gaji-guru.bulk-slip', [
        'ids' => "{$this->salary1->id},{$this->salary2->id}",
    ]));

    $responseIds->assertStatus(200);
    expect($responseIds->headers->get('content-type'))->toBe('application/pdf');
});

test('manajemen gaji guru mendukung select all checkbox untuk bulk slip', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(ManajemenGajiGuru::class)
        ->assertSet('selectAll', false)
        ->assertSet('selectedGajiIds', [])
        ->set('selectAll', true)
        ->assertCount('selectedGajiIds', 2)
        ->set('selectAll', false)
        ->assertCount('selectedGajiIds', 0);
});

