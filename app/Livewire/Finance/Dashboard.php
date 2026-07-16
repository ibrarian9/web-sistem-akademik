<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\Tagihan;
use Carbon\Carbon;

class Dashboard extends Component
{
    public float $incomeThisMonth = 0.00;
    public float $expenseThisMonth = 0.00;
    public float $outstandingBills = 0.00;
    public array $recentPayments = [];

    public function mount()
    {
        $this->loadFinanceStats();
    }

    public function loadFinanceStats()
    {
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        // Income this month
        $this->incomeThisMonth = floatval(
            Pembayaran::whereBetween('tanggal_bayar', [$startOfMonth, $endOfMonth])
                ->sum('nominal_dibayar')
        );

        // Expense this month
        $this->expenseThisMonth = floatval(
            Pengeluaran::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->sum('jumlah')
        );

        // Outstanding Bills (unpaid amount)
        $this->outstandingBills = floatval(
            Tagihan::whereIn('status', ['belum_bayar', 'sebagian'])
                ->get()
                ->sum(fn($t) => $t->nominal - $t->total_dibayar)
        );

        // Recent Payments
        $this->recentPayments = Pembayaran::with(['tagihan.siswa.user', 'tagihan.jenisTagihan'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'siswa' => $p->tagihan->siswa->user->nama ?? '-',
                'jenis' => $p->tagihan->jenisTagihan->nama ?? 'Tagihan',
                'nominal' => floatval($p->nominal_dibayar),
                'tanggal' => date('d-m-Y', strtotime($p->tanggal_bayar)),
                'metode' => $p->metode_bayar,
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.finance.dashboard')
            ->layout('components.layouts.app', ['title' => 'Dashboard Keuangan']);
    }
}
