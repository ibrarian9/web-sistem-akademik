<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Panduan Pusat Kendali Keuangan Sekolah (Finance)"
        :steps="[
            ['title' => 'Ringkasan Kas', 'desc' => 'Pantau saldo total kas, pemasukan bulan berjalan, dan pengeluaran operasional secara realtime.'],
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
                            <td class="p-3 text-xs font-black text-emerald-800 text-right border-r border-stone-200">Rp {{ number_format($pay['nominal'], 0, ',', '.') }}</td>
                            <td class="p-3 text-xs font-semibold text-stone-600 text-center border-r border-stone-200">{{ $pay['tanggal'] }}</td>
                            <td class="p-3 text-center">
                                <a href="{{ route('pembayaran.resi', $pay['id']) }}" target="_blank" class="p-1.5 bg-stone-100 hover:bg-emerald-100 text-stone-700 hover:text-emerald-900 rounded-lg inline-flex items-center justify-center border border-stone-300 transition" title="Cetak Resi">
                                    <x-lucide-printer class="w-3.5 h-3.5" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <x-table.empty :colspan="6" title="Belum ada transaksi pembayaran masuk" message="Transaksi kasir pembayaran siswa akan tampil di sini." />
                    @endforelse
                </tbody>
            </x-table>
        </div>

        <!-- Quick Access panel -->
        <div class="lg:col-span-1 bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
            <h3 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider border-b border-stone-200 pb-3">
                Menu Cepat Keuangan
            </h3>
            
            <div class="grid grid-cols-1 gap-2.5">
                <a href="{{ route('finance.input-pembayaran') }}" class="p-3.5 bg-stone-50 border border-stone-200 hover:border-emerald-500 hover:bg-emerald-50/50 rounded-2xl flex items-center gap-3.5 group transition duration-150">
                    <div class="p-2.5 bg-emerald-100 text-emerald-700 rounded-xl border border-emerald-300 group-hover:bg-emerald-700 group-hover:text-white transition duration-150 shrink-0">
                        <x-lucide-plus-circle class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-stone-900">Input Pembayaran</h4>
                        <span class="text-[11px] text-stone-500 font-medium block">Catat setoran SPP siswa</span>
                    </div>
                </a>

                <a href="{{ route('finance.tagihan') }}" class="p-3.5 bg-stone-50 border border-stone-200 hover:border-emerald-500 hover:bg-emerald-50/50 rounded-2xl flex items-center gap-3.5 group transition duration-150">
                    <div class="p-2.5 bg-sky-100 text-sky-700 rounded-xl border border-sky-300 group-hover:bg-sky-700 group-hover:text-white transition duration-150 shrink-0">
                        <x-lucide-file-text class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-stone-900">Kelola Tagihan</h4>
                        <span class="text-[11px] text-stone-500 font-medium block">Buat tagihan SPP bulanan</span>
                    </div>
                </a>

                <a href="{{ route('finance.tabungan') }}" class="p-3.5 bg-stone-50 border border-stone-200 hover:border-emerald-500 hover:bg-emerald-50/50 rounded-2xl flex items-center gap-3.5 group transition duration-150">
                    <div class="p-2.5 bg-purple-100 text-purple-700 rounded-xl border border-purple-300 group-hover:bg-purple-700 group-hover:text-white transition duration-150 shrink-0">
                        <x-lucide-wallet class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-stone-900">Tabungan Siswa</h4>
                        <span class="text-[11px] text-stone-500 font-medium block">Setor & tarik tabungan</span>
                    </div>
                </a>

                <a href="{{ route('finance.dana-bos') }}" class="p-3.5 bg-stone-50 border border-stone-200 hover:border-emerald-500 hover:bg-emerald-50/50 rounded-2xl flex items-center gap-3.5 group transition duration-150">
                    <div class="p-2.5 bg-amber-100 text-amber-700 rounded-xl border border-amber-300 group-hover:bg-amber-700 group-hover:text-white transition duration-150 shrink-0">
                        <x-lucide-box class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-stone-900">Dana BOS (Masuk & Keluar)</h4>
                        <span class="text-[11px] text-stone-500 font-medium block">Realisasi belanja RKAS BOS</span>
                    </div>
                </a>

                <a href="{{ route('finance.arus-kas-masuk') }}" class="p-3.5 bg-stone-50 border border-stone-200 hover:border-emerald-500 hover:bg-emerald-50/50 rounded-2xl flex items-center gap-3.5 group transition duration-150">
                    <div class="p-2.5 bg-emerald-100 text-emerald-700 rounded-xl border border-emerald-300 group-hover:bg-emerald-700 group-hover:text-white transition duration-150 shrink-0">
                        <x-lucide-heart-handshake class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-stone-900">Kas Masuk Yayasan</h4>
                        <span class="text-[11px] text-stone-500 font-medium block">Infaq, sedekah & donasi</span>
                    </div>
                </a>

                <a href="{{ route('finance.arus-kas-keluar') }}" class="p-3.5 bg-stone-50 border border-stone-200 hover:border-emerald-500 hover:bg-emerald-50/50 rounded-2xl flex items-center gap-3.5 group transition duration-150">
                    <div class="p-2.5 bg-rose-100 text-rose-700 rounded-xl border border-rose-300 group-hover:bg-rose-700 group-hover:text-white transition duration-150 shrink-0">
                        <x-lucide-trending-down class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-stone-900">Kas Keluar Yayasan</h4>
                        <span class="text-[11px] text-stone-500 font-medium block">Pengeluaran operasional</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
