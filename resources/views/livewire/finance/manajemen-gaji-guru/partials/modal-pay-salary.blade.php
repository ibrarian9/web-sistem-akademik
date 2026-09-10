{{-- 6. Modal Konfirmasi Pembayaran Gaji Pegawai (Input Bukti Struk / TF) --}}
@if ($showPayModal && $paySalaryRecord)
    <x-floating-card 
        :show="true" 
        :title="'Input Pembayaran Gaji — ' . ($paySalaryRecord->guru->user->nama ?? 'Pegawai')" 
        :subtitle="'Periode: ' . $paySalaryRecord->bulan . ' ' . $paySalaryRecord->tahun . ' • Jabatan: ' . ($paySalaryRecord->jabatan ?: 'Guru')" 
        badge="PEMBAYARAN GAJI" 
        badgeVariant="emerald" 
        icon="credit-card" 
        maxWidth="max-w-xl" 
        closeAction="closePayModal"
        zIndex="z-[99995]"
    >
        <form wire:submit.prevent="confirmPaySalary" class="space-y-4 font-sans">
            <!-- Nominal Card -->
            <div class="p-4 bg-emerald-900 text-white rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-emerald-300 block">Total Gaji Yang Dibayarkan (THP)</span>
                    <span class="text-2xl font-black text-white block mt-0.5 font-mono">
                        Rp {{ number_format($paySalaryRecord->total_diterima, 0, ',', '.') }}
                    </span>
                </div>
                <div class="p-2.5 bg-emerald-800 rounded-xl text-emerald-200 shrink-0">
                    <x-lucide-wallet class="w-6 h-6" />
                </div>
            </div>

            <!-- Tanggal Bayar -->
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                    Tanggal Pembayaran <span class="text-rose-500">*</span>
                </label>
                <input type="date" wire:model="payTanggalBayar" 
                       class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" required />
                @error('payTanggalBayar') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Input Foto Bukti TF / Struk Nota -->
            <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl space-y-2.5">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-black text-stone-800 uppercase tracking-wider">
                        Foto Bukti Transfer / Struk Nota <span class="text-stone-400 font-normal lowercase">(opsional)</span>
                    </label>
                    <span class="text-[10px] text-stone-500 font-semibold">Maks 2MB (JPG/PNG/WEBP)</span>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <div class="relative flex-1 w-full">
                        <input type="file" wire:model="payBuktiFoto" accept="image/jpeg,image/png,image/jpg,image/webp" 
                               class="w-full text-xs text-stone-600 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer border border-stone-300 rounded-xl bg-white p-1" />
                    </div>

                    @if ($payBuktiFoto)
                        <div class="flex items-center gap-2 shrink-0 bg-white p-1.5 rounded-xl border border-emerald-300">
                            <img src="{{ $payBuktiFoto->temporaryUrl() }}" alt="Pratinjau" class="w-12 h-12 rounded-lg object-cover" />
                            <button type="button" wire:click="$set('payBuktiFoto', null)" class="text-xs text-rose-600 font-bold hover:underline px-1 cursor-pointer">
                                Hapus
                            </button>
                        </div>
                    @endif
                </div>

                <div wire:loading wire:target="payBuktiFoto" class="text-xs text-emerald-600 font-semibold flex items-center gap-1.5">
                    <x-lucide-loader-2 class="w-3.5 h-3.5 animate-spin" />
                    <span>Mengunggah foto bukti...</span>
                </div>

                @error('payBuktiFoto') <span class="text-xs text-rose-600 font-medium block">{{ $message }}</span> @enderror
            </div>

            <!-- Catatan Pembayaran -->
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                    Catatan / Referensi Pembayaran <span class="text-stone-400 font-normal lowercase">(opsional)</span>
                </label>
                <input type="text" wire:model="payCatatan" placeholder="Contoh: Transfer BSI Rekening Guru / Tunai / Kwitansi no. 123" 
                       class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                @error('payCatatan') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Footer Buttons -->
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button type="button" variant="secondary" size="md" wire:click="closePayModal">
                    Batal
                </x-button>

                <x-button type="submit" variant="primary" size="md" icon="check-circle" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="confirmPaySalary">Konfirmasi & Simpan Pembayaran</span>
                    <span wire:loading wire:target="confirmPaySalary">Memproses...</span>
                </x-button>
            </div>
        </form>
    </x-floating-card>
@endif
