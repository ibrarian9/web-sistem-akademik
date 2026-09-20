<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Models\PemasukanKas;
use App\Models\Pengaturan;
use App\Traits\WithDateFilter;
use App\Traits\WithCurrencySanitizer;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class ArusKas extends Component
{
    use WithPagination, WithDateFilter, WithFileUploads, WithCurrencySanitizer;

    // Active Tab: 'semua', 'masuk', 'keluar'
    public string $tab = 'semua';

    // Stream selector: 'semua', 'spp', 'infaq', 'tabungan', 'operasional', 'gaji', 'kasbon'
    public string $stream = 'semua';

    // Filters
    public string $search = '';
    public ?int $filterKategoriKeluar = null;
    public string $filterKategoriMasuk = '';
    public ?string $nominalMin = null;
    public ?string $nominalMax = null;
    public string $filterMetode = 'semua';

    // Modals
    public bool $showIncomeModal = false;
    public bool $showExpenseModal = false;

    // Proof file and preview
    public $bukti_keluar = null;
    public bool $showPreviewBuktiModal = false;
    public ?string $previewBuktiUrl = null;
    public ?string $previewBuktiTitle = null;

    // Edit Expense Modal
    public bool $showEditExpenseModal = false;
    public ?int $editingPengeluaranId = null;
    public ?int $edit_kategori_pengeluaran_id = null;
    public float $edit_jumlah = 0.00;
    public string $edit_tanggal = '';
    public string $edit_keterangan = '';
    public ?string $edit_existing_bukti = null;
    public $edit_bukti_keluar = null;

    // Form: Kas Masuk Yayasan
    public string $kategori_masuk = 'Infaq';
    public bool $is_kategori_masuk_kustom = false;
    public string $kategori_masuk_kustom = '';
    public float $jumlah_masuk = 0.00;
    public string $tanggal_masuk = '';
    public string $keterangan_masuk = '';

    // Form: Kas Keluar Operasional
    public ?int $kategori_pengeluaran_id = null;
    public string $kategori_keluar_kustom = '';
    public bool $is_kategori_kustom = false;
    public float $jumlah_keluar = 0.00;
    public string $tanggal_keluar = '';
    public string $keterangan_keluar = '';

    // Aliases for compatibility
    public float $jumlah = 0.00;
    public string $tanggal = '';
    public string $keterangan = '';
    public string $kategori = 'Infaq';

    public array $kategoriKeluarOptions = [];
    public array $kategoriMasukOptions = [
        'Infaq',
        'Sedekah Subuh',
        'Maghrib Mengaji',
        'Donasi',
        'Sponsor / Acara',
        'Hibah Yayasan',
        'Lainnya'
    ];

    protected $queryString = [
        'tab' => ['except' => 'semua'],
        'stream' => ['except' => 'semua'],
        'filterPeriode' => ['except' => 'semua'],
        'startDate' => ['except' => null],
        'endDate' => ['except' => null],
        'search' => ['except' => ''],
        'filterKategoriMasuk' => ['except' => ''],
        'filterKategoriKeluar' => ['except' => null],
        'nominalMin' => ['except' => null],
        'nominalMax' => ['except' => null],
        'filterMetode' => ['except' => 'semua'],
    ];

    public function mount()
    {
        if (request()->routeIs('finance.arus-kas-masuk') || request()->routeIs('arus-kas-masuk')) {
            $this->tab = 'masuk';
        } elseif (request()->routeIs('finance.arus-kas-keluar') || request()->routeIs('arus-kas-keluar')) {
            $this->tab = 'keluar';
        }

        $this->kategoriKeluarOptions = KategoriPengeluaran::orderBy('nama')->get()->toArray();
        if (!empty($this->kategoriKeluarOptions)) {
            $this->kategori_pengeluaran_id = $this->kategoriKeluarOptions[0]['id'];
        }

        $distinctMasuk = PemasukanKas::distinct()->pluck('kategori')->filter()->toArray();
        $tagihanCategories = \App\Models\JenisTagihan::distinct()->pluck('nama')->filter()->toArray();
        $this->kategoriMasukOptions = array_values(array_unique(array_merge(
            $this->kategoriMasukOptions,
            $distinctMasuk,
            $tagihanCategories,
            ['Tabungan Siswa']
        )));

        $this->tanggal_masuk = date('Y-m-d');
        $this->tanggal_keluar = date('Y-m-d');
        $this->tanggal = date('Y-m-d');
    }

    public function selectTab(string $tab)
    {
        $this->tab = $tab;
        $this->stream = 'semua';
        $this->resetPage();
    }

    public function filterByCard(string $targetTab)
    {
        if ($this->tab === $targetTab) {
            $this->tab = 'semua';
        } else {
            $this->tab = $targetTab;
        }
        $this->stream = 'semua';
        $this->resetPage();
    }

    public function selectStream(string $stream)
    {
        $this->stream = $stream;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterKategoriKeluar()
    {
        $this->resetPage();
    }

    public function updatingFilterKategoriMasuk()
    {
        $this->resetPage();
    }

    public function updatingFilterMetode()
    {
        $this->resetPage();
    }

    public function updatingNominalMin()
    {
        $this->resetPage();
    }

    public function updatingNominalMax()
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

    // Modal Kas Masuk
    public function openIncomeModal()
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->resetValidation();
        $this->reset(['jumlah_masuk', 'keterangan_masuk', 'is_kategori_masuk_kustom', 'kategori_masuk_kustom']);
        $this->tanggal_masuk = date('Y-m-d');
        $this->kategori_masuk = $this->kategoriMasukOptions[0] ?? 'Infaq';
        $this->showIncomeModal = true;
    }

    public function closeIncomeModal()
    {
        $this->showIncomeModal = false;
        $this->is_kategori_masuk_kustom = false;
        $this->kategori_masuk_kustom = '';
    }

    public function saveIncome()
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->sanitizeCurrencies(['jumlah_masuk', 'jumlah']);

        if ($this->is_kategori_masuk_kustom) {
            $this->validate([
                'kategori_masuk_kustom' => 'required|string|max:100',
                'jumlah_masuk' => 'required|numeric|min:0',
                'tanggal_masuk' => 'required|date',
                'keterangan_masuk' => 'nullable|string|max:500',
            ], [
                'kategori_masuk_kustom.required' => 'Nama kategori penerimaan baru wajib diisi.',
                'kategori_masuk_kustom.max' => 'Nama kategori maksimal 100 karakter.',
                'jumlah_masuk.required' => 'Nominal penerimaan wajib diisi.',
                'jumlah_masuk.min' => 'Nominal penerimaan tidak boleh bernilai negatif.',
            ]);
            $kat = trim($this->kategori_masuk_kustom);
        } else {
            $this->validate([
                'kategori_masuk' => 'required|string|max:100',
                'jumlah_masuk' => 'required|numeric|min:0',
                'tanggal_masuk' => 'required|date',
                'keterangan_masuk' => 'nullable|string|max:500',
            ], [
                'kategori_masuk.required' => 'Kategori penerimaan wajib dipilih.',
                'jumlah_masuk.required' => 'Nominal penerimaan wajib diisi.',
                'jumlah_masuk.min' => 'Nominal penerimaan tidak boleh bernilai negatif.',
            ]);
            $kat = $this->kategori_masuk ?: 'Infaq';
        }

        $amount = $this->jumlah_masuk ?: $this->jumlah;
        $date = $this->tanggal_masuk ?: ($this->tanggal ?: date('Y-m-d'));
        $desc = $this->keterangan_masuk ?: $this->keterangan;

        PemasukanKas::create([
            'kategori' => $kat,
            'jumlah' => $amount,
            'tanggal' => $date,
            'keterangan' => $desc,
            'petugas_id' => auth()->id(),
        ]);

        if (!in_array($kat, $this->kategoriMasukOptions)) {
            $this->kategoriMasukOptions[] = $kat;
        }

        session()->flash('message', 'Pemasukan kas yayasan (' . $kat . ') berhasil dicatat.');
        $this->showIncomeModal = false;
        $this->reset(['jumlah_masuk', 'keterangan_masuk', 'jumlah', 'keterangan', 'is_kategori_masuk_kustom', 'kategori_masuk_kustom']);
        $this->resetPage();
    }

    public function deleteIncome(int $id, ?string $alasan = null)
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $item = PemasukanKas::findOrFail($id);
        $userRole = auth()->user()->role->nama ?? '';

        if ($userRole === 'finance') {
            $reason = $alasan ?: 'Penghapusan catatan pemasukan kas diajukan oleh staf keuangan';
            \App\Services\FinancialApprovalService::createRequest(
                auth()->user(),
                'hapus',
                'arus_kas',
                $item,
                null,
                $reason,
                "Hapus Pemasukan Kas: {$item->kategori} - Rp " . number_format($item->jumlah, 0, ',', '.') . " (" . ($item->tanggal ? $item->tanggal->format('d/m/Y') : '-') . ")"
            );

            session()->flash('message', 'Permohonan penghapusan pemasukan kas telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.');
            $this->dispatch('show-alert', [
                'title' => 'Menunggu Persetujuan',
                'message' => 'Permohonan penghapusan pemasukan kas telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.',
                'type' => 'info',
            ]);
            return;
        }

        $item->delete();
        session()->flash('message', 'Catatan pemasukan kas berhasil dihapus.');
        $this->dispatch('show-alert', [
            'title' => 'Berhasil',
            'message' => 'Catatan pemasukan kas berhasil dihapus.',
            'type' => 'delete',
        ]);
    }

    // Modal Kas Keluar
    public function openExpenseModal()
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->resetValidation();
        $this->reset(['jumlah_keluar', 'keterangan_keluar', 'jumlah', 'keterangan', 'kategori_keluar_kustom', 'is_kategori_kustom', 'bukti_keluar']);
        $this->tanggal_keluar = date('Y-m-d');
        $this->tanggal = date('Y-m-d');
        if (!empty($this->kategoriKeluarOptions)) {
            $this->kategori_pengeluaran_id = $this->kategoriKeluarOptions[0]['id'];
        }
        $this->showExpenseModal = true;
    }

    public function closeExpenseModal()
    {
        $this->showExpenseModal = false;
        $this->bukti_keluar = null;
    }

    public function saveExpense()
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->sanitizeCurrencies(['jumlah_keluar', 'jumlah']);

        $amount = $this->jumlah_keluar > 0 ? $this->jumlah_keluar : $this->jumlah;
        $date = $this->tanggal_keluar ?: ($this->tanggal ?: date('Y-m-d'));
        $desc = $this->keterangan_keluar ?: $this->keterangan;

        $this->jumlah_keluar = $amount;
        $this->tanggal_keluar = $date;
        $this->keterangan_keluar = $desc;

        $rules = [
            'jumlah_keluar' => 'required|numeric|min:0',
            'tanggal_keluar' => 'required|date',
            'keterangan_keluar' => 'nullable|string|max:500',
            'bukti_keluar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        $messages = [
            'bukti_keluar.image' => 'File bukti pengeluaran harus berupa foto/gambar.',
            'bukti_keluar.mimes' => 'Format foto hanya boleh JPG, JPEG, PNG, atau WEBP.',
            'bukti_keluar.max' => 'Ukuran file foto bukti pengeluaran maksimal 2MB.',
        ];

        if ($this->is_kategori_kustom && !empty(trim($this->kategori_keluar_kustom))) {
            $rules['kategori_keluar_kustom'] = 'required|string|max:100';
            $this->validate($rules, $messages);

            $kategori = KategoriPengeluaran::firstOrCreate([
                'nama' => trim($this->kategori_keluar_kustom)
            ]);
            $this->kategori_pengeluaran_id = $kategori->id;
            $this->kategoriKeluarOptions = KategoriPengeluaran::orderBy('nama')->get()->toArray();
        } else {
            $rules['kategori_pengeluaran_id'] = 'required|exists:kategori_pengeluaran,id';
            $this->validate($rules, $messages);
        }

        $buktiPath = null;
        if ($this->bukti_keluar) {
            $buktiPath = $this->bukti_keluar->store('bukti_pengeluaran', 'public');
        }

        Pengeluaran::create([
            'kategori_pengeluaran_id' => $this->kategori_pengeluaran_id,
            'jumlah' => $amount,
            'tanggal' => $date,
            'keterangan' => $desc,
            'bukti' => $buktiPath,
            'petugas_id' => auth()->id(),
        ]);

        session()->flash('message', 'Pengeluaran kas operasional yayasan berhasil dicatat.');
        $this->showExpenseModal = false;
        $this->reset(['jumlah_keluar', 'keterangan_keluar', 'jumlah', 'keterangan', 'kategori_keluar_kustom', 'is_kategori_kustom', 'bukti_keluar']);
        $this->resetPage();
    }

    public function openEditExpenseModal(int $id)
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $item = Pengeluaran::findOrFail($id);
        $this->editingPengeluaranId = $item->id;
        $this->edit_kategori_pengeluaran_id = $item->kategori_pengeluaran_id;
        $this->edit_jumlah = (float) $item->jumlah;
        $this->edit_tanggal = $item->tanggal ? $item->tanggal->format('Y-m-d') : date('Y-m-d');
        $this->edit_keterangan = $item->keterangan ?? '';
        $this->edit_existing_bukti = $item->bukti;
        $this->edit_bukti_keluar = null;
        $this->resetValidation();
        $this->showEditExpenseModal = true;
    }

    public function closeEditExpenseModal()
    {
        $this->showEditExpenseModal = false;
        $this->editingPengeluaranId = null;
        $this->edit_bukti_keluar = null;
        $this->resetValidation();
    }

    public function updateExpense()
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        if (!$this->editingPengeluaranId) return;

        $this->sanitizeCurrencies(['edit_jumlah']);

        $this->validate([
            'edit_kategori_pengeluaran_id' => 'required|exists:kategori_pengeluaran,id',
            'edit_jumlah' => 'required|numeric|min:0',
            'edit_tanggal' => 'required|date',
            'edit_keterangan' => 'nullable|string|max:500',
            'edit_bukti_keluar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'edit_bukti_keluar.image' => 'File bukti pengeluaran harus berupa foto/gambar.',
            'edit_bukti_keluar.mimes' => 'Format foto hanya boleh JPG, JPEG, PNG, atau WEBP.',
            'edit_bukti_keluar.max' => 'Ukuran file foto bukti pengeluaran maksimal 2MB.',
        ]);

        $item = Pengeluaran::findOrFail($this->editingPengeluaranId);

        $path = $item->bukti;
        if ($this->edit_bukti_keluar) {
            if ($item->bukti && Storage::disk('public')->exists($item->bukti)) {
                Storage::disk('public')->delete($item->bukti);
            }
            $path = $this->edit_bukti_keluar->store('bukti_pengeluaran', 'public');
        }

        $item->update([
            'kategori_pengeluaran_id' => $this->edit_kategori_pengeluaran_id,
            'jumlah' => $this->edit_jumlah,
            'tanggal' => $this->edit_tanggal,
            'keterangan' => $this->edit_keterangan,
            'bukti' => $path,
        ]);

        session()->flash('message', 'Catatan pengeluaran & bukti pembayaran berhasil diperbarui.');
        $this->closeEditExpenseModal();
    }

    public function deleteEditBukti()
    {
        if (auth()->user()->isSuperAdmin2() || !$this->editingPengeluaranId) return;

        $item = Pengeluaran::findOrFail($this->editingPengeluaranId);
        if ($item->bukti && Storage::disk('public')->exists($item->bukti)) {
            Storage::disk('public')->delete($item->bukti);
        }
        $item->update(['bukti' => null]);
        $this->edit_existing_bukti = null;
        session()->flash('message', 'Foto bukti pengeluaran berhasil dihapus.');
    }

    public function openPreviewBukti(string $url, ?string $title = null)
    {
        $this->previewBuktiUrl = $url;
        $this->previewBuktiTitle = $title ?: 'Foto Bukti Transaksi';
        $this->showPreviewBuktiModal = true;
    }

    public function closePreviewBukti()
    {
        $this->showPreviewBuktiModal = false;
        $this->previewBuktiUrl = null;
        $this->previewBuktiTitle = null;
    }

    public function deleteExpense(int $id, ?string $alasan = null)
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $item = Pengeluaran::with('kategoriPengeluaran')->findOrFail($id);
        $userRole = auth()->user()->role->nama ?? '';

        if ($userRole === 'finance') {
            $reason = $alasan ?: 'Penghapusan catatan pengeluaran kas diajukan oleh staf keuangan';
            \App\Services\FinancialApprovalService::createRequest(
                auth()->user(),
                'hapus',
                'arus_kas',
                $item,
                null,
                $reason,
                "Hapus Pengeluaran Kas: " . ($item->kategoriPengeluaran->nama ?? 'Pengeluaran') . " - Rp " . number_format($item->jumlah, 0, ',', '.') . " (" . ($item->tanggal ? $item->tanggal->format('d/m/Y') : '-') . ")"
            );

            session()->flash('message', 'Permohonan penghapusan pengeluaran kas telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.');
            $this->dispatch('show-alert', [
                'title' => 'Menunggu Persetujuan',
                'message' => 'Permohonan penghapusan pengeluaran kas telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.',
                'type' => 'info',
            ]);
            return;
        }

        $item->delete();
        session()->flash('message', 'Catatan pengeluaran kas berhasil dihapus.');
        $this->dispatch('show-alert', [
            'title' => 'Berhasil',
            'message' => 'Catatan pengeluaran kas berhasil dihapus.',
            'type' => 'delete',
        ]);
    }

    /**
     * Get unified collection of transactions based on active tab, stream, search, and date filters.
     */
    public function getUnifiedTransactions(): \Illuminate\Support\Collection
    {
        return \App\Services\Finance\CashFlowService::getUnifiedTransactions([
            'tab' => $this->tab,
            'stream' => $this->stream,
            'search' => $this->search,
            'filter_periode' => $this->filterPeriode,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'filter_kategori_masuk' => $this->filterKategoriMasuk,
            'filter_kategori_keluar' => $this->filterKategoriKeluar,
            'nominal_min' => $this->nominalMin,
            'nominal_max' => $this->nominalMax,
            'filter_metode' => $this->filterMetode,
        ]);
    }


    public function resetFilters()
    {
        $this->search = '';
        $this->filterPeriode = 'semua';
        $this->startDate = null;
        $this->endDate = null;
        $this->filterKategoriMasuk = '';
        $this->filterKategoriKeluar = null;
        $this->nominalMin = null;
        $this->nominalMax = null;
        $this->filterMetode = 'semua';
        $this->stream = 'semua';
        $this->resetPage();
    }

    public function getActiveFilterCountProperty(): int
    {
        $count = 0;
        if (!empty($this->search)) $count++;
        if ($this->filterPeriode !== 'semua') $count++;
        if (!empty($this->startDate) || !empty($this->endDate)) $count++;
        if (!empty($this->filterKategoriMasuk)) $count++;
        if ($this->filterKategoriKeluar !== null && $this->filterKategoriKeluar !== '') $count++;
        if ($this->nominalMin !== null && $this->nominalMin !== '') $count++;
        if ($this->nominalMax !== null && $this->nominalMax !== '') $count++;
        if ($this->filterMetode !== 'semua') $count++;
        if ($this->stream !== 'semua') $count++;
        return $count;
    }

    /**
     * Export active filtered cash flow table data to PDF document.
     */
    public function exportPdf()
    {
        @ini_set('max_execution_time', 120);
        @ini_set('memory_limit', '512M');

        $data = $this->getUnifiedTransactions();

        if ($data->isEmpty()) {
            session()->flash('error', 'Tidak dapat mencetak PDF karena tidak ada transaksi pada filter yang dipilih.');
            return;
        }

        $totalMasuk = (float) $data->sum('nominal_masuk');
        $totalKeluar = (float) $data->sum('nominal_keluar');
        $netBalance = $totalMasuk - $totalKeluar;

        $namaSekolah = Pengaturan::getValue('nama_sekolah', 'PONDOK PESANTREN & SEKOLAH ISLAM TERPADU');
        $alamatSekolah = Pengaturan::getValue('alamat_sekolah', 'Jl. Pendidikan Karakter Islami, Pekanbaru');
        $noTelepon = Pengaturan::getValue('no_telepon', '(0761) 123456');

        $periodeText = match ($this->filterPeriode) {
            'hari_ini' => 'Hari Ini (' . date('d/m/Y') . ')',
            'kemarin' => 'Kemarin (' . date('d/m/Y', strtotime('-1 day')) . ')',
            'minggu_ini' => 'Minggu Ini',
            'bulan_ini' => 'Bulan Ini (' . date('F Y') . ')',
            'bulan_lalu' => 'Bulan Lalu (' . Carbon::now()->subMonth()->translatedFormat('F Y') . ')',
            'tahun_ini' => 'Tahun Ini (' . date('Y') . ')',
            'custom' => ($this->startDate ? Carbon::parse($this->startDate)->translatedFormat('d M Y') : '') . ' s/d ' . ($this->endDate ? Carbon::parse($this->endDate)->translatedFormat('d M Y') : ''),
            default => 'Semua Periode Transaksi',
        };

        $tabText = match ($this->tab) {
            'masuk' => 'Kas Masuk Sahaja',
            'keluar' => 'Kas Keluar Sahaja',
            default => 'Semua Arus Kas (Masuk & Keluar)',
        };

        $streamText = match ($this->stream) {
            'spp' => 'SPP & Tagihan Siswa',
            'infaq' => 'Kas Masuk Yayasan (Infaq/Donasi)',
            'tabungan' => 'Setoran Tabungan Siswa',
            'operasional' => 'Beban Operasional Yayasan',
            'gaji' => 'Gaji & Honor Guru',
            'kasbon' => 'Fasilitas Kasbon Guru',
            default => 'Semua Stream Kas',
        };

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.shared.laporan.pdf-jurnal-arus-kas', [
            'data' => $data,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'netBalance' => $netBalance,
            'periodeText' => $periodeText,
            'tabText' => $tabText,
            'streamText' => $streamText,
            'namaSekolah' => $namaSekolah,
            'alamatSekolah' => $alamatSekolah,
            'noTelepon' => $noTelepon,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'jurnal_arus_kas_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Export active filtered cash flow table data to Excel (.csv format with UTF-8 BOM).
     */
    public function exportExcel()
    {
        $data = $this->getUnifiedTransactions();

        if ($data->isEmpty()) {
            session()->flash('error', 'Tidak ada data arus kas untuk diekspor ke Excel pada filter ini.');
            return;
        }

        $filename = 'jurnal-arus-kas-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'No',
                'Tanggal Transaksi',
                'Waktu',
                'Tipe Kas',
                'Stream / Sumber',
                'Kategori / Pos',
                'Kas Masuk (Rp)',
                'Kas Keluar (Rp)',
                'Keterangan & Rincian',
                'Metode / No Resi',
                'Petugas Pencatat'
            ]);

            foreach ($data as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->tanggal ? Carbon::parse($item->tanggal)->translatedFormat('d M Y') : '-',
                    $item->tanggal && Carbon::parse($item->tanggal)->format('H:i') !== '00:00' ? Carbon::parse($item->tanggal)->format('H:i') . ' WIB' : '-',
                    strtoupper($item->type),
                    $item->stream_label,
                    $item->kategori,
                    $item->nominal_masuk > 0 ? number_format($item->nominal_masuk, 0, ',', '.') : '0',
                    $item->nominal_keluar > 0 ? number_format($item->nominal_keluar, 0, ',', '.') : '0',
                    $item->keterangan,
                    $item->no_resi ? ($item->metode_resi . ' (' . $item->no_resi . ')') : $item->metode_resi,
                    $item->petugas ?? 'Sistem'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        $metrics = \App\Services\Finance\CashFlowService::calculateMetrics([
            'filter_periode' => $this->filterPeriode,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);

        $trend = \App\Services\Finance\CashFlowService::calculateMonthlyTrend(6);
        $monthlyChartData = $trend['monthlyChartData'];
        $maxMonthVal = $trend['maxMonthVal'];

        // Query Unified Transactions
        $sortedTransactions = $this->getUnifiedTransactions();

        // Paginate manually
        $perPage = 15;
        $total = $sortedTransactions->count();
        $maxPage = max(1, (int) ceil($total / $perPage));
        $page = (int) $this->getPage();
        if ($page > $maxPage) {
            $page = 1;
            $this->setPage(1);
        }
        $currentItems = $sortedTransactions->slice(($page - 1) * $perPage, $perPage)->values();
        $paginatedTransactions = new LengthAwarePaginator(
            $currentItems,
            $total,
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );

        return view('livewire.finance.arus-kas', array_merge([
            'paginatedTransactions' => $paginatedTransactions,
            'monthlyChartData' => $monthlyChartData,
            'maxMonthVal' => $maxMonthVal,
        ], $metrics))->layout('components.layouts.app', ['title' => 'Arus Kas (Cash Flow) Terpadu']);
    }
}
