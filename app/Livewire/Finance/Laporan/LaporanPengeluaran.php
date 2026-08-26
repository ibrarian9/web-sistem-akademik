<?php

namespace App\Livewire\Finance\Laporan;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\WithPagination;

class LaporanPengeluaran extends Component
{
    use WithPagination;

    // Date & Period Filter State (Global Presets + Custom)
    public string $filterPeriode = 'semua'; // 'semua', 'hari_ini', 'kemarin', 'minggu_ini', 'bulan_ini', 'custom'
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $bulan = '';
    public ?int $kategori_pengeluaran_id = null;
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

    public function updatingKategoriPengeluaranId()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    private function getFilteredQuery()
    {
        $query = Pengeluaran::with(['kategori', 'petugas']);

        if (!empty($this->bulan)) {
            $monthIndex = array_search($this->bulan, $this->listBulan);
            if ($monthIndex !== false) {
                $query->whereMonth('tanggal', $monthIndex + 1);
            }
        }

        if ($this->filterPeriode === 'hari_ini') {
            $query->whereDate('tanggal', date('Y-m-d'));
        } elseif ($this->filterPeriode === 'kemarin') {
            $query->whereDate('tanggal', date('Y-m-d', strtotime('-1 day')));
        } elseif ($this->filterPeriode === 'minggu_ini') {
            $query->whereBetween('tanggal', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]);
        } elseif ($this->filterPeriode === 'bulan_ini') {
            $query->whereBetween('tanggal', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')]);
        } elseif ($this->filterPeriode === 'custom' || ($this->startDate && $this->endDate)) {
            if ($this->startDate && $this->endDate) {
                $query->whereBetween('tanggal', [$this->startDate, $this->endDate]);
            } elseif ($this->startDate) {
                $query->whereDate('tanggal', '>=', $this->startDate);
            } elseif ($this->endDate) {
                $query->whereDate('tanggal', '<=', $this->endDate);
            }
        }

        if ($this->kategori_pengeluaran_id) {
            $query->where('kategori_pengeluaran_id', $this->kategori_pengeluaran_id);
        }

        if ($this->search) {
            $query->where('keterangan', 'like', '%' . $this->search . '%');
        }

        return $query;
    }

    public function openPreviewPdf()
    {
        $count = $this->getFilteredQuery()->count();
        if ($count === 0) {
            session()->flash('error', 'Tidak dapat membuka pratinjau karena tidak ada data pengeluaran yang sesuai filter.');
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
        $data = $this->getFilteredQuery()->orderBy('tanggal', 'asc')->get();

        if ($data->isEmpty()) {
            session()->flash('error', 'Tidak dapat mengunduh CSV karena tidak ada data pengeluaran yang sesuai filter.');
            return;
        }

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=laporan_pengeluaran_" . date('Ymd_His') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Tanggal', 'Kategori', 'Keterangan', 'Jumlah Pengeluaran', 'Petugas']);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->tanggal ? $row->tanggal->format('d-m-Y') : '-',
                    $row->kategori->nama ?? '-',
                    $row->keterangan ?? '-',
                    $row->jumlah,
                    $row->petugas->nama ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, 'laporan_pengeluaran_' . date('Ymd_His') . '.csv', $headers);
    }

    public function exportPdf()
    {
        $data = $this->getFilteredQuery()->orderBy('tanggal', 'asc')->get();

        if ($data->isEmpty()) {
            session()->flash('error', 'Tidak dapat mengunduh PDF karena tidak ada data pengeluaran yang sesuai filter.');
            return;
        }

        $cat = KategoriPengeluaran::find($this->kategori_pengeluaran_id);

        $periodeText = match ($this->filterPeriode) {
            'hari_ini' => 'Hari Ini (' . date('d/m/Y') . ')',
            'kemarin' => 'Kemarin (' . date('d/m/Y', strtotime('-1 day')) . ')',
            'minggu_ini' => 'Minggu Ini',
            'bulan_ini' => 'Bulan Ini',
            'custom' => ($this->startDate ? date('d/m/Y', strtotime($this->startDate)) : '') . ' s/d ' . ($this->endDate ? date('d/m/Y', strtotime($this->endDate)) : ''),
            default => 'Semua Periode',
        };

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-laporan-pengeluaran', [
            'data' => $data,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'periodeText' => $periodeText,
            'bulan' => $this->bulan,
            'kategori' => $cat?->nama ?? 'Semua',
            'totalPengeluaran' => $data->sum('jumlah'),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan_pengeluaran_' . date('Ymd_His') . '.pdf');
    }

    public function render()
    {
        $expenditures = $this->getFilteredQuery()->orderBy('tanggal', 'desc')->paginate(15);
        $categories = KategoriPengeluaran::all();

        return view('livewire.finance.laporan.laporan-pengeluaran', [
            'expenditures' => $expenditures,
            'categories' => $categories,
            'listBulan' => $this->listBulan,
            'totalCount' => $expenditures->total(),
        ])->layout('components.layouts.app', ['title' => 'Laporan Pengeluaran Keuangan']);
    }
}
