<?php

namespace App\Livewire\SuperAdmin\TataKelola;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ManajemenSiswa extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    // Form fields
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
    public ?string $tanggal_masuk = null;
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
        $siswa = Siswa::with('user')->findOrFail($id);
        $this->siswaId = $siswa->id;
        $this->nama = $siswa->user->nama ?? '';
        $this->username = $siswa->user->username ?? '';
        $this->email = $siswa->user->email ?? '';
        $this->nis = $siswa->nis;
        $this->nisn = $siswa->nisn ?? '';
        $this->jenis_kelamin = $siswa->jenis_kelamin;
        $this->tempat_lahir = $siswa->tempat_lahir ?? '';
        $this->tanggal_lahir = $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Y-m-d') : null;
        $this->alamat = $siswa->alamat ?? '';
        $this->nama_wali = $siswa->nama_wali ?? '';
        $this->no_hp_wali = $siswa->no_hp_wali ?? '';
        $this->kelas_id = $siswa->kelas_id;
        $this->tanggal_masuk = $siswa->tanggal_masuk ? $siswa->tanggal_masuk->format('Y-m-d') : null;
        $this->status = $siswa->status;

        $this->isFormOpen = true;
    }

    public function save()
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . ($this->siswaId ? Siswa::find($this->siswaId)->user_id : 'NULL'),
            'nis' => 'required|string|max:20|unique:siswa,nis,' . ($this->siswaId ?? 'NULL'),
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_masuk' => 'required|date',
            'status' => 'required|in:aktif,lulus,pindah,keluar',
        ];

        if (!$this->siswaId) {
            $rules['password'] = 'required|string|min:6';
        }

        $this->validate($rules);

        DB::transaction(function () {
            $roleMurid = Role::where('nama', 'murid')->firstOrFail();

            if ($this->siswaId) {
                // Update
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
                    'kelas_id' => $this->kelas_id,
                    'tanggal_masuk' => $this->tanggal_masuk,
                    'status' => $this->status,
                ]);
            } else {
                // Create
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

                Siswa::create([
                    'user_id' => $user->id,
                    'nis' => $this->nis,
                    'nisn' => $this->nisn ?: null,
                    'jenis_kelamin' => $this->jenis_kelamin,
                    'tempat_lahir' => $this->tempat_lahir ?: null,
                    'tanggal_lahir' => $this->tanggal_lahir ?: null,
                    'alamat' => $this->alamat ?: null,
                    'nama_wali' => $this->nama_wali ?: null,
                    'no_hp_wali' => $this->no_hp_wali ?: null,
                    'kelas_id' => $this->kelas_id,
                    'tanggal_masuk' => $this->tanggal_masuk,
                    'status' => 'aktif',
                ]);
            }
        });

        session()->flash('message', 'Data siswa berhasil disimpan.');
        $this->isFormOpen = false;
        $this->resetForm();
    }

    public function delete(int $id)
    {
        DB::transaction(function () use ($id) {
            $siswa = Siswa::findOrFail($id);
            $user = $siswa->user;
            
            $siswa->delete();
            if ($user) {
                $user->delete();
            }
        });

        session()->flash('message', 'Data siswa berhasil dihapus.');
    }

    private function resetForm()
    {
        $this->siswaId = null;
        $this->nama = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->nis = '';
        $this->nisn = '';
        $this->jenis_kelamin = 'L';
        $this->tempat_lahir = '';
        $this->tanggal_lahir = null;
        $this->alamat = '';
        $this->nama_wali = '';
        $this->no_hp_wali = '';
        $this->kelas_id = null;
        $this->tanggal_masuk = date('Y-m-d');
        $this->status = 'aktif';
    }

    public function render()
    {
        $siswas = Siswa::with(['user', 'kelas'])
            ->where(function ($query) {
                $query->where('nis', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('nama', 'like', '%' . $this->search . '%')
                          ->orWhere('username', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate($this->perPage);

        $kelases = Kelas::all();

        return view('livewire.super-admin.tata-kelola.manajemen-siswa', [
            'siswas' => $siswas,
            'kelases' => $kelases,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Siswa']);
    }
}
