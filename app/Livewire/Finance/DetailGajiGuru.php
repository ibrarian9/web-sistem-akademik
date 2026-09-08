<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\GajiGuru;
use App\Models\Guru;
use App\Models\Peminjaman;
use App\Models\Pengeluaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DetailGajiGuru extends Component
{
    use WithPagination, WithFileUploads;

    public int $guruId;
    public ?Guru $guru = null;

    // Filters
    public string $filterTahun = '';
    public string $filterBulan = '';
    public string $filterStatus = '';
    public string $search = '';

    // Bulk selection
    public array $selectedGajiIds = [];
    public bool $selectAll = false;

    // Modals
    public bool $showDetailModal = false;
    public ?GajiGuru $selectedSalaryDetail = null;
    public $detailBuktiFoto = null;

    public bool $showPreviewModal = false;
    public ?int $previewSalaryId = null;

    protected $queryString = [
        'filterTahun' => ['except' => ''],
        'filterBulan' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'search' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function mount(int $guruId)
    {
        $this->guruId = $guruId;
        $this->guru = Guru::with(['user', 'peminjamans'])->findOrFail($guruId);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterTahun()
    {
        $this->resetPage();
    }

    public function updatedFilterBulan()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $query = $this->getBaseQuery();
            $this->selectedGajiIds = $query->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedGajiIds = [];
        }
    }

    public function clearFilters()
    {
        $this->filterTahun = '';
        $this->filterBulan = '';
        $this->filterStatus = '';
        $this->search = '';
        $this->resetPage();
        $this->selectedGajiIds = [];
        $this->selectAll = false;
    }

    public function openDetailModal(int $id)
    {
        $this->selectedSalaryDetail = GajiGuru::with(['guru.user', 'pengeluaran'])->find($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedSalaryDetail = null;
        $this->detailBuktiFoto = null;
    }

    public function updateSalaryBuktiFoto(int $salaryId)
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->validate([
            'detailBuktiFoto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'detailBuktiFoto.required' => 'Silakan pilih file foto bukti terlebih dahulu.',
            'detailBuktiFoto.image' => 'File bukti harus berupa gambar/foto.',
            'detailBuktiFoto.mimes' => 'Format foto hanya boleh JPG, JPEG, PNG, atau WEBP.',
            'detailBuktiFoto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $salary = GajiGuru::with('pengeluaran')->findOrFail($salaryId);

        if ($salary->bukti_bayar && Storage::disk('public')->exists($salary->bukti_bayar)) {
            Storage::disk('public')->delete($salary->bukti_bayar);
        }

        $path = $this->detailBuktiFoto->store('bukti-gaji', 'public');

        $salary->update(['bukti_bayar' => $path]);
        if ($salary->pengeluaran) {
            $salary->pengeluaran->update(['bukti' => $path]);
        }

        $this->detailBuktiFoto = null;
        $this->selectedSalaryDetail = $salary->fresh(['guru.user', 'pengeluaran']);
        session()->flash('message', 'Foto bukti transfer/struk pembayaran berhasil diperbarui.');
    }

    public function deleteSalaryBuktiFoto(int $salaryId)
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $salary = GajiGuru::with('pengeluaran')->findOrFail($salaryId);

        if ($salary->bukti_bayar && Storage::disk('public')->exists($salary->bukti_bayar)) {
            Storage::disk('public')->delete($salary->bukti_bayar);
        }

        $salary->update(['bukti_bayar' => null]);
        if ($salary->pengeluaran) {
            $salary->pengeluaran->update(['bukti' => null]);
        }

        $this->detailBuktiFoto = null;
        $this->selectedSalaryDetail = $salary->fresh(['guru.user', 'pengeluaran']);
        session()->flash('message', 'Foto bukti transfer/struk berhasil dihapus.');
    }

    public function openPreview(int $id)
    {
        $this->previewSalaryId = $id;
        $this->showPreviewModal = true;
    }

    public function closePreview()
    {
        $this->showPreviewModal = false;
        $this->previewSalaryId = null;
    }

    public function deleteSalary(int $id, ?string $alasan = null)
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $gaji = GajiGuru::with('guru.user')->where('guru_id', $this->guruId)->findOrFail($id);
        $userRole = auth()->user()->role->nama ?? '';

        if ($userRole === 'finance') {
            $reason = $alasan ?: 'Penghapusan data gaji diajukan oleh staf keuangan';
            \App\Services\FinancialApprovalService::createRequest(
                auth()->user(),
                'hapus',
                'gaji_guru',
                $gaji,
                null,
                $reason,
                "Hapus Gaji Guru: " . ($gaji->guru->user->nama ?? 'Guru') . " - {$gaji->bulan} {$gaji->tahun} (THP: Rp " . number_format($gaji->total_diterima, 0, ',', '.') . ")"
            );

            session()->flash('message', 'Permohonan penghapusan data gaji telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.');
            return;
        }

        DB::transaction(function () use ($gaji) {
            if ($gaji->status === 'dibayar') {
                if ($gaji->pengeluaran_id) {
                    $pengeluaran = Pengeluaran::find($gaji->pengeluaran_id);
                    if ($pengeluaran) {
                        $pengeluaran->delete();
                    }
                }

                if ($gaji->potongan_peminjaman > 0) {
                    $loan = Peminjaman::where('guru_id', $gaji->guru_id)->first();
                    if ($loan) {
                        $loan->update([
                            'sisa_pinjaman' => $loan->sisa_pinjaman + $gaji->potongan_peminjaman,
                            'status' => 'berjalan'
                        ]);
                    }
                }
            }

            $gaji->delete();
        });

        $this->selectedGajiIds = array_values(array_diff($this->selectedGajiIds, [(string)$id]));
        session()->flash('message', 'Data gaji berhasil dihapus.');
    }

    public function deleteSelected()
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.']);
            return;
        }

        if (empty($this->selectedGajiIds)) {
            session()->flash('error', 'Silakan pilih riwayat gaji yang ingin dihapus terlebih dahulu.');
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Silakan pilih riwayat gaji yang ingin dihapus terlebih dahulu.']);
            return;
        }

        $salaries = GajiGuru::with('guru.user')->where('guru_id', $this->guruId)
            ->whereIn('id', $this->selectedGajiIds)
            ->get();
        $count = $salaries->count();
        $userRole = auth()->user()->role?->nama ?? '';

        if ($userRole === 'finance') {
            $submittedCount = 0;
            $deletedDraftCount = 0;

            DB::transaction(function () use ($salaries, &$submittedCount, &$deletedDraftCount) {
                foreach ($salaries as $gaji) {
                    if ($gaji->status === 'dibayar') {
                        \App\Services\FinancialApprovalService::createRequest(
                            auth()->user(),
                            'hapus',
                            'gaji_guru',
                            $gaji,
                            null,
                            'Penghapusan batch diajukan oleh staf keuangan',
                            "Hapus Gaji Guru: " . ($gaji->guru->user->nama ?? 'Guru') . " - {$gaji->bulan} {$gaji->tahun} (THP: Rp " . number_format($gaji->total_diterima, 0, ',', '.') . ")"
                        );
                        $submittedCount++;
                    } else {
                        $gaji->delete();
                        $deletedDraftCount++;
                    }
                }
            });

            $this->selectedGajiIds = [];
            $this->selectAll = false;

            $msg = [];
            if ($deletedDraftCount > 0) {
                $msg[] = "{$deletedDraftCount} data draf gaji berhasil dihapus.";
            }
            if ($submittedCount > 0) {
                $msg[] = "{$submittedCount} permohonan hapus gaji berstatus dibayar diajukan ke Super Admin untuk disetujui.";
            }

            $messageText = implode(' ', $msg) ?: 'Aksi batch selesai.';
            session()->flash('message', $messageText);
            $this->dispatch('notify', ['type' => 'success', 'message' => $messageText]);
            $this->dispatch('modal-alert', ['type' => 'create', 'title' => 'Permohonan Berhasil Diajukan', 'message' => $messageText]);
            return;
        }

        DB::transaction(function () use ($salaries) {
            foreach ($salaries as $gaji) {
                if ($gaji->status === 'dibayar') {
                    if ($gaji->pengeluaran_id) {
                        $pengeluaran = Pengeluaran::find($gaji->pengeluaran_id);
                        if ($pengeluaran) {
                            $pengeluaran->delete();
                        }
                    }

                    if ($gaji->potongan_peminjaman > 0) {
                        $loan = Peminjaman::where('guru_id', $gaji->guru_id)->first();
                        if ($loan) {
                            $loan->update([
                                'sisa_pinjaman' => $loan->sisa_pinjaman + $gaji->potongan_peminjaman,
                                'status' => 'berjalan'
                            ]);
                        }
                    }
                }

                $gaji->delete();
            }
        });

        $this->selectedGajiIds = [];
        $this->selectAll = false;
        $messageText = "Berhasil menghapus {$count} data riwayat gaji terpilih.";
        session()->flash('message', $messageText);
        $this->dispatch('notify', ['type' => 'success', 'message' => $messageText]);
        $this->dispatch('modal-alert', ['type' => 'delete', 'title' => 'Data Berhasil Dihapus', 'message' => $messageText]);
    }

    protected function getBaseQuery()
    {
        $query = GajiGuru::with(['guru.user', 'pengeluaran'])
            ->where('guru_id', $this->guruId);

        if ($this->filterTahun) {
            $query->where('tahun', $this->filterTahun);
        }

        if ($this->filterBulan) {
            $query->where('bulan', $this->filterBulan);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('catatan', 'like', $searchTerm)
                  ->orWhere('jabatan', 'like', $searchTerm)
                  ->orWhere('sumber_dana', 'like', $searchTerm);
            });
        }

        return $query;
    }

    public function render()
    {
        // 1. Base query for stats and records of this teacher
        $statQuery = $this->getBaseQuery();
        $statRecords = (clone $statQuery)->get();

        // 2. Metrics calculation for this teacher
        $paidRecords = $statRecords->where('status', 'dibayar');
        $statTotalDibayar = $paidRecords->sum('total_diterima');
        $statTotalPokok = $statRecords->sum('gaji_pokok') + $statRecords->sum('gaji_berkala');
        $statTotalInsentif = $statRecords->sum(function ($sal) {
            return $sal->insentif + $sal->honor_ekskul + $sal->insentif_bpjs + $sal->insentif_maghrib_mengaji;
        });
        $statTotalKasbon = $paidRecords->sum('potongan_peminjaman');
        $statCountDibayar = $paidRecords->count();
        $statCountDraft = $statRecords->where('status', 'draft')->count();
        $statTotalRecords = $statRecords->count();

        // Active Loans / Kasbon Info for this teacher
        $activeLoan = Peminjaman::where('guru_id', $this->guruId)
            ->where('status', 'disetujui')
            ->where('sisa_pinjaman', '>', 0)
            ->first();

        // 3. Paginated records
        $salaries = $this->getBaseQuery()
            ->orderBy('tahun', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $listBulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return view('livewire.finance.detail-gaji-guru', [
            'salaries' => $salaries,
            'listBulan' => $listBulan,
            'statTotalDibayar' => $statTotalDibayar,
            'statTotalPokok' => $statTotalPokok,
            'statTotalInsentif' => $statTotalInsentif,
            'statTotalKasbon' => $statTotalKasbon,
            'statCountDibayar' => $statCountDibayar,
            'statCountDraft' => $statCountDraft,
            'statTotalRecords' => $statTotalRecords,
            'activeLoan' => $activeLoan,
        ])->layout('components.layouts.app', ['title' => 'Riwayat Gaji: ' . ($this->guru->user->nama ?? 'Guru') . ' - Yayasan F3']);
    }
}
