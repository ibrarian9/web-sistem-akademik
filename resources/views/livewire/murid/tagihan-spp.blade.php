<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Tagihan & Keuangan Murid</h2>
        <p class="text-xs text-slate-500">Lihat status pembayaran SPP dan tagihan administrasi sekolah lainnya.</p>
    </div>

    <!-- Outstanding Summary Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="space-y-1 text-center sm:text-left">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Total Tunggakan Aktif</span>
            <span class="text-2xl font-black text-rose-400 tracking-tight">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</span>
        </div>
        
        <div class="px-5 py-3 rounded-2xl text-xs font-bold text-center
            {{ $totalTunggakan > 0 ? 'bg-amber-500/10 border border-amber-500/20 text-amber-400' : 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-400' }}
        ">
            {{ $totalTunggakan > 0 ? 'Terdapat Tunggakan Aktif' : 'Semua Tagihan Lunas' }}
        </div>
    </div>

    <!-- Invoices List -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Daftar Tagihan Murid</h3>
        
        <div class="space-y-4">
            @forelse ($invoices as $inv)
                <div class="p-4 bg-slate-950/40 border border-slate-850 rounded-2xl space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-850 pb-2">
                        <div>
                            <h4 class="text-xs font-bold text-white">{{ $inv['jenis'] }}</h4>
                            <span class="text-[9px] text-slate-500 font-bold block uppercase tracking-wider">Periode: {{ $inv['bulan'] }} | Jatuh Tempo: {{ $inv['jatuh_tempo'] }}</span>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                {{ $inv['status'] === 'lunas' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                {{ $inv['status'] === 'sebagian' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                {{ $inv['status'] === 'belum_bayar' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : '' }}
                            ">
                                {{ $inv['status'] === 'belum_bayar' ? 'Belum Bayar' : ($inv['status'] === 'sebagian' ? 'Dibayar Sebagian' : 'Lunas') }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="p-2 bg-slate-900/60 border border-slate-850 rounded-xl">
                            <span class="text-[8px] text-slate-500 font-bold block uppercase tracking-wider">Nominal Tagihan</span>
                            <span class="text-xs font-black text-white block mt-0.5">Rp {{ number_format($inv['nominal'], 0, ',', '.') }}</span>
                        </div>
                        <div class="p-2 bg-slate-900/60 border border-slate-850 rounded-xl">
                            <span class="text-[8px] text-slate-500 font-bold block uppercase tracking-wider">Telah Dibayar</span>
                            <span class="text-xs font-black text-emerald-400 block mt-0.5">Rp {{ number_format($inv['total_dibayar'], 0, ',', '.') }}</span>
                        </div>
                        <div class="p-2 bg-slate-900/60 border border-slate-850 rounded-xl">
                            <span class="text-[8px] text-slate-500 font-bold block uppercase tracking-wider">Sisa Pembayaran</span>
                            <span class="text-xs font-black text-rose-400 block mt-0.5">Rp {{ number_format($inv['sisa'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Payment Sub-logs (History of payment cycles) -->
                    @if (count($inv['pembayaran']) > 0)
                        <div class="pt-2">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Riwayat Pembayaran Cicilan / Pelunasan</span>
                            <div class="space-y-1.5">
                                @foreach ($inv['pembayaran'] as $pay)
                                    <div class="px-3 py-1.5 bg-slate-900/40 border border-slate-850 rounded-lg flex items-center justify-between text-[10px]">
                                        <div class="space-y-0.5">
                                            <span class="text-slate-400 block">Metode: {{ $pay['metode'] }}</span>
                                            <span class="text-[8px] text-slate-500 block">Tanggal: {{ $pay['tanggal'] }}</span>
                                        </div>
                                        <span class="font-bold text-emerald-400">Rp {{ number_format($pay['jumlah'], 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="py-8 text-center text-slate-500 font-semibold text-xs">
                    Belum ada tagihan terdaftar untuk akun Anda.
                </div>
            @endforelse
        </div>
    </div>
</div>
