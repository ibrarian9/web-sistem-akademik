<div class="space-y-6 font-sans" 
    x-data="{
        cashflowChart: null,
        billStatusChart: null,
        initCharts() {
            if (typeof window.Chart === 'undefined') return;

            // 1. Arus Kas Trend Chart
            const cashflowCtx = document.getElementById('financeCashflowChart');
            if (cashflowCtx) {
                if (this.cashflowChart) this.cashflowChart.destroy();
                this.cashflowChart = new window.Chart(cashflowCtx, {
                    type: 'line',
                    data: {
                        labels: @js($cashflowLabels),
                        datasets: [
                            {
                                label: 'Pemasukan (Rp)',
                                data: @js($cashflowIncomes),
                                borderColor: '#059669',
                                backgroundColor: 'rgba(16, 185, 129, 0.12)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#059669',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                            },
                            {
                                label: 'Pengeluaran (Rp)',
                                data: @js($cashflowExpenses),
                                borderColor: '#e11d48',
                                backgroundColor: 'rgba(244, 63, 94, 0.08)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#e11d48',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    usePointStyle: true,
                                    font: { family: 'inherit', size: 11, weight: 'bold' }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f5f5f4' },
                                ticks: {
                                    font: { size: 10 },
                                    callback: function(value) {
                                        if (value >= 1000000) return (value / 1000000) + ' Jt';
                                        if (value >= 1000) return (value / 1000) + ' Rb';
                                        return value;
                                    }
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11, weight: '600' } }
                            }
                        }
                    }
                });
            }

            // 2. Status Tagihan SPP Doughnut Chart
            const billStatusCtx = document.getElementById('financeBillStatusChart');
            if (billStatusCtx) {
                if (this.billStatusChart) this.billStatusChart.destroy();
                this.billStatusChart = new window.Chart(billStatusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Lunas', 'Belum Bayar', 'Sebagian'],
                        datasets: [{
                            data: @js($billStatusCounts),
                            backgroundColor: ['#10b981', '#f43f5e', '#f59e0b'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 10,
                                    usePointStyle: true,
                                    font: { family: 'inherit', size: 11, weight: 'bold' }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const count = context.raw;
                                        return `${context.label}: ${count} Tagihan`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    }"
    x-init="initCharts()"
>
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Panduan Pusat Kendali Keuangan Sekolah (Finance)"
        :steps="[
            ['title' => 'Ringkasan Kas & Tren', 'desc' => 'Pantau saldo kas dan perbandingan grafik arus kas masuk versus pengeluaran operasional 6 bulan.'],
            ['title' => 'Cek Tagihan & SPP', 'desc' => 'Kelola pembayaran SPP siswa, beri persetujuan keringanan, dan buat tagihan rutin bulanan.'],
            ['title' => 'Pengajuan Dana', 'desc' => 'Tinjau proposal pengajuan anggaran belanja dari unit dan beri persetujuan pencairan dana.']
        ]"
        notes="Gunakan tombol Ekspor Laporan pada menu Arus Kas untuk mengunduh laporan keuangan berformat Excel/PDF."
    />

    <!-- Header Title Bar -->
    <x-page-header 
        title="Dashboard Keuangan Sekolah" 
        subtitle="Pantau arus kas yayasan, realisasi SPP siswa, dan pengeluaran operasional secara terpusat."
        badge="PUSAT KENDALI KEUANGAN"
        badgeVariant="emerald"
        icon="wallet"
    >
        <x-slot:actions>
            <div class="px-4 py-2.5 bg-stone-50 border border-stone-200 rounded-2xl shadow-2xs">
                <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Periode Berjalan</span>
                <span class="text-xs font-black text-stone-800">{{ Carbon\Carbon::now()->locale('id')->isoFormat('MMMM YYYY') }}</span>
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Stats Grid -->
    @php
        $netFlow = $incomeThisMonth - $expenseThisMonth;
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card 
            title="Pemasukan Bulan Ini" 
            :value="'Rp ' . number_format($incomeThisMonth, 0, ',', '.')" 
            icon="trending-up" 
            variant="white" 
        />
        <x-stat-card 
            title="Pengeluaran Bulan Ini" 
            :value="'Rp ' . number_format($expenseThisMonth, 0, ',', '.')" 
            icon="trending-down" 
            variant="white" 
        />
        <x-stat-card 
            title="Kas Bersih Bulan Ini" 
            :value="'Rp ' . number_format($netFlow, 0, ',', '.')" 
            icon="dollar-sign" 
            variant="white" 
        />
        <x-stat-card 
            title="Total Tunggakan Aktif" 
            :value="'Rp ' . number_format($outstandingBills, 0, ',', '.')" 
            icon="alert-triangle" 
            variant="white" 
        />

        <div class="sm:col-span-2 lg:col-span-4">
            <x-stat-card 
                title="Total Saldo Deposit Siswa Mengendap" 
                :value="'Rp ' . number_format($totalStudentDeposit, 0, ',', '.')" 
                subtitle="Akumulasi kelebihan pembayaran tagihan dari seluruh siswa yang dapat dialokasikan untuk tagihan berikutnya."
                icon="wallet" 
                variant="emerald" 
            />
        </div>
    </div>

    <!-- Visual Interactive Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 6-Month Cashflow Line/Area Chart -->
        <div class="lg:col-span-2">
            <x-chart-card 
                title="Tren Arus Kas 6 Bulan Terakhir" 
                subtitle="Perbandingan grafik pemasukan kas vs pengeluaran operasional per bulan."
                icon="line-chart"
                badge="TREN REAL-TIME"
                badgeVariant="emerald"
                canvasId="financeCashflowChart"
                height="270px"
            />
        </div>

        <!-- Tagihan SPP Status Distribution Doughnut Chart -->
        <div>
            <x-chart-card 
                title="Rasio Status Tagihan SPP" 
                subtitle="Komposisi tagihan lunas, tertunggak, dan dibayar sebagian."
                icon="pie-chart"
                badge="STATUS SPP"
                badgeVariant="amber"
                canvasId="financeBillStatusChart"
                height="270px"
            />
        </div>
    </div>

    <!-- Content Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Payment Logs -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                    <x-lucide-clock class="w-4 h-4 text-emerald-600" />
                    <span>Pembayaran Masuk Terbaru</span>
                </h3>
                <x-button variant="outline" size="xs" href="{{ route('finance.input-pembayaran') }}">
                    Input Kasir
                </x-button>
            </div>
            
            <x-table>
                <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                    <tr>
                        <x-table.th class="w-28">No. Resi</x-table.th>
                        <x-table.th class="min-w-[150px]">Siswa</x-table.th>
                        <x-table.th class="min-w-[140px]">Jenis Tagihan</x-table.th>
                        <x-table.th align="right" class="w-36">Nominal</x-table.th>
                        <x-table.th align="center" class="w-28">Tanggal</x-table.th>
                        <x-table.th align="center" class="w-20">Cetak</x-table.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse ($recentPayments as $pay)
                        <tr class="hover:bg-emerald-50/40 transition">
                            <td class="p-3 font-mono font-bold text-xs text-stone-900 border-r border-stone-200">{{ $pay['no_resi'] }}</td>
                            <td class="p-3 text-xs font-bold text-stone-900 border-r border-stone-200">{{ $pay['siswa'] }}</td>
                            <td class="p-3 text-xs font-semibold text-stone-700 border-r border-stone-200">{{ $pay['jenis'] }}</td>
                            <td class="p-3 text-xs font-black text-emerald-700 text-right border-r border-stone-200">
                                Rp {{ number_format($pay['nominal'], 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-xs font-semibold text-stone-600 text-center border-r border-stone-200">{{ $pay['tanggal'] }}</td>
                            <td class="p-3 text-center">
                                <a href="{{ route('finance.resi.print', $pay['id']) }}" target="_blank" class="inline-flex items-center justify-center p-1.5 bg-stone-100 hover:bg-emerald-100 text-stone-600 hover:text-emerald-800 rounded-lg transition" title="Cetak Kuitansi Resi">
                                    <x-lucide-printer class="w-4 h-4" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <x-table.empty :colspan="6" title="Belum Ada Pembayaran" message="Belum ada transaksi pembayaran masuk yang tercatat." />
                    @endforelse
                </tbody>
            </x-table>
        </div>

        <!-- Quick Access Shortcuts -->
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
            <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2 border-b border-stone-200 pb-3">
                <x-lucide-zap class="w-4 h-4 text-emerald-600" />
                <span>Aksi Cepat Keuangan</span>
            </h3>

            <div class="space-y-2.5">
                <a href="{{ route('finance.input-pembayaran') }}" class="group flex items-center justify-between p-3.5 bg-stone-50 hover:bg-emerald-50/80 border border-stone-200 hover:border-emerald-300 rounded-2xl transition shadow-2xs">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white border border-stone-200 group-hover:border-emerald-400 group-hover:bg-emerald-600 flex items-center justify-center text-stone-700 group-hover:text-white transition">
                            <x-lucide-receipt class="w-4 h-4" />
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-stone-900">Input Pembayaran</h4>
                            <p class="text-[11px] text-stone-500 font-medium">Kasir setoran SPP siswa</p>
                        </div>
                    </div>
                    <x-lucide-chevron-right class="w-4 h-4 text-stone-400 group-hover:text-emerald-600 transition" />
                </a>

                <a href="{{ route('finance.tagihan') }}" class="group flex items-center justify-between p-3.5 bg-stone-50 hover:bg-emerald-50/80 border border-stone-200 hover:border-emerald-300 rounded-2xl transition shadow-2xs">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white border border-stone-200 group-hover:border-emerald-400 group-hover:bg-emerald-600 flex items-center justify-center text-stone-700 group-hover:text-white transition">
                            <x-lucide-file-spreadsheet class="w-4 h-4" />
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-stone-900">Rilis Tagihan Massal</h4>
                            <p class="text-[11px] text-stone-500 font-medium">Generate SPP bulanan kelas</p>
                        </div>
                    </div>
                    <x-lucide-chevron-right class="w-4 h-4 text-stone-400 group-hover:text-emerald-600 transition" />
                </a>

                <a href="{{ route('finance.laporan.tunggakan') }}" class="group flex items-center justify-between p-3.5 bg-stone-50 hover:bg-emerald-50/80 border border-stone-200 hover:border-emerald-300 rounded-2xl transition shadow-2xs">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white border border-stone-200 group-hover:border-emerald-400 group-hover:bg-emerald-600 flex items-center justify-center text-stone-700 group-hover:text-white transition">
                            <x-lucide-alert-circle class="w-4 h-4" />
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-stone-900">Rekap Tunggakan</h4>
                            <p class="text-[11px] text-stone-500 font-medium">Monitoring tagihan jatuh tempo</p>
                        </div>
                    </div>
                    <x-lucide-chevron-right class="w-4 h-4 text-stone-400 group-hover:text-emerald-600 transition" />
                </a>

                <a href="{{ route('finance.arus-kas') }}" class="group flex items-center justify-between p-3.5 bg-stone-50 hover:bg-emerald-50/80 border border-stone-200 hover:border-emerald-300 rounded-2xl transition shadow-2xs">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white border border-stone-200 group-hover:border-emerald-400 group-hover:bg-emerald-600 flex items-center justify-center text-stone-700 group-hover:text-white transition">
                            <x-lucide-book-open-check class="w-4 h-4" />
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-stone-900">Buku Kas Umum (BKU)</h4>
                            <p class="text-[11px] text-stone-500 font-medium">Buku kas & saldo riil yayasan</p>
                        </div>
                    </div>
                    <x-lucide-chevron-right class="w-4 h-4 text-stone-400 group-hover:text-emerald-600 transition" />
                </a>
            </div>
        </div>
    </div>
</div>
