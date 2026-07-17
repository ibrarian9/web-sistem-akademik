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

    public function exportCsv()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=laporan_tunggakan_" . date('Ymd_His') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $query = Tagihan::with(['siswa.user', 'siswa.kelas', 'jenisTagihan'])
            ->where('status', '!=', 'lunas');

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

        $data = $query->get();

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
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
        $query = Tagihan::with(['siswa.user', 'siswa.kelas', 'jenisTagihan'])
            ->where('status', '!=', 'lunas');

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

        $data = $query->get();
        $kelas = Kelas::find($this->kelas_id);
        $ta = TahunAjaran::find($this->tahun_ajaran_id);

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-laporan-tunggakan', [
            'data' => $data,
            'kelas' => $kelas?->nama_kelas ?? 'Semua Kelas',
            'tahunAjaran' => $ta?->nama ?? 'Semua',
            'totalTunggakan' => $data->sum(fn($t) => $t->nominal - $t->total_dibayar),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'laporan_tunggakan_' . date('Ymd_His') . '.pdf');
    }

    public function render()
    {
        $query = Tagihan::with(['siswa.user', 'siswa.kelas', 'jenisTagihan'])
            ->where('status', '!=', 'lunas');

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

        $tunggakans = $query->paginate(15);
        $kelases = Kelas::all();
        $tahunAjarans = TahunAjaran::all();

        return view('livewire.finance.laporan.laporan-tunggakan', [
            'tunggakans' => $tunggakans,
            'kelases' => $kelases,
            'tahunAjarans' => $tahunAjarans,
        ])->layout('components.layouts.app', ['title' => 'Laporan Tunggakan Pembayaran']);
    }
}
