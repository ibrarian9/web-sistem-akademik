<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Input Pembayaran Siswa</h2>
        <p class="text-xs text-slate-500">Pilih siswa, tagihan aktif, lalu rekam bukti setoran cicilan atau pelunasan.</p>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Student Selection Panel -->
        <div class="lg:col-span-1 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4 h-fit">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider border-b border-slate-850 pb-2">Pilih Penerima</h3>
            
            <!-- Kelas -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kelas</label>
                <select wire:model.live="kelas_id" class="w-full px-3 py-2.5 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                    <option value="">Pilih Kelas</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Siswa -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Siswa</label>
                <select wire:model.live="siswa_id" class="w-full px-3 py-2.5 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" {{ empty($students) ? 'disabled' : '' }}>
                    <option value="">Pilih Siswa</option>
                    @foreach ($students as $s)
                        <option value="{{ $s['id'] }}">{{ $s['user']['nama'] ?? '-' }} (NIS: {{ $s['nis'] }})</option>
                    @endforeach
                </select>
                @error('siswa_id') <span class="text-rose-400 text-[10px] block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Tagihan Aktif -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tagihan Aktif</label>
                <select wire:model.live="tagihan_id" class="w-full px-3 py-2.5 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" {{ empty($unpaidInvoices) ? 'disabled' : '' }}>
                    <option value="">Pilih Tagihan</option>
                    @foreach ($unpaidInvoices as $ui)
                        <option value="{{ $ui['id'] }}">{{ $ui['jenis_tagihan']['nama'] }} (Periode: {{ $ui['bulan'] }})</option>
                    @endforeach
                </select>
                @error('tagihan_id') <span class="text-rose-400 text-[10px] block mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Payment Details & Form Panel -->
        <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
            @if ($selectedInvoiceInfo)
                <div class="space-y-6">
                    <!-- Invoice Summary Card -->
                    <div class="p-5 bg-slate-950/40 border border-slate-850 rounded-2xl space-y-4">
                        <div class="flex justify-between items-center border-b border-slate-850 pb-2">
                            <div>
                                <h4 class="text-xs font-bold text-white">Detail Tagihan Terpilih</h4>
                                <span class="text-[9px] text-slate-500 font-bold block uppercase tracking-wider">Kategori: {{ $selectedInvoiceInfo['jenis'] }} | Periode: {{ $selectedInvoiceInfo['periode'] }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="p-2.5 bg-slate-900/60 border border-slate-850 rounded-xl">
                                <span class="text-[8px] text-slate-500 font-bold block uppercase tracking-wider">Total Tagihan</span>
                                <span class="text-xs font-black text-white block mt-0.5">Rp {{ number_format($selectedInvoiceInfo['nominal'], 0, ',', '.') }}</span>
                            </div>
                            <div class="p-2.5 bg-slate-900/60 border border-slate-850 rounded-xl">
                                <span class="text-[8px] text-slate-500 font-bold block uppercase tracking-wider">Telah Dibayar</span>
                                <span class="text-xs font-black text-emerald-400 block mt-0.5">Rp {{ number_format($selectedInvoiceInfo['total_dibayar'], 0, ',', '.') }}</span>
                            </div>
                            <div class="p-2.5 bg-slate-900/60 border border-slate-850 rounded-xl">
                                <span class="text-[8px] text-slate-500 font-bold block uppercase tracking-wider">Sisa Tunggakan</span>
                                <span class="text-xs font-black text-rose-450 block mt-0.5">Rp {{ number_format($selectedInvoiceInfo['sisa'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Record Form -->
                    <form wire:submit.prevent="savePayment" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Nominal Bayar -->
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nominal Bayar (Rp)</label>
                                <input wire:model="nominal_dibayar" type="number" class="w-full px-3 py-2.5 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 font-bold text-right text-emerald-400" />
                                @error('nominal_dibayar') <span class="text-rose-400 text-[10px] block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tanggal Bayar -->
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Bayar</label>
                                <input wire:model="tanggal_bayar" type="date" class="w-full px-3 py-2.5 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
                                @error('tanggal_bayar') <span class="text-rose-400 text-[10px] block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Metode Bayar -->
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Metode Pembayaran</label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach (['Tunai', 'Transfer Bank', 'E-Wallet'] as $method)
                                    <label class="flex items-center justify-center gap-2 p-3 bg-slate-950/30 border rounded-xl cursor-pointer text-xs font-semibold select-none transition duration-150
                                        {{ $metode_bayar === $method ? 'border-indigo-650 text-indigo-400 bg-indigo-500/5' : 'border-slate-800 text-slate-400 hover:border-slate-750' }}
                                    ">
                                        <input type="radio" wire:model="metode_bayar" value="{{ $method }}" class="hidden" />
                                        <span>{{ $method }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('metode_bayar') <span class="text-rose-400 text-[10px] block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end pt-4 border-t border-slate-850">
                            <button type="submit" class="py-3 px-8 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition duration-200 shadow-lg shadow-emerald-600/10">
                                Rekam Setoran Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="h-[250px] flex flex-col items-center justify-center text-slate-500 font-medium text-xs">
                    <x-lucide-credit-card class="w-8 h-8 text-slate-650 mb-3 animate-pulse" />
                    <span>Pilih siswa dan tagihan aktif di sebelah kiri untuk merekam pembayaran.</span>
                </div>
            @endif
        </div>
    </div>
</div>
