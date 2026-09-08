<?php

use App\Livewire\TataUsaha\ManajemenSurat;
use App\Models\Pengaturan;
use App\Models\RiwayatSurat;
use App\Models\Role;
use App\Models\User;
use App\Services\ESignatureService;
use Livewire\Livewire;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

    // Set sample values in Pengaturan
    Pengaturan::setValue('kepala_sekolah_nama', 'Dr. H. Ahmad Musthofa, M.Pd.', 'Nama Kepala Sekolah');
    Pengaturan::setValue('kepala_sekolah_nip', '197905102005011005', 'NIP Kepala Sekolah');
    Pengaturan::setValue('kepala_sekolah_jabatan', 'Kepala Sekolah SD Tahfizh F3', 'Jabatan Resmi');
    Pengaturan::setValue('kota', 'Pekanbaru', 'Kota Instansi');

    $roleTu = Role::firstOrCreate(['nama' => 'tata_usaha'], ['display_name' => 'Tata Usaha']);
    $this->tuUser = User::firstOrCreate(
        ['username' => 'tu_tester_surat'],
        [
            'nama' => 'Staff Tata Usaha',
            'email' => 'tu_tester_surat@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleTu->id,
            'status' => 'aktif',
        ]
    );
});

test('manajemen surat memuat data default penandatangan dari pengaturan sistem', function () {
    $this->actingAs($this->tuUser);

    Livewire::test(ManajemenSurat::class)
        ->assertSet('penandatangan_nama', 'Dr. H. Ahmad Musthofa, M.Pd.')
        ->assertSet('penandatangan_jabatan', 'Kepala Sekolah SD Tahfizh F3')
        ->assertSet('penandatangan_niy', '197905102005011005')
        ->assertSet('kota_surat', 'Pekanbaru')
        ->assertSee('Kembalikan ke Kepala Sekolah');
});

test('pejabat penandatangan surat bisa diedit secara bebas dan di-reset kembali ke kepala sekolah', function () {
    $this->actingAs($this->tuUser);

    Livewire::test(ManajemenSurat::class)
        // 1. Ubah pejabat ke Plt / Pejabat Lain
        ->set('penandatangan_nama', 'Ustadz Salman, Lc. (Plt. Kepala Sekolah)')
        ->set('penandatangan_jabatan', 'Plt. Kepala Sekolah')
        ->set('penandatangan_niy', '198501012010011002')
        ->assertSet('penandatangan_nama', 'Ustadz Salman, Lc. (Plt. Kepala Sekolah)')
        ->assertSet('penandatangan_jabatan', 'Plt. Kepala Sekolah')
        ->assertSet('penandatangan_niy', '198501012010011002')
        // 2. Klik reset kembali ke data kepala sekolah di pengaturan
        ->call('resetPenandatanganToDefault')
        ->assertSet('penandatangan_nama', 'Dr. H. Ahmad Musthofa, M.Pd.')
        ->assertSet('penandatangan_jabatan', 'Kepala Sekolah SD Tahfizh F3')
        ->assertSet('penandatangan_niy', '197905102005011005');
});

test('simpan surat menghasilkan qr code resmi dan url verifikasi dengan id riwayat surat', function () {
    $this->actingAs($this->tuUser);

    $component = Livewire::test(ManajemenSurat::class)
        ->set('jenis_surat', 'aktif_sekolah')
        ->set('nomor_surat', '099/SDTF3/IX/2026')
        ->set('penerima_nama', 'Muhammad Zaidan')
        ->set('penerima_nisn', '0123456789')
        ->set('penerima_kelas', '4 Abu Bakar')
        ->call('simpanDanCetak')
        ->assertHasNoErrors()
        ->assertSet('showPrintModal', true);

    $surat = RiwayatSurat::where('nomor_surat', '099/SDTF3/IX/2026')->first();
    expect($surat)->not->toBeNull();
    expect($surat->penerima_nama)->toBe('Muhammad Zaidan');

    $payload = $surat->payload_json;
    expect($payload)->toBeArray();
    expect($payload['verification_code'])->toStartWith("TTD-SUR-{$surat->id}-");
    expect($payload['verification_url'])->toBe(url('/verifikasi-dokumen/' . $payload['verification_code']));
    expect($payload['qr_code'])->toStartWith('data:image/png;base64,');
    expect($payload['penandatangan_nama'])->toBe('Dr. H. Ahmad Musthofa, M.Pd.');

    // Verifikasi tampilan modal preview menampilkan QR Code resmi
    $component->assertSee('Ditandatangani Secara Elektronik')
        ->assertSee('Verifikasi Keaslian Dokumen')
        ->assertSee($payload['verification_code']);
});

test('halaman publik verifikasi dokumen menampilkan keabsahan surat resmi ketika qr code di-scan', function () {
    $surat = RiwayatSurat::create([
        'nomor_surat' => '101/SDTF3/IX/2026',
        'jenis_surat' => 'aktif_sekolah',
        'penerima_nama' => 'Aisyah Humaira',
        'tanggal_surat' => '2026-09-08',
        'payload_json' => [
            'penerima_nama' => 'Aisyah Humaira',
            'penerima_nisn' => '0098765432',
            'penerima_kelas' => '5 Khadijah',
            'penandatangan_nama' => 'Dr. H. Ahmad Musthofa, M.Pd.',
            'penandatangan_jabatan' => 'Kepala Sekolah SD Tahfizh F3',
        ],
        'created_by' => $this->tuUser->id,
    ]);

    $code = ESignatureService::generateCode('SUR', $surat->id, '20260908');

    $response = $this->get('/verifikasi-dokumen/' . $code);

    $response->assertStatus(200);
    $response->assertSee('DOKUMEN RESMI SAH');
    $response->assertSee('101/SDTF3/IX/2026');
    $response->assertSee('Surat Keterangan Aktif Sekolah');
    $response->assertSee('Aisyah Humaira');
    $response->assertSee('0098765432');
    $response->assertSee('Dr. H. Ahmad Musthofa, M.Pd.');
    $response->assertSee($code);
});

test('download pdf surat menyertakan gambar qr code resmi dan badge tanda tangan elektronik', function () {
    $this->actingAs($this->tuUser);

    $surat = RiwayatSurat::create([
        'nomor_surat' => '102/SDTF3/IX/2026',
        'jenis_surat' => 'aktif_sekolah',
        'penerima_nama' => 'Fatima Az-Zahra',
        'tanggal_surat' => '2026-09-08',
        'payload_json' => [
            'jenis_surat' => 'aktif_sekolah',
            'nomor_surat' => '102/SDTF3/IX/2026',
            'tanggal_surat' => '2026-09-08',
            'kota_surat' => 'Pekanbaru',
            'penandatangan_nama' => 'Dr. H. Ahmad Musthofa, M.Pd.',
            'penandatangan_jabatan' => 'Kepala Sekolah',
            'penandatangan_niy' => '197905102005011005',
            'penerima_nama' => 'Fatima Az-Zahra',
            'penerima_nisn' => '0011223344',
            'penerima_kelas' => '3 Umar',
        ],
        'created_by' => $this->tuUser->id,
    ]);

    // Test downloadPdfById auto-generates QR code
    Livewire::test(ManajemenSurat::class)
        ->call('downloadPdfById', $surat->id)
        ->assertFileDownloaded('102_SDTF3_IX_2026.pdf');

    $surat->refresh();
    expect($surat->payload_json['qr_code'])->toStartWith('data:image/png;base64,');
    expect($surat->payload_json['verification_code'])->toStartWith("TTD-SUR-{$surat->id}-");
});
