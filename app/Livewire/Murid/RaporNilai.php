<?php

namespace App\Livewire\Murid;

use App\Models\Nilai;
use App\Models\NilaiSumatifTp;
use App\Models\Rapor;
use App\Models\RaporDetail;
use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RaporNilai extends Component
{
    public bool $hasOutstanding = false;
    public array $nilaiHarianTp = [];
    public array $nilaiMidSts = [];
    public array $rekapMapelUmum = [];
    public string $activeTab = 'tp'; // 'tp' (Nilai Harian Per-TP), 'mid' (Mid Semester STS)

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

        // 1. Load Nilai Harian per-TP (Formatif / Sumatif TP Kurikulum Merdeka)
        $tpScores = NilaiSumatifTp::where('siswa_id', $siswa->id)
            ->where('semester_id', $activeSemester->id)
            ->with(['tujuanPembelajaran.lingkupMateri.mapel'])
            ->get();

        $groupedTp = [];
        foreach ($tpScores as $tp) {
            $mapel = $tp->tujuanPembelajaran->lingkupMateri->mapel ?? null;
            if ($mapel && ($mapel->jenis ?? 'umum') === 'umum') {
                $mapelId = $mapel->id;
                if (!isset($groupedTp[$mapelId])) {
                    $groupedTp[$mapelId] = [
                        'nama_mapel' => $mapel->nama_mapel,
                        'items' => [],
                    ];
                }
                $groupedTp[$mapelId]['items'][] = [
                    'kode_tp' => $tp->tujuanPembelajaran->kode_tp ?? 'TP',
                    'deskripsi' => $tp->tujuanPembelajaran->deskripsi_tp ?? '-',
                    'lingkup' => $tp->tujuanPembelajaran->lingkupMateri->judul_lingkup_materi ?? '-',
                    'nilai' => floatval($tp->nilai),
                ];
            }
        }
        $this->nilaiHarianTp = $groupedTp;

        // 2. Load Nilai Mid Semester (STS / PTS) & Nilai Harian Komponen
        $nilaiRecords = Nilai::where('siswa_id', $siswa->id)
            ->where('semester_id', $activeSemester->id)
            ->with(['mapel', 'komponenNilai'])
            ->get();

        $groupedMid = [];
        $groupedAll = [];
        foreach ($nilaiRecords as $n) {
            if (($n->mapel->jenis ?? 'umum') === 'umum') {
                $mapelId = $n->mapel_id;
                $mapelName = $n->mapel->nama_mapel ?? '-';
                $komponenName = $n->komponenNilai->nama ?? '-';

                if (!isset($groupedAll[$mapelId])) {
                    $groupedAll[$mapelId] = [
                        'nama_mapel' => $mapelName,
                        'harian' => [],
                        'mid' => [],
                        'avg' => 0.0,
                    ];
                }

                if (str_contains(strtoupper($komponenName), 'PTS') || str_contains(strtoupper($komponenName), 'STS') || str_contains(strtoupper($komponenName), 'MID')) {
                    $groupedAll[$mapelId]['mid'][] = [
                        'komponen' => $komponenName,
                        'nilai' => floatval($n->nilai),
                        'catatan' => $n->catatan,
                    ];
                    $groupedMid[$mapelId]['nama_mapel'] = $mapelName;
                    $groupedMid[$mapelId]['items'][] = [
                        'komponen' => $komponenName,
                        'nilai' => floatval($n->nilai),
                        'catatan' => $n->catatan,
                    ];
                } else {
                    $groupedAll[$mapelId]['harian'][] = [
                        'komponen' => $komponenName,
                        'nilai' => floatval($n->nilai),
                        'catatan' => $n->catatan,
                    ];
                }
            }
        }

        foreach ($groupedAll as $mid => $data) {
            $allScores = array_merge(
                array_column($data['harian'], 'nilai'),
                array_column($data['mid'], 'nilai')
            );
            $count = count($allScores);
            $groupedAll[$mid]['avg'] = $count > 0 ? round(array_sum($allScores) / $count, 1) : 0.0;
        }

        $this->nilaiMidSts = $groupedMid;
        $this->rekapMapelUmum = $groupedAll;
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.murid.rapor-nilai')
            ->layout('components.layouts.app', ['title' => 'Nilai Akademik Murid']);
    }
}
