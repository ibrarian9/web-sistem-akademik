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

    // Form fields
    public ?int $kelasId = null;
    public string $nama_kelas = '';
    public string $tingkat = '7';
    public ?int $guru_umum_id = null;
    public ?int $guru_tahfidz_id = null;

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
        $kelas = Kelas::findOrFail($id);
        $this->kelasId = $kelas->id;
        $this->nama_kelas = $kelas->nama_kelas;
        $this->tingkat = $kelas->tingkat;
        $this->guru_umum_id = $kelas->guru_umum_id;
        $this->guru_tahfidz_id = $kelas->guru_tahfidz_id;

        $this->isFormOpen = true;
    }

    public function save()
    {
        $rules = [
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas,' . ($this->kelasId ?? 'NULL'),
            'tingkat' => 'required|in:7,8,9',
            'guru_umum_id' => 'nullable|exists:guru,id',
            'guru_tahfidz_id' => 'nullable|exists:guru,id',
        ];

        $this->validate($rules);

        $activeSemester = Semester::where('status_aktif', true)->first();
        if (!$activeSemester) {
            session()->flash('error', 'Tidak ada semester aktif untuk ditautkan pada kelas.');
            return;
        }

        Kelas::updateOrCreate(
            ['id' => $this->kelasId],
            [
                'nama_kelas' => $this->nama_kelas,
                'tingkat' => $this->tingkat,
                'semester_id' => $activeSemester->id,
                'guru_umum_id' => $this->guru_umum_id ?: null,
                'guru_tahfidz_id' => $this->guru_tahfidz_id ?: null,
            ]
        );

        session()->flash('message', 'Data kelas berhasil disimpan.');
        $this->isFormOpen = false;
        $this->resetForm();
    }

    public function delete(int $id)
    {
        $kelas = Kelas::findOrFail($id);
        
        // Safety check: check if class has students
        if ($kelas->siswas()->count() > 0) {
            session()->flash('error', 'Kelas tidak bisa dihapus karena masih memiliki siswa terdaftar.');
            return;
        }

        $kelas->delete();
        session()->flash('message', 'Data kelas berhasil dihapus.');
    }

    private function resetForm()
    {
        $this->kelasId = null;
        $this->nama_kelas = '';
        $this->tingkat = '7';
        $this->guru_umum_id = null;
        $this->guru_tahfidz_id = null;
    }

    public function render()
    {
        $kelases = Kelas::with(['guruUmum.user', 'guruTahfidz.user'])
            ->where('nama_kelas', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate($this->perPage);

        $gurusUmum = Guru::with('user')->where('jenis_guru', 'umum')->where('status_aktif', true)->get();
        $gurusTahfidz = Guru::with('user')->where('jenis_guru', 'tahfidz')->where('status_aktif', true)->get();

        return view('livewire.super-admin.tata-kelola.manajemen-kelas', [
            'kelases' => $kelases,
            'gurusUmum' => $gurusUmum,
            'gurusTahfidz' => $gurusTahfidz,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Kelas']);
    }
}
