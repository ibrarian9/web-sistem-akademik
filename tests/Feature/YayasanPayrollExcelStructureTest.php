<?php

use App\Models\GajiGuru;
use App\Models\Guru;
use App\Models\Role;
use App\Models\User;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Livewire\Finance\ManajemenGajiGuru;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Finance']);
    $this->roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);

    $this->financeUser = User::create([
        'nama' => 'Bendahara Yayasan',
        'username' => 'bendahara_firyal',
        'email' => 'bendahara@firyal.sch.id',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleFinance->id,
        'status' => 'aktif',
    ]);

    // Mudir F3 (Zulkifli)
    $this->mudirUser = User::create([
        'nama' => 'ZULKIFLI',
        'username' => 'mudir_zulkifli',
        'email' => 'zulkifli@firyal.sch.id',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleGuru->id,
        'status' => 'aktif',
    ]);

    $this->mudirGuru = Guru::create([
        'user_id' => $this->mudirUser->id,
        'nip' => '198001012005011001',
        'niy' => 'YFI-001',
        'jenis_guru' => 'umum',
        'jabatan' => 'MUDIR F3',
        'status_kepegawaian' => 'tetap_yayasan',
        'pendidikan' => 'S1 Pendidikan',
        'tanggal_masuk' => '2015-01-01',
        'status_aktif' => true,
    ]);

    // Wali Tahfizh (Alfiah Hasanah)
    $this->tahfizhUser = User::create([
        'nama' => 'ALFIAH HASANAH',
        'username' => 'wali_alfiah',
        'email' => 'alfiah@firyal.sch.id',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleGuru->id,
        'status' => 'aktif',
    ]);

    $this->tahfizhGuru = Guru::create([
        'user_id' => $this->tahfizhUser->id,
        'nip' => '199505052021012002',
        'niy' => 'YFI-003',
        'jenis_guru' => 'tahfidz',
        'jabatan' => 'WALI TAHFIZH AL-FATTIHAH',
        'status_kepegawaian' => 'honorer',
        'pendidikan' => 'S1 Ilmu Al-Quran',
        'tanggal_masuk' => '2021-07-01',
        'status_aktif' => true,
    ]);
});

test('generate draft gaji otomatis menghitung komponen excel yayasan f3', function () {
    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenGajiGuru::class)
        ->set('generateBulan', 'Juni')
        ->set('generateTahun', 2026)
        ->call('generateDrafts')
        ->assertHasNoErrors();

    // Verify Mudir Salary record
    $gajiMudir = GajiGuru::where('guru_id', $this->mudirGuru->id)->where('bulan', 'Juni')->where('tahun', 2026)->first();
    expect($gajiMudir)->not->toBeNull();
    expect(floatval($gajiMudir->gaji_pokok))->toBe(2000000.00);
    expect(floatval($gajiMudir->gaji_berkala))->toBe(120000.00);
    expect(floatval($gajiMudir->insentif))->toBe(500000.00);
    expect(floatval($gajiMudir->insentif_bpjs))->toBe(17928.00);
    expect(floatval($gajiMudir->potongan_sosial))->toBe(10000.00);
    expect(floatval($gajiMudir->potongan_bpjstk))->toBe(17928.00);
    expect(floatval($gajiMudir->total_bruto))->toBe(2637928.00);
    expect(floatval($gajiMudir->total_diterima))->toBe(2610000.00); // 2.637.928 - 10.000 - 17.928 = 2.610.000

    // Verify Wali Tahfizh record
    $gajiTahfizh = GajiGuru::where('guru_id', $this->tahfizhGuru->id)->where('bulan', 'Juni')->where('tahun', 2026)->first();
    expect($gajiTahfizh)->not->toBeNull();
    expect(floatval($gajiTahfizh->gaji_pokok))->toBe(1000000.00);
    expect(floatval($gajiTahfizh->insentif))->toBe(150000.00);
    expect(floatval($gajiTahfizh->potongan_sosial))->toBe(10000.00);
    expect(floatval($gajiTahfizh->total_bruto))->toBe(1150000.00);
    expect(floatval($gajiTahfizh->total_diterima))->toBe(1140000.00); // 1.150.000 - 10.000 = 1.140.000
});

test('finance dapat membuat gaji manual satuan untuk guru tertentu', function () {
    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenGajiGuru::class)
        ->call('openCreateModal')
        ->set('createGuruId', $this->tahfizhGuru->id)
        ->set('createBulan', 'Juli')
        ->set('createTahun', 2026)
        ->set('createGajiPokok', 1200000.00)
        ->set('createHonorEkskul', 150000.00)
        ->set('createJumlahEkskul', 3)
        ->set('createInsentif', 200000.00)
        ->set('createPotonganSosial', 10000.00)
        ->set('createStatus', 'draft')
        ->call('saveCreate')
        ->assertHasNoErrors();

    $gaji = GajiGuru::where('guru_id', $this->tahfizhGuru->id)->where('bulan', 'Juli')->where('tahun', 2026)->first();
    expect($gaji)->not->toBeNull();
    expect(floatval($gaji->gaji_pokok))->toBe(1200000.00);
    expect(floatval($gaji->honor_ekskul))->toBe(150000.00);
    expect($gaji->jumlah_ekskul)->toBe(3);
    expect(floatval($gaji->total_bruto))->toBe(1550000.00); // 1.200.000 + 150.000 + 200.000
    expect(floatval($gaji->total_diterima))->toBe(1540000.00); // 1.550.000 - 10.000
    expect($gaji->status)->toBe('draft');
});

test('edit modal rincian penggajian memperbarui nilai bruto dan take home pay dengan tepat', function () {
    $this->actingAs($this->financeUser);

    $gaji = GajiGuru::create([
        'guru_id' => $this->mudirGuru->id,
        'bulan' => 'Juni',
        'tahun' => 2026,
        'gaji_pokok' => 2000000.00,
        'gaji_berkala' => 120000.00,
        'jumlah_ekskul' => 4,
        'honor_ekskul' => 200000.00,
        'insentif' => 500000.00,
        'insentif_bpjs' => 17928.00,
        'potongan_sosial' => 10000.00,
        'potongan_peminjaman' => 0.00,
        'potongan_bpjstk' => 17928.00,
        'potongan_lainnya' => 0.00,
        'total_bruto' => 2837928.00,
        'total_diterima' => 2810000.00,
        'status' => 'draft',
        'sumber_dana' => 'Yayasan',
        'jam_kerja' => '07.00-14.00 (Fleksibel)',
        'jabatan' => 'MUDIR F3',
        'tanggal_bayar' => '2026-06-25',
    ]);

    Livewire::test(ManajemenGajiGuru::class)
        ->call('openEditModal', $gaji->id)
        ->set('editHonorEkskul', 300000.00)
        ->set('editJumlahEkskul', 6)
        ->call('saveEdit')
        ->assertHasNoErrors();

    $gajiFresh = $gaji->fresh();
    expect(floatval($gajiFresh->honor_ekskul))->toBe(300000.00);
    expect($gajiFresh->jumlah_ekskul)->toBe(6);
    expect(floatval($gajiFresh->total_bruto))->toBe(2937928.00);
    expect(floatval($gajiFresh->total_diterima))->toBe(2910000.00);
});

test('finance dapat mengubah gaji yang sudah dibayar dan menyinkronkan pengeluaran kas', function () {
    $this->actingAs($this->financeUser);

    $kategori = KategoriPengeluaran::firstOrCreate(['nama' => 'Gaji Guru'], ['jenis' => 'operasional']);
    $pengeluaran = Pengeluaran::create([
        'kategori_pengeluaran_id' => $kategori->id,
        'jumlah' => 2610000.00,
        'tanggal' => '2026-06-25',
        'keterangan' => 'Gaji Mudir F3',
        'petugas_id' => $this->financeUser->id,
    ]);

    $gaji = GajiGuru::create([
        'guru_id' => $this->mudirGuru->id,
        'pengeluaran_id' => $pengeluaran->id,
        'bulan' => 'Juni',
        'tahun' => 2026,
        'gaji_pokok' => 2000000.00,
        'gaji_berkala' => 120000.00,
        'insentif' => 500000.00,
        'insentif_bpjs' => 17928.00,
        'potongan_sosial' => 10000.00,
        'potongan_bpjstk' => 17928.00,
        'total_bruto' => 2637928.00,
        'total_diterima' => 2610000.00,
        'status' => 'dibayar',
        'sumber_dana' => 'Yayasan',
        'tanggal_bayar' => '2026-06-25',
    ]);

    // Edit the already-paid salary
    Livewire::test(ManajemenGajiGuru::class)
        ->call('openEditModal', $gaji->id)
        ->set('editGajiPokok', 2200000.00)
        ->call('saveEdit')
        ->assertHasNoErrors();

    $gajiFresh = $gaji->fresh();
    expect(floatval($gajiFresh->gaji_pokok))->toBe(2200000.00);
    expect(floatval($gajiFresh->total_diterima))->toBe(2810000.00);

    // Verify linked cash expenditure is automatically synced
    $pengeluaranFresh = $pengeluaran->fresh();
    expect(floatval($pengeluaranFresh->jumlah))->toBe(2810000.00);
});

test('finance dapat membatalkan pembayaran dan mengembalikan gaji ke status draf', function () {
    $this->actingAs($this->financeUser);

    $kategori = KategoriPengeluaran::firstOrCreate(['nama' => 'Gaji Guru'], ['jenis' => 'operasional']);
    $pengeluaran = Pengeluaran::create([
        'kategori_pengeluaran_id' => $kategori->id,
        'jumlah' => 2610000.00,
        'tanggal' => '2026-06-25',
        'keterangan' => 'Gaji Mudir F3',
        'petugas_id' => $this->financeUser->id,
    ]);

    $gaji = GajiGuru::create([
        'guru_id' => $this->mudirGuru->id,
        'pengeluaran_id' => $pengeluaran->id,
        'bulan' => 'Juni',
        'tahun' => 2026,
        'gaji_pokok' => 2000000.00,
        'gaji_berkala' => 120000.00,
        'insentif' => 500000.00,
        'insentif_bpjs' => 17928.00,
        'potongan_sosial' => 10000.00,
        'potongan_bpjstk' => 17928.00,
        'total_bruto' => 2637928.00,
        'total_diterima' => 2610000.00,
        'status' => 'dibayar',
        'sumber_dana' => 'Yayasan',
        'tanggal_bayar' => '2026-06-25',
    ]);

    Livewire::test(ManajemenGajiGuru::class)
        ->call('revertToDraft', $gaji->id)
        ->assertHasNoErrors();

    $gajiFresh = $gaji->fresh();
    expect($gajiFresh->status)->toBe('draft');
    expect($gajiFresh->pengeluaran_id)->toBeNull();

    // Verify linked cash expenditure is deleted
    expect(Pengeluaran::find($pengeluaran->id))->toBeNull();
});

test('finance dapat meninjau dan mengubah nominal gaji guru di modal generate sebelum di generate', function () {
    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenGajiGuru::class)
        ->set('generateBulan', 'Agustus')
        ->set('generateTahun', 2026)
        ->call('openGenerateModal')
        // Customize Mudir Gaji in pre-generation preview
        ->set("generateItems.{$this->mudirGuru->id}.gaji_pokok", 2300000.00)
        ->set("generateItems.{$this->mudirGuru->id}.honor_ekskul", 250000.00)
        ->set("generateItems.{$this->mudirGuru->id}.jumlah_ekskul", 5)
        ->call('recalculateGenerateRow', $this->mudirGuru->id)
        ->call('generateDrafts')
        ->assertHasNoErrors();

    $gajiMudir = GajiGuru::where('guru_id', $this->mudirGuru->id)
        ->where('bulan', 'Agustus')
        ->where('tahun', 2026)
        ->first();

    expect($gajiMudir)->not->toBeNull();
    expect(floatval($gajiMudir->gaji_pokok))->toBe(2300000.00);
    expect(floatval($gajiMudir->honor_ekskul))->toBe(250000.00);
    expect($gajiMudir->jumlah_ekskul)->toBe(5);
    expect(floatval($gajiMudir->total_bruto))->toBe(3187928.00); // 2.300.000 + 120.000 + 250.000 + 500.000 + 17.928
    expect(floatval($gajiMudir->total_diterima))->toBe(3160000.00); // 3.187.928 - 10.000 - 17.928
});

test('finance dapat membuka modal detail rincian gaji guru dan melihat kalkulasi lengkap', function () {
    $this->actingAs($this->financeUser);

    $gaji = GajiGuru::create([
        'guru_id' => $this->mudirGuru->id,
        'bulan' => 'September',
        'tahun' => 2026,
        'jabatan' => 'Mudir Pesantren',
        'jam_kerja' => '07.00-14.00',
        'gaji_pokok' => 2000000.00,
        'gaji_berkala' => 120000.00,
        'insentif' => 500000.00,
        'insentif_bpjs' => 17928.00,
        'potongan_sosial' => 10000.00,
        'potongan_bpjstk' => 17928.00,
        'total_bruto' => 2637928.00,
        'total_diterima' => 2610000.00,
        'status' => 'dibayar',
        'sumber_dana' => 'Yayasan',
        'tanggal_bayar' => '2026-09-25',
    ]);

    Livewire::test(ManajemenGajiGuru::class)
        ->call('openDetailModal', $gaji->id)
        ->assertSet('showDetailModal', true)
        ->assertSet('selectedSalaryDetail.id', $gaji->id)
        ->assertSee('Rincian Lengkap Honorarium Pegawai')
        ->assertSee('2.610.000')
        ->call('closeDetailModal')
        ->assertSet('showDetailModal', false)
        ->assertSet('selectedSalaryDetail', null);
});

test('finance dapat membuka modal riwayat gaji guru dan melihat histori seluruh gaji yang telah dibayarkan', function () {
    $this->actingAs($this->financeUser);

    // Create multiple salary records for mudir
    $salary1 = GajiGuru::create([
        'guru_id' => $this->mudirGuru->id,
        'bulan' => 'Januari',
        'tahun' => 2026,
        'gaji_pokok' => 2000000.00,
        'total_bruto' => 2637928.00,
        'total_diterima' => 2610000.00,
        'potongan_peminjaman' => 150000.00,
        'status' => 'dibayar',
        'sumber_dana' => 'Yayasan',
        'tanggal_bayar' => '2026-01-25',
    ]);

    $salary2 = GajiGuru::create([
        'guru_id' => $this->mudirGuru->id,
        'bulan' => 'Februari',
        'tahun' => 2026,
        'gaji_pokok' => 2000000.00,
        'total_bruto' => 2637928.00,
        'total_diterima' => 2610000.00,
        'potongan_peminjaman' => 150000.00,
        'status' => 'dibayar',
        'sumber_dana' => 'Yayasan',
        'tanggal_bayar' => '2026-02-25',
    ]);

    // Test Dedicated DetailGajiGuru page component
    Livewire::actingAs($this->financeUser)
        ->test(\App\Livewire\Finance\DetailGajiGuru::class, ['guruId' => $this->mudirGuru->id])
        ->assertSet('guruId', $this->mudirGuru->id)
        ->assertSee('ZULKIFLI')
        ->assertSee('5.220.000') // 2.610.000 * 2
        ->assertSee('300.000')   // 150.000 * 2
        ->assertSee('2 Periode Terbayar')
        ->set('filterStatus', 'dibayar')
        ->assertSee('ZULKIFLI')
        ->call('openDetailModal', $salary1->id)
        ->assertSet('showDetailModal', true)
        ->call('closeDetailModal')
        ->assertSet('showDetailModal', false);

    // Test route access and verify sidebar is highlighted for Gaji Guru
    $response = $this->actingAs($this->financeUser)
        ->get(route('finance.gaji-guru.detail', ['guruId' => $this->mudirGuru->id]));
        
    $response->assertStatus(200)
        ->assertSee('Riwayat Gaji')
        ->assertSee('ZULKIFLI')
        ->assertSee('sidebar-active-link');

    // Test riwayat route alias as well
    $this->actingAs($this->financeUser)
        ->get(route('finance.gaji-guru.riwayat', ['guruId' => $this->mudirGuru->id]))
        ->assertStatus(200)
        ->assertSee('sidebar-active-link');
});

test('finance dapat menghapus beberapa data gaji terpilih sekaligus di manajemen gaji dan riwayat gaji', function () {
    $this->actingAs($this->financeUser);

    $salary1 = GajiGuru::create([
        'guru_id' => $this->mudirGuru->id,
        'bulan' => 'Maret',
        'tahun' => 2026,
        'gaji_pokok' => 2000000.00,
        'total_bruto' => 2000000.00,
        'total_diterima' => 2000000.00,
        'status' => 'draft',
        'sumber_dana' => 'Yayasan',
        'tanggal_bayar' => '2026-03-25',
    ]);

    $salary2 = GajiGuru::create([
        'guru_id' => $this->tahfizhGuru->id,
        'bulan' => 'Maret',
        'tahun' => 2026,
        'gaji_pokok' => 1800000.00,
        'total_bruto' => 1800000.00,
        'total_diterima' => 1800000.00,
        'status' => 'draft',
        'sumber_dana' => 'Yayasan',
        'tanggal_bayar' => '2026-03-25',
    ]);

    // Test bulk delete in ManajemenGajiGuru
    Livewire::test(ManajemenGajiGuru::class)
        ->set('selectedGajiIds', [(string)$salary1->id, (string)$salary2->id])
        ->call('deleteSelected')
        ->assertHasNoErrors();

    expect(GajiGuru::find($salary1->id))->toBeNull();
    expect(GajiGuru::find($salary2->id))->toBeNull();

    // Create a salary for DetailGajiGuru test
    $salary3 = GajiGuru::create([
        'guru_id' => $this->mudirGuru->id,
        'bulan' => 'April',
        'tahun' => 2026,
        'gaji_pokok' => 2000000.00,
        'total_bruto' => 2000000.00,
        'total_diterima' => 2000000.00,
        'status' => 'draft',
        'sumber_dana' => 'Yayasan',
        'tanggal_bayar' => '2026-04-25',
    ]);

    // Test bulk delete in DetailGajiGuru
    Livewire::test(\App\Livewire\Finance\DetailGajiGuru::class, ['guruId' => $this->mudirGuru->id])
        ->set('selectedGajiIds', [(string)$salary3->id])
        ->call('deleteSelected')
        ->assertHasNoErrors();

    expect(GajiGuru::find($salary3->id))->toBeNull();
});

