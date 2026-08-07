<?php

namespace App\Livewire\Murid;

use App\Models\Nilai;
use App\Models\NilaiP5;
use App\Models\NilaiTahfidz;
use App\Models\Rapor;
use App\Models\RaporDetail;
use App\Models\RaporTahfidzDetail;
use App\Models\Siswa;
use App\Models\SiswaEkstrakurikuler;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class RaporNilai extends Component
{
    public bool $hasOutstanding = false;
    public ?Rapor $rapor = null;
    public array $raporDetails = [];
    public array $raporTahfidz = [];
    public array $nilaiP5List = [];
    public array $liveGrades = [];
    public array $ekskulList = [];
    public string $activeTab = 'km'; // 'km', 'tahfidz', 'p5'

    public function mount()
    {
        $this->checkOutstandingAndLoad();
    }

    public function downloadPdf()
    {
        if ($this->hasOutstanding || !$this->rapor) {
            session()->flash('error', 'Tidak dapat mengunduh rapor.');
            return;
        }

        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            return;
        }

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-rapor-siswa', [
            'rapor' => $this->rapor,
            'raporDetails' => $this->raporDetails,
            'siswa' => $siswa,
        ]);

        return response()->streamDownload(function () use ($pdf, $siswa) {
            echo $pdf->stream();
        }, 'rapor_' . str_replace(' ', '_', strtolower($siswa->user->nama ?? 'siswa')) . '.pdf');
    }

    public function checkOutstandingAndLoad()
    {
        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            return;
        }

        // Check for unpaid/partially paid blocking bills past due date
        $this->hasOutstanding = Tagihan::where('siswa_id', $siswa->id)
            ->whereIn('status', ['belum_bayar', 'sebagian'])
            ->whereHas('jenisTagihan', function ($q) {
                $q->where('is_blocking', true);
            })
            ->whereDate('jatuh_tempo', '<=', \Carbon\Carbon::today())
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

        // Fetch extracurricular activities
        $this->ekskulList = SiswaEkstrakurikuler::where('siswa_id', $siswa->id)
            ->where('semester_id', $activeSemester->id)
            ->with('ekstrakurikuler.pembina.user')
            ->get()
            ->toArray();

        // Fetch official Rapor
        $this->rapor = Rapor::where('siswa_id', $siswa->id)
            ->where('semester_id', $activeSemester->id)
            ->first();

        if ($this->rapor) {
            // Ensure QR Code Hash exists
            if (empty($this->rapor->qr_code_hash)) {
                $this->rapor->qr_code_hash = 'RAP-' . $siswa->id . '-' . Str::random(12);
                $this->rapor->save();
            }

            $this->raporDetails = RaporDetail::where('rapor_id', $this->rapor->id)
                ->with('mapel')
                ->get()
                ->toArray();

            // Load Rapor Tahfidz Detail
            $tahfidzDetail = RaporTahfidzDetail::where('rapor_id', $this->rapor->id)->first();
            $tahfidzScores = NilaiTahfidz::where('siswa_id', $siswa->id)
                ->where('semester_id', $activeSemester->id)
                ->get();

            $this->raporTahfidz = [
                'summary' => $tahfidzDetail,
                'scores' => $tahfidzScores,
            ];

            // Load Nilai P5 List
            $this->nilaiP5List = NilaiP5::where('siswa_id', $siswa->id)
                ->where('semester_id', $activeSemester->id)
                ->with(['proyek', 'subdimensiP5.dimensi'])
                ->get()
                ->toArray();
        } else {
            // Fallback live grades
            $nilaiRecords = Nilai::where('siswa_id', $siswa->id)
                ->where('semester_id', $activeSemester->id)
                ->with(['mapel', 'komponenNilai'])
                ->get();

            $grouped = [];
            foreach ($nilaiRecords as $n) {
                $mapelId = $n->mapel_id;
                $mapelName = $n->mapel->nama_mapel ?? '-';
                $jenis = $n->mapel->jenis ?? 'umum';

                if (!isset($grouped[$mapelId])) {
                    $grouped[$mapelId] = [
                        'nama_mapel' => $mapelName,
                        'jenis' => $jenis,
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

            foreach ($grouped as $mid => $data) {
                $sum = array_sum(array_column($data['komponen'], 'nilai'));
                $count = count($data['komponen']);
                $grouped[$mid]['avg'] = $count > 0 ? round($sum / $count, 2) : 0.0;
            }

            $this->liveGrades = $grouped;
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.murid.rapor-nilai')
            ->layout('components.layouts.app', ['title' => 'Rapor & Nilai Murid']);
    }
}
