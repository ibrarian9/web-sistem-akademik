<!-- MODAL BUKTI PEMBAYARAN: LIHAT, UPLOAD & EDIT FOTO (STRUK / TF) -->
@if ($showBuktiModal && $selectedPembayaran)
    <x-floating-card 
        :show="true"
        title="Bukti Pembayaran" 
        :subtitle="'Kwitansi ' . ($selectedPembayaran->no_resi ?: ('RES-' . str_pad($selectedPembayaran->id, 5, '0', STR_PAD_LEFT))) . ' • ' . ($siswa->user->nama ?? 'Siswa')"
        badge="BUKTI BAYAR"
        badgeVariant="emerald"
        icon="image"
        maxWidth="max-w-md"
        closeAction="closeBuktiModal"
    >
        <div class="space-y-4 text-xs">
            <!-- Info Ringkas Transaksi -->
            <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1.5 text-[11px]">
                <div class="flex justify-between">
                    <span class="text-stone-500 font-medium">Tagihan:</span>
                    <span class="font-bold text-stone-800">{{ $selectedPembayaran->tagihan->jenisTagihan->nama ?? '-' }} ({{ $selectedPembayaran->tagihan->bulan ?? '-' }})</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-stone-500 font-medium">Nominal:</span>
                    <span class="font-black text-emerald-700">Rp {{ number_format($selectedPembayaran->nominal_dibayar, 0, ',', '.') }} ({{ $selectedPembayaran->metode_bayar }})</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-stone-500 font-medium">Tanggal Bayar:</span>
                    <span class="font-semibold text-stone-700">{{ $selectedPembayaran->tanggal_bayar ? $selectedPembayaran->tanggal_bayar->format('d/m/Y') : '-' }}</span>
                </div>
            </div>

            <!-- Foto Bukti Existing -->
            @if ($selectedPembayaran->bukti_bayar)
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-stone-700 uppercase tracking-wider text-[10px]">Foto Bukti Tersimpan</span>
                        <a href="{{ asset('storage/' . $selectedPembayaran->bukti_bayar) }}" target="_blank" class="text-emerald-700 hover:text-emerald-900 hover:underline font-bold text-[11px] flex items-center gap-1">
                            <x-lucide-external-link class="w-3 h-3" />
                            <span>Lihat Penuh</span>
                        </a>
                    </div>
                    <div class="rounded-xl overflow-hidden border border-stone-300 bg-stone-900/90 shadow-2xs p-1">
                        <img src="{{ asset('storage/' . $selectedPembayaran->bukti_bayar) }}" alt="Bukti Pembayaran" class="w-full max-h-60 object-contain mx-auto rounded-lg" />
                    </div>
                    @if (!auth()->user()->isSuperAdmin2())
                        <div class="flex justify-end">
                            <button type="button" wire:click="deleteBuktiFoto" wire:confirm="Hapus foto bukti pembayaran ini?" class="text-rose-600 hover:text-rose-800 text-[11px] font-bold inline-flex items-center gap-1 cursor-pointer">
                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                <span>Hapus Foto Bukti</span>
                            </button>
                        </div>
                    @endif
                </div>
            @else
                <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-xl text-center text-amber-900">
                    <x-lucide-image-off class="w-7 h-7 text-amber-500 mx-auto mb-1" />
                    <span class="font-bold text-xs block">Belum Ada Foto Bukti</span>
                    <span class="text-[11px] text-amber-700">Unggah foto struk kasir atau screenshot bukti transfer bank di bawah ini.</span>
                </div>
            @endif

            <!-- Upload / Edit Form -->
            @if (!auth()->user()->isSuperAdmin2())
                <form wire:submit.prevent="saveBuktiFoto" class="space-y-3 pt-2 border-t border-stone-200">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                            {{ $selectedPembayaran->bukti_bayar ? 'Ganti Foto Bukti Pembayaran' : 'Unggah Foto Bukti Pembayaran' }}
                        </label>
                        
                        @if ($edit_bukti_foto)
                            <div class="p-2.5 bg-emerald-50 border border-emerald-300 rounded-xl flex items-center justify-between gap-2 shadow-2xs">
                                <div class="flex items-center gap-2 min-w-0">
                                    <img src="{{ $edit_bukti_foto->temporaryUrl() }}" alt="Preview" class="w-10 h-10 object-cover rounded-lg border border-emerald-200 shadow-2xs shrink-0" />
                                    <div class="min-w-0">
                                        <span class="text-xs font-bold text-emerald-950 block truncate">{{ $edit_bukti_foto->getClientOriginalName() }}</span>
                                        <span class="text-[10px] text-emerald-700 font-semibold">{{ number_format($edit_bukti_foto->getSize() / 1024, 1) }} KB</span>
                                    </div>
                                </div>
                                <button type="button" wire:click="$set('edit_bukti_foto', null)" class="px-2 py-1 text-[11px] font-bold text-rose-700 bg-white border border-rose-200 rounded-lg shrink-0 cursor-pointer">
                                    Batal
                                </button>
                            </div>
                        @else
                            <div class="relative border-2 border-dashed border-stone-300 hover:border-emerald-500 rounded-xl p-3 bg-stone-50 hover:bg-emerald-50/20 text-center transition group cursor-pointer">
                                <input 
                                    type="file" 
                                    wire:model="edit_bukti_foto" 
                                    accept="image/jpeg,image/png,image/jpg,image/webp" 
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                                />
                                <div class="flex items-center justify-center gap-2 pointer-events-none text-stone-600 group-hover:text-emerald-800">
                                    <x-lucide-camera class="w-4 h-4 text-emerald-600" />
                                    <span class="text-xs font-bold">Pilih file foto (Maks. 2MB, JPG/PNG/WEBP)</span>
                                </div>
                            </div>
                        @endif
                        <div wire:loading wire:target="edit_bukti_foto" class="text-[11px] text-emerald-700 font-bold mt-1 flex items-center gap-1.5">
                            <span class="animate-spin inline-block w-3 h-3 border-2 border-emerald-600 border-t-transparent rounded-full"></span>
                            <span>Mengunggah foto...</span>
                        </div>
                        @error('edit_bukti_foto') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <x-button type="button" variant="secondary" size="sm" wire:click="closeBuktiModal">Tutup</x-button>
                        <x-button type="submit" variant="primary" size="sm" icon="check" loadingTarget="saveBuktiFoto" :disabled="!$edit_bukti_foto">
                            Simpan Foto Bukti
                        </x-button>
                    </div>
                </form>
            @else
                <div class="flex justify-end pt-2 border-t border-stone-200">
                    <x-button type="button" variant="secondary" size="sm" wire:click="closeBuktiModal">Tutup</x-button>
                </div>
            @endif
        </div>
    </x-floating-card>
@endif
