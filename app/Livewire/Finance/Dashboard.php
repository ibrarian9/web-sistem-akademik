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
    public float $futureBills = 0.00;
    public float $totalStudentDeposit = 0.00;
    public array $recentPayments = [];

    // Chart Data Properties
    public array $cashflowLabels = [];
    public array $cashflowIncomes = [];
    public array $cashflowExpenses = [];
    public array $billStatusCounts = [0, 0, 0]; // Lunas, Belum Bayar, Sebagian
    public array $billStatusNominals = [0, 0, 0];

    public function mount()
    {
        $this->loadFinanceStats();
        $this->loadChartData();
    }

    public function loadFinanceStats()
    {
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        // Income this month (excluding void)
        $this->incomeThisMonth = floatval(
            Pembayaran::where('is_void', false)
                ->whereBetween('tanggal_bayar', [$startOfMonth, $endOfMonth])
                ->sum('nominal_dibayar')
        );

        // Expense this month
        $this->expenseThisMonth = floatval(
            Pengeluaran::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->sum('jumlah')
        );

        // Outstanding Bills (unpaid amount for bills due up to the end of current month)
        $this->outstandingBills = floatval(
            Tagihan::tunggakan()
                ->selectRaw('SUM(nominal - total_dibayar) as aggregate')
                ->value('aggregate') ?? 0.00
        );

        // Future scheduled bills (bills due after current month)
        $this->futureBills = floatval(
            Tagihan::mendatang()
                ->selectRaw('SUM(nominal - total_dibayar) as aggregate')
                ->value('aggregate') ?? 0.00
        );

        // Total student deposit balance
        $this->totalStudentDeposit = floatval(\App\Models\Siswa::sum('saldo_deposit'));

        // Recent Payments
        $this->recentPayments = Pembayaran::where('is_void', false)
            ->with(['tagihan.siswa.user', 'tagihan.jenisTagihan'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'no_resi' => $p->no_resi ?? '-',
                'siswa' => $p->tagihan->siswa->user->nama ?? '-',
                'jenis' => $p->tagihan->jenisTagihan->nama ?? 'Tagihan',
                'nominal' => floatval($p->nominal_dibayar),
                'tanggal' => date('d-m-Y', strtotime($p->tanggal_bayar)),
                'metode' => $p->metode_bayar,
            ])
            ->toArray();
    }

    public function loadChartData()
    {
        // 1. 6-Month Cashflow Trend
        $labels = [];
        $incomes = [];
        $expenses = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $start = $month->copy()->startOfMonth()->toDateString();
            $end = $month->copy()->endOfMonth()->toDateString();

            $monthName = $month->locale('id')->isoFormat('MMM YY');
            $labels[] = $monthName;

            $inc = Pembayaran::where('is_void', false)
                ->whereBetween('tanggal_bayar', [$start, $end])
                ->sum('nominal_dibayar');
            $incomes[] = floatval($inc);

            $exp = Pengeluaran::whereBetween('tanggal', [$start, $end])
                ->sum('jumlah');
            $expenses[] = floatval($exp);
        }

        $this->cashflowLabels = $labels;
        $this->cashflowIncomes = $incomes;
        $this->cashflowExpenses = $expenses;

        // 2. Tagihan Status Distribution (s/d Periode Berjalan)
        $lunasCount = Tagihan::jatuhTempo()->where('status', 'lunas')->count();
        $belumBayarCount = Tagihan::tunggakan()->where('status', 'belum_bayar')->count();
        $sebagianCount = Tagihan::tunggakan()->where('status', 'sebagian')->count();

        $lunasNominal = floatval(Tagihan::jatuhTempo()->where('status', 'lunas')->sum('nominal'));
        $belumBayarNominal = floatval(Tagihan::tunggakan()->where('status', 'belum_bayar')->sum('nominal'));
        $sebagianNominal = floatval(Tagihan::tunggakan()->where('status', 'sebagian')->sum('nominal'));

        $this->billStatusCounts = [$lunasCount, $belumBayarCount, $sebagianCount];
        $this->billStatusNominals = [$lunasNominal, $belumBayarNominal, $sebagianNominal];
    }

    public function render()
    {
        return view('livewire.finance.dashboard')
            ->layout('components.layouts.app', ['title' => 'Dashboard Keuangan']);
    }
}
