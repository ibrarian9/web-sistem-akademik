<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\Ekstrakurikuler as EkskulModel;
use App\Models\SiswaEkstrakurikuler;
use App\Models\Semester;

class Ekstrakurikuler extends Component
{
    public string $searchCatalog = '';
    public ?int $selectedEkskulId = null;

    // Predikat editing state
    public array $predikatInputs = []; // [siswa_ekskul_id => 'A']
    public array $catatanInputs = []; // [siswa_ekskul_id => 'Catatan']

    public function mount()
    {
        $guru = auth()->user()?->guru;
        if ($guru) {
            $myEkskul = EkskulModel::where('pembina_guru_id', $guru->id)->first();
            if ($myEkskul) {
                $this->selectedEkskulId = $myEkskul->id;
            }
        }

        if (!$this->selectedEkskulId) {
            $first = EkskulModel::first();
            if ($first) {
                $this->selectedEkskulId = $first->id;
            }
        }

        $this->loadRoster();
    }

    public function selectEkskul(int $id)
    {
        $this->selectedEkskulId = $id;
        $this->loadRoster();
    }

    public function loadRoster()
    {
        if (!$this->selectedEkskulId) return;

        $members = SiswaEkstrakurikuler::where('ekstrakurikuler_id', $this->selectedEkskulId)->get();
        $this->predikatInputs = [];
        $this->catatanInputs = [];

        foreach ($members as $m) {
            $this->predikatInputs[$m->id] = $m->predikat ?? 'B';
            $this->catatanInputs[$m->id] = $m->catatan ?? '';
        }
    }

    public function saveScore(int $memberId)
    {
        $member = SiswaEkstrakurikuler::find($memberId);
        if (!$member) return;

        $member->update([
            'predikat' => $this->predikatInputs[$memberId] ?? 'B',
            'catatan' => $this->catatanInputs[$memberId] ?? null,
        ]);

        session()->flash('message', 'Penilaian santri ekstrakurikuler berhasil diperbarui.');
    }

    public function render()
    {
        $guru = auth()->user()?->guru;
        $activeSemester = Semester::where('status_aktif', true)->first() ?? Semester::first();

        $catalogQuery = EkskulModel::with(['pembina.user', 'siswaEkskul.siswa.user']);

        if (!empty($this->searchCatalog)) {
            $catalogQuery->where('nama', 'like', '%' . $this->searchCatalog . '%')
                ->orWhere('deskripsi', 'like', '%' . $this->searchCatalog . '%');
        }

        $catalogEkskuls = $catalogQuery->get();

        $selectedEkskul = null;
        $roster = collect();

        if ($this->selectedEkskulId) {
            $selectedEkskul = EkskulModel::with(['pembina.user'])->find($this->selectedEkskulId);
            $roster = SiswaEkstrakurikuler::with(['siswa.user', 'siswa.kelas'])
                ->where('ekstrakurikuler_id', $this->selectedEkskulId)
                ->get();
        }

        $myEkskuls = collect();
        if ($guru) {
            $myEkskuls = EkskulModel::where('pembina_guru_id', $guru->id)->with('siswaEkskul')->get();
        }

        return view('livewire.guru.ekstrakurikuler', [
            'catalogEkskuls' => $catalogEkskuls,
            'selectedEkskul' => $selectedEkskul,
            'roster' => $roster,
            'myEkskuls' => $myEkskuls,
            'activeSemester' => $activeSemester,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Ekstrakurikuler Guru']);
    }
}
