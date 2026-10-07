<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DepositSiswa extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $filterKelas = null;
    public string $filterStatus = 'aktif'; // 'aktif' (saldo > 0), 'semua' (pernah ada mutasi / riwayat)

    // Modal Detail Properties
    public bool $showDetailModal = false;
    public ?int $selectedSiswaId = null;
    public ?Siswa $selectedSiswa = null;
    public array $siswaOverpaymentHistory = [];
    public array $siswaDepositUsageHistory = [];
    public array $siswaUnpaidBills = [];

    public function mount(?int $siswa_id = null): void
    {
        $targetId = $siswa_id ?? request()->query('siswa_id');
        if ($targetId) {
            $this->openDetail((int) $targetId);
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterKelas(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function openDetail(int $siswaId): void
    {
        $siswa = Siswa::with(['user', 'kelas'])->find($siswaId);
        if (!$siswa) {
            session()->flash('error', 'Data siswa tidak ditemukan.');
            return;
        }

        $this->selectedSiswa = $siswa;
        $this->selectedSiswaId = $siswa->id;

        // 1. Riwayat asal-usul kelebihan saldo (Overpayment Credits)
        $this->siswaOverpaymentHistory = Pembayaran::with(['tagihan.jenisTagihan', 'petugas'])
            ->whereHas('tagihan', fn($q) => $q->where('siswa_id', $siswaId))
            ->where('kelebihan_bayar', '>', 0)
            ->where('is_void', false)
            ->orderBy('tanggal_bayar', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'no_resi' => $p->no_resi ?? ('RES-' . str_pad($p->id, 5, '0', STR_PAD_LEFT)),
                'tanggal' => $p->tanggal_bayar ? date('d/m/Y', strtotime($p->tanggal_bayar)) : '-',
                'tagihan_nama' => ($p->tagihan->jenisTagihan->nama ?? 'Tagihan') . ($p->tagihan->bulan ? ' (' . $p->tagihan->bulan . ')' : ''),
                'nominal_dibayar' => floatval($p->nominal_dibayar),
                'nominal_tagihan' => floatval($p->tagihan->nominal ?? 0),
                'kelebihan_bayar' => floatval($p->kelebihan_bayar),
                'metode_bayar' => $p->metode_bayar,
                'petugas' => $p->petugas->nama ?? 'Kasir',
            ])
            ->toArray();

        // 2. Riwayat pemakaian saldo deposit (Debits)
        $this->siswaDepositUsageHistory = Pembayaran::with(['tagihan.jenisTagihan', 'petugas'])
            ->whereHas('tagihan', fn($q) => $q->where('siswa_id', $siswaId))
            ->where('metode_bayar', 'Deposit')
            ->where('is_void', false)
            ->orderBy('tanggal_bayar', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'no_resi' => $p->no_resi ?? ('RES-' . str_pad($p->id, 5, '0', STR_PAD_LEFT)),
                'tanggal' => $p->tanggal_bayar ? date('d/m/Y', strtotime($p->tanggal_bayar)) : '-',
                'tagihan_nama' => ($p->tagihan->jenisTagihan->nama ?? 'Tagihan') . ($p->tagihan->bulan ? ' (' . $p->tagihan->bulan . ')' : ''),
                'nominal_dipotong' => floatval($p->nominal_dibayar),
                'petugas' => $p->petugas->nama ?? 'Kasir',
            ])
            ->toArray();

        // 3. Tagihan siswa yang belum lunas (Unpaid Bills)
        $this->siswaUnpaidBills = Tagihan::with('jenisTagihan')
            ->where('siswa_id', $siswaId)
            ->whereIn('status', ['belum_bayar', 'sebagian'])
            ->orderBy('jatuh_tempo', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'jenis' => $t->jenisTagihan->nama ?? 'Tagihan',
                'bulan' => $t->bulan ?: '-',
                'nominal' => floatval($t->nominal),
                'total_dibayar' => floatval($t->total_dibayar),
                'sisa' => max(0, floatval($t->nominal) - floatval($t->total_dibayar)),
                'status' => $t->status,
                'jatuh_tempo' => $t->jatuh_tempo ? date('d/m/Y', strtotime($t->jatuh_tempo)) : '-',
            ])
            ->toArray();

        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedSiswa = null;
        $this->selectedSiswaId = null;
        $this->siswaOverpaymentHistory = [];
        $this->siswaDepositUsageHistory = [];
        $this->siswaUnpaidBills = [];
    }

    public function render(): View
    {
        // 1. Metrics Overview
        $statTotalMengendap = floatval(Siswa::sum('saldo_deposit'));
        $statCountSiswaMengendap = Siswa::where('saldo_deposit', '>', 0)->count();
        $statTotalAkumulasiOverpayment = floatval(
            Pembayaran::where('kelebihan_bayar', '>', 0)->where('is_void', false)->sum('kelebihan_bayar')
        );
        $statTotalDepositTerpakai = floatval(
            Pembayaran::where('metode_bayar', 'Deposit')->where('is_void', false)->sum('nominal_dibayar')
        );

        // 2. Query Students
        $query = Siswa::with(['user', 'kelas'])
            ->withCount([
                'tagihans as unpaid_bills_count' => fn($q) => $q->whereIn('status', ['belum_bayar', 'sebagian']),
            ]);

        if ($this->filterStatus === 'aktif') {
            $query->where('saldo_deposit', '>', 0);
        } else {
            $query->where(function ($q) {
                $q->where('saldo_deposit', '>', 0)
                  ->orWhereHas('tagihans.pembayarans', fn($sq) => $sq->where('kelebihan_bayar', '>', 0));
            });
        }

        if ($this->filterKelas) {
            $query->where('kelas_id', $this->filterKelas);
        }

        if (trim($this->search) !== '') {
            $term = trim($this->search);
            $query->where(function ($q) use ($term) {
                $q->whereHas('user', fn($uq) => $uq->where('nama', 'like', "%{$term}%"))
                  ->orWhere('nis', 'like', "%{$term}%")
                  ->orWhere('nisn', 'like', "%{$term}%");
            });
        }

        $students = $query->orderByDesc('saldo_deposit')->orderBy('nis')->paginate(15);
        $classes = Kelas::orderBy('nama_kelas')->get();

        return view('livewire.finance.deposit-siswa', [
            'students' => $students,
            'classes' => $classes,
            'statTotalMengendap' => $statTotalMengendap,
            'statCountSiswaMengendap' => $statCountSiswaMengendap,
            'statTotalAkumulasiOverpayment' => $statTotalAkumulasiOverpayment,
            'statTotalDepositTerpakai' => $statTotalDepositTerpakai,
        ])->layout('components.layouts.app', ['title' => 'Monitoring Saldo Deposit Siswa']);
    }
}
