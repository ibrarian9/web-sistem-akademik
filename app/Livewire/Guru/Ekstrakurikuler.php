<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\Ekstrakurikuler as EkskulModel;
use App\Models\SiswaEkstrakurikuler;
use App\Models\KegiatanEkstrakurikuler;
use App\Models\PresensiEkstrakurikuler;
use App\Models\Semester;
use Carbon\Carbon;

class Ekstrakurikuler extends Component
{
    public string $searchCatalog = '';
    public ?int $selectedEkskulId = null;
    public string $viewTab = 'sesi'; // 'sesi' (presensi & nilai berkala) or 'roster' (rekap rapor)

    // Form Sesi / Pertemuan Baru
    public bool $isKegiatanFormOpen = false;
    public string $kegiatanTanggal = '';
    public string $kegiatanNama = '';
    public string $kegiatanKeterangan = '';

    // Selected Sesi Kegiatan
    public ?int $selectedKegiatanId = null;

    // Presensi & Nilai Berkala Inputs per Sesi
    public array $presensiStatus = []; // [siswa_id => 'Hadir'|'Sakit'|'Izin'|'Alpa']
    public array $nilaiBerkala = []; // [siswa_id => 85]
    public array $catatanBerkala = []; // [siswa_id => 'Catatan']

    // Predikat & Catatan Rapor Akhir Inputs
    public array $predikatInputs = []; // [siswa_ekskul_id => 'A']
    public array $catatanInputs = []; // [siswa_ekskul_id => 'Catatan']

    public function mount()
    {
        $this->kegiatanTanggal = Carbon::today()->toDateString();

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
        $this->initLatestKegiatan();
    }

    public function selectEkskul(int $id)
    {
        $this->selectedEkskulId = $id;
        $this->loadRoster();
        $this->initLatestKegiatan();
    }

    public function initLatestKegiatan()
    {
        if (!$this->selectedEkskulId) return;

        $latest = KegiatanEkstrakurikuler::where('ekstrakurikuler_id', $this->selectedEkskulId)
            ->latest('tanggal')
            ->first();

        if ($latest) {
            $this->selectedKegiatanId = $latest->id;
            $this->loadPresensiDanNilaiSesi();
        } else {
            $this->selectedKegiatanId = null;
            $this->presensiStatus = [];
            $this->nilaiBerkala = [];
            $this->catatanBerkala = [];
        }
    }

    public function selectKegiatan(int $kegiatanId)
    {
        $this->selectedKegiatanId = $kegiatanId;
        $this->loadPresensiDanNilaiSesi();
    }

    public function openCreateKegiatan()
    {
        $this->kegiatanTanggal = Carbon::today()->toDateString();
        $this->kegiatanNama = 'Pertemuan ' . (KegiatanEkstrakurikuler::where('ekstrakurikuler_id', $this->selectedEkskulId)->count() + 1);
        $this->kegiatanKeterangan = '';
        $this->isKegiatanFormOpen = true;
    }

    public function saveKegiatan()
    {
        $this->validate([
            'kegiatanTanggal' => 'required|date',
            'kegiatanNama' => 'required|string|max:255',
            'kegiatanKeterangan' => 'nullable|string',
        ]);

        $activeSemester = Semester::where('status_aktif', true)->first() ?? Semester::latest()->first();

        $kegiatan = KegiatanEkstrakurikuler::create([
            'ekstrakurikuler_id' => $this->selectedEkskulId,
            'semester_id' => $activeSemester?->id,
            'tanggal' => $this->kegiatanTanggal,
            'nama_kegiatan' => $this->kegiatanNama,
            'keterangan' => $this->kegiatanKeterangan,
        ]);

        $this->isKegiatanFormOpen = false;
        $this->selectedKegiatanId = $kegiatan->id;
        $this->loadPresensiDanNilaiSesi();

        session()->flash('message', "Sesi kegiatan '{$kegiatan->nama_kegiatan}' berhasil dibuat.");
    }

    public function deleteKegiatan(int $id)
    {
        $kegiatan = KegiatanEkstrakurikuler::find($id);
        if ($kegiatan && $kegiatan->ekstrakurikuler_id === $this->selectedEkskulId) {
            $kegiatan->delete();
            session()->flash('message', 'Sesi kegiatan berhasil dihapus.');
            $this->initLatestKegiatan();
        }
    }

    public function loadPresensiDanNilaiSesi()
    {
        if (!$this->selectedKegiatanId || !$this->selectedEkskulId) return;

        $members = SiswaEkstrakurikuler::where('ekstrakurikuler_id', $this->selectedEkskulId)->get();
        $presensis = PresensiEkstrakurikuler::where('kegiatan_ekstrakurikuler_id', $this->selectedKegiatanId)->get()->keyBy('siswa_id');

        $this->presensiStatus = [];
        $this->nilaiBerkala = [];
        $this->catatanBerkala = [];

        foreach ($members as $m) {
            $record = $presensis[$m->siswa_id] ?? null;
            $this->presensiStatus[$m->siswa_id] = $record ? $record->status_kehadiran : 'Hadir';
            $this->nilaiBerkala[$m->siswa_id] = $record && $record->nilai !== null ? (float) $record->nilai : 85;
            $this->catatanBerkala[$m->siswa_id] = $record ? ($record->catatan ?? '') : '';
        }
    }

    public function setSemuaHadir()
    {
        foreach ($this->presensiStatus as $siswaId => $status) {
            $this->presensiStatus[$siswaId] = 'Hadir';
        }
        session()->flash('message', 'Seluruh santri disetel Hadir untuk sesi ini.');
    }

    public function savePresensiDanNilaiSesi()
    {
        if (!$this->selectedKegiatanId || !$this->selectedEkskulId) return;

        $members = SiswaEkstrakurikuler::where('ekstrakurikuler_id', $this->selectedEkskulId)->get();

        foreach ($members as $m) {
            $status = $this->presensiStatus[$m->siswa_id] ?? 'Hadir';
            $nilai = isset($this->nilaiBerkala[$m->siswa_id]) && $this->nilaiBerkala[$m->siswa_id] !== '' 
                ? (float) $this->nilaiBerkala[$m->siswa_id] 
                : null;
            $catatan = $this->catatanBerkala[$m->siswa_id] ?? null;

            PresensiEkstrakurikuler::updateOrCreate(
                [
                    'kegiatan_ekstrakurikuler_id' => $this->selectedKegiatanId,
                    'siswa_id' => $m->siswa_id,
                ],
                [
                    'status_kehadiran' => $status,
                    'nilai' => $nilai,
                    'catatan' => $catatan,
                ]
            );
        }

        session()->flash('message', 'Presensi kehadiran dan penilaian berkala sesi berhasil disimpan.');
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

        $selectedEkskul = null;
        $roster = collect();
        $kegiatans = collect();
        $currentKegiatan = null;

        if ($this->selectedEkskulId) {
            $selectedEkskul = EkskulModel::with(['pembina.user'])->find($this->selectedEkskulId);
            $roster = SiswaEkstrakurikuler::with(['siswa.user', 'siswa.kelas'])
                ->where('ekstrakurikuler_id', $this->selectedEkskulId)
                ->get();

            $kegiatans = KegiatanEkstrakurikuler::where('ekstrakurikuler_id', $this->selectedEkskulId)
                ->orderBy('tanggal', 'desc')
                ->get();

            if ($this->selectedKegiatanId) {
                $currentKegiatan = $kegiatans->firstWhere('id', $this->selectedKegiatanId);
            }
        }

        // Calculate Average Score and Attendance count for Roster summary
        $rosterSummary = [];
        if ($this->selectedEkskulId) {
            $allKegiatanIds = $kegiatans->pluck('id')->toArray();
            $allPresensis = PresensiEkstrakurikuler::whereIn('kegiatan_ekstrakurikuler_id', $allKegiatanIds)->get();

            foreach ($roster as $m) {
                $studentPresensis = $allPresensis->where('siswa_id', $m->siswa_id);
                $hadirCount = $studentPresensis->where('status_kehadiran', 'Hadir')->count();
                $avgScore = $studentPresensis->whereNotNull('nilai')->avg('nilai');

                $rosterSummary[$m->id] = [
                    'hadir_count' => $hadirCount,
                    'total_sesi' => count($allKegiatanIds),
                    'avg_score' => $avgScore ? round($avgScore, 1) : null,
                ];
            }
        }

        $myEkskuls = collect();
        if ($guru) {
            $myEkskuls = EkskulModel::where('pembina_guru_id', $guru->id)->with('siswaEkskul')->get();
        }

        return view('livewire.guru.ekstrakurikuler', [
            'selectedEkskul' => $selectedEkskul,
            'roster' => $roster,
            'kegiatans' => $kegiatans,
            'currentKegiatan' => $currentKegiatan,
            'rosterSummary' => $rosterSummary,
            'myEkskuls' => $myEkskuls,
            'activeSemester' => $activeSemester,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Ekstrakurikuler Guru']);
    }
}
