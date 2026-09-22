<!-- MODAL INPUT PEMBAYARAN LANGSUNG (QUICK PAY) PADA RINCIAN SISWA -->
@if ($showQuickPayModal && $quickPayTagihan)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-xs transition-opacity animate-fade-in font-sans">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-stone-200 space-y-5 animate-scale-up" @click.outside="$wire.closeQuickPay()">
            <!-- Header Modal -->
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="p-2.5 bg-emerald-100 text-emerald-800 rounded-xl border border-emerald-200">
                        <x-lucide-credit-card class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-stone-900 uppercase tracking-tight">Input Pembayaran Langsung</h3>
                        <p class="text-xs text-stone-500">Catat pelunasan atau cicilan tagihan santri langsung dari tabel.</p>
                    </div>
                </div>
                <button 
                    type="button" 
                    wire:click="closeQuickPay" 
                    class="p-1.5 rounded-xl text-stone-400 hover:text-stone-700 hover:bg-stone-100 transition cursor-pointer"
                >
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>

            <!-- Identitas Siswa & Tagihan -->
            <div class="p-3.5 bg-stone-50 border border-stone-200 rounded-xl space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-stone-500 font-bold uppercase text-[10px]">Santri / Siswa:</span>
                    <span class="font-extrabold text-stone-900">{{ $quickPayTagihan->siswa->user->nama ?? '-' }} (NIS: {{ $quickPayTagihan->siswa->nis ?? '-' }})</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-stone-500 font-bold uppercase text-[10px]">Jenis Tagihan:</span>
                    <span class="font-bold text-stone-800">{{ $quickPayTagihan->jenisTagihan->nama ?? '-' }} • Periode {{ $quickPayTagihan->bulan }}</span>
                </div>
                <div class="flex items-center justify-between text-xs pt-1 border-t border-stone-200">
                    <span class="text-rose-700 font-bold uppercase text-[10px]">Sisa Tagihan:</span>
                    <span class="font-mono font-black text-rose-700 text-sm">
                        Rp {{ number_format(max(0, floatval($quickPayTagihan->nominal) - floatval($quickPayTagihan->total_dibayar)), 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Form Fields -->
            <form wire:submit.prevent="saveQuickPay" class="space-y-3.5">
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Nominal Bayar (Rp) *</label>
                    <input 
                        type="number" 
                        wire:model="quickPayNominal" 
                        min="1" 
                        max="{{ max(0, floatval($quickPayTagihan->nominal) - floatval($quickPayTagihan->total_dibayar)) }}" 
                        class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-sm font-mono font-black text-stone-900 focus:ring-2 focus:ring-emerald-600 shadow-2xs"
                        required
                    />
                    @error('quickPayNominal')
                        <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Metode Bayar *</label>
                        <select wire:model="quickPayMetode" class="w-full bg-white border border-stone-300 rounded-xl px-3 py-2 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                            <option value="Tunai">Tunai</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="Deposit">Deposit</option>
                            <option value="Beasiswa">Beasiswa</option>
                        </select>
                        @error('quickPayMetode')
                            <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Tanggal Bayar *</label>
                        <input 
                            type="date" 
                            wire:model="quickPayTanggal" 
                            class="w-full bg-white border border-stone-300 rounded-xl px-3 py-2 text-xs font-bold text-stone-900 focus:ring-2 focus:ring-emerald-600 shadow-2xs"
                            required
                        />
                        @error('quickPayTanggal')
                            <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-3 border-t border-stone-100 flex items-center justify-between gap-2">
                    <a 
                        href="{{ route('finance.input-pembayaran', ['siswa_id' => $quickPayTagihan->siswa_id, 'tagihan_id' => $quickPayTagihan->id]) }}" 
                        class="text-xs font-bold text-emerald-800 hover:text-emerald-950 underline flex items-center gap-1"
                        title="Buka kasir untuk cetak struk atau upload bukti transfer"
                    >
                        <span>Kasir Lengkap</span>
                        <x-lucide-external-link class="w-3 h-3" />
                    </a>

                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            wire:click="closeQuickPay" 
                            class="px-3 py-2 text-xs font-bold rounded-xl border border-stone-300 text-stone-600 hover:bg-stone-100 transition cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 text-xs font-black rounded-xl bg-emerald-800 hover:bg-emerald-900 text-white shadow-xs transition flex items-center gap-1.5 cursor-pointer"
                        >
                            <x-lucide-check class="w-4 h-4" />
                            <span>Simpan Pembayaran</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif
