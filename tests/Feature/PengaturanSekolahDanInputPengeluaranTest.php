<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Pengaturan;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use Livewire\Livewire;
use App\Livewire\SuperAdmin\TataKelola\ManajemenPengaturan;
use App\Livewire\Finance\ArusKas;
use App\Livewire\Finance\Laporan\LaporanPengeluaran;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    Storage::fake('public');

    $roleSuperAdmin = Role::where('nama', 'super_admin')->first();
    $this->superAdmin = User::create([
        'nama' => 'Super Admin Test',
        'username' => 'superadmin_test',
        'email' => 'superadmin_test@example.com',
        'password' => bcrypt('password123'),
        'role_id' => $roleSuperAdmin->id,
        'status' => 'aktif',
    ]);

    $roleFinance = Role::where('nama', 'finance')->first();
    $this->finance = User::create([
        'nama' => 'Staf Finance Test',
        'username' => 'finance_test',
        'email' => 'finance_test@example.com',
        'password' => bcrypt('password123'),
        'role_id' => $roleFinance->id,
        'status' => 'aktif',
    ]);

    KategoriPengeluaran::firstOrCreate(['nama' => 'Operasional']);
    KategoriPengeluaran::firstOrCreate(['nama' => 'ATK & Cetak']);
});

test('pengaturan sekolah uses a single data source without duplicate school inputs', function () {
    $this->actingAs($this->superAdmin);

    // Seed both old and new keys to test deduplication
    Pengaturan::setValue('nama_instansi', 'Yayasan Pendidikan Teladan');
    Pengaturan::setValue('nama_sekolah', 'SD Islam Terpadu Al-Fatih');
    Pengaturan::setValue('no_telepon', '(0761) 888999');
    Pengaturan::setValue('alamat_sekolah', 'Jl. Pendidikan Karakter No. 10');

    $component = Livewire::test(ManajemenPengaturan::class);

    // Verify settings only contain one canonical school key for each identity attribute
    $keys = collect($component->get('settings'))->pluck('key')->toArray();

    expect($keys)->toContain('nama_sekolah');
    expect($keys)->toContain('no_telepon');
    expect($keys)->toContain('alamat_sekolah');

    // Duplicate mirror keys must NOT be separate inputs in settings
    expect($keys)->not->toContain('nama_instansi');
    expect($keys)->not->toContain('telepon_instansi');
    expect($keys)->not->toContain('alamat_instansi');

    // UI displays single source of data indicator
    $component->assertSee('Identitas Resmi Sekolah / Lembaga (Satu Sumber Data)')
        ->assertSee('Sumber Data Tunggal');
});

test('saving in manajemen pengaturan synchronizes school and instansi mirror keys', function () {
    $this->actingAs($this->superAdmin);

    $component = Livewire::test(ManajemenPengaturan::class);
    $settings = $component->get('settings');

    // Update school name, phone, and address in settings array
    foreach ($settings as $index => $item) {
        if ($item['key'] === 'nama_sekolah') {
            $settings[$index]['value'] = 'SMP ISLAM INSAN CENDEKIA';
        } elseif ($item['key'] === 'no_telepon') {
            $settings[$index]['value'] = '(0761) 555666';
        } elseif ($item['key'] === 'alamat_sekolah') {
            $settings[$index]['value'] = 'Jl. Teratai Indah No. 45, Pekanbaru';
        }
    }

    $component->set('settings', $settings)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Pengaturan sekolah dan parameter sistem berhasil disimpan.');

    // Both canonical and mirror keys must have identical synchronized values
    expect(Pengaturan::getValue('nama_sekolah'))->toBe('SMP ISLAM INSAN CENDEKIA');
    expect(Pengaturan::getValue('nama_instansi'))->toBe('SMP ISLAM INSAN CENDEKIA');
    expect(Pengaturan::getValue('no_telepon'))->toBe('(0761) 555666');
    expect(Pengaturan::getValue('telepon_instansi'))->toBe('(0761) 555666');
    expect(Pengaturan::getValue('alamat_sekolah'))->toBe('Jl. Teratai Indah No. 45, Pekanbaru');
    expect(Pengaturan::getValue('alamat_instansi'))->toBe('Jl. Teratai Indah No. 45, Pekanbaru');
});

test('laporan pengeluaran can create expense with formatted currency and custom category', function () {
    $this->actingAs($this->finance);

    $file = UploadedFile::fake()->image('nota_belanja.jpg');

    Livewire::test(LaporanPengeluaran::class)
        ->call('openCreateModal')
        ->assertSet('showCreateModal', true)
        ->set('createTanggal', '2026-09-28')
        ->set('is_kategori_kustom', true)
        ->set('kategori_kustom', 'Konsumsi Tamu Yayasan')
        ->set('createJumlah', '275.000') // Formatted currency input
        ->set('createKeterangan', 'Beli konsumsi rapat pleno yayasan')
        ->set('createBukti', $file)
        ->call('savePengeluaran')
        ->assertHasNoErrors()
        ->assertSet('showCreateModal', false);

    $expense = Pengeluaran::where('keterangan', 'Beli konsumsi rapat pleno yayasan')->first();
    expect($expense)->not->toBeNull();
    expect(floatval($expense->jumlah))->toBe(275000.0);
    expect($expense->kategori->nama)->toBe('Konsumsi Tamu Yayasan');
    expect($expense->bukti)->not->toBeNull();
    Storage::disk('public')->assertExists($expense->bukti);
});

test('arus kas can create expense with currency and custom category matching appearance', function () {
    $this->actingAs($this->finance);

    $file = UploadedFile::fake()->image('struk_atk.jpg');

    Livewire::test(ArusKas::class)
        ->call('openExpenseModal')
        ->assertSet('showExpenseModal', true)
        ->set('tanggal_keluar', '2026-09-28')
        ->set('is_kategori_kustom', true)
        ->set('kategori_keluar_kustom', 'Perawatan Kendaraan Operasional')
        ->set('jumlah_keluar', 450000)
        ->set('keterangan_keluar', 'Ganti oli dan servis motor inventaris')
        ->set('bukti_keluar', $file)
        ->call('saveExpense')
        ->assertHasNoErrors()
        ->assertSet('showExpenseModal', false);

    $expense = Pengeluaran::where('keterangan', 'Ganti oli dan servis motor inventaris')->first();
    expect($expense)->not->toBeNull();
    expect(floatval($expense->jumlah))->toBe(450000.0);
    expect($expense->kategori->nama)->toBe('Perawatan Kendaraan Operasional');
});
