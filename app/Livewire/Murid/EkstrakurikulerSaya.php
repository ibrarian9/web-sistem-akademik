<?php

namespace App\Livewire\Murid;

use Livewire\Component;
use App\Models\Ekstrakurikuler;
use App\Models\SiswaEkstrakurikuler;
use App\Models\Semester;

class EkstrakurikulerSaya extends Component
{
    public string $searchCatalog = '';

    public function render()
    {
        $siswa = auth()->user()->siswa;
        $activeSemester = Semester::where('status_aktif', true)->first();

        $enrolledEkskuls = [];
        if ($siswa) {
            $enrolledEkskuls = SiswaEkstrakurikuler::where('siswa_id', $siswa->id)
                ->with(['ekstrakurikuler.pembina.user', 'semester'])
                ->get();
        }

        $catalogQuery = Ekstrakurikuler::with(['pembina.user', 'siswaEkskul']);

        if (!empty($this->searchCatalog)) {
            $catalogQuery->where('nama', 'like', '%' . $this->searchCatalog . '%')
                ->orWhere('deskripsi', 'like', '%' . $this->searchCatalog . '%');
        }

        $catalogEkskuls = $catalogQuery->get();

        $enrolledIds = $enrolledEkskuls->pluck('ekstrakurikuler_id')->toArray();

        return view('livewire.murid.ekstrakurikuler-saya', [
            'enrolledEkskuls' => $enrolledEkskuls,
            'catalogEkskuls' => $catalogEkskuls,
            'enrolledIds' => $enrolledIds,
            'activeSemester' => $activeSemester,
        ])->layout('components.layouts.app', ['title' => 'Ekstrakurikuler Saya']);
    }
}
