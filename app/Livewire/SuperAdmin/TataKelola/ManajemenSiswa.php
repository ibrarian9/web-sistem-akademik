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
    public ?int $kelas_id = null; // Kelas Umum
    public ?int $kelas_tahfidz_id = null; // Kelas Tahfizh
    public ?string $tanggal_masuk = null;
    public string $status = 'aktif';

    public bool $isFormOpen = false;

    // Student Detail Modal state
    public $selectedSiswaDetail = null;
    public bool $showDetailModal = false;

    protected $queryString = ['search' => ['except' => '']];

    public function openDetail(int $id)
    {
        $siswa = Siswa::with(['user', 'kelas.guruUmum.user', 'kelasTahfidz.guruTahfidz.user'])->findOrFail($id);
        $this->selectedSiswaDetail = $siswa;
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->selectedSiswaDetail = null;
        $this->showDetailModal = false;
    }

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
        $this->kelas_tahfidz_id = $siswa->kelas_tahfidz_id;
        $this->tanggal_masuk = $siswa->tanggal_masuk ? $siswa->tanggal_masuk->format('Y-m-d') : null;
        $this->status = $siswa->status;

        $this->isFormOpen = true;
    }

    public function save()
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
            'tanggal_masuk' => 'required|date',
            'status' => 'required|in:aktif,lulus,pindah,keluar',
        ];

        if (!$this->siswaId) {
            $rules['password'] = 'required|string|min:6';
        }

        $messages = [
            'email.unique' => 'Alamat email ini sudah terdaftar untuk pengguna lain. Silakan gunakan email lain atau kosongkan.',
            'email.email' => 'Format alamat email tidak valid.',
            'username.unique' => 'Username ini sudah terdaftar di sistem. Silakan pilih username lain.',
            'nis.unique' => 'NIS (Nomor Induk Siswa) ini sudah terdaftar untuk siswa lain.',
            'nisn.unique' => 'NISN ini sudah terdaftar untuk siswa lain.',
        ];

        $this->validate($rules, $messages);

        try {
            $isUpdate = (bool) $this->siswaId;
            $namaSiswa = $this->nama;

            DB::transaction(function () use (&$isUpdate, &$namaSiswa) {
                $roleMurid = Role::firstOrCreate(
                    ['nama' => 'murid'],
                    ['deskripsi' => 'Murid / Siswa']
                );

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
                        'kelas_id' => $this->kelas_id ?: null,
                        'kelas_tahfidz_id' => $this->kelas_tahfidz_id ?: null,
                        'tanggal_masuk' => $this->tanggal_masuk,
                        'status' => $this->status,
                    ]);

                    // If student status changed to 'pindah' or 'keluar', cancel future unpaid bills
                    if (in_array($this->status, ['pindah', 'keluar'])) {
                        \App\Models\Tagihan::where('siswa_id', $siswa->id)
                            ->where('status', 'belum_bayar')
                            ->whereDate('jatuh_tempo', '>', now())
                            ->update(['status' => 'batal']);
                    }

                    \App\Services\AuditLogger::log('updated', 'Mengubah profil siswa: ' . $namaSiswa, $siswa, [
                        'log_name' => 'manajemen_siswa',
                        'siswa_id' => $siswa->id,
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

                    $siswa = Siswa::create([
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
                        'tanggal_masuk' => $this->tanggal_masuk,
                        'status' => 'aktif',
                    ]);

                    \App\Services\AuditLogger::log('created', 'Menambahkan siswa baru: ' . $namaSiswa, $siswa, [
                        'log_name' => 'manajemen_siswa',
                        'siswa_id' => $siswa->id,
                    ]);
                }
            });

            $msg = 'Data siswa ' . $namaSiswa . ' berhasil ' . ($isUpdate ? 'perbarui.' : 'disimpan.');
            session()->flash('message', $msg);
            $this->dispatch('show-alert', [
                'title' => $isUpdate ? 'Data Siswa Diperbarui' : 'Siswa Baru Ditambahkan',
                'message' => $msg,
                'type' => $isUpdate ? 'edit' : 'create',
            ]);

            $this->isFormOpen = false;
            $this->resetForm();
        } catch (\Throwable $e) {
            $rawError = $e->getMessage();
            \App\Services\AuditLogger::log('error', 'Gagal memproses data siswa: ' . $rawError, null, [
                'log_name' => 'manajemen_siswa',
            ]);

            // User-friendly error message sanitization
            if (str_contains($rawError, 'users_email_unique') || (str_contains($rawError, '1062') && str_contains($rawError, 'email'))) {
                $userFriendlyMessage = 'Alamat email yang dimasukkan sudah terdaftar untuk pengguna/siswa lain. Silakan gunakan email lain atau kosongkan.';
            } elseif (str_contains($rawError, 'users_username_unique') || (str_contains($rawError, '1062') && str_contains($rawError, 'username'))) {
                $userFriendlyMessage = 'Username yang dimasukkan sudah terdaftar di sistem. Silakan pilih username lain.';
            } elseif (str_contains($rawError, 'siswa_nis_unique') || (str_contains($rawError, '1062') && str_contains($rawError, 'nis'))) {
                $userFriendlyMessage = 'NIS (Nomor Induk Siswa) yang dimasukkan sudah terdaftar untuk siswa lain.';
            } else {
                $userFriendlyMessage = 'Gagal memproses data siswa. Mohon periksa kembali isian formulir Anda.';
            }

            session()->flash('error', $userFriendlyMessage);
            $this->dispatch('show-alert', [
                'title' => 'Gagal Memproses Data Siswa',
                'message' => $userFriendlyMessage,
                'type' => 'danger',
            ]);
        }
    }

    public function delete(int $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $siswa = Siswa::findOrFail($id);
                $namaSiswa = $siswa->user->nama ?? ('ID ' . $id);
                $user = $siswa->user;
                
                \App\Services\AuditLogger::log('deleted', 'Menghapus data siswa: ' . $namaSiswa, $siswa, [
                    'log_name' => 'manajemen_siswa',
                    'siswa_id' => $siswa->id,
                ]);

                $siswa->delete();
                if ($user) {
                    $user->delete();
                }
            });

            session()->flash('message', 'Data siswa berhasil dihapus.');
            $this->dispatch('show-alert', [
                'title' => 'Hapus Data Berhasil',
                'message' => 'Data siswa berhasil dihapus dari sistem.',
                'type' => 'delete',
            ]);
        } catch (\Throwable $e) {
            \App\Services\AuditLogger::log('error', 'Gagal menghapus data siswa ID ' . $id . ': ' . $e->getMessage(), null, [
                'log_name' => 'manajemen_siswa',
            ]);

            session()->flash('error', 'Gagal menghapus data: ' . $e->getMessage());
            $this->dispatch('show-alert', [
                'title' => 'Gagal Menghapus Data',
                'message' => $e->getMessage(),
                'type' => 'danger',
            ]);
        }
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
        $this->kelas_tahfidz_id = null;
        $this->tanggal_masuk = date('Y-m-d');
        $this->status = 'aktif';
    }

    public function render()
    {
        $siswas = Siswa::with(['user', 'kelas', 'kelasTahfidz'])
            ->where(function ($query) {
                $query->where('nis', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('nama', 'like', '%' . $this->search . '%')
                          ->orWhere('username', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate($this->perPage);

        $kelasesUmum = Kelas::where(function($q) {
            $q->where('jenis_kelas', 'umum')->orWhereNull('jenis_kelas');
        })->get();

        $kelasesTahfidz = Kelas::where('jenis_kelas', 'tahfidz')->get();

        return view('livewire.super-admin.tata-kelola.manajemen-siswa', [
            'siswas' => $siswas,
            'kelasesUmum' => $kelasesUmum,
            'kelasesTahfidz' => $kelasesTahfidz,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Siswa']);
    }
}
