<!-- MODAL: Upload Foto Bukti Transaksi (Single & Bulk Support) -->
<x-floating-card 
    :show="$showUploadBuktiModal" 
    :title="$isBulkUpload ? 'Upload Bukti Massal (' . count($selectedTransactionIds) . ' Transaksi)' : 'Upload Foto Bukti Transaksi'" 
    :subtitle="$isBulkUpload ? 'Unggah satu foto bukti bersama untuk ' . count($selectedTransactionIds) . ' transaksi kas yang dipilih.' : 'Unggah dokumen bukti transaksi (kuitansi, struk belanja, nota, atau bukti transfer).'"
    icon="camera"
    maxWidth="max-w-lg"
    closeAction="closeUploadBuktiModal"
>
    <form wire:submit.prevent="saveUploadedBukti" class="space-y-4">
        @if ($isBulkUpload)
            <!-- Info Transaksi Massal -->
            <div class="p-3.5 bg-stone-50 border border-stone-200 rounded-xl space-y-2.5 text-xs">
                <div class="flex items-center justify-between text-stone-600">
                    <span class="font-medium">Total Transaksi Dipilih:</span>
                    <span class="font-extrabold text-stone-900 bg-stone-200/80 px-2 py-0.5 rounded-md">{{ count($selectedTransactionIds) }} Transaksi</span>
                </div>
                <div class="flex items-center justify-between text-stone-600">
                    <span class="font-medium">Total Akumulasi Nominal:</span>
                    <span class="font-mono font-black text-emerald-800 text-sm">
                        Rp {{ number_format($this->selectedTransactions->sum(fn($t) => $t->nominal_masuk > 0 ? $t->nominal_masuk : $t->nominal_keluar), 0, ',', '.') }}
                    </span>
                </div>
                <div class="pt-2 border-t border-stone-200">
                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block mb-1.5">Rincian Transaksi Terpilih:</span>
                    <div class="max-h-36 overflow-y-auto space-y-1.5 pr-1">
                        @foreach ($this->selectedTransactions as $st)
                            <div class="flex items-center justify-between p-2 rounded-lg bg-white border border-stone-200 text-[11px]">
                                <div class="min-w-0 pr-2">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="font-extrabold text-stone-900 truncate">{{ $st->kategori }}</span>
                                        <span class="text-[9px] px-1.5 py-0.2 rounded font-bold uppercase tracking-wider {{ $st->type === 'masuk' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                            {{ $st->type }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-stone-400 block truncate mt-0.5">{{ $st->keterangan }}</span>
                                </div>
                                <span class="font-mono font-black text-xs shrink-0 {{ $st->type === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                                    Rp {{ number_format($st->nominal_masuk > 0 ? $st->nominal_masuk : $st->nominal_keluar, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <!-- Info Transaksi Tunggal -->
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
        @endif

        <!-- File Upload Area with Instant Canvas Compression -->
        <div x-data="{ 
            uploading: false, 
            progress: 0,
            compressAndUpload(file) {
                if (!file) return;
                this.uploading = true;
                this.progress = 10;
                
                // If not image or smaller than 350KB, upload directly
                if (!file.type.match(/image.*/) || file.size <= 350 * 1024) {
                    this.progress = 40;
                    @this.upload('uploadBuktiFile', file, 
                        () => { this.uploading = false; this.progress = 100; }, 
                        () => { this.uploading = false; }, 
                        (e) => { this.progress = e.detail.progress; }
                    );
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;
                        const maxDim = 1600;
                        if (width > maxDim || height > maxDim) {
                            if (width > height) {
                                height = Math.round((height * maxDim) / width);
                                width = maxDim;
                            } else {
                                width = Math.round((width * maxDim) / height);
                                height = maxDim;
                            }
                        }
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        canvas.toBlob((blob) => {
                            this.progress = 60;
                            const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, '.jpg'), { type: 'image/jpeg' });
                            @this.upload('uploadBuktiFile', compressedFile, 
                                () => { this.uploading = false; this.progress = 100; }, 
                                () => { this.uploading = false; }, 
                                (e) => { this.progress = e.detail.progress; }
                            );
                        }, 'image/jpeg', 0.82);
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }">
            <label class="block text-xs font-bold text-stone-700 mb-1.5">
                Pilih Foto Bukti Transaksi
            </label>

            @if ($uploadBuktiFile)
                @php
                    $isImage = false;
                    try {
                        $ext = strtolower($uploadBuktiFile->getClientOriginalExtension());
                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                    } catch (\Throwable $e) {}
                @endphp
                <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-200 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        @if ($isImage)
                            <img src="{{ $uploadBuktiFile->temporaryUrl() }}" alt="Pratinjau" class="w-12 h-12 object-cover rounded-lg border border-emerald-300 shadow-2xs shrink-0" />
                        @else
                            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-700 shrink-0">
                                <x-lucide-file-text class="w-6 h-6" />
                            </div>
                        @endif
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
                        <span class="text-[11px] text-stone-500 font-medium">Maks. 5MB (JPG, JPEG, PNG, atau WEBP) : Kompresi otomatis</span>
                    </div>
                    <input type="file" @change="compressAndUpload($event.target.files[0])" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" />
                </label>
            @endif

            <div x-show="uploading" x-cloak class="mt-2 space-y-1">
                <div class="flex items-center justify-between text-[10px] font-bold text-emerald-800">
                    <span class="flex items-center gap-1.5">
                        <x-lucide-loader-2 class="w-3.5 h-3.5 animate-spin text-emerald-600" />
                        <span>Mengunggah foto bukti...</span>
                    </span>
                    <span x-text="progress + '%'"></span>
                </div>
                <div class="w-full bg-stone-200 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-emerald-600 h-full rounded-full transition-all duration-150" :style="'width: ' + progress + '%'"></div>
                </div>
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
                <span wire:loading.remove wire:target="saveUploadedBukti">
                    Simpan Bukti ({{ $isBulkUpload ? count($selectedTransactionIds) . ' Transaksi' : '1 Transaksi' }})
                </span>
                <span wire:loading wire:target="saveUploadedBukti">Menyimpan...</span>
            </x-button>
        </div>
    </form>
</x-floating-card>
