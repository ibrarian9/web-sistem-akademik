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

    // Tagihan Filters
    public string $filterBulan = '';
    public ?int $filterJenis = null;
    public string $filterStatus = '';
    public ?int $filterTahunAjaran = null;
    public string $search = '';

    // Riwayat Pembayaran Filters
    public string $searchBayar = '';
    public string $filterBayarBulan = '';
    public ?int $filterBayarJenis = null;
    public string $filterBayarMetode = '';
    public ?int $filterBayarTahunAjaran = null;

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
    public int $perPage = 25;

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
    }

    public function updatingFilterBulan()
    {
        $this->resetPage();
    }

    public function updatingFilterJenis()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterTahunAjaran()
    {
        $this->resetPage();
    }

    public function setCategoryTab(string $tab)
    {
        $this->activeCategoryTab = $tab;
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['filterBulan', 'filterJenis', 'filterStatus', 'filterTahunAjaran', 'search']);
        $this->activeCategoryTab = 'all';
        $this->resetPage();
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
            if ($jt && $jt->nominal_default > 0) {
                $this->nominal = (float) $jt->nominal_default;
            }
        }
    }

    public function setPresetRange(string $start, string $end)
    {
        $this->bulan_mulai = $start;
        $this->bulan_selesai = $end;
    }

    public function getMonthsBetween(string $start, string $end): array
    {
        $academicOrder = [
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'
        ];

        $calendarOrder = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        if ($start === $end) {
            return [$start];
        }

        // Check in Academic Order first (most common for schools)
        $acadStart = array_search($start, $academicOrder);
        $acadEnd = array_search($end, $academicOrder);

        if ($acadStart !== false && $acadEnd !== false && $acadStart <= $acadEnd) {
            return array_slice($academicOrder, $acadStart, $acadEnd - $acadStart + 1);
        }

        // Check in Calendar Order
        $calStart = array_search($start, $calendarOrder);
        $calEnd = array_search($end, $calendarOrder);

        if ($calStart !== false && $calEnd !== false && $calStart <= $calEnd) {
            return array_slice($calendarOrder, $calStart, $calEnd - $calStart + 1);
        }

        // Cyclic fallback in Academic order
        if ($acadStart !== false && $acadEnd !== false) {
            $result = [];
            $curr = $acadStart;
            while (true) {
                $result[] = $academicOrder[$curr];
                if ($curr === $acadEnd) {
                    break;
                }
                $curr = ($curr + 1) % 12;
            }
            return $result;
        }

        return [$start];
    }

    public function getTargetMonths(): array
    {
        if ($this->periodeTipe === 'full_year_jan_des') {
            return [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
        } elseif ($this->periodeTipe === 'full_year_juli_juni') {
            return [
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'
            ];
        } elseif ($this->periodeTipe === 'custom_range') {
            return $this->getMonthsBetween($this->bulan_mulai, $this->bulan_selesai);
        }
        return [$this->bulan];
    }

    protected function calculateDueDateForMonth(string $monthName, ?string $baseDueDate = null, ?string $tahunAjaranNama = null): string
    {
        $monthNumbers = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];

        if (!isset($monthNumbers[$monthName])) {
            return date('Y-m-10');
        }

        $targetMonth = $monthNumbers[$monthName];
        $targetDay = 10; // Fix tanggal 10 setiap bulannya

        $year = (int) date('Y');
        if ($tahunAjaranNama && str_contains($tahunAjaranNama, '/')) {
            $parts = explode('/', $tahunAjaranNama);
            $y1 = (int) trim($parts[0]);
            $y2 = (int) trim($parts[1]);

            if ($this->periodeTipe === 'full_year_jan_des') {
                if (!empty($baseDueDate)) {
                    try {
                        $year = \Carbon\Carbon::parse($baseDueDate)->year;
                    } catch (\Exception $e) {
                        $year = $y2;
                    }
                } else {
                    $year = $y2;
                }
            } else {
                // Bulan Juli - Desember jatuh pada tahun ajaran pertama ($y1), Januari - Juni pada tahun kedua ($y2)
                $year = ($targetMonth >= 7) ? $y1 : $y2;
            }
        } elseif (!empty($baseDueDate)) {
            try {
                $year = \Carbon\Carbon::parse($baseDueDate)->year;
            } catch (\Exception $e) {
                $year = (int) date('Y');
            }
        }

        return sprintf('%04d-%02d-%02d', $year, $targetMonth, $targetDay);
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

        if ($this->periodeTipe === 'single') {
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

        DB::transaction(function () use ($t) {
            $siswa = $t->siswa ?: Siswa::find($this->siswaId);

            // Revert and delete any payments associated with this tagihan
            if ($t->pembayarans && $t->pembayarans->count() > 0) {
                foreach ($t->pembayarans as $pembayaran) {
                    if ($pembayaran->metode_bayar === 'Deposit' && $pembayaran->nominal_dibayar > 0 && $siswa) {
                        $siswa->increment('saldo_deposit', $pembayaran->nominal_dibayar);
                    }
                    if ($pembayaran->kelebihan_bayar > 0 && $siswa) {
                        $siswa->decrement('saldo_deposit', min(floatval($siswa->saldo_deposit), floatval($pembayaran->kelebihan_bayar)));
                    }
                    $pembayaran->delete();
                }
            }

            $t->delete();
        });

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

            // 2. Delete payment record
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
        $allTagihanQuery = Tagihan::where('siswa_id', $this->siswaId);
        $totalNominal = (clone $allTagihanQuery)->sum('nominal');
        $totalTerbayar = (clone $allTagihanQuery)->sum('total_dibayar');
        $totalSisa = max(0, $totalNominal - $totalTerbayar);
        $countLunas = (clone $allTagihanQuery)->where('status', 'lunas')->count();
        $countBelumLunas = (clone $allTagihanQuery)->where('status', '!=', 'lunas')->count();

        // Paginated filtered invoices
        $tagihanQuery = Tagihan::with(['jenisTagihan', 'tahunAjaran', 'pembayarans'])
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
            })
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
            $sppMatrix[$m] = [
                'bulan' => $m,
                'has_bill' => !is_null($item),
                'status' => $item ? $item->status : 'unreleased',
                'nominal' => $item ? floatval($item->nominal) : 0,
                'total_dibayar' => $item ? floatval($item->total_dibayar) : 0,
                'sisa' => $item ? max(0, floatval($item->nominal) - floatval($item->total_dibayar)) : 0,
                'id' => $item ? $item->id : null,
            ];
        }

        // Paginated & filtered payment transactions for this student
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

        $recentPayments = $pembayaranQuery->orderBy('tanggal_bayar', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'pembayaranPage');

        return view('livewire.finance.detail-tagihan-siswa', [
            'tagihans' => $tagihans,
            'recentPayments' => $recentPayments,
            'totalNominal' => $totalNominal,
            'totalTerbayar' => $totalTerbayar,
            'totalSisa' => $totalSisa,
            'countLunas' => $countLunas,
            'countBelumLunas' => $countBelumLunas,
            'countAll' => $countAll,
            'countSpp' => $countSpp,
            'countNonSpp' => $countNonSpp,
            'sppMatrix' => $sppMatrix,
            'activeTAName' => $activeTA->nama ?? '-',
        ])->layout('components.layouts.app', ['title' => 'Rincian Tagihan - ' . ($this->siswa->user->nama ?? 'Siswa')]);
    }
}
