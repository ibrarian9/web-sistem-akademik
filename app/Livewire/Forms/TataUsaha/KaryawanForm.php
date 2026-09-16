<?php

namespace App\Livewire\Forms\TataUsaha;

use App\Models\Guru;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Form;

class KaryawanForm extends Form
{
    public ?int $karyawanId = null;
    public string $nama = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public ?int $role_id = null;
    public string $nip = '';
    public string $no_hp = '';
    public string $alamat = '';
    public string $status = 'aktif';
    public string $jenis_guru = 'umum';
    public string $status_kepegawaian = 'honorer';

    public function rules(): array
    {
        $userId = $this->karyawanId;
        $rules = [
            'nama' => 'required|string|max:100',
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($userId)],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('users', 'email')->ignore($userId)],
            'role_id' => 'required|exists:roles,id',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
            'jenis_guru' => 'required|in:umum,tahfidz,pendamping,keduanya',
            'status_kepegawaian' => 'required|in:tetap,honorer',
        ];

        if (!$userId) {
            $rules['password'] = 'required|string|min:6';
        } else {
            $rules['password'] = 'nullable|string|min:6';
        }

        $userForGuru = $this->karyawanId ? User::find($this->karyawanId) : null;
        $guruId = $userForGuru?->guru?->id;

        if ($this->nip) {
            $rules['nip'] = 'unique:guru,nip,' . ($guruId ?? 'NULL');
        }

        return $rules;
    }

    public function setKaryawan(User $user): void
    {
        $this->karyawanId = $user->id;
        $this->nama = $user->nama;
        $this->username = $user->username;
        $this->email = $user->email ?? '';
        $this->password = '';
        $this->role_id = $user->role_id;
        $this->no_hp = $user->no_hp ?? '';
        $this->alamat = $user->alamat ?? '';
        $this->status = $user->status;

        if ($user->guru) {
            $this->nip = $user->guru->nip ?? '';
            $this->jenis_guru = $user->guru->jenis_guru ?? 'umum';
            $this->status_kepegawaian = $user->guru->status_kepegawaian ?? 'honorer';
        } else {
            $this->nip = '';
            $this->jenis_guru = 'umum';
            $this->status_kepegawaian = 'honorer';
        }
    }

    public function resetForm(?int $defaultRoleId = null): void
    {
        $this->reset([
            'karyawanId', 'nama', 'username', 'email', 'password',
            'nip', 'no_hp', 'alamat'
        ]);
        $this->status = 'aktif';
        $this->jenis_guru = 'umum';
        $this->status_kepegawaian = 'honorer';
        $this->role_id = $defaultRoleId;
    }

    public function store(Role $selectedRole): User
    {
        $userData = [
            'nama' => $this->nama,
            'username' => $this->username,
            'email' => $this->email ?: null,
            'role_id' => $this->role_id,
            'no_hp' => $this->no_hp ?: null,
            'alamat' => $this->alamat ?: null,
            'status' => $this->status,
        ];

        if ($this->password) {
            $userData['password'] = Hash::make($this->password);
        }

        if ($this->karyawanId) {
            $user = User::findOrFail($this->karyawanId);
            $user->update($userData);
        } else {
            $user = User::create($userData);
        }

        if ($user->guru) {
            $user->guru->update([
                'nip' => $this->nip ?: null,
                'jenis_guru' => $this->jenis_guru ?: 'umum',
                'status_kepegawaian' => $this->status_kepegawaian ?: 'honorer',
                'no_hp' => $this->no_hp ?: '-',
                'alamat' => $this->alamat ?: '-',
                'status_aktif' => $this->status === 'aktif',
            ]);
        } else {
            Guru::create([
                'user_id' => $user->id,
                'nip' => $this->nip ?: ($selectedRole->nama === 'guru' ? null : 'STAFF-' . $user->id),
                'jenis_guru' => $this->jenis_guru ?: 'umum',
                'status_kepegawaian' => $this->status_kepegawaian ?: 'honorer',
                'no_hp' => $this->no_hp ?: '-',
                'alamat' => $this->alamat ?: '-',
                'tanggal_masuk' => date('Y-m-d'),
                'status_aktif' => $this->status === 'aktif',
            ]);
        }

        return $user;
    }
}
