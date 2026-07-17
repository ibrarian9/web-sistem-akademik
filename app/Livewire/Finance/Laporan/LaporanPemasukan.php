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

    public string $startDate = '';
    public string $endDate = '';
    public string $metode_bayar = '';
    public ?int $jenis_tagihan_id = null;
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

    public function exportCsv()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=laporan_pemasukan_" . date('Ymd_His') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $query = Pembayaran::with(['tagihan.siswa.user', 'tagihan.siswa.kelas', 'tagihan.jenisTagihan']);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal_bayar', [$this->startDate, $this->endDate]);
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

        $data = $query->orderBy('tanggal_bayar', 'asc')->get();

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nama Siswa', 'Kelas', 'Jenis Tagihan', 'Tanggal Bayar', 'Metode Bayar', 'Jumlah Pemasukan']);

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
        $query = Pembayaran::with(['tagihan.siswa.user', 'tagihan.siswa.kelas', 'tagihan.jenisTagihan']);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal_bayar', [$this->startDate, $this->endDate]);
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

        $data = $query->orderBy('tanggal_bayar', 'asc')->get();
        $jt = JenisTagihan::find($this->jenis_tagihan_id);

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-laporan-pemasukan', [
            'data' => $data,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'metodeBayar' => $this->metode_bayar ?: 'Semua',
            'jenisTagihan' => $jt?->nama ?? 'Semua',
            'totalPemasukan' => $data->sum('nominal_dibayar'),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'laporan_pemasukan_' . date('Ymd_His') . '.pdf');
    }

    public function render()
    {
        $query = Pembayaran::with(['tagihan.siswa.user', 'tagihan.siswa.kelas', 'tagihan.jenisTagihan']);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal_bayar', [$this->startDate, $this->endDate]);
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

        $payments = $query->orderBy('tanggal_bayar', 'desc')->paginate(15);
        $jenisTagihans = JenisTagihan::all();

        return view('livewire.finance.laporan.laporan-pemasukan', [
            'payments' => $payments,
            'jenisTagihans' => $jenisTagihans,
        ])->layout('components.layouts.app', ['title' => 'Laporan Pemasukan Keuangan']);
    }
}
