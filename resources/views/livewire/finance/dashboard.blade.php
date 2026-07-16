<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Dashboard Keuangan</h2>
            <p class="text-xs text-slate-500">Pantau arus kas, realisasi SPP, dan pengeluaran operasional sekolah.</p>
        </div>
        <div class="px-4 py-2 bg-slate-900 border border-slate-800 rounded-2xl">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Bulan Ini</span>
            <span class="text-xs font-bold text-white">{{ Carbon\Carbon::now()->locale('id')->monthName }} {{ Carbon\Carbon::now()->year }}</span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Pemasukan -->
        <div class="bg-slate-900 border border-slate-850 rounded-3xl p-5 flex items-center justify-between shadow-xl">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Pemasukan Bulan Ini</span>
                <span class="text-lg font-black text-emerald-400 tracking-tight">Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}</span>
            </div>
            <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-2xl">
                <x-lucide-trending-up class="w-6 h-6" />
            </div>
        </div>

        <!-- Pengeluaran -->
        <div class="bg-slate-900 border border-slate-850 rounded-3xl p-5 flex items-center justify-between shadow-xl">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Pengeluaran Bulan Ini</span>
                <span class="text-lg font-black text-rose-400 tracking-tight">Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}</span>
            </div>
            <div class="p-3 bg-rose-500/10 text-rose-400 rounded-2xl">
                <x-lucide-trending-down class="w-6 h-6" />
            </div>
        </div>

        <!-- Arus Kas Bersih -->
        @php
            $netFlow = $incomeThisMonth - $expenseThisMonth;
        @endphp
        <div class="bg-slate-900 border border-slate-850 rounded-3xl p-5 flex items-center justify-between shadow-xl">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Kas Bersih Bulan Ini</span>
                <span class="text-lg font-black {{ $netFlow >= 0 ? 'text-indigo-400' : 'text-rose-400' }} tracking-tight">
                    Rp {{ number_format($netFlow, 0, ',', '.') }}
                </span>
            </div>
            <div class="p-3 bg-indigo-500/10 text-indigo-400 rounded-2xl">
                <x-lucide-dollar-sign class="w-6 h-6" />
            </div>
        </div>

        <!-- Total Tunggakan SPP -->
        <div class="bg-slate-900 border border-slate-850 rounded-3xl p-5 flex items-center justify-between shadow-xl">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Total Tunggakan Aktif</span>
                <span class="text-lg font-black text-amber-400 tracking-tight">Rp {{ number_format($outstandingBills, 0, ',', '.') }}</span>
            </div>
            <div class="p-3 bg-amber-500/10 text-amber-400 rounded-2xl">
                <x-lucide-alert-triangle class="w-6 h-6" />
            </div>
        </div>
    </div>

    <!-- Content Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Payment Logs -->
        <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Pembayaran Terbaru</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800">
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Siswa</th>
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Jenis Tagihan</th>
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Nominal</th>
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Tanggal</th>
                            <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Metode</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @forelse ($recentPayments as $pay)
                            <tr class="hover:bg-slate-950/20">
                                <td class="py-3 text-xs font-bold text-white">{{ $pay['siswa'] }}</td>
                                <td class="py-3 text-xs text-slate-350">{{ $pay['jenis'] }}</td>
                                <td class="py-3 text-xs font-bold text-emerald-400 text-right">Rp {{ number_format($pay['nominal'], 0, ',', '.') }}</td>
                                <td class="py-3 text-xs text-slate-400 text-center">{{ $pay['tanggal'] }}</td>
                                <td class="py-3 text-xs text-slate-400 text-center uppercase">{{ $pay['metode'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500 font-semibold text-xs">
                                    Belum ada transaksi pembayaran masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Access panel -->
        <div class="lg:col-span-1 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Akses Cepat Keuangan</h3>
            
            <div class="grid grid-cols-1 gap-3">
                <a href="{{ route('finance.input-pembayaran') }}" class="p-4 bg-slate-950/40 border border-slate-850 hover:border-indigo-500/50 rounded-2xl flex items-center gap-4 group transition duration-200">
                    <div class="p-2.5 bg-indigo-600/10 text-indigo-400 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition duration-200">
                        <x-lucide-plus-circle class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">Input Pembayaran</h4>
                        <span class="text-[10px] text-slate-500 block">Catat setoran SPP siswa</span>
                    </div>
                </a>

                <a href="{{ route('finance.tagihan') }}" class="p-4 bg-slate-950/40 border border-slate-850 hover:border-emerald-500/50 rounded-2xl flex items-center gap-4 group transition duration-200">
                    <div class="p-2.5 bg-emerald-600/10 text-emerald-400 rounded-xl group-hover:bg-emerald-600 group-hover:text-white transition duration-200">
                        <x-lucide-file-text class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">Kelola Tagihan</h4>
                        <span class="text-[10px] text-slate-500 block">Buat tagihan SPP bulanan</span>
                    </div>
                </a>

                <a href="{{ route('finance.arus-kas') }}" class="p-4 bg-slate-950/40 border border-slate-850 hover:border-rose-500/50 rounded-2xl flex items-center gap-4 group transition duration-200">
                    <div class="p-2.5 bg-rose-600/10 text-rose-400 rounded-xl group-hover:bg-rose-600 group-hover:text-white transition duration-200">
                        <x-lucide-trending-down class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">Pengeluaran Kas</h4>
                        <span class="text-[10px] text-slate-500 block">Catat pengeluaran operasional</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
