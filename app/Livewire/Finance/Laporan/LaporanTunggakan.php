<?php

namespace App\Livewire\Finance\Laporan;

use Livewire\Component;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\WithPagination;

class LaporanTunggakan extends Component
{
    use WithPagination;

    public ?int $kelas_id = null;
    public ?int $tahun_ajaran_id = null;
    public string $search = '';

    // Date & Period Filter State (Global Presets + Custom)
    public string $filterPeriode = 'semua'; // 'semua', 'hari_ini', 'kemarin', 'minggu_ini', 'bulan_ini', 'custom'
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $bulan = '';

    // Interactive PDF Preview Modal State
    public bool $showPreviewModal = false;

    public array $listBulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    public function mount()
    {
        $activeTA = TahunAjaran::where('status_aktif', true)->first();
        if ($activeTA) {
            $this->tahun_ajaran_id = $activeTA->id;
        } else {
            $this->tahun_ajaran_id = TahunAjaran::latest()->first()?->id;
        }
    }

    public function updatingKelasId()
    {
        $this->resetPage();
    }

    public function updatingTahunAjaranId()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterPeriode()
    {
        $this->resetPage();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function updatingBulan()
    {
        $this->resetPage();
    }

    private function applyFilters($query)
    {
        $query->where('status', '!=', 'lunas');

        if ($this->kelas_id) {
            $query->whereHas('siswa', function ($q) {
                $q->where('kelas_id', $this->kelas_id);
            });
        }

        if ($this->tahun_ajaran_id) {
            $query->where('tahun_ajaran_id', $this->tahun_ajaran_id);
        }

        if ($this->search) {
            $query->whereHas('siswa.user', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->bulan)) {
            $query->where('bulan', $this->bulan);
        }

        if ($this->filterPeriode === 'hari_ini') {
            $query->whereDate('jatuh_tempo', date('Y-m-d'));
        } elseif ($this->filterPeriode === 'kemarin') {
            $query->whereDate('jatuh_tempo', date('Y-m-d', strtotime('-1 day')));
        } elseif ($this->filterPeriode === 'minggu_ini') {
            $query->whereBetween('jatuh_tempo', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]);
        } elseif ($this->filterPeriode === 'bulan_ini') {
            $query->whereBetween('jatuh_tempo', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')]);
        } elseif ($this->filterPeriode === 'custom') {
            if ($this->startDate && $this->endDate) {
                $query->whereBetween('jatuh_tempo', [$this->startDate, $this->endDate]);
            } elseif ($this->startDate) {
                $query->whereDate('jatuh_tempo', '>=', $this->startDate);
            } elseif ($this->endDate) {
                $query->whereDate('jatuh_tempo', '<=', $this->endDate);
            }
        }

        return $query;
    }

    public function openPreviewPdf()
    {
        $query = Tagihan::query();
        $count = $this->applyFilters($query)->count();

        if ($count === 0) {
            session()->flash('error', 'Tidak dapat membuka pratinjau karena tidak ada data tunggakan yang sesuai filter.');
            return;
        }

        $this->showPreviewModal = true;
    }

    public function closePreviewPdf()
    {
        $this->showPreviewModal = false;
    }

    public function exportCsv()
    {
        $query = Tagihan::with(['siswa.user', 'siswa.kelas', 'jenisTagihan']);
        $this->applyFilters($query);

        $data = $query->get();

        if ($data->isEmpty()) {
            session()->flash('error', 'Tidak dapat mengunduh CSV karena tidak ada data tunggakan yang sesuai filter.');
            return;
        }

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=laporan_tunggakan_" . date('Ymd_His') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Nama Siswa', 'Kelas', 'Jenis Tagihan', 'Bulan', 'Nominal', 'Dibayar', 'Sisa Tunggakan', 'Jatuh Tempo']);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->siswa->user->nama ?? '-',
                    $row->siswa->kelas->nama_kelas ?? '-',
                    $row->jenisTagihan->nama ?? '-',
                    $row->bulan ?? '-',
                    $row->nominal,
                    $row->total_dibayar,
                    $row->nominal - $row->total_dibayar,
                    $row->jatuh_tempo ? $row->jatuh_tempo->format('d-m-Y') : '-'
                ]);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, 'laporan_tunggakan_' . date('Ymd_His') . '.csv', $headers);
    }

    public function exportPdf()
    {
        $query = Tagihan::with(['siswa.user', 'siswa.kelas', 'jenisTagihan']);
        $this->applyFilters($query);

        $data = $query->get();

        if ($data->isEmpty()) {
            session()->flash('error', 'Tidak dapat mengunduh PDF karena tidak ada data tunggakan yang sesuai filter.');
            return;
        }

        $kelas = Kelas::find($this->kelas_id);
        $ta = TahunAjaran::find($this->tahun_ajaran_id);

        $periodeText = match ($this->filterPeriode) {
            'hari_ini' => 'Hari Ini (' . date('d/m/Y') . ')',
            'kemarin' => 'Kemarin (' . date('d/m/Y', strtotime('-1 day')) . ')',
            'minggu_ini' => 'Minggu Ini',
            'bulan_ini' => 'Bulan Ini',
            'custom' => ($this->startDate ? date('d/m/Y', strtotime($this->startDate)) : '') . ' s/d ' . ($this->endDate ? date('d/m/Y', strtotime($this->endDate)) : ''),
            default => 'Semua Periode',
        };

        $namaSekolah = \App\Models\Pengaturan::getValue('nama_sekolah', 'PONDOK PESANTREN & SEKOLAH ISLAM TERPADU');
        $alamatSekolah = \App\Models\Pengaturan::getValue('alamat_sekolah', 'Jl. Pendidikan Karakter Islami No. 123');
        $noTelepon = \App\Models\Pengaturan::getValue('no_telepon', '(0274) 123456');

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-laporan-tunggakan', [
            'data' => $data,
            'kelas' => $kelas?->nama_kelas ?? 'Semua Kelas',
            'tahunAjaran' => $ta?->nama ?? 'Semua',
            'bulan' => $this->bulan ?: 'Semua Bulan',
            'periodeText' => $periodeText,
            'namaSekolah' => $namaSekolah,
            'alamatSekolah' => $alamatSekolah,
            'noTelepon' => $noTelepon,
            'totalTunggakan' => $data->sum(fn($t) => $t->nominal - $t->total_dibayar),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan_tunggakan_' . date('Ymd_His') . '.pdf');
    }

    public function render()
    {
        $query = Tagihan::with(['siswa.user', 'siswa.kelas', 'jenisTagihan']);
        $this->applyFilters($query);

        $tunggakans = $query->paginate(15);
        $kelases = Kelas::all();
        $tahunAjarans = TahunAjaran::all();

        return view('livewire.finance.laporan.laporan-tunggakan', [
            'tunggakans' => $tunggakans,
            'kelases' => $kelases,
            'tahunAjarans' => $tahunAjarans,
            'totalCount' => $tunggakans->total(),
        ])->layout('components.layouts.app', ['title' => 'Laporan Tunggakan Pembayaran']);
    }
}
