<?php

namespace App\Livewire\SuperAdmin\TataKelola;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MataPelajaran;

class ManajemenMapel extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    // Form fields
    public ?int $mapelId = null;
    public string $kode_mapel = '';
    public string $nama_mapel = '';
    public string $kelompok = 'umum';
    public $kkm = 70;

    public bool $isFormOpen = false;

    protected $queryString = ['search' => ['except' => '']];

    public function updatingSearch()
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
        $mapel = MataPelajaran::findOrFail($id);
        $this->mapelId = $mapel->id;
        $this->kode_mapel = $mapel->kode_mapel ?? '';
        $this->nama_mapel = $mapel->nama_mapel;
        $this->kelompok = $mapel->jenis ?? 'umum';
        $this->kkm = (int) ($mapel->kkm ?? 70);

        $this->isFormOpen = true;
    }

    public function save()
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->validate([
            'kode_mapel' => 'required|string|max:20|unique:mata_pelajaran,kode_mapel,' . ($this->mapelId ?? 'NULL'),
            'nama_mapel' => 'required|string|max:100',
            'kelompok' => 'required|in:umum,keagamaan,tahfidz,mulok',
            'kkm' => 'required|numeric|min:0|max:100',
        ]);

        $jenis = ($this->kelompok === 'tahfidz') ? 'tahfidz' : 'umum';

        MataPelajaran::updateOrCreate(
            ['id' => $this->mapelId],
            [
                'kode_mapel' => strtoupper(trim($this->kode_mapel)),
                'nama_mapel' => trim($this->nama_mapel),
                'jenis' => $jenis,
                'kkm' => $this->kkm ?: 70,
            ]
        );

        session()->flash('message', 'Mata pelajaran berhasil disimpan.');
        $this->isFormOpen = false;
        $this->resetForm();
    }

    public function delete(int $id)
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $mapel = MataPelajaran::findOrFail($id);
        
        // Safety check: has assignments?
        if ($mapel->guruMapelKelas()->count() > 0) {
            session()->flash('error', 'Mata pelajaran tidak bisa dihapus karena telah ditugaskan ke kelas.');
            return;
        }

        $mapel->delete();
        session()->flash('message', 'Mata pelajaran berhasil dihapus.');
    }

    private function resetForm()
    {
        $this->mapelId = null;
        $this->kode_mapel = '';
        $this->nama_mapel = '';
        $this->kelompok = 'umum';
        $this->kkm = 70;
    }

    public function render()
    {
        $mapels = MataPelajaran::where('nama_mapel', 'like', '%' . $this->search . '%')
            ->orWhere('kode_mapel', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.super-admin.tata-kelola.manajemen-mapel', [
            'mapels' => $mapels,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Mata Pelajaran']);
    }
}
