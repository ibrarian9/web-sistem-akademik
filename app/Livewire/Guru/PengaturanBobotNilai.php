<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\GuruMapelKelas;
use App\Models\KomponenNilai;
use App\Models\BobotNilaiGuru;

class PengaturanBobotNilai extends Component
{
    public $selectedGmkId = null;
    public array $bobotInputs = []; // [komponen_id => bobot_persen]

    public array $assignments = [];
    public array $komponens = [];
    public ?array $selectedAssignment = null;

    public function mount()
    {
        $this->loadAssignments();
    }

    public function loadAssignments()
    {
        $guru = auth()->user()?->guru;
        if (!$guru) return;

        $this->assignments = GuruMapelKelas::with(['kelas', 'mapel', 'semester'])
            ->where('guru_id', $guru->id)
            ->whereHas('semester.tahunAjaran', function ($q) {
                $q->where('status_aktif', true);
            })
            ->get()
            ->toArray();

        if (count($this->assignments) > 0 && !$this->selectedGmkId) {
            $this->selectedGmkId = (string) $this->assignments[0]['id'];
            $this->loadBobot();
        }
    }

    public function updatedSelectedGmkId()
    {
        $this->loadBobot();
    }

    public function loadBobot()
    {
        if (!$this->selectedGmkId) return;

        $gmk = GuruMapelKelas::with('mapel')->find($this->selectedGmkId);
        if (!$gmk) return;

        $this->selectedAssignment = $gmk->toArray();

        $isTahfidz = $gmk->mapel ? ($gmk->mapel->is_tahfidz || strtolower($gmk->mapel->kategori ?? '') === 'tahfidz') : false;
        $berlakuFilter = $isTahfidz ? ['tahfidz', 'semua'] : ['umum', 'semua'];

        $this->komponens = KomponenNilai::whereIn('berlaku_untuk', $berlakuFilter)
            ->orderBy('urutan')
            ->get()
            ->toArray();

        $existingBobot = BobotNilaiGuru::where('guru_mapel_kelas_id', $this->selectedGmkId)
            ->get()
            ->pluck('bobot', 'komponen_nilai_id')
            ->toArray();

        $this->bobotInputs = [];
        foreach ($this->komponens as $k) {
            $kid = $k['id'];
            // If teacher hasn't customized weight yet, use TU's default recommendation weight
            $this->bobotInputs[$kid] = isset($existingBobot[$kid]) ? floatval($existingBobot[$kid]) : floatval($k['bobot']);
        }
    }

    public function saveBobot()
    {
        if (!$this->selectedGmkId) return;

        $total = array_sum(array_map('floatval', $this->bobotInputs));
        if (abs($total - 100) > 0.01 && $total > 0) {
            session()->flash('warning', 'Total bobot persen saat ini: ' . $total . '%. Disarankan total akumulasi bobot adalah 100%.');
        }

        foreach ($this->bobotInputs as $komponenId => $bobotVal) {
            BobotNilaiGuru::updateOrCreate([
                'guru_mapel_kelas_id' => $this->selectedGmkId,
                'komponen_nilai_id' => $komponenId,
            ], [
                'bobot' => max(0, floatval($bobotVal)),
            ]);
        }

        session()->flash('message', 'Pengaturan persentase bobot penilaian mata pelajaran berhasil disimpan.');
        $this->loadBobot();
    }

    public function render()
    {
        return view('livewire.guru.pengaturan-bobot-nilai')
            ->layout('components.layouts.app', ['title' => 'Pengaturan Bobot Penilaian']);
    }
}
