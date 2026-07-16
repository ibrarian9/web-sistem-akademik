<?php

namespace App\Livewire\Murid;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\Rapor;
use App\Models\RaporDetail;
use App\Models\Nilai;
use App\Models\Tagihan;
use Illuminate\Support\Facades\DB;

class RaporNilai extends Component
{
    public bool $hasOutstanding = false;
    public ?Rapor $rapor = null;
    public array $raporDetails = [];
    public array $liveGrades = []; // Fallback live grades grouped by subject

    public function mount()
    {
        $this->checkOutstandingAndLoad();
    }

    public function checkOutstandingAndLoad()
    {
        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            return;
        }

        // Check for unpaid/partially paid billing
        $this->hasOutstanding = Tagihan::where('siswa_id', $siswa->id)
            ->whereIn('status', ['belum_bayar', 'sebagian'])
            ->exists();

        if ($this->hasOutstanding) {
            return;
        }

        // Get active semester
        $activeSemester = DB::table('semester')
            ->join('tahun_ajaran', 'semester.tahun_ajaran_id', '=', 'tahun_ajaran.id')
            ->where('tahun_ajaran.status_aktif', true)
            ->where('semester.status_aktif', true)
            ->select('semester.id')
            ->first();

        if (!$activeSemester) {
            return;
        }

        // Fetch official Rapor
        $this->rapor = Rapor::where('siswa_id', $siswa->id)
            ->where('semester_id', $activeSemester->id)
            ->first();

        if ($this->rapor) {
            $this->raporDetails = RaporDetail::where('rapor_id', $this->rapor->id)
                ->with('mapel')
                ->get()
                ->toArray();
        } else {
            // Fallback: Compile dynamic current grades from 'nilai' table
            $nilaiRecords = Nilai::where('siswa_id', $siswa->id)
                ->where('semester_id', $activeSemester->id)
                ->with(['mapel', 'komponenNilai'])
                ->get();

            $grouped = [];
            foreach ($nilaiRecords as $n) {
                $mapelId = $n->mapel_id;
                $mapelName = $n->mapel->nama_mapel ?? '-';
                
                if (!isset($grouped[$mapelId])) {
                    $grouped[$mapelId] = [
                        'nama_mapel' => $mapelName,
                        'komponen' => [],
                        'avg' => 0.0,
                    ];
                }

                $grouped[$mapelId]['komponen'][] = [
                    'nama' => $n->komponenNilai->nama ?? '-',
                    'nilai' => floatval($n->nilai),
                    'catatan' => $n->catatan,
                ];
            }

            // Calculate averages
            foreach ($grouped as $mid => $data) {
                $sum = array_sum(array_column($data['komponen'], 'nilai'));
                $count = count($data['komponen']);
                $grouped[$mid]['avg'] = $count > 0 ? round($sum / $count, 2) : 0.0;
            }

            $this->liveGrades = $grouped;
        }
    }

    public function render()
    {
        return view('livewire.murid.rapor-nilai')
            ->layout('components.layouts.app', ['title' => 'Rapor & Nilai Murid']);
    }
}
