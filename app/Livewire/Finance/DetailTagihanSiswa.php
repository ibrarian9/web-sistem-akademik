<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Traits\WithCurrencySanitizer;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class DetailTagihanSiswa extends Component
{
    use WithPagination, WithFileUploads, WithCurrencySanitizer;

    public int $siswaId;
    public ?Siswa $siswa = null;

    // Tagihan Filters & Selection
    public string $filterBulan = '';
    public ?int $filterJenis = null;
    public string $filterStatus = '';
    public ?int $filterTahunAjaran = null;
    public string $search = '';
    public array $selectedTagihanIds = [];
    public bool $selectAllTagihan = false;

    // Riwayat Pembayaran Filters
    public string $searchBayar = '';
    public string $filterBayarBulan = '';
    public ?int $filterBayarJenis = null;
    public string $filterBayarMetode = '';
    public ?int $filterBayarTahunAjaran = null;
    public array $selectedPembayaranIds = [];
    public bool $selectAllPembayaran = false;

    // Bukti Pembayaran Modal & Properties
    public bool $showBuktiModal = false;
    public ?int $selectedPembayaranId = null;
    public ?Pembayaran $selectedPembayaran = null;
    public $edit_bukti_foto = null;

    // Create Tagihan Modal for this student
    public bool $showCreateModal = false;
    public ?int $jenis_tagihan_id = null;
    public string $periodeTipe = 'single'; // 'single' | 'full_year_jan_des' | 'full_year_juli_juni' | 'custom_range'
    public string $bulan = 'Juli';
    public string $bulan_mulai = 'Juli';
    public string $bulan_selesai = 'Desember';
    public $nominal = 0.00;
    public string $jatuh_tempo = '';

    // Category Tabs & Display
    public string $activeCategoryTab = 'all'; // 'all' | 'spp' | 'non_spp'
    public string $matrixViewStyle = 'table'; // 'table' | 'cards'
    public int $perPage = 25;

    // Quick Pay Modal from Matrix
    public bool $showQuickPayModal = false;
    public ?int $quickPayTagihanId = null;
    public ?Tagihan $quickPayTagihan = null;
    public $quickPayNominal = 0;
    public string $quickPayMetode = 'Tunai';
    public string $quickPayTanggal = '';
    public string $quickPayCatatan = '';

    public function setMatrixViewStyle(string $style): void
    {
        $this->matrixViewStyle = $style;
    }

    // SPP 6-Month Period Switcher
    public string $sppPeriode = 'ganjil'; // 'ganjil' | 'genap' | 'terakhir'

    public function setSppPeriode(string $periode): void
    {
        $this->sppPeriode = $periode;
    }

    public function getSppMatrixMonths(): array
    {
        if ($this->sppPeriode === 'ganjil') {
            return ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        }

        if ($this->sppPeriode === 'genap') {
            return ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];
        }

        // Default 'terakhir': 6 bulan s/d bulan berjalan
        $calendarMonths = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $currentMonthName = \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM');
        $currIdx = array_search($currentMonthName, $calendarMonths);
        if ($currIdx === false) {
            $currIdx = 8; // September
        }

        $result = [];
        for ($i = 5; $i >= 0; $i--) {
            $idx = ($currIdx - $i + 12) % 12;
            $result[] = $calendarMonths[$idx];
        }

        return $result;
    }

    public function openQuickPay(int $tagihanId): void
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $tagihan = Tagihan::with(['siswa.user', 'jenisTagihan'])->find($tagihanId);
        if (!$tagihan) return;

        $this->quickPayTagihanId = $tagihan->id;
        $this->quickPayTagihan = $tagihan;
        $sisa = max(0, floatval($tagihan->nominal) - floatval($tagihan->total_dibayar));
        $this->quickPayNominal = $sisa;
        $this->quickPayMetode = 'Tunai';
        $this->quickPayTanggal = date('Y-m-d');
        $this->quickPayCatatan = '';
        $this->showQuickPayModal = true;
    }

    public function closeQuickPay(): void
    {
        $this->showQuickPayModal = false;
        $this->quickPayTagihanId = null;
        $this->quickPayTagihan = null;
        $this->resetValidation(['quickPayNominal', 'quickPayMetode', 'quickPayTanggal']);
    }

    public function saveQuickPay(): void
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->sanitizeCurrencies(['quickPayNominal']);

        $this->validate([
            'quickPayTagihanId' => 'required|exists:tagihan,id',
            'quickPayNominal' => 'required|numeric|min:1',
            'quickPayMetode' => 'required|string|in:Tunai,Transfer Bank,Deposit,Beasiswa',
            'quickPayTanggal' => 'required|date',
        ]);

        $tagihan = Tagihan::findOrFail($this->quickPayTagihanId);
        $sisaTunggakan = max(0, floatval($tagihan->nominal) - floatval($tagihan->total_dibayar));

        if ($this->quickPayNominal > $sisaTunggakan) {
            $this->addError('quickPayNominal', 'Nominal bayar tidak boleh melebihi sisa tagihan (Rp ' . number_format($sisaTunggakan, 0, ',', '.') . ').');
            return;
        }

        \DB::transaction(function () use ($tagihan) {
            $tagihanRow = Tagihan::lockForUpdate()->find($tagihan->id);
            if (!$tagihanRow) return;

            $noResi = 'KW-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            Pembayaran::create([
                'no_resi' => $noResi,
                'tagihan_id' => $tagihanRow->id,
                'tanggal_bayar' => $this->quickPayTanggal,
                'nominal_dibayar' => $this->quickPayNominal,
                'kelebihan_bayar' => 0,
                'metode_bayar' => $this->quickPayMetode,
                'is_void' => false,
                'petugas_id' => auth()->id(),
            ]);

            $newPaid = floatval($tagihanRow->total_dibayar) + floatval($this->quickPayNominal);
            $status = ($newPaid >= floatval($tagihanRow->nominal)) ? 'lunas' : 'sebagian';

            $tagihanRow->update([
                'total_dibayar' => $newPaid,
                'status' => $status,
            ]);
        });

        session()->flash('success', "Pembayaran sebesar Rp " . number_format($this->quickPayNominal, 0, ',', '.') . " berhasil dicatat.");
        $this->closeQuickPay();
    }

    public function quickCreateTagihanForMonth(int $jenisTagihanId, string $bulan): void
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->openCreateModal();
        $this->jenis_tagihan_id = $jenisTagihanId;
        $this->bulan = $bulan;
        $this->periodeTipe = 'single';

        $jt = JenisTagihan::find($jenisTagihanId);
        if ($jt) {
            $this->nominal = floatval($jt->default_nominal ?? $jt->nominal_default ?? 0.00);
        }

        $activeTA = TahunAjaran::where('status_aktif', true)->first();
        $this->jatuh_tempo = $this->calculateDueDateForMonth($bulan, null, $activeTA?->nama);
        $this->showCreateModal = true;
    }

    // Edit Tagihan Modal
    public bool $showEditModal = false;
    public ?int $editingTagihanId = null;
    public ?int $edit_jenis_tagihan_id = null;
    public string $edit_bulan = 'Juli';
    public $edit_nominal = 0.00;
    public string $edit_jatuh_tempo = '';
    public float $edit_total_dibayar = 0.00;
    public string $edit_alasan = '';
    public string $delete_alasan = '';

    // Option Lists
    public array $jenisTagihans = [];
    public array $tahunAjarans = [];
    public array $bulanOptions = [
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Semester Ganjil', 'Semester Genap', 'Tahunan'
    ];
    public array $standardMonths = [
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'
    ];

    public function mount(int $siswaId)
    {
        $this->siswaId = $siswaId;
        $this->siswa = Siswa::with(['user', 'kelas'])->findOrFail($siswaId);

        $this->jenisTagihans = JenisTagihan::where('nama', 'not like', '%Infaq%')
            ->where('nama', 'not like', '%Sedekah%')
            ->where('nama', 'not like', '%Donasi%')
            ->orderBy('nama')
            ->get()
            ->toArray();

        $this->tahunAjarans = TahunAjaran::orderBy('id', 'desc')->get()->toArray();
        $this->jatuh_tempo = date('Y-m-10');
    }

    public function isFounder(): bool
    {
        $role = auth()->user()->role->nama ?? '';
        return in_array($role, ['super_admin', 'founder']);
    }

    public function isFinanceOrAdmin(): bool
    {
        $role = auth()->user()->role->nama ?? '';
        return in_array($role, ['super_admin', 'founder', 'finance']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selectAllTagihan = false;
    }

    public function updatingFilterBulan()
    {
        $this->resetPage();
        $this->selectAllTagihan = false;
    }

    public function updatingFilterJenis()
    {
        $this->resetPage();
        $this->selectAllTagihan = false;
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
        $this->selectAllTagihan = false;
    }

    public function updatingFilterTahunAjaran()
    {
        $this->resetPage();
        $this->selectAllTagihan = false;
    }

    public function setCategoryTab(string $tab)
    {
        $this->activeCategoryTab = $tab;
        $this->resetPage();
        $this->selectAllTagihan = false;
    }

    public function updatingPerPage()
    {
        $this->resetPage();
        $this->selectAllTagihan = false;
    }

    public function resetFilters()
    {
        $this->reset(['filterBulan', 'filterJenis', 'filterStatus', 'filterTahunAjaran', 'search']);
        $this->activeCategoryTab = 'all';
        $this->resetPage();
        $this->resetTagihanSelection();
    }

    public function updatingSearchBayar()
    {
        $this->resetPage('pembayaranPage');
    }

    public function updatingFilterBayarBulan()
    {
        $this->resetPage('pembayaranPage');
    }

    public function updatingFilterBayarJenis()
    {
        $this->resetPage('pembayaranPage');
    }

    public function updatingFilterBayarMetode()
    {
        $this->resetPage('pembayaranPage');
    }

    public function updatingFilterBayarTahunAjaran()
    {
        $this->resetPage('pembayaranPage');
    }

    public function resetBayarFilters()
    {
        $this->reset(['searchBayar', 'filterBayarBulan', 'filterBayarJenis', 'filterBayarMetode', 'filterBayarTahunAjaran']);
        $this->resetPage('pembayaranPage');
    }

    public function openCreateModal()
    {
        if (auth()->user()->role?->nama === 'super_admin_2') {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->resetValidation();
        $this->reset(['nominal']);
        $this->periodeTipe = 'single';
        $this->bulan_mulai = 'Juli';
        $this->bulan_selesai = 'Desember';
        $this->jenis_tagihan_id = $this->jenisTagihans[0]['id'] ?? null;
        $this->bulan = 'Juli';
        $this->jatuh_tempo = date('Y-m-10');
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function updatedJenisTagihanId($val)
    {
        if ($val) {
            $jt = JenisTagihan::find($val);
            if ($jt) {
                $this->nominal = (float) ($jt->default_nominal ?? $jt->nominal_default ?? 0.00);
                if (in_array($jt->kategori, ['one_time', 'semester', 'per_6_bulan', 'tahunan'])) {
                    $this->periodeTipe = 'single';
                }
            }
        }
    }

    public function setSingleMonthPreset(string $month)
    {
        $this->bulan = $month;
        $this->periodeTipe = 'single';
        $activeTA = TahunAjaran::where('status_aktif', true)->first();
        $this->jatuh_tempo = $this->calculateDueDateForMonth($month, null, $activeTA?->nama);
    }

    public function updatedBulan($val)
    {
        if (in_array($this->periodeTipe, ['single', 'one_time'])) {
            $activeTA = TahunAjaran::where('status_aktif', true)->first();
            $this->jatuh_tempo = $this->calculateDueDateForMonth($val, null, $activeTA?->nama);
        }
    }

    public function setPresetRange(string $start, string $end)
    {
        $this->bulan_mulai = $start;
        $this->bulan_selesai = $end;
    }

    public function getMonthsBetween(string $start, string $end): array
    {
        $form = new \App\Livewire\Forms\Finance\ReleaseTagihanForm($this, 'releaseForm');
        return $form->getMonthsBetween($start, $end);
    }

    public function getTargetMonths(): array
    {
        $form = new \App\Livewire\Forms\Finance\ReleaseTagihanForm($this, 'releaseForm');
        $form->periodeTipe = $this->periodeTipe;
        $form->bulan = $this->bulan;
        $form->bulan_mulai = $this->bulan_mulai;
        $form->bulan_selesai = $this->bulan_selesai;
        return $form->getTargetMonths();
    }

    protected function calculateDueDateForMonth(string $monthName, ?string $baseDueDate = null, ?string $tahunAjaranNama = null): string
    {
        $form = new \App\Livewire\Forms\Finance\ReleaseTagihanForm($this, 'releaseForm');
        $form->periodeTipe = $this->periodeTipe;
        return $form->calculateDueDateForMonth($monthName, $baseDueDate, $tahunAjaranNama);
    }

    public function createTagihan()
    {
        if (auth()->user()->role?->nama === 'super_admin_2') {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->sanitizeCurrencies(['nominal']);

        $rules = [
            'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'nominal' => 'required|numeric|min:0',
        ];

        if (in_array($this->periodeTipe, ['single', 'one_time'])) {
            $rules['bulan'] = 'required|string|max:50';
        } elseif ($this->periodeTipe === 'custom_range') {
            $rules['bulan_mulai'] = 'required|string|in:' . implode(',', $this->standardMonths);
            $rules['bulan_selesai'] = 'required|string|in:' . implode(',', $this->standardMonths);
        }

        $this->validate($rules);

        $activeTA = TahunAjaran::where('status_aktif', true)->first();
        if (!$activeTA) {
            session()->flash('error', 'Tidak ada tahun ajaran aktif.');
            return;
        }

        $targetMonths = $this->getTargetMonths();
        $createdCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($activeTA, $targetMonths, &$createdCount, &$skippedCount) {
            $status = ($this->nominal <= 0) ? 'lunas' : 'belum_bayar';

            foreach ($targetMonths as $m) {
                $exists = Tagihan::where('siswa_id', $this->siswaId)
                    ->where('tahun_ajaran_id', $activeTA->id)
                    ->where('jenis_tagihan_id', $this->jenis_tagihan_id)
                    ->where('bulan', $m)
                    ->exists();

                if (!$exists) {
                    $monthDueDate = $this->calculateDueDateForMonth($m, $this->jatuh_tempo, $activeTA->nama);
                    Tagihan::create([
                        'siswa_id' => $this->siswaId,
                        'tahun_ajaran_id' => $activeTA->id,
                        'jenis_tagihan_id' => $this->jenis_tagihan_id,
                        'bulan' => $m,
                        'nominal' => $this->nominal,
                        'total_dibayar' => 0.00,
                        'status' => $status,
                        'jatuh_tempo' => $monthDueDate,
                    ]);
                    $createdCount++;
                } else {
                    $skippedCount++;
                }
            }
        });

        if ($createdCount === 0 && $skippedCount > 0) {
            session()->flash('error', 'Tagihan untuk jenis dan periode terpilih sudah pernah dibuat sebelumnya.');
            return;
        }

        $this->closeCreateModal();

        if (count($targetMonths) > 1) {
            $rangeLabel = ($this->periodeTipe === 'custom_range') 
                ? "{$this->bulan_mulai} - {$this->bulan_selesai}" 
                : ($this->periodeTipe === 'full_year_jan_des' ? 'Januari - Desember' : 'Juli - Juni');
            $msg = "Berhasil menerbitkan {$createdCount} tagihan ({$rangeLabel}) untuk siswa ini" . ($skippedCount > 0 ? " ({$skippedCount} bulan dilewati karena sudah ada)." : ".");
        } else {
            $singleMonth = $targetMonths[0] ?? $this->bulan;
            $msg = "Tagihan baru berhasil ditambahkan untuk siswa ini ({$singleMonth})" . ($this->nominal <= 0 ? " (Nominal Rp 0 - Otomatis Lunas)." : ".");
        }

        session()->flash('success', $msg);
    }

    public function openEditModal(int $tagihanId)
    {
        if (auth()->user()->role?->nama === 'super_admin_2') {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->resetValidation();
        $t = Tagihan::findOrFail($tagihanId);
        $this->editingTagihanId = $t->id;
        $this->edit_jenis_tagihan_id = $t->jenis_tagihan_id;
        $this->edit_bulan = $t->bulan;
        $this->edit_nominal = (float) $t->nominal;
        $this->edit_jatuh_tempo = $t->jatuh_tempo ? date('Y-m-d', strtotime($t->jatuh_tempo)) : '';
        $this->edit_total_dibayar = (float) $t->total_dibayar;
        $this->edit_alasan = '';
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingTagihanId = null;
        $this->edit_alasan = '';
        $this->resetValidation();
    }

    public function updateTagihan()
    {
        if (auth()->user()->role?->nama === 'super_admin_2') {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $userRole = auth()->user()->role->nama ?? '';
        $this->sanitizeCurrencies(['edit_nominal']);
        $rules = [
            'edit_jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'edit_bulan' => 'required|string|max:50',
            'edit_nominal' => 'required|numeric|min:' . ($this->edit_total_dibayar > 0 ? $this->edit_total_dibayar : 0),
            'edit_jatuh_tempo' => 'required|date',
        ];

        if ($userRole === 'finance') {
            $rules['edit_alasan'] = 'required|string|min:5|max:500';
        }

        $this->validate($rules, [
            'edit_nominal.min' => 'Nominal tagihan tidak boleh lebih kecil dari jumlah yang sudah dibayar (Rp ' . number_format($this->edit_total_dibayar, 0, ',', '.') . ').',
            'edit_alasan.required' => 'Alasan perubahan wajib diisi untuk persetujuan Super Admin atau Super Admin 2.',
        ]);

        $t = Tagihan::findOrFail($this->editingTagihanId);

        // If finance, submit approval request
        if ($userRole === 'finance') {
            \App\Services\FinancialApprovalService::createRequest(
                auth()->user(),
                'edit',
                'tagihan',
                $t,
                [
                    'jenis_tagihan_id' => $this->edit_jenis_tagihan_id,
                    'bulan' => $this->edit_bulan,
                    'nominal' => $this->edit_nominal,
                    'jatuh_tempo' => $this->edit_jatuh_tempo,
                ],
                $this->edit_alasan,
                "Edit Tagihan: " . ($this->siswa->user->nama ?? 'Siswa') . " - {$this->edit_bulan} (Rp " . number_format($t->nominal, 0, ',', '.') . " -> Rp " . number_format($this->edit_nominal, 0, ',', '.') . ")"
            );

            $this->closeEditModal();
            session()->flash('success', 'Permohonan perubahan tagihan telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.');
            return;
        }

        $newStatus = 'belum_bayar';
        if ($this->edit_nominal <= 0 || $t->total_dibayar >= $this->edit_nominal) {
            $newStatus = 'lunas';
        } elseif ($t->total_dibayar > 0) {
            $newStatus = 'sebagian';
        }

        $t->update([
            'jenis_tagihan_id' => $this->edit_jenis_tagihan_id,
            'bulan' => $this->edit_bulan,
            'nominal' => $this->edit_nominal,
            'jatuh_tempo' => $this->edit_jatuh_tempo,
            'status' => $newStatus,
        ]);

        $this->closeEditModal();
        session()->flash('success', 'Data tagihan berhasil diperbarui.');
    }

    public function deleteTagihan(int $id, ?string $alasan = null)
    {
        if (auth()->user()->role?->nama === 'super_admin_2') {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        if (!$this->isFinanceOrAdmin()) {
            session()->flash('error', 'Akses Ditolak: Anda tidak memiliki wewenang untuk menghapus tagihan.');
            return;
        }

        $t = Tagihan::with(['pembayarans', 'siswa'])->findOrFail($id);
        $userRole = auth()->user()->role->nama ?? '';

        if ($userRole === 'finance') {
            $alreadyPending = \App\Models\ApprovalKeuangan::where('model_type', Tagihan::class)
                ->where('model_id', $t->id)
                ->where('status', 'menunggu')
                ->where('tipe_aksi', 'hapus')
                ->exists();

            if ($alreadyPending) {
                session()->flash('warning', 'Permohonan penghapusan tagihan ini sedang menunggu persetujuan Super Admin.');
                return;
            }

            $reason = $alasan ?: ($this->delete_alasan ?: 'Penghapusan tagihan diajukan oleh staf keuangan');
            \App\Services\FinancialApprovalService::createRequest(
                auth()->user(),
                'hapus',
                'tagihan',
                $t,
                null,
                $reason,
                "Hapus Tagihan: " . ($this->siswa->user->nama ?? 'Siswa') . " - {$t->bulan} (Rp " . number_format($t->nominal, 0, ',', '.') . ")"
            );

            session()->flash('success', 'Permohonan penghapusan tagihan telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.');
            return;
        }

        app(\App\Actions\Finance\DeleteTagihanAction::class)->execute($t, $this->siswaId);

        session()->flash('success', 'Data tagihan berhasil dihapus.');
    }

    public function deletePembayaran(int $pembayaranId, ?string $alasan = null)
    {
        if (auth()->user()->role?->nama === 'super_admin_2') {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        if (!$this->isFinanceOrAdmin()) {
            session()->flash('error', 'Akses Ditolak: Anda tidak memiliki izin untuk membatalkan riwayat pembayaran.');
            return;
        }

        $pembayaran = Pembayaran::with('tagihan')->findOrFail($pembayaranId);
        $userRole = auth()->user()->role->nama ?? '';

        if ($userRole === 'finance') {
            $reason = $alasan ?: ($this->delete_alasan ?: 'Pembatalan pembayaran diajukan oleh staf keuangan');
            \App\Services\FinancialApprovalService::createRequest(
                auth()->user(),
                'hapus',
                'pembayaran',
                $pembayaran,
                null,
                $reason,
                "Hapus Pembayaran: {$pembayaran->no_resi} - " . ($this->siswa->user->nama ?? 'Siswa') . " (Rp " . number_format($pembayaran->nominal_dibayar, 0, ',', '.') . ")"
            );

            session()->flash('success', 'Permohonan pembatalan pembayaran telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.');
            return;
        }

        DB::transaction(function () use ($pembayaranId) {
            $pembayaran = Pembayaran::with('tagihan')->findOrFail($pembayaranId);
            $tagihan = $pembayaran->tagihan;
            $siswa = $this->siswa ?: Siswa::find($this->siswaId);

            $nominalDibayar = floatval($pembayaran->nominal_dibayar);
            $kelebihan = floatval($pembayaran->kelebihan_bayar);
            $metode = $pembayaran->metode_bayar;
            $noResi = $pembayaran->no_resi ?: ('RES-' . str_pad($pembayaran->id, 5, '0', STR_PAD_LEFT));

            // 1. Rollback deposit if applicable
            if ($siswa) {
                // If paid using Deposit, return the deducted amount back to student deposit
                if (strtolower($metode) === 'deposit') {
                    $siswa->increment('saldo_deposit', $nominalDibayar);
                }
                // If there was excess payment added to deposit, deduct it back
                if ($kelebihan > 0) {
                    $currentDeposit = floatval($siswa->saldo_deposit);
                    $siswa->decrement('saldo_deposit', min($currentDeposit, $kelebihan));
                }
            }

            // 2. Mark void and delete payment record
            $pembayaran->update(['is_void' => true]);
            $pembayaran->delete();

            // 3. Recalculate tagihan total paid and status
            if ($tagihan) {
                $remainingPaid = floatval($tagihan->pembayarans()->sum('nominal_dibayar'));
                $tagihanNominal = floatval($tagihan->nominal);

                $newStatus = 'belum_bayar';
                if ($tagihanNominal <= 0 || $remainingPaid >= $tagihanNominal) {
                    $newStatus = 'lunas';
                } elseif ($remainingPaid > 0) {
                    $newStatus = 'sebagian';
                }

                $tagihan->update([
                    'total_dibayar' => $remainingPaid,
                    'status' => $newStatus,
                ]);
            }

            session()->flash('success', "Riwayat pembayaran ({$noResi}) berhasil dihapus dan saldo tagihan telah disesuaikan.");
        });
    }

    public function updatedSelectAllPembayaran($value)
    {
        if ($value) {
            $this->selectedTagihanIds = [];
            $this->selectAllTagihan = false;
            $this->selectedPembayaranIds = $this->getPembayaranQuery()
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();
        } else {
            $this->selectedPembayaranIds = [];
        }
    }

    public function updatedSelectedPembayaranIds()
    {
        if (!empty($this->selectedPembayaranIds)) {
            $this->selectedTagihanIds = [];
            $this->selectAllTagihan = false;
        }
    }

    public function resetPembayaranSelection()
    {
        $this->selectedPembayaranIds = [];
        $this->selectAllPembayaran = false;
    }

    public function updatedSelectAllTagihan($value)
    {
        if ($value) {
            $this->selectedPembayaranIds = [];
            $this->selectAllPembayaran = false;

            $pendingIds = \App\Models\ApprovalKeuangan::where('model_type', Tagihan::class)
                ->where('status', 'menunggu')
                ->where('tipe_aksi', 'hapus')
                ->pluck('model_id')
                ->toArray();

            $this->selectedTagihanIds = $this->getTagihanQuery()
                ->whereNotIn('id', $pendingIds)
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();
        } else {
            $this->selectedTagihanIds = [];
        }
    }

    public function updatedSelectedTagihanIds()
    {
        if (!empty($this->selectedTagihanIds)) {
            $this->selectedPembayaranIds = [];
            $this->selectAllPembayaran = false;
        }
    }

    public function resetTagihanSelection()
    {
        $this->selectedTagihanIds = [];
        $this->selectAllTagihan = false;
    }

    public function bulkDeleteTagihan(?string $alasan = null)
    {
        if (auth()->user()->isSuperAdmin2() || auth()->user()->role?->nama === 'super_admin_2') {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            $this->dispatch('show-alert', [
                'title' => 'Akses Ditolak',
                'message' => 'Super Admin 2 hanya memiliki hak akses Lihat Saja.',
                'type' => 'danger',
            ]);
            return;
        }

        if (!$this->isFinanceOrAdmin()) {
            session()->flash('error', 'Akses Ditolak: Anda tidak memiliki wewenang untuk menghapus tagihan.');
            $this->dispatch('show-alert', [
                'title' => 'Akses Ditolak',
                'message' => 'Anda tidak memiliki wewenang untuk menghapus tagihan.',
                'type' => 'danger',
            ]);
            return;
        }

        if (empty($this->selectedTagihanIds)) {
            session()->flash('warning', 'Pilih minimal satu tagihan untuk dihapus.');
            return;
        }

        $tagihans = Tagihan::with(['pembayarans', 'siswa.user', 'jenisTagihan'])
            ->where('siswa_id', $this->siswaId)
            ->whereIn('id', $this->selectedTagihanIds)
            ->get();

        if ($tagihans->isEmpty()) {
            $this->resetTagihanSelection();
            return;
        }

        $userRole = auth()->user()->role->nama ?? '';

        if ($userRole === 'finance') {
            $reason = $alasan ?: ($this->delete_alasan ?: 'Penghapusan tagihan massal diajukan oleh staf keuangan');
            $submittedCount = 0;
            $pendingCount = 0;
            $skippedPaidCount = 0;

            foreach ($tagihans as $t) {
                if ($t->total_dibayar > 0) {
                    $skippedPaidCount++;
                    continue;
                }

                $alreadyPending = \App\Models\ApprovalKeuangan::where('model_type', Tagihan::class)
                    ->where('model_id', $t->id)
                    ->where('status', 'menunggu')
                    ->where('tipe_aksi', 'hapus')
                    ->exists();

                if ($alreadyPending) {
                    $pendingCount++;
                    continue;
                }

                \App\Services\FinancialApprovalService::createRequest(
                    auth()->user(),
                    'hapus',
                    'tagihan',
                    $t,
                    null,
                    $reason,
                    "Hapus Tagihan: " . ($this->siswa->user->nama ?? 'Siswa') . " : {$t->bulan} (Rp " . number_format($t->nominal, 0, ',', '.') . ")"
                );
                $submittedCount++;
            }

            $this->resetTagihanSelection();

            if ($submittedCount === 0) {
                $msg = 'Tidak ada tagihan yang diajukan untuk dihapus.';
                if ($skippedPaidCount > 0) {
                    $msg .= " {$skippedPaidCount} tagihan dilewati karena sudah memiliki transaksi pembayaran.";
                }
                if ($pendingCount > 0) {
                    $msg .= " {$pendingCount} tagihan sudah dalam status menunggu persetujuan.";
                }
                session()->flash('warning', $msg);
                $this->dispatch('show-alert', [
                    'title' => 'Perhatian',
                    'message' => $msg,
                    'type' => 'warning',
                ]);
                return;
            }

            $msg = "Permohonan penghapusan {$submittedCount} tagihan berhasil diajukan ke Super Admin / Super Admin 2.";
            if ($pendingCount > 0) {
                $msg .= " ({$pendingCount} tagihan dilewati karena sudah menunggu persetujuan).";
            }
            if ($skippedPaidCount > 0) {
                $msg .= " ({$skippedPaidCount} tagihan dilewati karena sudah ada pembayaran).";
            }

            session()->flash('success', $msg);
            $this->dispatch('show-alert', [
                'title' => 'Menunggu Persetujuan',
                'message' => $msg,
                'type' => 'info',
            ]);
            return;
        }

        // Super Admin / Founder direct deletion
        $deletedCount = 0;
        $skippedPaidCount = 0;

        foreach ($tagihans as $t) {
            if ($t->total_dibayar > 0) {
                $skippedPaidCount++;
                continue;
            }

            app(\App\Actions\Finance\DeleteTagihanAction::class)->execute($t, $this->siswaId);
            $deletedCount++;
        }

        $this->resetTagihanSelection();

        if ($deletedCount === 0 && $skippedPaidCount > 0) {
            $msg = "Tidak ada tagihan yang dihapus. {$skippedPaidCount} tagihan terpilih dilewati karena sudah memiliki riwayat pembayaran.";
            session()->flash('warning', $msg);
            $this->dispatch('show-alert', [
                'title' => 'Perhatian',
                'message' => $msg,
                'type' => 'warning',
            ]);
            return;
        }

        $msg = "Berhasil menghapus {$deletedCount} data tagihan.";
        if ($skippedPaidCount > 0) {
            $msg .= " ({$skippedPaidCount} tagihan dilewati karena sudah ada pembayaran).";
        }

        session()->flash('success', $msg);
        $this->dispatch('show-alert', [
            'title' => 'Berhasil',
            'message' => $msg,
            'type' => 'success',
        ]);
    }

    public function bulkDeletePembayaran(?string $alasan = null)
    {
        if (auth()->user()->role?->nama === 'super_admin_2') {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        if (!$this->isFinanceOrAdmin()) {
            session()->flash('error', 'Akses Ditolak: Anda tidak memiliki izin untuk membatalkan riwayat pembayaran.');
            return;
        }

        if (empty($this->selectedPembayaranIds)) {
            session()->flash('warning', 'Pilih minimal satu transaksi pembayaran untuk dibatalkan/dihapus.');
            return;
        }

        $pembayarans = Pembayaran::with('tagihan')
            ->whereHas('tagihan', fn($q) => $q->where('siswa_id', $this->siswaId))
            ->whereIn('id', $this->selectedPembayaranIds)
            ->get();

        if ($pembayarans->isEmpty()) {
            return;
        }

        $userRole = auth()->user()->role->nama ?? '';

        if ($userRole === 'finance') {
            $reason = $alasan ?: ($this->delete_alasan ?: 'Pembatalan pembayaran massal diajukan oleh staf keuangan');
            $submittedCount = 0;
            $pendingCount = 0;

            foreach ($pembayarans as $p) {
                $alreadyPending = \App\Models\ApprovalKeuangan::where('model_type', Pembayaran::class)
                    ->where('model_id', $p->id)
                    ->where('status', 'menunggu')
                    ->where('tipe_aksi', 'hapus')
                    ->exists();

                if ($alreadyPending) {
                    $pendingCount++;
                    continue;
                }

                \App\Services\FinancialApprovalService::createRequest(
                    auth()->user(),
                    'hapus',
                    'pembayaran',
                    $p,
                    null,
                    $reason,
                    "Hapus Pembayaran: {$p->no_resi} - " . ($this->siswa->user->nama ?? 'Siswa') . " (Rp " . number_format($p->nominal_dibayar, 0, ',', '.') . ")"
                );
                $submittedCount++;
            }

            $this->resetPembayaranSelection();

            $msg = "Permohonan pembatalan {$submittedCount} transaksi pembayaran telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.";
            if ($pendingCount > 0) {
                $msg .= " ({$pendingCount} pembayaran dilewati karena sudah dalam proses pengajuan).";
            }

            session()->flash('success', $msg);
            $this->dispatch('show-alert', [
                'title' => 'Menunggu Persetujuan',
                'message' => $msg,
                'type' => 'info',
            ]);
            return;
        }

        // Super Admin / Founder: Langsung Hapus & Rollback
        DB::transaction(function () use ($pembayarans) {
            $siswa = $this->siswa ?: Siswa::find($this->siswaId);
            $affectedTagihanIds = [];

            foreach ($pembayarans as $p) {
                $nominalDibayar = floatval($p->nominal_dibayar);
                $kelebihan = floatval($p->kelebihan_bayar);
                $metode = $p->metode_bayar;

                if ($siswa) {
                    if (strtolower($metode) === 'deposit') {
                        $siswa->increment('saldo_deposit', $nominalDibayar);
                    }
                    if ($kelebihan > 0) {
                        $currentDeposit = floatval($siswa->saldo_deposit);
                        $siswa->decrement('saldo_deposit', min($currentDeposit, $kelebihan));
                    }
                }

                if ($p->tagihan_id) {
                    $affectedTagihanIds[] = $p->tagihan_id;
                }

                $p->update(['is_void' => true]);
                $p->delete();
            }

            // Recalculate affected tagihans
            foreach (array_unique($affectedTagihanIds) as $tagihanId) {
                $tagihan = Tagihan::find($tagihanId);
                if ($tagihan) {
                    $remainingPaid = floatval($tagihan->pembayarans()->sum('nominal_dibayar'));
                    $tagihanNominal = floatval($tagihan->nominal);

                    $newStatus = 'belum_bayar';
                    if ($tagihanNominal <= 0 || $remainingPaid >= $tagihanNominal) {
                        $newStatus = 'lunas';
                    } elseif ($remainingPaid > 0) {
                        $newStatus = 'sebagian';
                    }

                    $tagihan->update([
                        'total_dibayar' => $remainingPaid,
                        'status' => $newStatus,
                    ]);
                }
            }
        });

        $count = $pembayarans->count();
        $this->resetPembayaranSelection();

        session()->flash('success', "Berhasil membatalkan dan menghapus {$count} riwayat transaksi pembayaran.");
        $this->dispatch('show-alert', [
            'title' => 'Berhasil',
            'message' => "Berhasil membatalkan dan menghapus {$count} riwayat transaksi pembayaran.",
            'type' => 'delete',
        ]);
    }

    public function openBuktiModal(int $pembayaranId)
    {
        $this->resetValidation();
        $this->edit_bukti_foto = null;
        $this->selectedPembayaranId = $pembayaranId;
        $this->selectedPembayaran = Pembayaran::with(['tagihan.jenisTagihan', 'petugas'])->findOrFail($pembayaranId);
        $this->showBuktiModal = true;
    }

    public function closeBuktiModal()
    {
        $this->showBuktiModal = false;
        $this->selectedPembayaranId = null;
        $this->selectedPembayaran = null;
        $this->edit_bukti_foto = null;
        $this->resetValidation();
    }

    public function saveBuktiFoto()
    {
        if (auth()->user()->role?->nama === 'super_admin_2') {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->validate([
            'edit_bukti_foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'edit_bukti_foto.required' => 'Silakan pilih file foto bukti pembayaran terlebih dahulu.',
            'edit_bukti_foto.image' => 'File bukti pembayaran harus berupa foto/gambar.',
            'edit_bukti_foto.mimes' => 'Format foto hanya boleh JPG, JPEG, PNG, atau WEBP.',
            'edit_bukti_foto.max' => 'Ukuran file foto bukti pembayaran maksimal 2MB.',
        ]);

        $pembayaran = Pembayaran::findOrFail($this->selectedPembayaranId);

        // Hapus file bukti lama dari storage jika ada
        if ($pembayaran->bukti_bayar && Storage::disk('public')->exists($pembayaran->bukti_bayar)) {
            Storage::disk('public')->delete($pembayaran->bukti_bayar);
        }

        $path = $this->edit_bukti_foto->store('bukti_pembayaran', 'public');
        $pembayaran->update(['bukti_bayar' => $path]);

        $this->selectedPembayaran = $pembayaran->fresh(['tagihan.jenisTagihan', 'petugas']);
        $this->edit_bukti_foto = null;
        session()->flash('success', 'Foto bukti pembayaran berhasil diperbarui.');
    }

    public function deleteBuktiFoto()
    {
        if (auth()->user()->role?->nama === 'super_admin_2') {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $pembayaran = Pembayaran::findOrFail($this->selectedPembayaranId);

        if ($pembayaran->bukti_bayar && Storage::disk('public')->exists($pembayaran->bukti_bayar)) {
            Storage::disk('public')->delete($pembayaran->bukti_bayar);
        }

        $pembayaran->update(['bukti_bayar' => null]);
        $this->selectedPembayaran = $pembayaran->fresh(['tagihan.jenisTagihan', 'petugas']);
        session()->flash('success', 'Foto bukti pembayaran berhasil dihapus.');
    }

    public function render()
    {
        // Calculate cumulative metrics for this student
        $cutoffDate = now()->endOfMonth()->toDateString();
        $allTagihanQuery = Tagihan::where('siswa_id', $this->siswaId);
        $totalNominal = (clone $allTagihanQuery)->sum('nominal');
        $totalTerbayar = (clone $allTagihanQuery)->sum('total_dibayar');
        $totalSisa = max(0, $totalNominal - $totalTerbayar);

        // Split real overdue arrears vs upcoming future bills
        $totalTunggakan = (float) ((clone $allTagihanQuery)->tunggakan($cutoffDate)
            ->selectRaw('SUM(nominal - total_dibayar) as aggregate')
            ->value('aggregate') ?? 0.0);

        $totalMendatang = (float) ((clone $allTagihanQuery)->mendatang($cutoffDate)
            ->selectRaw('SUM(nominal - total_dibayar) as aggregate')
            ->value('aggregate') ?? 0.0);

        $countLunas = (clone $allTagihanQuery)->where('status', 'lunas')->count();
        $countBelumLunas = (clone $allTagihanQuery)->where('status', '!=', 'lunas')->count();
        $countTunggakan = (clone $allTagihanQuery)->tunggakan($cutoffDate)->count();
        $countMendatang = (clone $allTagihanQuery)->mendatang($cutoffDate)->count();

        // Paginated filtered invoices
        $tagihanQuery = $this->getTagihanQuery()
            ->orderBy('jatuh_tempo', 'desc')
            ->orderBy('id', 'desc');

        $perPageCount = $this->perPage > 0 ? $this->perPage : 100;
        $tagihans = $tagihanQuery->paginate($perPageCount);

        // Category counts
        $countAll = Tagihan::where('siswa_id', $this->siswaId)->count();
        $countSpp = Tagihan::where('siswa_id', $this->siswaId)->whereHas('jenisTagihan', fn($jt) => $jt->where('nama', 'like', '%SPP%'))->count();
        $countNonSpp = Tagihan::where('siswa_id', $this->siswaId)->whereHas('jenisTagihan', fn($jt) => $jt->where('nama', 'not like', '%SPP%'))->count();

        // 12-Month SPP Matrix Computation
        $activeTA = $this->filterTahunAjaran 
            ? TahunAjaran::find($this->filterTahunAjaran) 
            : TahunAjaran::where('status_aktif', true)->first();

        $sppRecords = Tagihan::where('siswa_id', $this->siswaId)
            ->whereHas('jenisTagihan', fn($jt) => $jt->where('nama', 'like', '%SPP%'))
            ->when($activeTA, fn($q) => $q->where('tahun_ajaran_id', $activeTA->id))
            ->get()
            ->keyBy('bulan');

        $monthsSequence = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $sppMatrix = [];
        foreach ($monthsSequence as $m) {
            $item = $sppRecords->get($m);
            $itemStatus = 'unreleased';
            if ($item) {
                if ($item->status === 'lunas') {
                    $itemStatus = 'lunas';
                } elseif ($item->is_mendatang) {
                    $itemStatus = 'mendatang';
                } else {
                    $itemStatus = $item->status;
                }
            }
            $sppMatrix[$m] = [
                'bulan' => $m,
                'has_bill' => !is_null($item),
                'status' => $itemStatus,
                'is_mendatang' => $item ? $item->is_mendatang : false,
                'nominal' => $item ? floatval($item->nominal) : 0,
                'total_dibayar' => $item ? floatval($item->total_dibayar) : 0,
                'sisa' => $item ? max(0, floatval($item->nominal) - floatval($item->total_dibayar)) : 0,
                'id' => $item ? $item->id : null,
            ];
        }

        // Multi-Tagihan Matrix Computation: Sumbu X (Jenis Tagihan) x Sumbu Y (Semua Bulan)
        $jenisTagihanList = JenisTagihan::where('nama', 'not like', '%Infaq%')
            ->where('nama', 'not like', '%Sedekah%')
            ->where('nama', 'not like', '%Donasi%')
            ->orderBy('id')
            ->get();

        $studentBills = Tagihan::where('siswa_id', $this->siswaId)
            ->when($activeTA, fn($q) => $q->where('tahun_ajaran_id', $activeTA->id))
            ->with(['pembayarans', 'jenisTagihan'])
            ->get();

        $hasTahunan = $studentBills->contains(fn($t) => in_array($t->bulan, ['Tahunan', null]));
        $matrixMonths = $this->standardMonths;
        if ($hasTahunan) {
            $matrixMonths[] = 'Tahunan';
        }

        $matrixRows = [];
        $detailGrandNominal = 0.0;
        $detailGrandDibayar = 0.0;
        $detailGrandTunggakan = 0.0;

        foreach ($matrixMonths as $m) {
            $rowBills = [];
            $rowNom = 0.0;
            $rowDib = 0.0;
            $rowSis = 0.0;

            foreach ($jenisTagihanList as $jt) {
                $isNonRutin = ($jt->kategori !== 'rutin') || !str_contains(strtolower($jt->nama), 'spp');
                $t = $studentBills->where('jenis_tagihan_id', $jt->id)->firstWhere('bulan', $m);

                if ($t) {
                    $nom = (float) $t->nominal;
                    $bayar = (float) $t->total_dibayar;
                    $sisa = max(0, $nom - $bayar);
                    $st = ($t->status === 'lunas' || ($nom > 0 && $bayar >= $nom) || $nom == 0)
                        ? 'lunas'
                        : ($bayar > 0 ? 'sebagian' : 'belum_bayar');

                    $rowNom += $nom;
                    $rowDib += $bayar;
                    $rowSis += $sisa;

                    $rowBills[$jt->id] = [
                        'has_tagihan' => true,
                        'nominal' => $nom,
                        'total_dibayar' => $bayar,
                        'sisa' => $sisa,
                        'status' => $st,
                        'id' => $t->id,
                        'is_mendatang' => $t->is_mendatang,
                        'is_one_time_fulfilled' => false,
                        'is_one_time_carried' => false,
                        'original_bulan' => $t->bulan,
                        'original_nominal' => $nom,
                        'terakhir_bayar' => $t->pembayarans->max('tanggal_bayar') ? \Carbon\Carbon::parse($t->pembayarans->max('tanggal_bayar'))->format('d/m/Y') : null,
                    ];
                } elseif ($isNonRutin && ($existingOneTime = $studentBills->where('jenis_tagihan_id', $jt->id)->first())) {
                    $isLunas = ($existingOneTime->status === 'lunas' || ((float)$existingOneTime->nominal > 0 && (float)$existingOneTime->total_dibayar >= (float)$existingOneTime->nominal) || (float)$existingOneTime->nominal == 0);
                    $origNom = (float) $existingOneTime->nominal;
                    $origBayar = (float) $existingOneTime->total_dibayar;
                    $origSisa = max(0, $origNom - $origBayar);
                    $origBulan = $existingOneTime->bulan ?: 'Tahunan';

                    if ($isLunas) {
                        $rowBills[$jt->id] = [
                            'has_tagihan' => true,
                            'nominal' => 0.0,
                            'total_dibayar' => 0.0,
                            'sisa' => 0.0,
                            'status' => 'lunas',
                            'id' => $existingOneTime->id,
                            'is_mendatang' => false,
                            'is_one_time_fulfilled' => true,
                            'is_one_time_carried' => false,
                            'original_bulan' => $origBulan,
                            'original_nominal' => $origNom,
                            'terakhir_bayar' => $existingOneTime->pembayarans->max('tanggal_bayar') ? \Carbon\Carbon::parse($existingOneTime->pembayarans->max('tanggal_bayar'))->format('d/m/Y') : null,
                        ];
                    } else {
                        $billedIdx = array_search($origBulan, $this->standardMonths);
                        $currIdx = array_search($m, $this->standardMonths);
                        $isAfterBilled = ($billedIdx === false) || ($currIdx !== false && $currIdx > $billedIdx);

                        if ($isAfterBilled) {
                            $st = ($origBayar > 0) ? 'sebagian' : 'belum_bayar';
                            $rowBills[$jt->id] = [
                                'has_tagihan' => true,
                                'nominal' => 0.0,
                                'total_dibayar' => 0.0,
                                'sisa' => $origSisa,
                                'status' => $st,
                                'id' => $existingOneTime->id,
                                'is_mendatang' => false,
                                'is_one_time_fulfilled' => false,
                                'is_one_time_carried' => true,
                                'original_bulan' => $origBulan,
                                'original_nominal' => $origNom,
                                'terakhir_bayar' => $existingOneTime->pembayarans->max('tanggal_bayar') ? \Carbon\Carbon::parse($existingOneTime->pembayarans->max('tanggal_bayar'))->format('d/m/Y') : null,
                            ];
                        } else {
                            $rowBills[$jt->id] = [
                                'has_tagihan' => false,
                                'nominal' => 0.0,
                                'total_dibayar' => 0.0,
                                'sisa' => 0.0,
                                'status' => 'tidak_ada',
                                'id' => null,
                                'is_mendatang' => false,
                                'is_one_time_fulfilled' => false,
                                'is_one_time_carried' => false,
                                'original_bulan' => null,
                                'original_nominal' => 0.0,
                                'terakhir_bayar' => null,
                            ];
                        }
                    }
                } else {
                    $rowBills[$jt->id] = [
                        'has_tagihan' => false,
                        'nominal' => 0.0,
                        'total_dibayar' => 0.0,
                        'sisa' => 0.0,
                        'status' => 'tidak_ada',
                        'id' => null,
                        'is_mendatang' => false,
                        'is_one_time_fulfilled' => false,
                        'is_one_time_carried' => false,
                        'original_bulan' => null,
                        'original_nominal' => 0.0,
                        'terakhir_bayar' => null,
                    ];
                }
            }

            $detailGrandNominal += $rowNom;
            $detailGrandDibayar += $rowDib;
            $detailGrandTunggakan += $rowSis;

            $hasAnyLunas = collect($rowBills)->contains(fn($b) => $b['has_tagihan'] && $b['status'] === 'lunas');

            $matrixRows[$m] = [
                'bulan' => $m,
                'bills' => $rowBills,
                'total_nominal' => $rowNom,
                'total_dibayar' => $rowDib,
                'sisa_tunggakan' => $rowSis,
                'status' => ($rowSis > 0) ? 'Ada Tunggakan' : ($rowNom > 0 || $hasAnyLunas ? 'Lunas' : '-'),
            ];
        }

        $matrixFooterPerJenis = [];
        foreach ($jenisTagihanList as $jt) {
            $sumNom = 0.0;
            $sumDib = 0.0;
            $sumSis = 0.0;
            foreach ($matrixMonths as $m) {
                $b = $matrixRows[$m]['bills'][$jt->id];
                if ($b['has_tagihan'] && empty($b['is_one_time_fulfilled']) && empty($b['is_one_time_carried'])) {
                    $sumNom += $b['nominal'];
                    $sumDib += $b['total_dibayar'];
                    $sumSis += $b['sisa'];
                }
            }
            $matrixFooterPerJenis[$jt->id] = [
                'nominal' => $sumNom,
                'dibayar' => $sumDib,
                'sisa' => $sumSis,
            ];
        }

        $detailMatrixData = [
            'months_rows' => $matrixRows,
            'footer_per_jenis' => $matrixFooterPerJenis,
            'grand_nominal' => $detailGrandNominal,
            'grand_dibayar' => $detailGrandDibayar,
            'grand_tunggakan' => $detailGrandTunggakan,
        ];

        // Data for MATRIKS 6 BULAN SPP + KATEGORI NON-SPP PADA SUMBU X
        $sppMatrixMonths = $this->getSppMatrixMonths();
        $sppJenis = $jenisTagihanList->first(fn($jt) => str_contains(strtolower($jt->nama), 'spp') || ($jt->kategori ?? '') === 'rutin') 
            ?: $jenisTagihanList->first();
        $nonSppJenisList = $jenisTagihanList->filter(fn($jt) => !str_contains(strtolower($jt->nama), 'spp'));

        // 1. Sel Data 6 Bulan SPP untuk Santri Ini
        $spp6MonthsData = [];
        foreach ($sppMatrixMonths as $m) {
            $sppBill = $studentBills->first(function ($t) use ($m) {
                return $t->bulan === $m && 
                       (str_contains(strtolower($t->jenisTagihan->nama ?? ''), 'spp') || ($t->jenisTagihan->kategori ?? '') === 'rutin');
            });

            if ($sppBill) {
                $nom = (float) $sppBill->nominal;
                $bayar = (float) $sppBill->total_dibayar;
                $sisa = max(0, $nom - $bayar);
                $st = ($sppBill->status === 'lunas' || ($nom > 0 && $bayar >= $nom) || $nom == 0)
                    ? 'lunas'
                    : ($bayar > 0 ? 'sebagian' : ($sppBill->is_mendatang ? 'mendatang' : 'belum_bayar'));

                $spp6MonthsData[$m] = [
                    'has_tagihan' => true,
                    'id' => $sppBill->id,
                    'nominal' => $nom,
                    'total_dibayar' => $bayar,
                    'sisa' => $sisa,
                    'status' => $st,
                    'is_mendatang' => (bool) $sppBill->is_mendatang,
                    'original_bulan' => $sppBill->bulan,
                    'original_nominal' => $nom,
                    'terakhir_bayar' => $sppBill->pembayarans->max('tanggal_bayar') ? \Carbon\Carbon::parse($sppBill->pembayarans->max('tanggal_bayar'))->format('d/m/Y') : null,
                ];
            } else {
                $spp6MonthsData[$m] = [
                    'has_tagihan' => false,
                    'id' => null,
                    'nominal' => 0.0,
                    'total_dibayar' => 0.0,
                    'sisa' => 0.0,
                    'status' => 'tidak_ada',
                    'is_mendatang' => false,
                    'original_bulan' => null,
                    'original_nominal' => 0.0,
                    'terakhir_bayar' => null,
                ];
            }
        }

        // 2. Sel Data Kategori Non-SPP untuk Santri Ini
        $nonSppBillsData = [];
        foreach ($nonSppJenisList as $jt) {
            $bills = $studentBills->where('jenis_tagihan_id', $jt->id);
            if ($bills->isNotEmpty()) {
                $nom = (float) $bills->sum('nominal');
                $bayar = (float) $bills->sum('total_dibayar');
                $sisa = max(0, $nom - $bayar);
                $unpaidBill = $bills->first(fn($b) => $b->status !== 'lunas' && ($b->nominal - $b->total_dibayar) > 0) ?: $bills->first();

                $isLunas = ($nom == 0 || $sisa <= 0 || $bills->every(fn($b) => $b->status === 'lunas'));
                $st = $isLunas ? 'lunas' : ($bayar > 0 ? 'sebagian' : 'belum_bayar');

                $firstBill = $bills->first();
                $origBulan = $firstBill?->bulan ?: 'Tahunan';
                $origNom = (float) ($firstBill?->nominal ?? 0);

                $nonSppBillsData[$jt->id] = [
                    'has_tagihan' => true,
                    'id' => $unpaidBill?->id ?: $firstBill?->id,
                    'nominal' => $nom,
                    'total_dibayar' => $bayar,
                    'sisa' => $sisa,
                    'status' => $st,
                    'is_one_time_fulfilled' => $isLunas,
                    'original_bulan' => $origBulan,
                    'original_nominal' => $origNom,
                    'terakhir_bayar' => $bills->flatMap(fn($b) => $b->pembayarans)->max('tanggal_bayar') ? \Carbon\Carbon::parse($bills->flatMap(fn($b) => $b->pembayarans)->max('tanggal_bayar'))->format('d/m/Y') : null,
                ];
            } else {
                $nonSppBillsData[$jt->id] = [
                    'has_tagihan' => false,
                    'id' => null,
                    'nominal' => 0.0,
                    'total_dibayar' => 0.0,
                    'sisa' => 0.0,
                    'status' => 'tidak_ada',
                    'is_one_time_fulfilled' => false,
                    'original_bulan' => null,
                    'original_nominal' => 0.0,
                    'terakhir_bayar' => null,
                ];
            }
        }

        // 3. Akumulasi Subtotal & Grand Total Matriks 6 Bulan
        $sppSummary = [
            'nominal' => collect($spp6MonthsData)->sum('nominal'),
            'dibayar' => collect($spp6MonthsData)->sum('total_dibayar'),
            'sisa' => collect($spp6MonthsData)->sum('sisa'),
        ];
        $nonSppSummary = [
            'nominal' => collect($nonSppBillsData)->sum('nominal'),
            'dibayar' => collect($nonSppBillsData)->sum('total_dibayar'),
            'sisa' => collect($nonSppBillsData)->sum('sisa'),
        ];
        $matrix6BulanSummary = [
            'spp' => $sppSummary,
            'non_spp' => $nonSppSummary,
            'grand_nominal' => $sppSummary['nominal'] + $nonSppSummary['nominal'],
            'grand_dibayar' => $sppSummary['dibayar'] + $nonSppSummary['dibayar'],
            'grand_sisa' => $sppSummary['sisa'] + $nonSppSummary['sisa'],
        ];

        $recentPayments = $this->getPembayaranQuery()
            ->orderBy('tanggal_bayar', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'pembayaranPage');

        $pendingApprovalTagihanIds = \App\Models\ApprovalKeuangan::where('model_type', Tagihan::class)
            ->where('status', 'menunggu')
            ->where('tipe_aksi', 'hapus')
            ->pluck('model_id')
            ->toArray();

        $pendingApprovalPembayaranIds = \App\Models\ApprovalKeuangan::where('model_type', Pembayaran::class)
            ->where('status', 'menunggu')
            ->where('tipe_aksi', 'hapus')
            ->pluck('model_id')
            ->toArray();

        return view('livewire.finance.detail-tagihan-siswa', [
            'tagihans' => $tagihans,
            'pendingApprovalTagihanIds' => $pendingApprovalTagihanIds,
            'pendingApprovalPembayaranIds' => $pendingApprovalPembayaranIds,
            'recentPayments' => $recentPayments,
            'totalNominal' => $totalNominal,
            'totalTerbayar' => $totalTerbayar,
            'totalSisa' => $totalSisa,
            'totalTunggakan' => $totalTunggakan,
            'totalMendatang' => $totalMendatang,
            'countLunas' => $countLunas,
            'countBelumLunas' => $countBelumLunas,
            'countTunggakan' => $countTunggakan,
            'countMendatang' => $countMendatang,
            'countAll' => $countAll,
            'countSpp' => $countSpp,
            'countNonSpp' => $countNonSpp,
            'sppMatrix' => $sppMatrix,
            'jenisTagihanList' => $jenisTagihanList,
            'detailMatrixData' => $detailMatrixData,
            'sppMatrixMonths' => $sppMatrixMonths,
            'spp6MonthsData' => $spp6MonthsData,
            'nonSppJenisList' => $nonSppJenisList,
            'nonSppBillsData' => $nonSppBillsData,
            'sppJenis' => $sppJenis,
            'matrix6BulanSummary' => $matrix6BulanSummary,
            'activeTAName' => $activeTA->nama ?? '-',
        ])->layout('components.layouts.app', ['title' => 'Rincian Tagihan - ' . ($this->siswa->user->nama ?? 'Siswa')]);
    }

    public function getTagihanQuery()
    {
        return Tagihan::with(['jenisTagihan', 'tahunAjaran', 'pembayarans'])
            ->where('siswa_id', $this->siswaId)
            ->when($this->activeCategoryTab === 'spp', function ($q) {
                $q->whereHas('jenisTagihan', fn($jt) => $jt->where('nama', 'like', '%SPP%'));
            })
            ->when($this->activeCategoryTab === 'non_spp', function ($q) {
                $q->whereHas('jenisTagihan', fn($jt) => $jt->where('nama', 'not like', '%SPP%'));
            })
            ->when($this->filterBulan, function ($q) {
                $q->where('bulan', $this->filterBulan);
            })
            ->when($this->filterJenis, function ($q) {
                $q->where('jenis_tagihan_id', $this->filterJenis);
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterTahunAjaran, function ($q) {
                $q->where('tahun_ajaran_id', $this->filterTahunAjaran);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->whereHas('jenisTagihan', function ($jt) {
                        $jt->where('nama', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('bulan', 'like', '%' . $this->search . '%');
                });
            });
    }

    public function getPembayaranQuery()
    {
        $pembayaranQuery = Pembayaran::with(['tagihan.jenisTagihan', 'tagihan.tahunAjaran', 'petugas'])
            ->whereHas('tagihan', function ($q) {
                $q->where('siswa_id', $this->siswaId);
            });

        if ($this->filterBayarBulan !== '') {
            $pembayaranQuery->whereHas('tagihan', function ($q) {
                $q->where('bulan', $this->filterBayarBulan);
            });
        }

        if ($this->filterBayarJenis) {
            $pembayaranQuery->whereHas('tagihan', function ($q) {
                $q->where('jenis_tagihan_id', $this->filterBayarJenis);
            });
        }

        if ($this->filterBayarMetode !== '') {
            $pembayaranQuery->where('metode_bayar', $this->filterBayarMetode);
        }

        if ($this->filterBayarTahunAjaran) {
            $pembayaranQuery->whereHas('tagihan', function ($q) {
                $q->where('tahun_ajaran_id', $this->filterBayarTahunAjaran);
            });
        }

        if ($this->searchBayar !== '') {
            $pembayaranQuery->where(function ($q) {
                $q->where('no_resi', 'like', '%' . $this->searchBayar . '%')
                  ->orWhere('metode_bayar', 'like', '%' . $this->searchBayar . '%')
                  ->orWhereHas('tagihan.jenisTagihan', function ($jt) {
                      $jt->where('nama', 'like', '%' . $this->searchBayar . '%');
                  })
                  ->orWhereHas('tagihan', function ($t) {
                      $t->where('bulan', 'like', '%' . $this->searchBayar . '%');
                  })
                  ->orWhereHas('petugas', function ($p) {
                      $p->where('nama', 'like', '%' . $this->searchBayar . '%');
                  });
            });
        }

        return $pembayaranQuery;
    }
}
