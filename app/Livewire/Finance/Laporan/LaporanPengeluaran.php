<?php

namespace App\Livewire\Finance\Laporan;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Models\Pengaturan;
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

    // Modal Catat Pengeluaran Kas Baru (Manual)
    public bool $showCreateModal = false;
    public string $createTanggal = '';
    public ?int $createKategoriId = null;
    public $createJumlah = 0;
    public string $createKeterangan = '';

    // Modal Buat Laporan Keuangan Manual / Kustom
    public bool $showManualReportModal = false;
    public string $reportJudul = 'LAPORAN PENGELUARAN KEUANGAN YAYASAN';
    public ?string $reportStartDate = null;
    public ?string $reportEndDate = null;
    public ?int $reportKategoriId = null;
    public string $reportCatatan = '';
    public string $reportPenandatangan = '';
    public string $reportJabatanPenandatangan = 'Bendahara Yayasan';

    public array $listBulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    public function mount()
    {
        $this->createTanggal = now()->toDateString();
        $this->reportStartDate = now()->startOfMonth()->toDateString();
        $this->reportEndDate = now()->toDateString();
        $this->reportPenandatangan = auth()->user()->nama ?? 'Bendahara';
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

    // ==========================================
    // 1. MODAL CATAT PENGELUARAN KAS MANUAL
    // ==========================================
    public function openCreateModal()
    {
        $this->resetValidation();
        $this->createTanggal = now()->toDateString();
        $this->createKategoriId = KategoriPengeluaran::first()?->id;
        $this->createJumlah = 0;
        $this->createKeterangan = '';
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function savePengeluaran()
    {
        $this->validate([
            'createTanggal' => 'required|date',
            'createKategoriId' => 'required|exists:kategori_pengeluaran,id',
            'createJumlah' => 'required|numeric|min:1',
            'createKeterangan' => 'required|string|max:255',
        ], [
            'createTanggal.required' => 'Tanggal pengeluaran wajib diisi.',
            'createKategoriId.required' => 'Pilih kategori pengeluaran.',
            'createJumlah.required' => 'Jumlah nominal pengeluaran wajib diisi.',
            'createJumlah.min' => 'Nominal pengeluaran minimal Rp 1.',
            'createKeterangan.required' => 'Keterangan pengeluaran wajib diisi.',
        ]);

        Pengeluaran::create([
            'tanggal' => $this->createTanggal,
            'kategori_pengeluaran_id' => $this->createKategoriId,
            'jumlah' => floatval($this->createJumlah),
            'keterangan' => $this->createKeterangan,
            'petugas_id' => auth()->id(),
        ]);

        $this->showCreateModal = false;
        $this->resetPage();
        session()->flash('message', 'Pengeluaran kas manual berhasil dicatat ke dalam pembukuan yayasan.');
    }

    public function deletePengeluaran(int $id)
    {
        $p = Pengeluaran::with('gajiGuru')->findOrFail($id);

        if ($p->gajiGuru) {
            session()->flash('error', 'Pengeluaran ini terkait dengan data penggajian guru dan tidak dapat dihapus manual dari menu ini.');
            return;
        }

        $p->delete();
        session()->flash('message', 'Catatan pengeluaran kas berhasil dihapus.');
    }

    // ==========================================
    // 2. MODAL BUAT LAPORAN MANUAL / KUSTOM
    // ==========================================
    public function openManualReportModal()
    {
        $this->reportJudul = 'LAPORAN PENGELUARAN KEUANGAN YAYASAN';
        $this->reportStartDate = $this->startDate ?: now()->startOfMonth()->toDateString();
        $this->reportEndDate = $this->endDate ?: now()->toDateString();
        $this->reportKategoriId = $this->kategori_pengeluaran_id;
        $this->reportCatatan = '';
        $this->reportPenandatangan = auth()->user()->nama ?? 'Bendahara';
        $this->reportJabatanPenandatangan = 'Bendahara Yayasan';
        $this->showManualReportModal = true;
    }

    public function closeManualReportModal()
    {
        $this->showManualReportModal = false;
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
            fputcsv($file, ['No', 'Tanggal', 'Kategori', 'Keterangan', 'Jumlah Pengeluaran (Rp)', 'Petugas']);

            foreach ($data as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') : '-',
                    $row->kategori->nama ?? '-',
                    $row->keterangan ?? '-',
                    number_format($row->jumlah, 0, ',', '.'),
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
            'custom' => ($this->startDate ? \Carbon\Carbon::parse($this->startDate)->translatedFormat('d M Y') : '') . ' s/d ' . ($this->endDate ? \Carbon\Carbon::parse($this->endDate)->translatedFormat('d M Y') : ''),
            default => 'Semua Periode',
        };

        $namaSekolah = Pengaturan::getValue('nama_sekolah', 'PONDOK PESANTREN & SEKOLAH ISLAM TERPADU');
        $alamatSekolah = Pengaturan::getValue('alamat_sekolah', 'Jl. Pendidikan Karakter Islami No. 123');
        $noTelepon = Pengaturan::getValue('no_telepon', '(0274) 123456');

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-laporan-pengeluaran', [
            'data' => $data,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'periodeText' => $periodeText,
            'bulan' => $this->bulan,
            'kategori' => $cat?->nama ?? 'Semua',
            'totalPengeluaran' => $data->sum('jumlah'),
            'namaSekolah' => $namaSekolah,
            'alamatSekolah' => $alamatSekolah,
            'noTelepon' => $noTelepon,
            'judul' => 'LAPORAN PENGELUARAN KEUANGAN YAYASAN',
            'catatan' => '',
            'penandatangan' => auth()->user()->nama ?? '',
            'jabatanPenandatangan' => 'Bendahara Yayasan',
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
            'totalSum' => $this->getFilteredQuery()->sum('jumlah'),
        ])->layout('components.layouts.app', ['title' => 'Laporan Pengeluaran Keuangan']);
    }
}
