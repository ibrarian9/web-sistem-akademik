<?php

namespace App\Livewire\SuperAdmin\TataKelola;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use App\Models\Kelas;
use App\Models\JadwalPelajaran;
use App\Models\GuruMapelKelas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

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
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

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
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

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
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        // Don't delete self
        if ($id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        $user = User::with(['role', 'guru', 'siswa'])->findOrFail($id);

        if ($this->isTuUser() && in_array($user->role?->nama, ['murid', 'super_admin'])) {
            session()->flash('error', 'Tata Usaha tidak dapat menghapus akun ini.');
            return;
        }

        try {
            DB::transaction(function () use ($user) {
                \App\Services\AuditLogger::log('deleted', 'Menghapus pengguna: ' . $user->nama, $user, [
                    'log_name' => 'manajemen_user',
                ]);

                if ($user->guru) {
                    $guru = $user->guru;
                    Kelas::where('guru_umum_id', $guru->id)->update(['guru_umum_id' => null]);
                    Kelas::where('guru_tahfidz_id', $guru->id)->update(['guru_tahfidz_id' => null]);
                    if (Schema::hasColumn('kelas', 'guru_id')) {
                        Kelas::where('guru_id', $guru->id)->update(['guru_id' => null]);
                    }
                    if (Schema::hasTable('jadwal_pelajaran') && Schema::hasColumn('jadwal_pelajaran', 'guru_id')) {
                        JadwalPelajaran::where('guru_id', $guru->id)->delete();
                    }
                    GuruMapelKelas::where('guru_id', $guru->id)->delete();
                    if (Schema::hasTable('jadwal_piket_guru')) {
                        DB::table('jadwal_piket_guru')->where('guru_id', $guru->id)->delete();
                    }
                    if (Schema::hasTable('ekstrakurikuler') && Schema::hasColumn('ekstrakurikuler', 'pembina_guru_id')) {
                        DB::table('ekstrakurikuler')->where('pembina_guru_id', $guru->id)->update(['pembina_guru_id' => null]);
                    }
                    $guru->delete();
                }

                if ($user->siswa) {
                    $siswa = $user->siswa;
                    $siswa->tagihans()->where('status', '!=', 'lunas')->delete();
                    $siswa->delete();
                }

                $user->delete();
            });

            session()->flash('message', 'Pengguna berhasil dihapus.');
        } catch (\Throwable $e) {
            \App\Services\AuditLogger::log('error', 'Gagal menghapus data pengguna ID ' . $id . ': ' . $e->getMessage(), null, [
                'log_name' => 'manajemen_user',
            ]);

            session()->flash('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        }
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
