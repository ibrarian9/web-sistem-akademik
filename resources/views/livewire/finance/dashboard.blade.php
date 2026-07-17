<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Dashboard Keuangan</h2>
            <p class="text-sm text-stone-500">Pantau arus kas, realisasi SPP, dan pengeluaran operasional sekolah.</p>
        </div>
        <div class="px-4 py-2 bg-white border border-stone-200 rounded-2xl shadow-sm">
            <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider block">Bulan Ini</span>
            <span class="text-sm font-bold text-stone-800">{{ Carbon\Carbon::now()->locale('id')->monthName }} {{ Carbon\Carbon::now()->year }}</span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Pemasukan -->
        <div class="bg-white border border-stone-200 rounded-2xl p-5 flex items-center justify-between shadow-sm">
            <div class="space-y-1">
                <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider block">Pemasukan Bulan Ini</span>
                <span class="text-lg font-bold text-green-700 tracking-tight">Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}</span>
            </div>
            <div class="p-3 bg-green-50 text-green-600 rounded-2xl border border-green-200">
                <x-lucide-trending-up class="w-6 h-6" />
            </div>
        </div>

        <!-- Pengeluaran -->
        <div class="bg-white border border-stone-200 rounded-2xl p-5 flex items-center justify-between shadow-sm">
            <div class="space-y-1">
                <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider block">Pengeluaran Bulan Ini</span>
                <span class="text-lg font-bold text-red-700 tracking-tight">Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}</span>
            </div>
            <div class="p-3 bg-red-50 text-red-600 rounded-2xl border border-red-200">
                <x-lucide-trending-down class="w-6 h-6" />
            </div>
        </div>

        <!-- Arus Kas Bersih -->
        @php
            $netFlow = $incomeThisMonth - $expenseThisMonth;
        @endphp
        <div class="bg-white border border-stone-200 rounded-2xl p-5 flex items-center justify-between shadow-sm">
            <div class="space-y-1">
                <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider block">Kas Bersih Bulan Ini</span>
                <span class="text-lg font-bold {{ $netFlow >= 0 ? 'text-blue-700' : 'text-red-700' }} tracking-tight">
                    Rp {{ number_format($netFlow, 0, ',', '.') }}
                </span>
            </div>
            <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl border border-blue-200">
                <x-lucide-dollar-sign class="w-6 h-6" />
            </div>
        </div>

        <!-- Total Tunggakan SPP -->
        <div class="bg-white border border-stone-200 rounded-2xl p-5 flex items-center justify-between shadow-sm">
            <div class="space-y-1">
                <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider block">Total Tunggakan Aktif</span>
                <span class="text-lg font-bold text-amber-700 tracking-tight">Rp {{ number_format($outstandingBills, 0, ',', '.') }}</span>
            </div>
            <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl border border-amber-200">
                <x-lucide-alert-triangle class="w-6 h-6" />
            </div>
        </div>
    </div>

    <!-- Content Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Payment Logs -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider">Pembayaran Terbaru</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-stone-200">
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Siswa</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Jenis Tagihan</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Nominal</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Tanggal</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Metode</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse ($recentPayments as $pay)
                            <tr class="hover:bg-stone-50">
                                <td class="py-3 text-sm font-semibold text-stone-800">{{ $pay['siswa'] }}</td>
                                <td class="py-3 text-sm text-stone-600">{{ $pay['jenis'] }}</td>
                                <td class="py-3 text-sm font-bold text-green-700 text-right">Rp {{ number_format($pay['nominal'], 0, ',', '.') }}</td>
                                <td class="py-3 text-sm text-stone-500 text-center">{{ $pay['tanggal'] }}</td>
                                <td class="py-3 text-sm text-stone-500 text-center capitalize">{{ $pay['metode'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-stone-400 font-medium text-sm">
                                    Belum ada transaksi pembayaran masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Access panel -->
        <div class="lg:col-span-1 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider">Akses Cepat Keuangan</h3>
            
            <div class="grid grid-cols-1 gap-3">
                <a href="{{ route('finance.input-pembayaran') }}" class="p-4 bg-stone-50 border border-stone-200 hover:border-green-300 rounded-2xl flex items-center gap-4 group transition duration-200">
                    <div class="p-2.5 bg-green-50 text-green-600 rounded-xl border border-green-200 group-hover:bg-green-600 group-hover:text-white transition duration-200">
                        <x-lucide-plus-circle class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-stone-800">Input Pembayaran</h4>
                        <span class="text-xs text-stone-500 block">Catat setoran SPP siswa</span>
                    </div>
                </a>

                <a href="{{ route('finance.tagihan') }}" class="p-4 bg-stone-50 border border-stone-200 hover:border-green-300 rounded-2xl flex items-center gap-4 group transition duration-200">
                    <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl border border-blue-200 group-hover:bg-blue-600 group-hover:text-white transition duration-200">
                        <x-lucide-file-text class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-stone-800">Kelola Tagihan</h4>
                        <span class="text-xs text-stone-500 block">Buat tagihan SPP bulanan</span>
                    </div>
                </a>

                <a href="{{ route('finance.arus-kas') }}" class="p-4 bg-stone-50 border border-stone-200 hover:border-green-300 rounded-2xl flex items-center gap-4 group transition duration-200">
                    <div class="p-2.5 bg-red-50 text-red-600 rounded-xl border border-red-200 group-hover:bg-red-600 group-hover:text-white transition duration-200">
                        <x-lucide-trending-down class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-stone-800">Pengeluaran Kas</h4>
                        <span class="text-xs text-stone-500 block">Catat pengeluaran operasional</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
