<?php

namespace App\Livewire\TataUsaha;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Siswa;

class DataAlumni extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterTahun = 'semua';
    public ?int $editingSiswaId = null;
    public ?int $tahun_lulus = null;
    public ?string $catatan_alumni = null;

    public function editAlumni($id)
    {
        $siswa = Siswa::find($id);
        if ($siswa) {
            $this->editingSiswaId = $siswa->id;
            $this->tahun_lulus = $siswa->tahun_lulus;
            $this->catatan_alumni = $siswa->catatan_alumni;
        }
    }

    public function saveAlumni()
    {
        if (!$this->editingSiswaId) return;

        $siswa = Siswa::find($this->editingSiswaId);
        if ($siswa) {
            $siswa->update([
                'tahun_lulus' => $this->tahun_lulus,
                'catatan_alumni' => $this->catatan_alumni,
            ]);
            session()->flash('message', 'Data alumni berhasil diperbarui.');
            $this->editingSiswaId = null;
        }
    }

    public function cancelEdit()
    {
        $this->editingSiswaId = null;
    }

    public function render()
    {
        $query = Siswa::with(['user', 'kelas'])
            ->where('status', 'lulus')
            ->orderBy('tahun_lulus', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nis', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($qu) {
                      $qu->where('nama', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->filterTahun !== 'semua') {
            $query->where('tahun_lulus', $this->filterTahun);
        }

        $alumnis = $query->paginate(12);

        $availableYears = Siswa::where('status', 'lulus')
            ->whereNotNull('tahun_lulus')
            ->distinct()
            ->pluck('tahun_lulus')
            ->sortDesc();

        return view('livewire.tata-usaha.data-alumni', [
            'alumnis' => $alumnis,
            'availableYears' => $availableYears,
        ])->layout('components.layouts.app', ['title' => 'Data Alumni Lulusan']);
    }
}
