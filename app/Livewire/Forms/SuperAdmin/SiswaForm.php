<?php

namespace App\Livewire\Forms\SuperAdmin;

use App\Models\Role;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\User;
use App\Rules\EligibleShadowTeacher;
use App\Rules\MaxOneShadowTeacherPerClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Form;

class SiswaForm extends Form
{
    public ?int $siswaId = null;
    public string $nama = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $nis = '';
    public string $nisn = '';
    public string $jenis_kelamin = 'L';
    public string $tempat_lahir = '';
    public ?string $tanggal_lahir = null;
    public string $alamat = '';
    public string $nama_wali = '';
    public string $no_hp_wali = '';
    public ?int $kelas_id = null;
    public ?int $kelas_tahfidz_id = null;
    public ?int $shadow_teacher_id = null;
    public ?string $tanggal_masuk = null;
    public string $status = 'aktif';

    public function rules(): array
    {
        $userId = $this->siswaId ? Siswa::find($this->siswaId)?->user_id : null;

        $rules = [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . ($userId ?: 'NULL'),
            'email' => 'nullable|email|max:255|unique:users,email,' . ($userId ?: 'NULL'),
            'nis' => 'required|string|max:20|unique:siswa,nis,' . ($this->siswaId ?: 'NULL'),
            'nisn' => 'nullable|string|max:20|unique:siswa,nisn,' . ($this->siswaId ?: 'NULL'),
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'nullable|exists:kelas,id',
            'kelas_tahfidz_id' => 'nullable|exists:kelas,id',
            'shadow_teacher_id' => [
                'nullable',
                'exists:guru,id',
                new EligibleShadowTeacher(),
                new MaxOneShadowTeacherPerClass($this->kelas_id, $this->siswaId),
            ],
            'tanggal_masuk' => 'required|date',
            'status' => 'required|in:aktif,lulus,pindah,keluar',
        ];

        if (!$this->siswaId) {
            $rules['password'] = 'required|string|min:6';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Alamat email ini sudah terdaftar untuk pengguna lain. Silakan gunakan email lain atau kosongkan.',
            'email.email' => 'Format alamat email tidak valid.',
            'username.unique' => 'Username ini sudah terdaftar di sistem. Silakan pilih username lain.',
            'nis.unique' => 'NIS (Nomor Induk Siswa) ini sudah terdaftar untuk siswa lain.',
            'nisn.unique' => 'NISN ini sudah terdaftar untuk siswa lain.',
        ];
    }

    public function setSiswa(Siswa $siswa): void
    {
        $this->siswaId = $siswa->id;
        $this->nama = $siswa->user->nama ?? '';
        $this->username = $siswa->user->username ?? '';
        $this->email = $siswa->user->email ?? '';
        $this->password = '';
        $this->nis = $siswa->nis ?? '';
        $this->nisn = $siswa->nisn ?? '';
        $this->jenis_kelamin = $siswa->jenis_kelamin ?? 'L';
        $this->tempat_lahir = $siswa->tempat_lahir ?? '';
        $this->tanggal_lahir = $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Y-m-d') : null;
        $this->alamat = $siswa->alamat ?? ($siswa->user->alamat ?? '');
        $this->nama_wali = $siswa->nama_wali ?? '';
        $this->no_hp_wali = $siswa->no_hp_wali ?? ($siswa->user->no_hp ?? '');
        $this->kelas_id = $siswa->kelas_id;
        $this->kelas_tahfidz_id = $siswa->kelas_tahfidz_id;
        $this->shadow_teacher_id = $siswa->shadow_teacher_id;
        $this->tanggal_masuk = $siswa->tanggal_masuk ? $siswa->tanggal_masuk->format('Y-m-d') : null;
        $this->status = $siswa->status ?? 'aktif';
    }

    public function resetForm(): void
    {
        $this->reset([
            'siswaId', 'nama', 'username', 'email', 'password', 'nis', 'nisn',
            'tempat_lahir', 'tanggal_lahir', 'alamat', 'nama_wali', 'no_hp_wali',
            'kelas_id', 'kelas_tahfidz_id', 'shadow_teacher_id', 'tanggal_masuk'
        ]);
        $this->jenis_kelamin = 'L';
        $this->status = 'aktif';
        $this->tanggal_masuk = date('Y-m-d');
    }

    public function store(): Siswa
    {
        $roleMurid = Role::firstOrCreate(
            ['nama' => 'murid'],
            ['deskripsi' => 'Murid / Siswa']
        );

        if ($this->siswaId) {
            $siswa = Siswa::findOrFail($this->siswaId);

            $siswa->user->update([
                'nama' => $this->nama,
                'username' => $this->username,
                'email' => $this->email ?: null,
                'no_hp' => $this->no_hp_wali,
                'alamat' => $this->alamat,
                'status' => $this->status === 'aktif' ? 'aktif' : 'nonaktif',
            ]);

            if ($this->password) {
                $siswa->user->update(['password' => Hash::make($this->password)]);
            }

            $siswa->update([
                'nis' => $this->nis,
                'nisn' => $this->nisn ?: null,
                'jenis_kelamin' => $this->jenis_kelamin,
                'tempat_lahir' => $this->tempat_lahir ?: null,
                'tanggal_lahir' => $this->tanggal_lahir ?: null,
                'alamat' => $this->alamat ?: null,
                'nama_wali' => $this->nama_wali ?: null,
                'no_hp_wali' => $this->no_hp_wali ?: null,
                'kelas_id' => $this->kelas_id ?: null,
                'kelas_tahfidz_id' => $this->kelas_tahfidz_id ?: null,
                'shadow_teacher_id' => $this->shadow_teacher_id ?: null,
                'tanggal_masuk' => $this->tanggal_masuk,
                'status' => $this->status,
            ]);

            if (in_array($this->status, ['pindah', 'keluar'], true)) {
                Tagihan::where('siswa_id', $siswa->id)
                    ->where('status', 'belum_bayar')
                    ->whereDate('jatuh_tempo', '>', now())
                    ->update(['status' => 'batal']);
            }

            return $siswa;
        }

        $user = User::create([
            'nama' => $this->nama,
            'username' => $this->username,
            'email' => $this->email ?: null,
            'password' => Hash::make($this->password),
            'role_id' => $roleMurid->id,
            'no_hp' => $this->no_hp_wali,
            'alamat' => $this->alamat,
            'status' => 'aktif',
        ]);

        return Siswa::create([
            'user_id' => $user->id,
            'nis' => $this->nis,
            'nisn' => $this->nisn ?: null,
            'jenis_kelamin' => $this->jenis_kelamin,
            'tempat_lahir' => $this->tempat_lahir ?: null,
            'tanggal_lahir' => $this->tanggal_lahir ?: null,
            'alamat' => $this->alamat ?: null,
            'nama_wali' => $this->nama_wali ?: null,
            'no_hp_wali' => $this->no_hp_wali ?: null,
            'kelas_id' => $this->kelas_id ?: null,
            'kelas_tahfidz_id' => $this->kelas_tahfidz_id ?: null,
            'shadow_teacher_id' => $this->shadow_teacher_id ?: null,
            'tanggal_masuk' => $this->tanggal_masuk,
            'status' => 'aktif',
        ]);
    }
}
