<?php

namespace App\Livewire\SuperAdmin\TataKelola;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\User;
use App\Models\Role;
use App\Rules\EligibleShadowTeacher;
use App\Rules\MaxOneShadowTeacherPerClass;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ManajemenSiswa extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    // Filter properties
    public string $filterKelas = '';
    public string $filterTingkat = '';
    public string $filterKelasTahfidz = '';
    public string $filterStatus = '';
    public string $filterJenisKelamin = '';
    public string $filterShadowTeacher = '';

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
    public ?int $shadow_teacher_id = null; // Guru Pendamping Khusus (Shadow Teacher)
    public ?string $tanggal_masuk = null;
    public string $status = 'aktif';

    public bool $isFormOpen = false;

    // Student Detail Modal state
    public $selectedSiswaDetail = null;
    public bool $showDetailModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterKelas' => ['except' => ''],
        'filterTingkat' => ['except' => ''],
        'filterKelasTahfidz' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterJenisKelamin' => ['except' => ''],
        'filterShadowTeacher' => ['except' => ''],
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }
    public function updatingFilterKelas() { $this->resetPage(); }
    public function updatingFilterTingkat() { $this->resetPage(); }
    public function updatingFilterKelasTahfidz() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterJenisKelamin() { $this->resetPage(); }
    public function updatingFilterShadowTeacher() { $this->resetPage(); }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterKelas = '';
        $this->filterTingkat = '';
        $this->filterKelasTahfidz = '';
        $this->filterStatus = '';
        $this->filterJenisKelamin = '';
        $this->filterShadowTeacher = '';
        $this->resetPage();
    }

    public function resetFilter(string $filterKey): void
    {
        if (property_exists($this, $filterKey)) {
            $this->$filterKey = '';
            $this->resetPage();
        }
    }

    public function getActiveFilterCountProperty(): int
    {
        $count = 0;
        if (!empty($this->search)) $count++;
        if (!empty($this->filterKelas)) $count++;
        if (!empty($this->filterTingkat)) $count++;
        if (!empty($this->filterKelasTahfidz)) $count++;
        if (!empty($this->filterStatus)) $count++;
        if (!empty($this->filterJenisKelamin)) $count++;
        if (!empty($this->filterShadowTeacher)) $count++;
        return $count;
    }

    public function openDetail(int $id)
    {
        $siswa = Siswa::with(['user', 'kelas.guruUmum.user', 'kelasTahfidz.guruTahfidz.user', 'shadowTeacher.user'])->findOrFail($id);
        $this->selectedSiswaDetail = $siswa;
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->selectedSiswaDetail = null;
        $this->showDetailModal = false;
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
        $this->shadow_teacher_id = $siswa->shadow_teacher_id;
        $this->tanggal_masuk = $siswa->tanggal_masuk ? $siswa->tanggal_masuk->format('Y-m-d') : null;
        $this->status = $siswa->status;

        $this->isFormOpen = true;
    }

    public function save()
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $form = new \App\Livewire\Forms\SuperAdmin\SiswaForm($this, 'siswaForm');
        $form->siswaId = $this->siswaId;
        $form->nama = $this->nama;
        $form->username = $this->username;
        $form->email = $this->email;
        $form->password = $this->password;
        $form->nis = $this->nis;
        $form->nisn = $this->nisn;
        $form->jenis_kelamin = $this->jenis_kelamin;
        $form->tempat_lahir = $this->tempat_lahir;
        $form->tanggal_lahir = $this->tanggal_lahir;
        $form->alamat = $this->alamat;
        $form->nama_wali = $this->nama_wali;
        $form->no_hp_wali = $this->no_hp_wali;
        $form->kelas_id = $this->kelas_id;
        $form->kelas_tahfidz_id = $this->kelas_tahfidz_id;
        $form->shadow_teacher_id = $this->shadow_teacher_id;
        $form->tanggal_masuk = $this->tanggal_masuk;
        $form->status = $this->status;

        $this->validate($form->rules(), $form->messages());

        try {
            $isUpdate = (bool) $this->siswaId;
            $namaSiswa = $this->nama;

            DB::transaction(function () use ($form, &$isUpdate, &$namaSiswa) {
                $siswa = $form->store();

                \App\Services\AuditLogger::log(
                    $isUpdate ? 'updated' : 'created',
                    ($isUpdate ? 'Mengubah profil siswa: ' : 'Menambahkan siswa baru: ') . $namaSiswa,
                    $siswa,
                    [
                        'log_name' => 'manajemen_siswa',
                        'siswa_id' => $siswa->id,
                    ]
                );
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
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        try {
            DB::transaction(function () use ($id) {
                $siswa = Siswa::findOrFail($id);
                $namaSiswa = $siswa->user->nama ?? ('ID ' . $id);
                $user = $siswa->user;
                
                \App\Services\AuditLogger::log('deleted', 'Menghapus data siswa: ' . $namaSiswa, $siswa, [
                    'log_name' => 'manajemen_siswa',
                    'siswa_id' => $siswa->id,
                ]);

                // Soft-delete tagihan yang belum lunas agar tidak menjadi tunggakan berjalan/mendatang
                $siswa->tagihans()->where('status', '!=', 'lunas')->delete();

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
        $this->shadow_teacher_id = null;
        $this->tanggal_masuk = date('Y-m-d');
        $this->status = 'aktif';
    }

    public function render()
    {
        $query = Siswa::with(['user', 'kelas', 'kelasTahfidz', 'shadowTeacher.user']);

        // 1. Search Query
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nis', 'like', '%' . $this->search . '%')
                    ->orWhere('nisn', 'like', '%' . $this->search . '%')
                    ->orWhere('nama_wali', 'like', '%' . $this->search . '%')
                    ->orWhere('no_hp_wali', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($qu) {
                        $qu->where('nama', 'like', '%' . $this->search . '%')
                          ->orWhere('username', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('shadowTeacher.user', function ($qs) {
                        $qs->where('nama', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // 2. Filter Tingkat Kelas Umum (1-6)
        if (!empty($this->filterTingkat)) {
            $query->whereHas('kelas', function ($q) {
                $q->where('tingkat', $this->filterTingkat);
            });
        }

        // 3. Filter Kelas Umum
        if ($this->filterKelas === 'belum_set') {
            $query->whereNull('kelas_id');
        } elseif (!empty($this->filterKelas)) {
            $query->where('kelas_id', $this->filterKelas);
        }

        // 4. Filter Kelas Tahfizh
        if ($this->filterKelasTahfidz === 'belum_set') {
            $query->whereNull('kelas_tahfidz_id');
        } elseif (!empty($this->filterKelasTahfidz)) {
            $query->where('kelas_tahfidz_id', $this->filterKelasTahfidz);
        }

        // 5. Filter Status Siswa (aktif, lulus, pindah, keluar)
        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }

        // 6. Filter Jenis Kelamin (L, P)
        if (!empty($this->filterJenisKelamin)) {
            $query->where('jenis_kelamin', $this->filterJenisKelamin);
        }

        // 7. Filter Guru Pendamping (Shadow Teacher / Inklusi)
        if ($this->filterShadowTeacher === 'inklusi') {
            $query->whereNotNull('shadow_teacher_id');
        } elseif ($this->filterShadowTeacher === 'reguler') {
            $query->whereNull('shadow_teacher_id');
        } elseif (!empty($this->filterShadowTeacher)) {
            $query->where('shadow_teacher_id', $this->filterShadowTeacher);
        }

        $siswas = $query->latest()->paginate($this->perPage);

        // Data for Form & Filters
        $allKelasesUmum = Kelas::where(function($q) {
            $q->where('jenis_kelas', 'umum')->orWhereNull('jenis_kelas');
        })->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        $kelasesTahfidz = Kelas::where('jenis_kelas', 'tahfidz')->orderBy('nama_kelas', 'asc')->get();

        $tingkatOptions = Kelas::where(function($q) {
            $q->where('jenis_kelas', 'umum')->orWhereNull('jenis_kelas');
        })
        ->whereNotNull('tingkat')
        ->distinct()
        ->orderBy('tingkat', 'asc')
        ->pluck('tingkat')
        ->toArray();

        if (empty($tingkatOptions)) {
            $tingkatOptions = [1, 2, 3, 4, 5, 6];
        }

        $shadowTeachers = Guru::where(function ($q) {
                $q->where('status_aktif', true);
                if ($this->shadow_teacher_id) {
                    $q->orWhere('id', $this->shadow_teacher_id);
                }
            })
            ->shadowTeacher()
            ->with(['user.role'])
            ->get();

        $allShadowTeachers = Guru::where('status_aktif', true)
            ->shadowTeacher()
            ->with(['user'])
            ->get();

        return view('livewire.super-admin.tata-kelola.manajemen-siswa', [
            'siswas' => $siswas,
            'kelasesUmum' => $allKelasesUmum,
            'kelasesTahfidz' => $kelasesTahfidz,
            'tingkatOptions' => $tingkatOptions,
            'shadowTeachers' => $shadowTeachers,
            'allShadowTeachers' => $allShadowTeachers,
            'gurus' => $shadowTeachers,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Siswa']);
    }
}
