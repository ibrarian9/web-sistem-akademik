<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Pengaturan;
use Livewire\Livewire;
use App\Livewire\Shared\ProfilSaya;
use App\Livewire\SuperAdmin\TataKelola\ManajemenPengaturan;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $role = Role::where('nama', 'guru')->first() ?? Role::first();
    
    $this->user = User::create([
        'nama' => 'Guru Test Profil',
        'username' => 'gurutestprofil',
        'email' => 'gurutestprofil@example.com',
        'password' => bcrypt('password123'),
        'role_id' => $role->id,
        'status' => 'aktif',
    ]);
});

test('authenticated user can render profil saya page', function () {
    $this->actingAs($this->user);

    Livewire::test(ProfilSaya::class)
        ->assertStatus(200)
        ->assertSee('Guru Test Profil')
        ->assertSee('Informasi Profil Pengguna');
});

test('user can update profile details', function () {
    $this->actingAs($this->user);

    Livewire::test(ProfilSaya::class)
        ->set('nama', 'Guru Test Profil Updated')
        ->set('nip', '199001012020011001')
        ->set('jabatan', 'Wali Kelas 7-A')
        ->call('saveProfile')
        ->assertHasNoErrors();

    $this->user->refresh();
    expect($this->user->nama)->toBe('Guru Test Profil Updated');
    expect($this->user->nip)->toBe('199001012020011001');
    expect($this->user->jabatan)->toBe('Wali Kelas 7-A');
});

test('user can update password', function () {
    $this->actingAs($this->user);

    Livewire::test(ProfilSaya::class)
        ->set('current_password', 'password123')
        ->set('new_password', 'newsecret123')
        ->set('new_password_confirmation', 'newsecret123')
        ->call('updatePassword')
        ->assertHasNoErrors();

    $this->user->refresh();
    expect(Hash::check('newsecret123', $this->user->password))->toBeTrue();
});

test('bendahara (finance) can update personal name and school information in profil and settings', function () {
    $roleFinance = Role::where('nama', 'finance')->first();
    $bendahara = User::create([
        'nama' => 'Hj. Siti Aminah, S.E.',
        'username' => 'bendahara_test',
        'email' => 'bendahara_test@example.com',
        'password' => bcrypt('password123'),
        'role_id' => $roleFinance->id,
        'status' => 'aktif',
    ]);

    $this->actingAs($bendahara);

    // 1. Update personal name via ProfilSaya
    Livewire::test(ProfilSaya::class)
        ->assertSee('Identitas & Informasi Sekolah / Lembaga', false)
        ->set('nama', 'Hj. Siti Aminah, M.Ak.')
        ->set('nip', '198501012010012001')
        ->set('jabatan', 'Kepala Bendahara Yayasan')
        ->call('saveProfile')
        ->assertHasNoErrors();

    $bendahara->refresh();
    expect($bendahara->nama)->toBe('Hj. Siti Aminah, M.Ak.');
    expect(Pengaturan::getValue('bendahara_nama'))->toBe('Hj. Siti Aminah, M.Ak.');
    expect(Pengaturan::getValue('bendahara_nip'))->toBe('198501012010012001');

    // 2. Update school information via ProfilSaya
    Livewire::test(ProfilSaya::class)
        ->set('nama_sekolah', 'SD ISLAM TERPADU AL-FATIH PEKANBARU')
        ->set('alamat_sekolah', 'Jl. KH. Ahmad Dahlan No. 45, Pekanbaru')
        ->set('no_telepon', '(0761) 888999')
        ->call('saveSchoolProfile')
        ->assertHasNoErrors();

    expect(Pengaturan::getValue('nama_sekolah'))->toBe('SD ISLAM TERPADU AL-FATIH PEKANBARU');
    expect(Pengaturan::getValue('nama_instansi'))->toBe('SD ISLAM TERPADU AL-FATIH PEKANBARU');
    expect(Pengaturan::getValue('alamat_sekolah'))->toBe('Jl. KH. Ahmad Dahlan No. 45, Pekanbaru');
    expect(Pengaturan::getValue('no_telepon'))->toBe('(0761) 888999');

    // 3. Check access to finance.pengaturan route
    $this->get(route('finance.pengaturan'))->assertStatus(200);
});

test('tata usaha can update personal name and school information in profil and settings', function () {
    $roleTU = Role::where('nama', 'tata_usaha')->first();
    $tu = User::create([
        'nama' => 'Ahmad Subandi, S.Kom.',
        'username' => 'tu_test',
        'email' => 'tu_test@example.com',
        'password' => bcrypt('password123'),
        'role_id' => $roleTU->id,
        'status' => 'aktif',
    ]);

    $this->actingAs($tu);

    // 1. Update personal name via ProfilSaya
    Livewire::test(ProfilSaya::class)
        ->assertSee('Identitas & Informasi Sekolah / Lembaga', false)
        ->set('nama', 'Ahmad Subandi, S.Kom., M.T.I.')
        ->set('nip', '199203152018021003')
        ->set('jabatan', 'Kepala Urusan Tata Usaha')
        ->call('saveProfile')
        ->assertHasNoErrors();

    $tu->refresh();
    expect($tu->nama)->toBe('Ahmad Subandi, S.Kom., M.T.I.');
    expect(Pengaturan::getValue('tata_usaha_nama'))->toBe('Ahmad Subandi, S.Kom., M.T.I.');
    expect(Pengaturan::getValue('tata_usaha_nip'))->toBe('199203152018021003');

    // 2. Update school information via ProfilSaya
    Livewire::test(ProfilSaya::class)
        ->set('nama_sekolah', 'SMP ISLAM TERPADU INSAN KAMIL')
        ->set('alamat_sekolah', 'Jl. Jenderal Sudirman No. 100, Pekanbaru')
        ->set('no_telepon', '(0761) 777123')
        ->call('saveSchoolProfile')
        ->assertHasNoErrors();

    expect(Pengaturan::getValue('nama_sekolah'))->toBe('SMP ISLAM TERPADU INSAN KAMIL');
    expect(Pengaturan::getValue('alamat_sekolah'))->toBe('Jl. Jenderal Sudirman No. 100, Pekanbaru');

    // 3. Check access to tata-usaha.pengaturan route
    $this->get(route('tata-usaha.pengaturan'))->assertStatus(200);
});
