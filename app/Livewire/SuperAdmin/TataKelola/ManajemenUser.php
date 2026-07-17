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

    public function openCreate()
    {
        $this->resetForm();
        $this->isFormOpen = true;
    }

    public function openEdit(int $id)
    {
        $this->resetForm();
        $user = User::findOrFail($id);
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

        User::findOrFail($id)->delete();
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
        $users = User::with('role')
            ->where(function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('username', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        $roles = Role::all();

        return view('livewire.super-admin.tata-kelola.manajemen-user', [
            'users' => $users,
            'roles' => $roles,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Pengguna']);
    }
}
