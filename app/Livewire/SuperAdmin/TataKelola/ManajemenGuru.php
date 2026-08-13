<?php

namespace App\Livewire\SuperAdmin\TataKelola;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Guru;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ManajemenGuru extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    // Form fields
    public ?int $guruId = null;
    public string $nama = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $niy = ''; // NIY (Nomor Induk Yayasan)
    public string $nik = ''; // NIK (KTP)
    public string $tempat_lahir = '';
    public ?string $tanggal_lahir = null;
    public string $status_kepegawaian = 'honorer';
    public string $jenis_guru = 'umum'; // umum, tahfidz, keduanya
    public string $pendidikan = '';
    public string $grade_guru = '';
    public string $status_pernikahan = 'belum_menikah'; // belum_menikah, menikah, cerai_hidup, cerai_mati
    public ?string $tanggal_masuk = null;
    public bool $status_aktif = true;
    public string $no_hp = '';
    public string $alamat = '';

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
        $guru = Guru::with('user')->findOrFail($id);
        $this->guruId = $guru->id;
        $this->nama = $guru->user->nama ?? '';
        $this->username = $guru->user->username ?? '';
        $this->email = $guru->user->email ?? '';
        $this->niy = $guru->niy ?? $guru->nip ?? '';
        $this->nik = $guru->nik ?? '';
        $this->tempat_lahir = $guru->tempat_lahir ?? '';
        $this->tanggal_lahir = $guru->tanggal_lahir ? $guru->tanggal_lahir->format('Y-m-d') : null;
        $this->status_kepegawaian = $guru->status_kepegawaian;
        $this->jenis_guru = $guru->jenis_guru ?? 'umum';
        $this->pendidikan = $guru->pendidikan ?? '';
        $this->grade_guru = $guru->grade_guru ?? '';
        $this->status_pernikahan = $guru->status_pernikahan ?? 'belum_menikah';
        $this->tanggal_masuk = $guru->tanggal_masuk ? $guru->tanggal_masuk->format('Y-m-d') : null;
        $this->status_aktif = (bool) $guru->status_aktif;
        $this->no_hp = $guru->user->no_hp ?? '';
        $this->alamat = $guru->user->alamat ?? '';

        $this->isFormOpen = true;
    }

    public function save()
    {
        $guruUserId = $this->guruId ? Guru::find($this->guruId)?->user_id : null;

        $rules = [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . ($guruUserId ?? 'NULL'),
            'nik' => 'nullable|string|max:20',
            'status_kepegawaian' => 'required|in:pns,gtt,honorer,tetap_yayasan,gty',
            'jenis_guru' => 'required|in:umum,tahfidz,keduanya',
            'pendidikan' => 'nullable|string|max:100',
            'grade_guru' => 'nullable|string|max:50',
            'status_pernikahan' => 'required|in:belum_menikah,menikah,cerai_hidup,cerai_mati',
            'tanggal_masuk' => 'required|date',
            'tanggal_lahir' => 'nullable|date',
            'status_aktif' => 'required|boolean',
        ];

        if ($this->niy) {
            $rules['niy'] = 'unique:guru,nip,' . ($this->guruId ?? 'NULL');
        }

        if (!$this->guruId) {
            $rules['password'] = 'required|string|min:6';
        }

        $this->validate($rules);

        try {
            $isUpdate = (bool) $this->guruId;
            $namaGuru = $this->nama;

            DB::transaction(function () use (&$isUpdate, &$namaGuru) {
                $roleGuru = Role::firstOrCreate(
                    ['nama' => 'guru'],
                    ['deskripsi' => 'Guru / Tenaga Pendidik']
                );

                if ($this->guruId) {
                    // Update
                    $guru = Guru::findOrFail($this->guruId);
                    
                    $guru->user->update([
                        'nama' => $this->nama,
                        'username' => $this->username,
                        'email' => $this->email ?: null,
                        'no_hp' => $this->no_hp,
                        'alamat' => $this->alamat,
                        'status' => $this->status_aktif ? 'aktif' : 'nonaktif',
                    ]);

                    if ($this->password) {
                        $guru->user->update(['password' => Hash::make($this->password)]);
                    }

                    $guru->update([
                        'nip' => $this->niy ?: null,
                        'nik' => $this->nik ?: null,
                        'tempat_lahir' => $this->tempat_lahir ?: null,
                        'tanggal_lahir' => $this->tanggal_lahir ?: null,
                        'status_kepegawaian' => $this->status_kepegawaian,
                        'jenis_guru' => $this->jenis_guru,
                        'pendidikan' => $this->pendidikan ?: null,
                        'grade_guru' => $this->grade_guru ?: null,
                        'status_pernikahan' => $this->status_pernikahan,
                        'tanggal_masuk' => $this->tanggal_masuk,
                        'status_aktif' => $this->status_aktif,
                    ]);

                    \App\Services\AuditLogger::log('updated', 'Mengubah profil data guru: ' . $namaGuru, $guru, [
                        'log_name' => 'manajemen_guru',
                    ]);
                } else {
                    // Create
                    $user = User::create([
                        'nama' => $this->nama,
                        'username' => $this->username,
                        'email' => $this->email ?: null,
                        'password' => Hash::make($this->password),
                        'role_id' => $roleGuru->id,
                        'no_hp' => $this->no_hp,
                        'alamat' => $this->alamat,
                        'status' => 'aktif',
                    ]);

                    $guru = Guru::create([
                        'user_id' => $user->id,
                        'nip' => $this->niy ?: ('GURU-' . str_pad($user->id, 5, '0', STR_PAD_LEFT)),
                        'nik' => $this->nik ?: null,
                        'tempat_lahir' => $this->tempat_lahir ?: null,
                        'tanggal_lahir' => $this->tanggal_lahir ?: null,
                        'status_kepegawaian' => $this->status_kepegawaian,
                        'jenis_guru' => $this->jenis_guru,
                        'pendidikan' => $this->pendidikan ?: null,
                        'grade_guru' => $this->grade_guru ?: null,
                        'status_pernikahan' => $this->status_pernikahan,
                        'tanggal_masuk' => $this->tanggal_masuk,
                        'status_aktif' => true,
                    ]);

                    \App\Services\AuditLogger::log('created', 'Menambahkan guru baru: ' . $namaGuru, $guru, [
                        'log_name' => 'manajemen_guru',
                    ]);
                }
            });

            $msg = 'Data guru ' . $namaGuru . ' berhasil ' . ($isUpdate ? 'perbarui.' : 'disimpan.');
            session()->flash('message', $msg);
            $this->dispatch('show-alert', [
                'title' => $isUpdate ? 'Data Guru Diperbarui' : 'Guru Baru Ditambahkan',
                'message' => $msg,
                'type' => $isUpdate ? 'edit' : 'create',
            ]);

            $this->isFormOpen = false;
            $this->resetForm();
        } catch (\Throwable $e) {
            \App\Services\AuditLogger::log('error', 'Gagal memproses data guru: ' . $e->getMessage(), null, [
                'log_name' => 'manajemen_guru',
            ]);

            session()->flash('error', 'Gagal memproses data: ' . $e->getMessage());
            $this->dispatch('show-alert', [
                'title' => 'Gagal Memproses Data Guru',
                'message' => $e->getMessage(),
                'type' => 'danger',
            ]);
        }
    }

    public function delete(int $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $guru = Guru::findOrFail($id);
                $namaGuru = $guru->user->nama ?? ('ID ' . $id);
                $user = $guru->user;
                
                \App\Services\AuditLogger::log('deleted', 'Menghapus data guru: ' . $namaGuru, $guru, [
                    'log_name' => 'manajemen_guru',
                ]);

                $guru->delete();
                if ($user) {
                    $user->delete();
                }
            });

            session()->flash('message', 'Data guru berhasil dihapus.');
            $this->dispatch('show-alert', [
                'title' => 'Hapus Data Berhasil',
                'message' => 'Data guru berhasil dihapus dari sistem.',
                'type' => 'delete',
            ]);
        } catch (\Throwable $e) {
            \App\Services\AuditLogger::log('error', 'Gagal menghapus data guru ID ' . $id . ': ' . $e->getMessage(), null, [
                'log_name' => 'manajemen_guru',
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
        $this->guruId = null;
        $this->nama = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->niy = '';
        $this->nik = '';
        $this->tempat_lahir = '';
        $this->tanggal_lahir = null;
        $this->status_kepegawaian = 'honorer';
        $this->jenis_guru = 'umum';
        $this->pendidikan = '';
        $this->grade_guru = '';
        $this->status_pernikahan = 'belum_menikah';
        $this->tanggal_masuk = date('Y-m-d');
        $this->status_aktif = true;
        $this->no_hp = '';
        $this->alamat = '';
    }

    public function render()
    {
        $gurus = Guru::with('user')
            ->where(function ($query) {
                $query->where('nip', 'like', '%' . $this->search . '%')
                    ->orWhere('nik', 'like', '%' . $this->search . '%')
                    ->orWhere('grade_guru', 'like', '%' . $this->search . '%')
                    ->orWhere('pendidikan', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('nama', 'like', '%' . $this->search . '%')
                          ->orWhere('username', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.super-admin.tata-kelola.manajemen-guru', [
            'gurus' => $gurus,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Guru']);
    }
}
