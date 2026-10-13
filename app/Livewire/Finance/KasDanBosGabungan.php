<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Services\Finance\ConsolidatedCashService;
use App\Services\Finance\FinanceReportService;
use App\Services\Finance\CsvExportService;
use App\Models\Pengaturan;
use App\Traits\WithDateFilter;
use App\Traits\WithCurrencySanitizer;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class KasDanBosGabungan extends Component
{
    use WithPagination, WithDateFilter, WithCurrencySanitizer, WithFileUploads;

    // Source Filter: 'semua', 'kas_umum', 'dana_bos'
    public string $filterSumber = 'semua';

    // Flow Type Filter: 'semua', 'masuk', 'keluar'
    public string $filterJenis = 'semua';

    // Filters
    public string $filterKategori = '';
    public string $search = '';
    public ?string $nominalMin = null;
    public ?string $nominalMax = null;

    public int $perPage = 20;

    // Metrics summary cache
    public array $metrics = [];

    // Lightbox Preview Modal
    public bool $showPreviewBuktiModal = false;
    public ?string $previewBuktiUrl = null;
    public ?string $previewBuktiTitle = null;

    // Upload Bukti Modal
    public bool $showUploadBuktiModal = false;
    public ?string $uploadTargetId = null;
    public ?string $uploadTargetKategori = null;
    public ?string $uploadTargetKeterangan = null;
    public ?float $uploadTargetNominal = null;
    public $uploadBuktiFile = null;

    protected $queryString = [
        'filterSumber' => ['except' => 'semua'],
        'filterJenis' => ['except' => 'semua'],
        'filterKategori' => ['except' => ''],
        'filterPeriode' => ['except' => 'semua'],
        'startDate' => ['except' => null],
        'endDate' => ['except' => null],
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterSumber()
    {
        $this->resetPage();
    }

    public function updatingFilterJenis()
    {
        $this->resetPage();
    }

    public function updatingFilterKategori()
    {
        $this->resetPage();
    }

    public function setSumber(string $sumber)
    {
        $this->filterSumber = $sumber;
        $this->resetPage();
    }

    public function setJenis(string $jenis)
    {
        $this->filterJenis = $jenis;
        $this->resetPage();
    }

    public function filterByFlow(string $jenis): void
    {
        $this->filterJenis = ($this->filterJenis === $jenis) ? 'semua' : $jenis;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->filterSumber = 'semua';
        $this->filterJenis = 'semua';
        $this->filterKategori = '';
        $this->search = '';
        $this->filterPeriode = 'semua';
        $this->startDate = null;
        $this->endDate = null;
        $this->nominalMin = null;
        $this->nominalMax = null;
        $this->resetPage();
    }

    public function getActiveFilterCountProperty(): int
    {
        $count = 0;
        if ($this->filterSumber !== 'semua') $count++;
        if ($this->filterJenis !== 'semua') $count++;
        if ($this->filterPeriode !== 'semua') $count++;
        if (!empty($this->startDate) || !empty($this->endDate)) $count++;
        if (!empty($this->search)) $count++;
        if (!empty($this->filterKategori)) $count++;
        if (!empty($this->nominalMin) || !empty($this->nominalMax)) $count++;
        return $count;
    }

    public function getCategoryGroupsProperty(): array
    {
        return ConsolidatedCashService::getCategoryGroups($this->filterSumber, $this->filterJenis);
    }

    public function openPreviewBukti(string $url, ?string $title = null)
    {
        $this->previewBuktiUrl = $url;
        $this->previewBuktiTitle = $title ?: 'Bukti Transaksi';
        $this->showPreviewBuktiModal = true;
    }

    public function closePreviewBukti()
    {
        $this->showPreviewBuktiModal = false;
        $this->previewBuktiUrl = null;
        $this->previewBuktiTitle = null;
    }

    public function openUploadBuktiModal(string $transactionId, string $kategori = '', string $keterangan = '', float $nominal = 0)
    {
        if (!in_array(auth()->user()->role?->nama, ['finance', 'super_admin', 'founder']) || auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Hanya Admin Keuangan dan Admin Utama yang berhak mengunggah bukti transaksi.');
            return;
        }

        $this->uploadTargetId = $transactionId;
        $this->uploadTargetKategori = $kategori;
        $this->uploadTargetKeterangan = $keterangan;
        $this->uploadTargetNominal = $nominal;
        $this->uploadBuktiFile = null;
        $this->resetErrorBag();
        $this->showUploadBuktiModal = true;
    }

    public function closeUploadBuktiModal()
    {
        $this->showUploadBuktiModal = false;
        $this->uploadTargetId = null;
        $this->uploadTargetKategori = null;
        $this->uploadTargetKeterangan = null;
        $this->uploadTargetNominal = null;
        $this->uploadBuktiFile = null;
        $this->resetErrorBag();
    }

    public function saveUploadedBukti()
    {
        if (!in_array(auth()->user()->role?->nama, ['finance', 'super_admin', 'founder']) || auth()->user()->isSuperAdmin2()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $this->validate([
            'uploadBuktiFile' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'uploadBuktiFile.required' => 'Silakan pilih file foto bukti terlebih dahulu.',
            'uploadBuktiFile.image' => 'File bukti harus berupa foto atau gambar.',
            'uploadBuktiFile.mimes' => 'Format foto hanya boleh JPG, JPEG, PNG, atau WEBP.',
            'uploadBuktiFile.max' => 'Ukuran file foto bukti maksimal 2MB.',
        ]);

        if (!$this->uploadTargetId) {
            $this->closeUploadBuktiModal();
            return;
        }

        $parts = explode('_', $this->uploadTargetId, 2);
        if (count($parts) !== 2) {
            session()->flash('error', 'Format transaksi tidak valid.');
            $this->closeUploadBuktiModal();
            return;
        }

        [$prefix, $rawId] = $parts;
        $rawId = (int) $rawId;

        switch ($prefix) {
            case 'spp':
                $model = \App\Models\Pembayaran::findOrFail($rawId);
                $path = $this->uploadBuktiFile->store('bukti_bayar', 'public');
                $model->update(['bukti_bayar' => $path]);
                break;

            case 'infaq':
                $model = \App\Models\PemasukanKas::findOrFail($rawId);
                $path = $this->uploadBuktiFile->store('pemasukan_kas', 'public');
                $model->update(['bukti' => $path]);
                break;

            case 'op':
                $model = \App\Models\Pengeluaran::findOrFail($rawId);
                $path = $this->uploadBuktiFile->store('bukti_pengeluaran', 'public');
                $model->update(['bukti' => $path]);
                break;

            case 'bos':
                $model = \App\Models\DanaBos::findOrFail($rawId);
                $path = $this->uploadBuktiFile->store('dana_bos', 'public');
                $model->update(['bukti' => $path]);
                break;

            case 'gaji':
                $model = \App\Models\GajiGuru::findOrFail($rawId);
                $path = $this->uploadBuktiFile->store('bukti_bayar', 'public');
                $model->update(['bukti_bayar' => $path]);
                break;

            default:
                session()->flash('error', 'Jenis transaksi ini tidak mendukung lampiran bukti.');
                $this->closeUploadBuktiModal();
                return;
        }

        session()->flash('message', 'Foto bukti transaksi berhasil diunggah.');
        $this->closeUploadBuktiModal();
    }

    public function getConsolidatedCollection(bool $ignoreJenis = false)
    {
        return ConsolidatedCashService::getTransactions([
            'sumber' => $this->filterSumber,
            'jenis' => $ignoreJenis ? 'semua' : $this->filterJenis,
            'search' => $this->search,
            'filter_periode' => $this->filterPeriode,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'kategori' => $this->filterKategori,
            'nominal_min' => $this->nominalMin,
            'nominal_max' => $this->nominalMax,
        ]);
    }

    public function exportExcel()
    {
        $data = $this->getConsolidatedCollection();

        if ($data->isEmpty()) {
            session()->flash('error', 'Tidak ada data transaksi untuk diekspor ke Excel / CSV.');
            return;
        }

        $filename = 'jurnal-kas-dan-bos-gabungan-' . ($this->filterKategori !== '' ? \Illuminate\Support\Str::slug($this->filterKategori) . '-' : '') . date('Ymd_His') . '.csv';

        return CsvExportService::stream(
            $filename,
            ['No', 'Tanggal', 'Sumber Dana', 'Jenis Arus', 'Kategori', 'Keterangan Transaksi', 'Pemasukan (Rp)', 'Pengeluaran (Rp)', 'Petugas / PIC', 'No. Resi'],
            $data,
            fn($item, $index) => [
                $index + 1,
                $item->tanggal ? Carbon::parse($item->tanggal)->translatedFormat('d/m/Y') : '-',
                $item->sumber_label,
                $item->type_label,
                $item->kategori,
                $item->keterangan,
                $item->nominal_masuk > 0 ? number_format($item->nominal_masuk, 0, ',', '.') : '0',
                $item->nominal_keluar > 0 ? number_format($item->nominal_keluar, 0, ',', '.') : '0',
                $item->petugas ?: '-',
                $item->no_resi ?: '-'
            ]
        );
    }

    public function exportPdf()
    {
        @ini_set('max_execution_time', 120);
        @ini_set('memory_limit', '512M');

        $data = $this->getConsolidatedCollection();

        if ($data->isEmpty()) {
            session()->flash('error', 'Tidak dapat mencetak PDF karena tidak ada transaksi pada filter yang dipilih.');
            return;
        }

        $metrics = ConsolidatedCashService::calculateMetrics($data);
        $schoolHeader = FinanceReportService::getSchoolHeader();
        $periodeText = FinanceReportService::formatPeriodeText($this->filterPeriode, $this->startDate, $this->endDate);

        $sumberText = match ($this->filterSumber) {
            'kas_umum' => 'Kas Operasional Sekolah Saja',
            'dana_bos' => 'Dana BOS Saja',
            default => 'Gabungan (Kas Sekolah & Dana BOS)',
        };

        $jenisText = match ($this->filterJenis) {
            'masuk' => 'Pemasukan Saja',
            'keluar' => 'Pengeluaran Saja',
            default => 'Semua Arus (Pemasukan & Pengeluaran)',
        };

        $kategoriText = $this->filterKategori !== '' ? $this->filterKategori : 'Semua Kategori';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.shared.laporan.pdf-kas-dan-bos-gabungan', [
            'data' => $data,
            'metrics' => $metrics,
            'schoolHeader' => $schoolHeader,
            'periodeText' => $periodeText,
            'sumberText' => $sumberText,
            'jenisText' => $jenisText,
            'kategoriText' => $kategoriText,
            'printedBy' => auth()->user()->nama ?? 'Petugas Keuangan',
            'printedAt' => Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB',
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan_kas_dan_bos_gabungan_' . ($this->filterKategori !== '' ? \Illuminate\Support\Str::slug($this->filterKategori) . '_' : '') . date('Ymd_His') . '.pdf';

        return FinanceReportService::renderPdf(
            $pdf,
            $filename,
            true
        );
    }

    public function render()
    {
        // 1. Ambil transaksi tanpa batasan jenis arus agar metrik ringkasan kartu tetap stabil (tidak berubah saat filter aktif)
        $baseTransactions = $this->getConsolidatedCollection(ignoreJenis: true);
        $this->metrics = ConsolidatedCashService::calculateMetrics($baseTransactions);

        // 2. Filter transaksi tabel sesuai jenis arus yang dipilih
        $tableTransactions = ($this->filterJenis === 'semua')
            ? $baseTransactions
            : $baseTransactions->where('type', $this->filterJenis)->values();

        // Manual pagination on collection
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginatedTransactions = new LengthAwarePaginator(
            $tableTransactions->forPage($page, $this->perPage),
            $tableTransactions->count(),
            $this->perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => request()->query()]
        );

        $trend = ConsolidatedCashService::calculateMonthlyTrend(6);
        $monthlyChartData = $trend['monthlyChartData'];
        $maxMonthVal = $trend['maxMonthVal'];

        return view('livewire.finance.kas-dan-bos-gabungan', [
            'transactions' => $paginatedTransactions,
            'metrics' => $this->metrics,
            'categoryGroups' => $this->categoryGroups,
            'monthlyChartData' => $monthlyChartData,
            'maxMonthVal' => $maxMonthVal,
        ])->layout('components.layouts.app', ['title' => 'Arus Kas & Dana Bos']);
    }
}
