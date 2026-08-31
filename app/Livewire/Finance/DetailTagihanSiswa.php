<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class DetailTagihanSiswa extends Component
{
    use WithPagination;

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

    // Create Tagihan Modal for this student
    public bool $showCreateModal = false;
    public ?int $jenis_tagihan_id = null;
    public string $bulan = 'Juli';
    public float $nominal = 0.00;
    public string $jatuh_tempo = '';

    // Edit Tagihan Modal
    public bool $showEditModal = false;
    public ?int $editingTagihanId = null;
    public ?int $edit_jenis_tagihan_id = null;
    public string $edit_bulan = 'Juli';
    public float $edit_nominal = 0.00;
    public string $edit_jatuh_tempo = '';
    public float $edit_total_dibayar = 0.00;

    // Option Lists
    public array $jenisTagihans = [];
    public array $tahunAjarans = [];
    public array $bulanOptions = [
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Semester Ganjil', 'Semester Genap', 'Tahunan'
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
        $this->jatuh_tempo = date('Y-m-d', strtotime('+30 days'));
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

    public function resetFilters()
    {
        $this->reset(['filterBulan', 'filterJenis', 'filterStatus', 'filterTahunAjaran', 'search']);
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
        $this->resetValidation();
        $this->reset(['nominal']);
        $this->jenis_tagihan_id = $this->jenisTagihans[0]['id'] ?? null;
        $this->bulan = 'Juli';
        $this->jatuh_tempo = date('Y-m-d', strtotime('+30 days'));
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

    public function createTagihan()
    {
        $this->validate([
            'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'bulan' => 'required|string|max:50',
            'nominal' => 'required|numeric|min:0',
            'jatuh_tempo' => 'required|date',
        ]);

        $activeTA = TahunAjaran::where('status_aktif', true)->first();
        if (!$activeTA) {
            session()->flash('error', 'Tidak ada tahun ajaran aktif.');
            return;
        }

        // Check if duplicate tagihan already exists
        $exists = Tagihan::where('siswa_id', $this->siswaId)
            ->where('tahun_ajaran_id', $activeTA->id)
            ->where('jenis_tagihan_id', $this->jenis_tagihan_id)
            ->where('bulan', $this->bulan)
            ->exists();

        if ($exists) {
            session()->flash('error', 'Tagihan untuk jenis dan periode ini sudah pernah dibuat sebelumnya.');
            return;
        }

        $status = ($this->nominal <= 0) ? 'lunas' : 'belum_bayar';

        Tagihan::create([
            'siswa_id' => $this->siswaId,
            'tahun_ajaran_id' => $activeTA->id,
            'jenis_tagihan_id' => $this->jenis_tagihan_id,
            'bulan' => $this->bulan,
            'nominal' => $this->nominal,
            'total_dibayar' => 0.00,
            'status' => $status,
            'jatuh_tempo' => $this->jatuh_tempo,
        ]);

        $this->closeCreateModal();
        session()->flash('success', 'Tagihan baru berhasil ditambahkan untuk siswa ini' . ($this->nominal <= 0 ? ' (Nominal Rp 0 - Otomatis Lunas).' : '.'));
    }

    public function openEditModal(int $tagihanId)
    {
        $this->resetValidation();
        $t = Tagihan::findOrFail($tagihanId);
        $this->editingTagihanId = $t->id;
        $this->edit_jenis_tagihan_id = $t->jenis_tagihan_id;
        $this->edit_bulan = $t->bulan;
        $this->edit_nominal = (float) $t->nominal;
        $this->edit_jatuh_tempo = $t->jatuh_tempo ? date('Y-m-d', strtotime($t->jatuh_tempo)) : '';
        $this->edit_total_dibayar = (float) $t->total_dibayar;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingTagihanId = null;
        $this->resetValidation();
    }

    public function updateTagihan()
    {
        $this->validate([
            'edit_jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'edit_bulan' => 'required|string|max:50',
            'edit_nominal' => 'required|numeric|min:' . ($this->edit_total_dibayar > 0 ? $this->edit_total_dibayar : 0),
            'edit_jatuh_tempo' => 'required|date',
        ], [
            'edit_nominal.min' => 'Nominal tagihan tidak boleh lebih kecil dari jumlah yang sudah dibayar (Rp ' . number_format($this->edit_total_dibayar, 0, ',', '.') . ').',
        ]);

        $t = Tagihan::findOrFail($this->editingTagihanId);

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

    public function deleteTagihan(int $id)
    {
        if (!$this->isFinanceOrAdmin()) {
            session()->flash('error', 'Akses Ditolak: Anda tidak memiliki wewenang untuk menghapus tagihan.');
            return;
        }

        $t = Tagihan::with(['pembayarans', 'siswa'])->findOrFail($id);

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

    public function deletePembayaran(int $pembayaranId)
    {
        if (!$this->isFinanceOrAdmin()) {
            session()->flash('error', 'Akses Ditolak: Anda tidak memiliki izin untuk membatalkan riwayat pembayaran.');
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
        $tagihans = Tagihan::with(['jenisTagihan', 'tahunAjaran', 'pembayarans'])
            ->where('siswa_id', $this->siswaId)
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
            ->orderBy('id', 'desc')
            ->paginate(15);

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
        ])->layout('components.layouts.app', ['title' => 'Rincian Tagihan - ' . ($this->siswa->user->nama ?? 'Siswa')]);
    }
}
