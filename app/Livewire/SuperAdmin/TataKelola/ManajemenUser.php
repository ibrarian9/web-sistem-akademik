<?php

namespace App\Livewire\SuperAdmin\TataKelola;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class ManajemenUser extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    // Form fields
    public ?int $userId = null;
    public string $nama = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public ?int $role_id = null;
    public string $no_hp = '';
    public string $status = 'aktif';

    public bool $isFormOpen = false;

    protected $queryString = ['search' => ['except' => '']];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function isTuUser(): bool
    {
        return (auth()->user()->role->nama ?? '') === 'tata_usaha';
    }

    public function openCreate()
    {
        $this->resetForm();
        if ($this->isTuUser()) {
            $guruRole = Role::where('nama', 'guru')->first();
            if ($guruRole) {
                $this->role_id = $guruRole->id;
            }
        }
        $this->isFormOpen = true;
    }

    public function openEdit(int $id)
    {
        $this->resetForm();
        $user = User::with('role')->findOrFail($id);

        if ($this->isTuUser() && in_array($user->role?->nama, ['murid', 'super_admin'])) {
            session()->flash('error', 'Tata Usaha tidak berhak mengelola data akun ini.');
            return;
        }

        $this->userId = $user->id;
        $this->nama = $user->nama;
        $this->username = $user->username;
        $this->email = $user->email ?? '';
        $this->role_id = $user->role_id;
        $this->no_hp = $user->no_hp ?? '';
        $this->status = $user->status;

        $this->isFormOpen = true;
    }

    public function save()
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . ($this->userId ?? 'NULL'),
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:aktif,nonaktif',
        ];

        if ($this->email) {
            $rules['email'] = 'email|unique:users,email,' . ($this->userId ?? 'NULL');
        }

        if (!$this->userId) {
            $rules['password'] = 'required|string|min:6';
        }

        $this->validate($rules);

        if ($this->isTuUser()) {
            $targetRole = Role::find($this->role_id);
            if (in_array($targetRole?->nama, ['murid', 'super_admin'])) {
                session()->flash('error', 'Tata Usaha hanya dapat membuat atau mengubah akun karyawan / staf.');
                return;
            }
        }

        $data = [
            'nama' => $this->nama,
            'username' => $this->username,
            'email' => $this->email ?: null,
            'role_id' => $this->role_id,
            'no_hp' => $this->no_hp ?: null,
            'status' => $this->status,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        User::updateOrCreate(['id' => $this->userId], $data);

        session()->flash('message', 'Pengguna berhasil disimpan.');
        $this->isFormOpen = false;
        $this->resetForm();
    }

    public function delete(int $id)
    {
        // Don't delete self
        if ($id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        $user = User::with('role')->findOrFail($id);

        if ($this->isTuUser() && in_array($user->role?->nama, ['murid', 'super_admin'])) {
            session()->flash('error', 'Tata Usaha tidak dapat menghapus akun ini.');
            return;
        }

        $user->delete();
        session()->flash('message', 'Pengguna berhasil dihapus.');
    }

    private function resetForm()
    {
        $this->userId = null;
        $this->nama = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->role_id = null;
        $this->no_hp = '';
        $this->status = 'aktif';
    }

    public function render()
    {
        $isTu = $this->isTuUser();

        $users = User::with('role')
            ->when($isTu, function ($query) {
                $query->whereHas('role', function ($q) {
                    $q->whereNotIn('nama', ['murid', 'super_admin']);
                });
            })
            ->where(function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('username', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        $roles = Role::when($isTu, function ($query) {
            $query->whereNotIn('nama', ['murid', 'super_admin']);
        })->get();

        return view('livewire.super-admin.tata-kelola.manajemen-user', [
            'users' => $users,
            'roles' => $roles,
            'isTu' => $isTu,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Pengguna']);
    }
}
