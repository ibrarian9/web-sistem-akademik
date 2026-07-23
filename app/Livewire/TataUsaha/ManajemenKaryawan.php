<?php

namespace App\Livewire\TataUsaha;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Guru;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ManajemenKaryawan extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = 'semua';
    public int $perPage = 12;

    // Form fields
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

    public bool $isFormOpen = false;

    protected $queryString = ['search' => ['except' => ''], 'filterRole' => ['except' => 'semua']];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterRole()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->resetForm();
        $defaultRole = Role::where('nama', 'guru')->first();
        if ($defaultRole) {
            $this->role_id = $defaultRole->id;
        }
        $this->isFormOpen = true;
    }

    public function openEdit(int $id)
    {
        $this->resetForm();
        $user = User::with(['role', 'guru'])->findOrFail($id);

        // Security check: prevent editing super_admin accounts if logged in user is not super_admin
        if ($user->role?->nama === 'super_admin' && auth()->user()->role?->nama !== 'super_admin') {
            session()->flash('error', 'Anda tidak memiliki hak akses untuk mengedit akun Super Admin.');
            return;
        }

        $this->karyawanId = $user->id;
        $this->nama = $user->nama;
        $this->username = $user->username;
        $this->email = $user->email ?? '';
        $this->role_id = $user->role_id;
        $this->no_hp = $user->no_hp ?? '';
        $this->alamat = $user->alamat ?? '';
        $this->status = $user->status;

        if ($user->guru) {
            $this->nip = $user->guru->nip ?? '';
            $this->jenis_guru = $user->guru->jenis_guru ?? 'umum';
            $this->status_kepegawaian = $user->guru->status_kepegawaian ?? 'honorer';
        }

        $this->isFormOpen = true;
    }

    public function save()
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . ($this->karyawanId ?? 'NULL'),
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:aktif,nonaktif',
        ];

        if ($this->email) {
            $rules['email'] = 'email|unique:users,email,' . ($this->karyawanId ?? 'NULL');
        }

        if (!$this->karyawanId) {
            $rules['password'] = 'required|string|min:6';
        }

        $userForGuru = $this->karyawanId ? User::find($this->karyawanId) : null;
        $guruId = $userForGuru?->guru?->id;

        if ($this->nip) {
            $rules['nip'] = 'unique:guru,nip,' . ($guruId ?? 'NULL');
        }

        $this->validate($rules);

        $selectedRole = Role::findOrFail($this->role_id);
        if ($selectedRole->nama === 'murid') {
            session()->flash('error', 'Role murid tidak dapat ditambahkan dari direktori karyawan.');
            return;
        }

        if ($selectedRole->nama === 'super_admin' && auth()->user()->role?->nama !== 'super_admin') {
            session()->flash('error', 'Anda tidak diizinkan membuat akun dengan role Super Admin.');
            return;
        }

        DB::transaction(function () use ($selectedRole) {
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

            // Sync Guru profile for teacher or staff roles
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
        });

        session()->flash('message', 'Data karyawan dan akun berhasil disimpan.');
        $this->isFormOpen = false;
        $this->resetForm();
    }

    public function delete(int $id)
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        $user = User::with('role')->findOrFail($id);

        if ($user->role?->nama === 'super_admin') {
            session()->flash('error', 'Akun Super Admin tidak dapat dihapus.');
            return;
        }

        DB::transaction(function () use ($user) {
            if ($user->guru) {
                $user->guru->delete();
            }
            $user->delete();
        });

        session()->flash('message', 'Data karyawan dan akun berhasil dihapus.');
    }

    private function resetForm()
    {
        $this->karyawanId = null;
        $this->nama = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->role_id = null;
        $this->nip = '';
        $this->no_hp = '';
        $this->alamat = '';
        $this->status = 'aktif';
        $this->jenis_guru = 'umum';
        $this->status_kepegawaian = 'honorer';
    }

    public function render()
    {
        $query = User::with(['role', 'guru'])
            ->whereNotIn('role_id', function ($q) {
                $q->select('id')->from('roles')->where('nama', 'murid');
            })
            ->orderBy('nama', 'asc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('username', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhereHas('guru', fn($g) => $g->where('nip', 'like', '%' . $this->search . '%'));
            });
        }

        if ($this->filterRole !== 'semua') {
            $query->whereHas('role', function ($q) {
                $q->where('nama', $this->filterRole);
            });
        }

        $karyawanList = $query->paginate($this->perPage);

        $selectableRoles = Role::whereNotIn('nama', ['murid', 'super_admin'])->get();

        return view('livewire.tata-usaha.manajemen-karyawan', [
            'karyawanList' => $karyawanList,
            'selectableRoles' => $selectableRoles,
        ])->layout('components.layouts.app', ['title' => 'Direktori Karyawan & Staff']);
    }
}
