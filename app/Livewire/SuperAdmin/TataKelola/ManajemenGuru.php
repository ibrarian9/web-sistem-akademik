<?php

namespace App\Livewire\SuperAdmin\TataKelola;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Guru;
use App\Models\User;
use App\Models\Role;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\JadwalPelajaran;
use App\Models\GuruMapelKelas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ManajemenGuru extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = '';
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

    protected $queryString = [
        'search' => ['except' => ''],
        'filterRole' => ['except' => ''],
    ];

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
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->resetForm();
        $this->isFormOpen = true;
    }

    public function openEdit(int $id)
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

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
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $form = new \App\Livewire\Forms\SuperAdmin\GuruForm($this, 'guruForm');
        $form->guruId = $this->guruId;
        $form->nama = $this->nama;
        $form->username = $this->username;
        $form->email = $this->email;
        $form->password = $this->password;
        $form->niy = $this->niy;
        $form->nik = $this->nik;
        $form->tempat_lahir = $this->tempat_lahir;
        $form->tanggal_lahir = $this->tanggal_lahir;
        $form->status_kepegawaian = $this->status_kepegawaian;
        $form->jenis_guru = $this->jenis_guru;
        $form->pendidikan = $this->pendidikan;
        $form->grade_guru = $this->grade_guru;
        $form->status_pernikahan = $this->status_pernikahan;
        $form->tanggal_masuk = $this->tanggal_masuk;
        $form->status_aktif = $this->status_aktif;
        $form->no_hp = $this->no_hp;
        $form->alamat = $this->alamat;

        $this->validate($form->rules());

        try {
            $isUpdate = (bool) $this->guruId;
            $namaGuru = $this->nama;

            DB::transaction(function () use ($form, &$isUpdate, &$namaGuru) {
                $guru = $form->store();

                \App\Services\AuditLogger::log(
                    $isUpdate ? 'updated' : 'created',
                    ($isUpdate ? 'Mengubah profil data guru: ' : 'Menambahkan guru baru: ') . $namaGuru,
                    $guru,
                    ['log_name' => 'manajemen_guru']
                );
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
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            $this->dispatch('show-alert', [
                'title' => 'Akses Ditolak',
                'message' => 'Super Admin 2 hanya memiliki hak akses Lihat Saja.',
                'type' => 'danger',
            ]);
            return;
        }

        try {
            DB::transaction(function () use ($id) {
                $guru = Guru::findOrFail($id);
                $namaGuru = $guru->user->nama ?? ('ID ' . $id);
                $user = $guru->user;

                if ($guru->user_id === auth()->id()) {
                    throw new \Exception('Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
                }

                if ($user && $user->isSuperAdmin()) {
                    throw new \Exception('Akses Ditolak: Data Super Admin / Founder tidak dapat dihapus.');
                }
                
                \App\Services\AuditLogger::log('deleted', 'Menghapus data guru: ' . $namaGuru, $guru, [
                    'log_name' => 'manajemen_guru',
                ]);

                // Detach foreign relations gracefully
                Kelas::where('guru_umum_id', $guru->id)->update(['guru_umum_id' => null]);
                Kelas::where('guru_tahfidz_id', $guru->id)->update(['guru_tahfidz_id' => null]);
                if (Schema::hasColumn('kelas', 'guru_id')) {
                    Kelas::where('guru_id', $guru->id)->update(['guru_id' => null]);
                }
                Siswa::where('shadow_teacher_id', $guru->id)->update(['shadow_teacher_id' => null]);
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
        $gurus = Guru::with(['user.role'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('nik', 'like', '%' . $this->search . '%')
                        ->orWhere('grade_guru', 'like', '%' . $this->search . '%')
                        ->orWhere('pendidikan', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($uq) {
                            $uq->where('nama', 'like', '%' . $this->search . '%')
                              ->orWhere('username', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->filterRole, function ($query) {
                $query->whereHas('user.role', function ($q) {
                    $q->where('nama', $this->filterRole);
                });
            })
            ->latest()
            ->paginate($this->perPage);

        $roles = Role::orderBy('nama')->get();

        return view('livewire.super-admin.tata-kelola.manajemen-guru', [
            'gurus' => $gurus,
            'roles' => $roles,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Guru']);
    }
}
