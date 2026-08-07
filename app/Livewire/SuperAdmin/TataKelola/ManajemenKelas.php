<?php

namespace App\Livewire\SuperAdmin\TataKelola;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Semester;

class ManajemenKelas extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;
    public string $filterJenis = 'semua';

    // Form fields
    public ?int $kelasId = null;
    public string $jenis_kelas = 'umum'; // 'umum' or 'tahfidz'
    public string $nama_kelas = '';
    public string $tingkat = '1';
    public ?int $guru_umum_id = null;
    public ?int $guru_tahfidz_id = null;

    public bool $isFormOpen = false;

    protected $queryString = ['search' => ['except' => ''], 'filterJenis' => ['except' => 'semua']];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterJenis()
    {
        $this->resetPage();
    }

    public function updatedGuruTahfidzId($value)
    {
        if ($this->jenis_kelas === 'tahfidz' && $value) {
            $guru = Guru::with('user')->find($value);
            if ($guru) {
                $nama = $guru->user->nama ?? 'Guru';
                $this->nama_kelas = 'Halaqah ' . $nama;
            }
        }
    }

    public function updatedJenisKelas($value)
    {
        if ($value === 'tahfidz') {
            if ($this->guru_tahfidz_id) {
                $guru = Guru::with('user')->find($this->guru_tahfidz_id);
                if ($guru) {
                    $this->nama_kelas = 'Halaqah ' . ($guru->user->nama ?? 'Guru');
                }
            } else {
                $this->nama_kelas = 'Halaqah ';
            }
        } elseif ($value === 'umum') {
            $this->nama_kelas = '1A';
        }
    }

    public function openCreate()
    {
        $this->resetForm();
        $this->isFormOpen = true;
    }

    public function openEdit(int $id)
    {
        $this->resetForm();
        $kelas = Kelas::findOrFail($id);
        $this->kelasId = $kelas->id;
        $this->jenis_kelas = $kelas->jenis_kelas ?: 'umum';
        $this->nama_kelas = $kelas->nama_kelas;
        $this->tingkat = $kelas->tingkat ?: '1';
        $this->guru_umum_id = $kelas->guru_umum_id;
        $this->guru_tahfidz_id = $kelas->guru_tahfidz_id;

        $this->isFormOpen = true;
    }

    public function save()
    {
        if ($this->jenis_kelas === 'tahfidz') {
            $rules = [
                'jenis_kelas' => 'required|in:umum,tahfidz',
                'guru_tahfidz_id' => 'required|exists:guru,id',
                'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas,' . ($this->kelasId ?? 'NULL'),
            ];
        } else {
            $rules = [
                'jenis_kelas' => 'required|in:umum,tahfidz',
                'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas,' . ($this->kelasId ?? 'NULL'),
                'tingkat' => 'required|in:1,2,3,4,5,6,7,8,9',
                'guru_umum_id' => 'nullable|exists:guru,id',
            ];
        }

        $this->validate($rules);

        $activeSemester = Semester::where('status_aktif', true)->first() ?? Semester::first();
        if (!$activeSemester) {
            session()->flash('error', 'Tidak ada semester aktif untuk ditautkan pada kelas.');
            return;
        }

        // Auto format nama_kelas for Tahfizh if empty
        if ($this->jenis_kelas === 'tahfidz' && empty(trim($this->nama_kelas))) {
            $guru = Guru::with('user')->find($this->guru_tahfidz_id);
            $this->nama_kelas = 'Halaqah ' . ($guru->user->nama ?? 'Guru');
        }

        Kelas::updateOrCreate(
            ['id' => $this->kelasId],
            [
                'nama_kelas' => $this->nama_kelas,
                'jenis_kelas' => $this->jenis_kelas,
                'tingkat' => $this->jenis_kelas === 'umum' ? $this->tingkat : '1',
                'semester_id' => $activeSemester->id,
                'guru_umum_id' => $this->jenis_kelas === 'umum' ? ($this->guru_umum_id ?: null) : null,
                'guru_tahfidz_id' => $this->jenis_kelas === 'tahfidz' ? ($this->guru_tahfidz_id ?: null) : null,
            ]
        );

        session()->flash('message', 'Data kelas (' . ucfirst($this->jenis_kelas) . ') berhasil disimpan.');
        $this->isFormOpen = false;
        $this->resetForm();
    }

    public function delete(int $id)
    {
        $kelas = Kelas::findOrFail($id);
        
        // Safety check: check if class has students
        $totalSiswa = $kelas->jenis_kelas === 'tahfidz' 
            ? $kelas->siswasTahfidz()->count() 
            : $kelas->siswas()->count();

        if ($totalSiswa > 0) {
            session()->flash('error', 'Kelas tidak bisa dihapus karena masih memiliki ' . $totalSiswa . ' siswa terdaftar.');
            return;
        }

        $kelas->delete();
        session()->flash('message', 'Data kelas berhasil dihapus.');
    }

    private function resetForm()
    {
        $this->kelasId = null;
        $this->jenis_kelas = 'umum';
        $this->nama_kelas = '';
        $this->tingkat = '1';
        $this->guru_umum_id = null;
        $this->guru_tahfidz_id = null;
    }

    public function render()
    {
        $query = Kelas::with(['guruUmum.user', 'guruTahfidz.user']);

        if (!empty($this->search)) {
            $query->where('nama_kelas', 'like', '%' . $this->search . '%');
        }

        if ($this->filterJenis !== 'semua') {
            $query->where('jenis_kelas', $this->filterJenis);
        }

        $kelases = $query->latest()->paginate($this->perPage);

        $gurusUmum = Guru::with('user')
            ->where(function($q) {
                $q->where('jenis_guru', 'umum')->orWhere('jenis_guru', 'keduanya');
            })
            ->where('status_aktif', true)->get();

        $gurusTahfidz = Guru::with('user')
            ->where(function($q) {
                $q->where('jenis_guru', 'tahfidz')->orWhere('jenis_guru', 'tahfizh')->orWhere('jenis_guru', 'keduanya');
            })
            ->where('status_aktif', true)->get();

        return view('livewire.super-admin.tata-kelola.manajemen-kelas', [
            'kelases' => $kelases,
            'gurusUmum' => $gurusUmum,
            'gurusTahfidz' => $gurusTahfidz,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Kelas']);
    }
}
