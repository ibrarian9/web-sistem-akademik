<!-- MODAL: Upload Foto Bukti Transaksi -->
<x-floating-card 
    :show="$showUploadBuktiModal" 
    title="Upload Foto Bukti Transaksi" 
    subtitle="Unggah dokumen bukti transaksi (kuitansi, struk belanja, nota, atau bukti transfer)."
    icon="camera"
    maxWidth="max-w-md"
    closeAction="closeUploadBuktiModal"
>
    <form wire:submit.prevent="saveUploadedBukti" class="space-y-4">
        <!-- Info Transaksi Ringkas -->
        <div class="p-3.5 bg-stone-50 border border-stone-200 rounded-xl space-y-1.5 text-xs">
            <div class="flex items-center justify-between text-stone-500 font-medium">
                <span>Kategori:</span>
                <span class="font-bold text-stone-800">{{ $uploadTargetKategori }}</span>
            </div>
            <div class="flex items-center justify-between text-stone-500 font-medium">
                <span>Nominal:</span>
                <span class="font-extrabold text-emerald-700">Rp {{ number_format($uploadTargetNominal ?? 0, 0, ',', '.') }}</span>
            </div>
            @if($uploadTargetKeterangan)
                <div class="pt-1.5 border-t border-stone-200/80 text-[11px] text-stone-600 line-clamp-2">
                    {{ $uploadTargetKeterangan }}
                </div>
            @endif
        </div>

        <!-- File Upload Area -->
        <div>
            <label class="block text-xs font-bold text-stone-700 mb-1.5">
                Pilih Foto Bukti
            </label>

            @if ($uploadBuktiFile)
                <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-200 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="{{ $uploadBuktiFile->temporaryUrl() }}" alt="Pratinjau" class="w-12 h-12 object-cover rounded-lg border border-emerald-300 shadow-2xs shrink-0" />
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-stone-900 truncate block">{{ $uploadBuktiFile->getClientOriginalName() }}</span>
                            <span class="text-[10px] text-stone-500 font-semibold">{{ number_format($uploadBuktiFile->getSize() / 1024, 1) }} KB</span>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('uploadBuktiFile', null)" class="px-2 py-1 text-[11px] font-bold text-rose-700 bg-white border border-rose-200 rounded-lg hover:bg-rose-50 cursor-pointer shrink-0">
                        Batal
                    </button>
                </div>
            @else
                <label class="border-2 border-dashed border-stone-300 hover:border-emerald-500 rounded-xl p-5 flex flex-col items-center justify-center gap-2 cursor-pointer bg-stone-50/50 hover:bg-emerald-50/20 transition group">
                    <div class="w-10 h-10 rounded-full bg-stone-100 group-hover:bg-emerald-100 flex items-center justify-center text-stone-500 group-hover:text-emerald-700 transition">
                        <x-lucide-upload class="w-5 h-5" />
                    </div>
                    <div class="text-center">
                        <span class="text-xs font-bold text-stone-800 group-hover:text-emerald-800 block">Klik untuk memilih file</span>
                        <span class="text-[11px] text-stone-500 font-medium">Maks. 2MB (JPG, JPEG, PNG, atau WEBP)</span>
                    </div>
                    <input type="file" wire:model="uploadBuktiFile" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" />
                </label>
            @endif

            <div wire:loading wire:target="uploadBuktiFile" class="text-[11px] text-emerald-700 font-bold mt-2 flex items-center gap-1.5">
                <x-lucide-loader-2 class="w-3.5 h-3.5 animate-spin" />
                <span>Sedang memuat foto...</span>
            </div>

            @error('uploadBuktiFile')
                <span class="text-[11px] text-rose-600 font-bold mt-1.5 block">{{ $message }}</span>
            @enderror
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-stone-200">
            <x-button type="button" variant="secondary" size="sm" wire:click="closeUploadBuktiModal">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="sm" icon="check" wire:loading.attr="disabled" wire:target="uploadBuktiFile, saveUploadedBukti">
                <span wire:loading.remove wire:target="saveUploadedBukti">Simpan Bukti</span>
                <span wire:loading wire:target="saveUploadedBukti">Menyimpan...</span>
            </x-button>
        </div>
    </form>
</x-floating-card>
