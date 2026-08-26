<?php

namespace App\Livewire\Finance\Laporan;

use Livewire\Component;
use App\Models\Pembayaran;
use App\Models\JenisTagihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\WithPagination;

class LaporanPemasukan extends Component
{
    use WithPagination;

    // Date & Period Filter State (Global Presets + Custom)
    public string $filterPeriode = 'semua'; // 'semua', 'hari_ini', 'kemarin', 'minggu_ini', 'bulan_ini', 'custom'
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $bulan = '';
    public string $metode_bayar = '';
    public ?int $jenis_tagihan_id = null;
    public string $search = '';

    // Interactive PDF Preview Modal State
    public bool $showPreviewModal = false;

    public array $listBulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

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

    public function updatingMetodeBayar()
    {
        $this->resetPage();
    }

    public function updatingJenisTagihanId()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    private function getFilteredQuery()
    {
        $query = Pembayaran::with(['tagihan.siswa.user', 'tagihan.siswa.kelas', 'tagihan.jenisTagihan', 'petugas']);

        if (!empty($this->bulan)) {
            $monthIndex = array_search($this->bulan, $this->listBulan);
            if ($monthIndex !== false) {
                $monthNum = $monthIndex + 1;
                $bulanName = $this->bulan;
                $query->where(function ($q) use ($monthNum, $bulanName) {
                    $q->whereMonth('tanggal_bayar', $monthNum)
                      ->orWhereHas('tagihan', fn($tq) => $tq->where('bulan', $bulanName));
                });
            }
        }

        if ($this->filterPeriode === 'hari_ini') {
            $query->whereDate('tanggal_bayar', date('Y-m-d'));
        } elseif ($this->filterPeriode === 'kemarin') {
            $query->whereDate('tanggal_bayar', date('Y-m-d', strtotime('-1 day')));
        } elseif ($this->filterPeriode === 'minggu_ini') {
            $query->whereBetween('tanggal_bayar', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]);
        } elseif ($this->filterPeriode === 'bulan_ini') {
            $query->whereBetween('tanggal_bayar', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')]);
        } elseif ($this->filterPeriode === 'custom' || ($this->startDate && $this->endDate)) {
            if ($this->startDate && $this->endDate) {
                $query->whereBetween('tanggal_bayar', [$this->startDate, $this->endDate]);
            } elseif ($this->startDate) {
                $query->whereDate('tanggal_bayar', '>=', $this->startDate);
            } elseif ($this->endDate) {
                $query->whereDate('tanggal_bayar', '<=', $this->endDate);
            }
        }

        if ($this->metode_bayar) {
            $query->where('metode_bayar', $this->metode_bayar);
        }

        if ($this->jenis_tagihan_id) {
            $query->whereHas('tagihan', function ($q) {
                $q->where('jenis_tagihan_id', $this->jenis_tagihan_id);
            });
        }

        if ($this->search) {
            $query->whereHas('tagihan.siswa.user', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            });
        }

        return $query;
    }

    public function openPreviewPdf()
    {
        $count = $this->getFilteredQuery()->count();
        if ($count === 0) {
            session()->flash('error', 'Tidak dapat membuka pratinjau karena tidak ada data pemasukan yang sesuai filter.');
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
        $data = $this->getFilteredQuery()->orderBy('tanggal_bayar', 'asc')->get();

        if ($data->isEmpty()) {
            session()->flash('error', 'Tidak dapat mengunduh CSV karena tidak ada data pemasukan yang sesuai filter.');
            return;
        }

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=laporan_pemasukan_" . date('Ymd_His') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Nama Siswa', 'Kelas', 'Jenis Tagihan', 'Tanggal Bayar', 'Metode Bayar', 'Nominal']);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->tagihan->siswa->user->nama ?? '-',
                    $row->tagihan->siswa->kelas->nama_kelas ?? '-',
                    $row->tagihan->jenisTagihan->nama ?? '-',
                    $row->tanggal_bayar ? $row->tanggal_bayar->format('d-m-Y') : '-',
                    $row->metode_bayar,
                    $row->nominal_dibayar
                ]);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, 'laporan_pemasukan_' . date('Ymd_His') . '.csv', $headers);
    }

    public function exportPdf()
    {
        $data = $this->getFilteredQuery()->orderBy('tanggal_bayar', 'asc')->get();

        if ($data->isEmpty()) {
            session()->flash('error', 'Tidak dapat mengunduh PDF karena tidak ada data pemasukan yang sesuai filter.');
            return;
        }

        $jt = JenisTagihan::find($this->jenis_tagihan_id);

        $periodeText = match ($this->filterPeriode) {
            'hari_ini' => 'Hari Ini (' . date('d/m/Y') . ')',
            'kemarin' => 'Kemarin (' . date('d/m/Y', strtotime('-1 day')) . ')',
            'minggu_ini' => 'Minggu Ini',
            'bulan_ini' => 'Bulan Ini',
            'custom' => ($this->startDate ? date('d/m/Y', strtotime($this->startDate)) : '') . ' s/d ' . ($this->endDate ? date('d/m/Y', strtotime($this->endDate)) : ''),
            default => 'Semua Periode',
        };

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-laporan-pemasukan', [
            'data' => $data,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'periodeText' => $periodeText,
            'bulan' => $this->bulan,
            'metodeBayar' => $this->metode_bayar ?: 'Semua',
            'jenisTagihan' => $jt?->nama ?? 'Semua',
            'totalPemasukan' => $data->sum('nominal_dibayar'),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan_pemasukan_' . date('Ymd_His') . '.pdf');
    }

    public function render()
    {
        $incomes = $this->getFilteredQuery()->orderBy('tanggal_bayar', 'desc')->paginate(15);
        $jenisTagihans = JenisTagihan::all();

        return view('livewire.finance.laporan.laporan-pemasukan', [
            'payments' => $incomes,
            'jenisTagihans' => $jenisTagihans,
            'listBulan' => $this->listBulan,
            'totalCount' => $incomes->total(),
        ])->layout('components.layouts.app', ['title' => 'Laporan Pemasukan & Pembayaran']);
    }
}
