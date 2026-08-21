<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\Pembayaran;
use Livewire\WithPagination;

class DetailTagihanSiswa extends Component
{
    use WithPagination;

    public int $siswaId;
    public ?Siswa $siswa = null;

    // Filters
    public string $filterBulan = '';
    public ?int $filterJenis = null;
    public string $filterStatus = '';
    public ?int $filterTahunAjaran = null;
    public string $search = '';

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
            'nominal' => 'required|numeric|min:1',
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

        Tagihan::create([
            'siswa_id' => $this->siswaId,
            'tahun_ajaran_id' => $activeTA->id,
            'jenis_tagihan_id' => $this->jenis_tagihan_id,
            'bulan' => $this->bulan,
            'nominal' => $this->nominal,
            'total_dibayar' => 0.00,
            'status' => 'belum_bayar',
            'jatuh_tempo' => $this->jatuh_tempo,
        ]);

        $this->closeCreateModal();
        session()->flash('success', 'Tagihan baru berhasil ditambahkan untuk siswa ini.');
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
            'edit_nominal' => 'required|numeric|min:' . max(1, $this->edit_total_dibayar),
            'edit_jatuh_tempo' => 'required|date',
        ], [
            'edit_nominal.min' => 'Nominal tagihan tidak boleh lebih kecil dari jumlah yang sudah dibayar (Rp ' . number_format($this->edit_total_dibayar, 0, ',', '.') . ').',
        ]);

        $t = Tagihan::findOrFail($this->editingTagihanId);

        $newStatus = 'belum_bayar';
        if ($t->total_dibayar >= $this->edit_nominal) {
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
        if (!$this->isFounder()) {
            session()->flash('error', 'Hanya Founder/Super Admin yang berhak menghapus data tagihan.');
            return;
        }

        $t = Tagihan::findOrFail($id);
        if ($t->total_dibayar > 0) {
            session()->flash('error', 'Tagihan tidak dapat dihapus karena sudah memiliki riwayat pembayaran.');
            return;
        }

        $t->delete();
        session()->flash('success', 'Tagihan berhasil dihapus.');
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

        // Recent payment transactions for this student
        $recentPayments = Pembayaran::with(['tagihan.jenisTagihan', 'petugas'])
            ->whereHas('tagihan', function ($q) {
                $q->where('siswa_id', $this->siswaId);
            })
            ->orderBy('tanggal_bayar', 'desc')
            ->take(10)
            ->get();

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
