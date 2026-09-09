<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Student;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Livewire\SuperAdmin\TataKelola\ManajemenSiswa;
use App\Livewire\Guru\Dashboard as GuruDashboard;
use App\Livewire\Guru\CatatanPendampinganIndex;
use App\Livewire\Guru\AbsensiSiswa;
use App\Livewire\TataUsaha\ManajemenPiketGuru;
use App\Models\AbsensiSiswa as AbsensiSiswaModel;
use App\Models\JadwalPiketGuru;
use App\Rules\EligibleShadowTeacher;
use App\Rules\MaxOneShadowTeacherPerClass;
use Illuminate\Support\Facades\Validator;
use Livewire\Livewire;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);

    $this->roleGuru = Role::where('nama', 'guru')->first();
    $this->roleAdmin = Role::where('nama', 'super_admin')->first();

    // Active Academic Year & Semester
    $this->ta = TahunAjaran::firstOrCreate(
        ['nama' => '2026/2027'],
        ['status_aktif' => true]
    );

    $this->semester = Semester::firstOrCreate(
        ['tahun_ajaran_id' => $this->ta->id, 'semester' => 'ganjil'],
        ['status_aktif' => true, 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2026-12-31']
    );

    // 1. Shadow Teacher X
    $this->userShadowX = User::create([
        'nama' => 'Ustadzah Khadijah (Pendamping X)',
        'username' => 'shadow_x_' . uniqid(),
        'email' => 'shadow_x_' . uniqid() . '@test.com',
        'password' => bcrypt('password'),
        'role_id' => $this->roleGuru->id,
    ]);
    $this->guruShadowX = Guru::create([
        'user_id' => $this->userShadowX->id,
        'nip' => 'NIP-SX-' . rand(1000, 9999),
        'jenis_guru' => 'pendamping',
        'status_kepegawaian' => 'tetap',
        'tanggal_masuk' => '2024-01-01',
        'status_aktif' => true,
    ]);

    // 2. Shadow Teacher Y
    $this->userShadowY = User::create([
        'nama' => 'Ustadzah Aisyah (Pendamping Y)',
        'username' => 'shadow_y_' . uniqid(),
        'email' => 'shadow_y_' . uniqid() . '@test.com',
        'password' => bcrypt('password'),
        'role_id' => $this->roleGuru->id,
    ]);
    $this->guruShadowY = Guru::create([
        'user_id' => $this->userShadowY->id,
        'nip' => 'NIP-SY-' . rand(1000, 9999),
        'jenis_guru' => 'pendamping',
        'status_kepegawaian' => 'tetap',
        'tanggal_masuk' => '2024-01-01',
        'status_aktif' => true,
    ]);

    // 3. Regular Teacher (Guru Umum)
    $this->userGuruUmum = User::create([
        'nama' => 'Ustadz Ahmad (Guru Umum)',
        'username' => 'guru_umum_' . uniqid(),
        'email' => 'guru_umum_' . uniqid() . '@test.com',
        'password' => bcrypt('password'),
        'role_id' => $this->roleGuru->id,
    ]);
    $this->guruUmum = Guru::create([
        'user_id' => $this->userGuruUmum->id,
        'nip' => 'NIP-GU-' . rand(1000, 9999),
        'jenis_guru' => 'umum',
        'status_kepegawaian' => 'tetap',
        'tanggal_masuk' => '2024-01-01',
        'status_aktif' => true,
    ]);

    // 4. Classes
    $this->kelas1A = Kelas::firstOrCreate(
        ['nama_kelas' => 'Kelas 1A Inklusi Test'],
        ['tingkat' => 1, 'kapasitas' => 30, 'jenis_kelas' => 'umum', 'semester_id' => $this->semester->id]
    );

    $this->kelas1B = Kelas::firstOrCreate(
        ['nama_kelas' => 'Kelas 1B Inklusi Test'],
        ['tingkat' => 1, 'kapasitas' => 30, 'jenis_kelas' => 'umum', 'semester_id' => $this->semester->id]
    );

    // 5. Admin User
    $this->adminUser = User::where('role_id', $this->roleAdmin->id)->first();
});

test('guru model detects shadow teacher eligibility correctly', function () {
    expect($this->guruShadowX->isGuruPendamping())->toBeTrue();
    expect($this->guruShadowY->isGuruPendamping())->toBeTrue();
    expect($this->guruUmum->isGuruPendamping())->toBeFalse();

    $eligibleGurus = Guru::shadowTeacher()->pluck('id')->toArray();
    expect($eligibleGurus)->toContain($this->guruShadowX->id);
    expect($eligibleGurus)->toContain($this->guruShadowY->id);
    expect($eligibleGurus)->not->toContain($this->guruUmum->id);
});

test('manajemen siswa dropdown only renders eligible shadow teachers', function () {
    $this->actingAs($this->adminUser);

    Livewire::test(ManajemenSiswa::class)
        ->assertViewHas('shadowTeachers', function ($teachers) {
            $ids = $teachers->pluck('id')->toArray();
            return in_array($this->guruShadowX->id, $ids)
                && in_array($this->guruShadowY->id, $ids)
                && !in_array($this->guruUmum->id, $ids);
        });
});

test('can assign shadow teacher to a student per-siswa', function () {
    $this->actingAs($this->adminUser);

    // Create a new student assigned to GuruShadowX
    $username = 'murid_abk_' . uniqid();
    $nis = 'NIS-' . rand(10000, 99999);

    Livewire::test(ManajemenSiswa::class)
        ->call('openCreate')
        ->set('nama', 'Ananda Budi ABK')
        ->set('username', $username)
        ->set('password', 'password123')
        ->set('nis', $nis)
        ->set('jenis_kelamin', 'L')
        ->set('kelas_id', $this->kelas1A->id)
        ->set('shadow_teacher_id', $this->guruShadowX->id)
        ->set('tanggal_masuk', '2026-07-01')
        ->set('status', 'aktif')
        ->call('save')
        ->assertHasNoErrors();

    $siswa = Siswa::where('nis', $nis)->first();
    expect($siswa)->not->toBeNull();
    expect($siswa->shadow_teacher_id)->toBe($this->guruShadowX->id);
    expect($siswa->shadowTeacher->id)->toBe($this->guruShadowX->id);
});

test('validator rejects ineligible regular teacher as shadow teacher', function () {
    $validator = Validator::make(
        ['shadow_teacher_id' => $this->guruUmum->id],
        ['shadow_teacher_id' => ['required', new EligibleShadowTeacher()]]
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('shadow_teacher_id'))->toContain('Hanya guru dengan peran atau kategori Pendamping');
});

test('validator rejects assigning different shadow teacher in the same class', function () {
    // Siswa A in Kelas 1A is assigned to GuruShadowX
    $userA = User::create([
        'nama' => 'Siswa A',
        'username' => 'siswa_a_' . uniqid(),
        'password' => bcrypt('password'),
        'role_id' => Role::where('nama', 'murid')->first()->id,
    ]);
    $siswaA = Siswa::create([
        'user_id' => $userA->id,
        'nis' => 'NISA-' . rand(1000, 9999),
        'jenis_kelamin' => 'L',
        'kelas_id' => $this->kelas1A->id,
        'shadow_teacher_id' => $this->guruShadowX->id,
        'tanggal_masuk' => '2026-07-01',
        'status' => 'aktif',
    ]);

    // Now validate assigning GuruShadowY to a new Siswa B in the same Kelas 1A
    $validator = Validator::make(
        ['shadow_teacher_id' => $this->guruShadowY->id],
        ['shadow_teacher_id' => [new MaxOneShadowTeacherPerClass($this->kelas1A->id)]]
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('shadow_teacher_id'))->toContain('Dalam satu kelas hanya diperbolehkan maksimal 1 guru pendamping');
});

test('validator allows assigning same shadow teacher to multiple students in the same class', function () {
    // Siswa A in Kelas 1A is assigned to GuruShadowX
    $userA = User::create([
        'nama' => 'Siswa A',
        'username' => 'siswa_a_' . uniqid(),
        'password' => bcrypt('password'),
        'role_id' => Role::where('nama', 'murid')->first()->id,
    ]);
    Siswa::create([
        'user_id' => $userA->id,
        'nis' => 'NISA-' . rand(1000, 9999),
        'jenis_kelamin' => 'L',
        'kelas_id' => $this->kelas1A->id,
        'shadow_teacher_id' => $this->guruShadowX->id,
        'tanggal_masuk' => '2026-07-01',
        'status' => 'aktif',
    ]);

    // Validate assigning same GuruShadowX to Siswa B in Kelas 1A -> MUST PASS
    $validator = Validator::make(
        ['shadow_teacher_id' => $this->guruShadowX->id],
        ['shadow_teacher_id' => [new MaxOneShadowTeacherPerClass($this->kelas1A->id)]]
    );

    expect($validator->passes())->toBeTrue();
});

test('manajemen siswa livewire component enforces max 1 shadow teacher per class rule on save', function () {
    $this->actingAs($this->adminUser);

    // Existing student in Kelas 1A has GuruShadowX
    $userA = User::create([
        'nama' => 'Siswa A',
        'username' => 'siswa_a_' . uniqid(),
        'password' => bcrypt('password'),
        'role_id' => Role::where('nama', 'murid')->first()->id,
    ]);
    Siswa::create([
        'user_id' => $userA->id,
        'nis' => 'NISA-' . rand(1000, 9999),
        'jenis_kelamin' => 'L',
        'kelas_id' => $this->kelas1A->id,
        'shadow_teacher_id' => $this->guruShadowX->id,
        'tanggal_masuk' => '2026-07-01',
        'status' => 'aktif',
    ]);

    // Try creating Siswa B in Kelas 1A with GuruShadowY via Livewire
    Livewire::test(ManajemenSiswa::class)
        ->call('openCreate')
        ->set('nama', 'Siswa B Invalid')
        ->set('username', 'siswa_b_' . uniqid())
        ->set('password', 'password123')
        ->set('nis', 'NISB-' . rand(1000, 9999))
        ->set('jenis_kelamin', 'P')
        ->set('kelas_id', $this->kelas1A->id)
        ->set('shadow_teacher_id', $this->guruShadowY->id)
        ->set('tanggal_masuk', '2026-07-01')
        ->set('status', 'aktif')
        ->call('save')
        ->assertHasErrors(['shadow_teacher_id']);
});

test('strict data scoping on guru pendamping dashboard only shows assigned students', function () {
    // Siswa 1 assigned to GuruShadowX
    $user1 = User::create([
        'nama' => 'Siswa Binaan Guru X',
        'username' => 'siswa_binaan_x_' . uniqid(),
        'password' => bcrypt('password'),
        'role_id' => Role::where('nama', 'murid')->first()->id,
    ]);
    $siswa1 = Siswa::create([
        'user_id' => $user1->id,
        'nis' => 'NIS1-' . rand(1000, 9999),
        'jenis_kelamin' => 'L',
        'kelas_id' => $this->kelas1A->id,
        'shadow_teacher_id' => $this->guruShadowX->id,
        'tanggal_masuk' => '2026-07-01',
        'status' => 'aktif',
    ]);

    // Siswa 2 assigned to GuruShadowY
    $user2 = User::create([
        'nama' => 'Siswa Binaan Guru Y',
        'username' => 'siswa_binaan_y_' . uniqid(),
        'password' => bcrypt('password'),
        'role_id' => Role::where('nama', 'murid')->first()->id,
    ]);
    $siswa2 = Siswa::create([
        'user_id' => $user2->id,
        'nis' => 'NIS2-' . rand(1000, 9999),
        'jenis_kelamin' => 'P',
        'kelas_id' => $this->kelas1B->id,
        'shadow_teacher_id' => $this->guruShadowY->id,
        'tanggal_masuk' => '2026-07-01',
        'status' => 'aktif',
    ]);

    // Log in as GuruShadowX
    $this->actingAs($this->userShadowX);

    Livewire::test(GuruDashboard::class)
        ->assertSet('isGuruPendamping', true)
        ->assertSet('totalSiswaDidampingi', 1)
        ->assertViewHas('siswaDidampingiList', function ($list) use ($siswa1, $siswa2) {
            $ids = collect($list)->pluck('id')->toArray();
            return in_array($siswa1->id, $ids) && !in_array($siswa2->id, $ids);
        });
});

test('shadow teacher cannot create observation notes for unassigned student', function () {
    // Siswa 2 is assigned to GuruShadowY
    $user2 = User::create([
        'nama' => 'Siswa Binaan Guru Y',
        'username' => 'siswa_binaan_y_' . uniqid(),
        'password' => bcrypt('password'),
        'role_id' => Role::where('nama', 'murid')->first()->id,
    ]);
    $siswa2 = Siswa::create([
        'user_id' => $user2->id,
        'nis' => 'NIS2-' . rand(1000, 9999),
        'jenis_kelamin' => 'P',
        'kelas_id' => $this->kelas1B->id,
        'shadow_teacher_id' => $this->guruShadowY->id,
        'tanggal_masuk' => '2026-07-01',
        'status' => 'aktif',
    ]);

    // Log in as GuruShadowX
    $this->actingAs($this->userShadowX);

    Livewire::test(CatatanPendampinganIndex::class)
        ->set('form_tanggal', '2026-09-09')
        ->set('form_siswa_id', $siswa2->id) // Siswa of Guru Y
        ->set('form_aspek', 'Komunikasi')
        ->set('form_hasil_perkembangan', 'BSH')
        ->set('form_catatan', 'Catatan ilegal')
        ->call('save')
        ->assertHasErrors(['form_siswa_id']);
});

test('student model and scopeForShadowTeacher work accurately', function () {
    // Student 1 assigned to GuruShadowX
    $user1 = User::create([
        'nama' => 'Siswa Scope 1',
        'username' => 'siswa_scope_1_' . uniqid(),
        'password' => bcrypt('password'),
        'role_id' => Role::where('nama', 'murid')->first()->id,
    ]);
    $s1 = Siswa::create([
        'user_id' => $user1->id,
        'nis' => 'SC1-' . rand(1000, 9999),
        'jenis_kelamin' => 'L',
        'shadow_teacher_id' => $this->guruShadowX->id,
        'tanggal_masuk' => '2026-07-01',
        'status' => 'aktif',
    ]);

    // Query using Siswa scope
    expect(Siswa::forShadowTeacher($this->guruShadowX)->pluck('id')->toArray())->toContain($s1->id);
    expect(Siswa::forShadowTeacher($this->guruShadowY)->pluck('id')->toArray())->not->toContain($s1->id);

    // Query using Student alias
    expect(Student::where('shadow_teacher_id', $this->guruShadowX->id)->pluck('id')->toArray())->toContain($s1->id);
    expect(Student::forShadowTeacher($this->guruShadowX)->pluck('id')->toArray())->toContain($s1->id);
});

test('shadow teacher sees assigned student in absensi siswa in read only mode', function () {
    $userMuridRole = Role::where('nama', 'murid')->first();

    // Create student A assigned to shadow teacher X in kelas1A
    $u1 = User::create([
        'nama' => 'Murid Inklusi X',
        'username' => 'murid_inklusi_x_' . uniqid(),
        'email' => 'murid_x_' . uniqid() . '@test.com',
        'password' => bcrypt('password'),
        'role_id' => $userMuridRole->id,
    ]);
    $s1 = Siswa::create([
        'user_id' => $u1->id,
        'nis' => '11001',
        'nisn' => '00110011',
        'kelas_id' => $this->kelas1A->id,
        'shadow_teacher_id' => $this->guruShadowX->id,
        'tanggal_masuk' => '2026-07-01',
        'status' => 'aktif',
    ]);

    // Create student B in kelas1A with no shadow teacher
    $u2 = User::create([
        'nama' => 'Murid Reguler B',
        'username' => 'murid_reguler_b_' . uniqid(),
        'email' => 'murid_b_' . uniqid() . '@test.com',
        'password' => bcrypt('password'),
        'role_id' => $userMuridRole->id,
    ]);
    $s2 = Siswa::create([
        'user_id' => $u2->id,
        'nis' => '11002',
        'nisn' => '00110022',
        'kelas_id' => $this->kelas1A->id,
        'shadow_teacher_id' => null,
        'tanggal_masuk' => '2026-07-01',
        'status' => 'aktif',
    ]);

    // Create attendance record for student A
    AbsensiSiswaModel::create([
        'siswa_id' => $s1->id,
        'kelas_id' => $this->kelas1A->id,
        'guru_id' => $this->guruUmum->id,
        'tanggal' => date('Y-m-d'),
        'status' => 'hadir',
        'catatan' => 'Didampingi penuh di kelas',
    ]);

    $this->actingAs($this->userShadowX);

    Livewire::test(AbsensiSiswa::class)
        ->assertStatus(200)
        ->assertSet('isReadOnly', true)
        ->set('kelas_id', $this->kelas1A->id)
        ->assertSee('Mode Pratinjau (Hanya Lihat)')
        ->assertSee('Murid Inklusi X')
        ->assertSee('Didampingi penuh di kelas')
        ->assertDontSee('Murid Reguler B')
        ->assertDontSee('Simpan Seluruh Kehadiran');
});

test('shadow teacher cannot set status or save attendance in absensi siswa', function () {
    $userMuridRole = Role::where('nama', 'murid')->first();

    $u1 = User::create([
        'nama' => 'Murid Inklusi Save Lock',
        'username' => 'murid_lock_' . uniqid(),
        'email' => 'murid_lock_' . uniqid() . '@test.com',
        'password' => bcrypt('password'),
        'role_id' => $userMuridRole->id,
    ]);
    $s1 = Siswa::create([
        'user_id' => $u1->id,
        'nis' => '11003',
        'nisn' => '00110033',
        'kelas_id' => $this->kelas1A->id,
        'shadow_teacher_id' => $this->guruShadowX->id,
        'tanggal_masuk' => '2026-07-01',
        'status' => 'aktif',
    ]);

    $this->actingAs($this->userShadowX);

    $component = Livewire::test(AbsensiSiswa::class)
        ->set('kelas_id', $this->kelas1A->id);

    // Attempt to change status
    $component->call('setStatus', 0, 'alpa');
    $attendance = $component->get('attendance');
    expect($attendance[0]['status'])->toBe('hadir'); // Remains default 'hadir', not 'alpa' because blocked

    // Attempt to save
    $component->call('save')
        ->assertSee('Akses dibatasi. Pengisian presensi harian siswa hanya dapat dilakukan oleh Guru Umum');

    // Verify database has no attendance record created by shadow teacher
    expect(AbsensiSiswaModel::where('siswa_id', $s1->id)->where('guru_id', $this->guruShadowX->id)->exists())->toBeFalse();
});

test('shadow teacher can access jadwal piket menu and view schedule', function () {
    $activeSemester = Semester::where('status_aktif', true)->first() ?? Semester::first();

    // Create piket schedule for shadow teacher X on senin
    JadwalPiketGuru::firstOrCreate([
        'guru_id' => $this->guruShadowX->id,
        'hari' => 'senin',
        'semester_id' => $activeSemester->id,
    ]);

    $this->actingAs($this->userShadowX);

    Livewire::test(ManajemenPiketGuru::class)
        ->assertStatus(200)
        ->assertSee('Jadwal Piket Guru')
        ->assertSee($this->userShadowX->nama)
        ->assertDontSee('Simpan Penugasan'); // Teacher cannot manage, view-only
});

test('guru umum can still record and save attendance normally', function () {
    $userMuridRole = Role::where('nama', 'murid')->first();

    // Assign kelas1B to guru umum
    $this->kelas1B->update(['guru_umum_id' => $this->guruUmum->id]);

    $u1 = User::create([
        'nama' => 'Murid Reguler Umum',
        'username' => 'murid_reg_u_' . uniqid(),
        'email' => 'murid_reg_u_' . uniqid() . '@test.com',
        'password' => bcrypt('password'),
        'role_id' => $userMuridRole->id,
    ]);
    $s1 = Siswa::create([
        'user_id' => $u1->id,
        'nis' => '11005',
        'nisn' => '00110055',
        'kelas_id' => $this->kelas1B->id,
        'tanggal_masuk' => '2026-07-01',
        'status' => 'aktif',
    ]);

    $this->actingAs($this->userGuruUmum);

    Livewire::test(AbsensiSiswa::class)
        ->assertStatus(200)
        ->assertSet('isReadOnly', false)
        ->set('kelas_id', $this->kelas1B->id)
        ->assertSee('Simpan Seluruh Kehadiran')
        ->call('setStatus', 0, 'hadir')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('show-alert')
        ->assertSee('Kehadiran siswa berhasil disimpan');

    expect(AbsensiSiswaModel::where('siswa_id', $s1->id)->where('status', 'hadir')->exists())->toBeTrue();
});

