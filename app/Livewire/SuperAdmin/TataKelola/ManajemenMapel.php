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
        $mapel = MataPelajaran::findOrFail($id);
        $this->mapelId = $mapel->id;
        $this->kode_mapel = $mapel->kode_mapel;
        $this->nama_mapel = $mapel->nama_mapel;
        $this->kelompok = $mapel->kelompok;

        $this->isFormOpen = true;
    }

    public function save()
    {
        $this->validate([
            'kode_mapel' => 'required|string|max:20|unique:mata_pelajaran,kode_mapel,' . ($this->mapelId ?? 'NULL'),
            'nama_mapel' => 'required|string|max:100',
            'kelompok' => 'required|in:umum,keagamaan,tahfidz,mulok',
        ]);

        MataPelajaran::updateOrCreate(
            ['id' => $this->mapelId],
            [
                'kode_mapel' => $this->kode_mapel,
                'nama_mapel' => $this->nama_mapel,
                'kelompok' => $this->kelompok,
            ]
        );

        session()->flash('message', 'Mata pelajaran berhasil disimpan.');
        $this->isFormOpen = false;
        $this->resetForm();
    }

    public function delete(int $id)
    {
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
