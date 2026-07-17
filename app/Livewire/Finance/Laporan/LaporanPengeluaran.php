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

    public string $startDate = '';
    public string $endDate = '';
    public ?int $kategori_pengeluaran_id = null;
    public string $search = '';

    public function mount()
    {
        $this->startDate = date('Y-m-01');
        $this->endDate = date('Y-m-d');
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
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

    public function exportCsv()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=laporan_pengeluaran_" . date('Ymd_His') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $query = Pengeluaran::with(['kategori', 'petugas']);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal', [$this->startDate, $this->endDate]);
        }
        if ($this->kategori_pengeluaran_id) {
            $query->where('kategori_pengeluaran_id', $this->kategori_pengeluaran_id);
        }
        if ($this->search) {
            $query->where('keterangan', 'like', '%' . $this->search . '%');
        }

        $data = $query->orderBy('tanggal', 'asc')->get();

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
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
        $query = Pengeluaran::with(['kategori', 'petugas']);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal', [$this->startDate, $this->endDate]);
        }
        if ($this->kategori_pengeluaran_id) {
            $query->where('kategori_pengeluaran_id', $this->kategori_pengeluaran_id);
        }
        if ($this->search) {
            $query->where('keterangan', 'like', '%' . $this->search . '%');
        }

        $data = $query->orderBy('tanggal', 'asc')->get();
        $cat = KategoriPengeluaran::find($this->kategori_pengeluaran_id);

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-laporan-pengeluaran', [
            'data' => $data,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'kategori' => $cat?->nama ?? 'Semua',
            'totalPengeluaran' => $data->sum('jumlah'),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'laporan_pengeluaran_' . date('Ymd_His') . '.pdf');
    }

    public function render()
    {
        $query = Pengeluaran::with(['kategori', 'petugas']);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal', [$this->startDate, $this->endDate]);
        }

        if ($this->kategori_pengeluaran_id) {
            $query->where('kategori_pengeluaran_id', $this->kategori_pengeluaran_id);
        }

        if ($this->search) {
            $query->where('keterangan', 'like', '%' . $this->search . '%');
        }

        $expenditures = $query->orderBy('tanggal', 'desc')->paginate(15);
        $categories = KategoriPengeluaran::all();

        return view('livewire.finance.laporan.laporan-pengeluaran', [
            'expenditures' => $expenditures,
            'categories' => $categories,
        ])->layout('components.layouts.app', ['title' => 'Laporan Pengeluaran Keuangan']);
    }
}
