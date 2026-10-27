<!-- MODAL BUKTI PEMBAYARAN: LIHAT, UPLOAD & EDIT FOTO (STRUK / TF) BERSAMAAN -->
@if ($showBuktiModal && ($selectedPembayaran || count($targetPembayaranIds) > 0))
    <x-floating-card 
        :show="true"
        title="Bukti Pembayaran (Transfer Bank / Struk)" 
        :subtitle="'Siswa: ' . ($siswa->user->nama ?? 'Siswa') . ' • ' . (count($targetPembayaranIds) > 1 ? count($targetPembayaranIds) . ' Transaksi Dipilih' : ($selectedPembayaran->no_resi ?? 'Kwitansi'))"
        badge="BUKTI TRANSFER"
        badgeVariant="emerald"
        icon="image"
        maxWidth="max-w-lg"
        closeAction="closeBuktiModal"
    >
        @php
            $targetInts = array_filter(array_map('intval', $targetPembayaranIds));
            $existingProofs = !empty($targetInts) 
                ? \App\Models\Pembayaran::whereIn('id', $targetInts)->whereNotNull('bukti_bayar')->pluck('bukti_bayar')->unique() 
                : collect();
        @endphp

        <div 
            x-data="{
                targetIds: @entangle('targetPembayaranIds'),
                items: {{ json_encode($this->studentPembayarans->map(fn($p) => [
                    'id' => (int)$p->id,
                    'nominal' => (float)$p->nominal_dibayar,
                    'has_bukti' => !is_null($p->bukti_bayar)
                ])->values()) }},
                get totalNominal() {
                    const current = Array.isArray(this.targetIds) ? this.targetIds.map(Number) : [];
                    const idSet = new Set(current);
                    return this.items
                        .filter(i => idSet.has(Number(i.id)))
                        .reduce((sum, i) => sum + i.nominal, 0);
                },
                selectAll() {
                    this.targetIds = this.items.map(i => i.id);
                },
                selectTanpaBukti() {
                    const noProof = this.items.filter(i => !i.has_bukti).map(i => i.id);
                    this.targetIds = noProof.length > 0 ? noProof : (this.items.length > 0 ? [this.items[0].id] : []);
                },
                clearAll() {
                    this.targetIds = [];
                }
            }"
            class="space-y-4 text-xs"
        >
            <!-- Pilihan Transaksi yang Akan Menggunakan Bukti TF Ini -->
            <div class="space-y-2">
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <div class="flex items-center gap-1.5">
                        <span class="font-bold text-stone-800 uppercase tracking-wider text-[11px]">Pilih Transaksi</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black" :class="(targetIds && targetIds.length > 0) ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'" x-text="(targetIds ? targetIds.length : 0) + ' Transaksi'">
                            {{ count($targetInts) }} Transaksi
                        </span>
                    </div>
                    <div class="flex items-center gap-1 text-[10px]">
                        <button type="button" @click="selectAll()" class="px-2 py-0.5 font-bold text-stone-700 bg-stone-100 hover:bg-stone-200 rounded-md transition cursor-pointer">
                            Semua
                        </button>
                        <button type="button" @click="selectTanpaBukti()" class="px-2 py-0.5 font-bold text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-md transition cursor-pointer">
                            Tanpa Bukti
                        </button>
                        <button type="button" @click="clearAll()" class="px-2 py-0.5 font-bold text-stone-500 hover:text-stone-700 transition cursor-pointer">
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Daftar Transaksi Pembayaran Siswa -->
                <div class="max-h-48 overflow-y-auto space-y-1.5 p-2 bg-stone-50 border border-stone-200 rounded-xl">
                    @forelse ($this->studentPembayarans as $p)
                        <label :class="(targetIds && targetIds.map(Number).includes({{ $p->id }})) ? 'bg-emerald-50/80 border-emerald-300 ring-1 ring-emerald-200' : 'bg-white border-stone-200 hover:border-stone-300'" class="flex items-center justify-between gap-2.5 p-2 rounded-lg border text-xs transition cursor-pointer">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <input 
                                    type="checkbox" 
                                    value="{{ $p->id }}" 
                                    x-model="targetIds"
                                    class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer shrink-0" 
                                />
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="font-mono font-bold text-[11px] text-stone-900 truncate">
                                            {{ $p->no_resi ?: ('RES-' . str_pad($p->id, 5, '0', STR_PAD_LEFT)) }}
                                        </span>
                                        <span class="text-[10px] text-stone-400 font-medium">
                                            ({{ $p->tanggal_bayar ? $p->tanggal_bayar->format('d/m/Y') : '-' }})
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-stone-600 font-medium truncate">
                                        {{ $p->tagihan->jenisTagihan->nama ?? 'Tagihan' }} • Periode: {{ $p->tagihan->bulan ?? '-' }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="font-mono font-black text-xs text-emerald-800">
                                    Rp {{ number_format($p->nominal_dibayar, 0, ',', '.') }}
                                </div>
                                <div class="mt-0.5">
                                    @if ($p->bukti_bayar)
                                        <span class="inline-flex items-center gap-0.5 text-[9px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded border border-emerald-200">
                                            <x-lucide-check class="w-2.5 h-2.5" /> Bukti Ada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-[9px] font-bold text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded border border-amber-200">
                                            Tanpa Bukti
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="p-3 text-center text-stone-400 text-xs">
                            Tidak ada data transaksi pembayaran untuk siswa ini.
                        </div>
                    @endforelse
                </div>

                <!-- Total Nominal Rangkuman -->
                <div class="flex items-center justify-between px-2.5 py-1.5 bg-stone-100/70 border border-stone-200 rounded-lg text-[11px]">
                    <span class="text-stone-600 font-medium">Total Nominal Terpilih:</span>
                    <span class="font-mono font-extrabold text-emerald-800 text-xs" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalNominal)">
                        Rp {{ number_format($this->totalTargetNominal, 0, ',', '.') }}
                    </span>
                </div>
                @error('targetPembayaranIds') 
                    <span class="text-rose-600 text-[11px] font-bold block">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Foto Bukti Existing -->
            @if ($existingProofs->count() === 1)
                <div class="space-y-2 pt-2 border-t border-stone-200">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-stone-700 uppercase tracking-wider text-[10px]">
                            Foto Bukti Tersimpan ({{ count($targetInts) }} Transaksi)
                        </span>
                        <a href="{{ asset('storage/' . $existingProofs->first()) }}" target="_blank" class="text-emerald-700 hover:text-emerald-900 hover:underline font-bold text-[11px] flex items-center gap-1">
                            <x-lucide-external-link class="w-3 h-3" />
                            <span>Lihat Penuh</span>
                        </a>
                    </div>
                    <div class="rounded-xl overflow-hidden border border-stone-300 bg-stone-900/90 shadow-2xs p-1">
                        <img src="{{ asset('storage/' . $existingProofs->first()) }}" alt="Bukti Pembayaran" class="w-full max-h-56 object-contain mx-auto rounded-lg" />
                    </div>
                    @if (!auth()->user()->isSuperAdmin2())
                        <div class="flex justify-end">
                            <button type="button" wire:click="deleteBuktiFoto" wire:confirm="Hapus foto bukti pembayaran dari semua transaksi yang dipilih?" class="text-rose-600 hover:text-rose-800 text-[11px] font-bold inline-flex items-center gap-1 cursor-pointer">
                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                <span>Hapus Foto Bukti ({{ count($targetInts) }} Transaksi)</span>
                            </button>
                        </div>
                    @endif
                </div>
            @elseif ($existingProofs->count() > 1)
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-[11px] text-amber-900 flex items-start gap-2">
                    <x-lucide-alert-circle class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                    <div>
                        <span class="font-bold">Perhatian:</span> Transaksi yang dipilih memiliki {{ $existingProofs->count() }} foto bukti berbeda. Mengunggah foto bukti baru di bawah ini akan mengganti bukti foto pada semua {{ count($targetInts) }} transaksi terpilih menjadi bukti yang sama.
                    </div>
                </div>
            @elseif ($selectedPembayaran && $selectedPembayaran->bukti_bayar && count($targetInts) === 0)
                <div class="space-y-2 pt-2 border-t border-stone-200">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-stone-700 uppercase tracking-wider text-[10px]">Foto Bukti Tersimpan</span>
                        <a href="{{ asset('storage/' . $selectedPembayaran->bukti_bayar) }}" target="_blank" class="text-emerald-700 hover:text-emerald-900 hover:underline font-bold text-[11px] flex items-center gap-1">
                            <x-lucide-external-link class="w-3 h-3" />
                            <span>Lihat Penuh</span>
                        </a>
                    </div>
                    <div class="rounded-xl overflow-hidden border border-stone-300 bg-stone-900/90 shadow-2xs p-1">
                        <img src="{{ asset('storage/' . $selectedPembayaran->bukti_bayar) }}" alt="Bukti Pembayaran" class="w-full max-h-56 object-contain mx-auto rounded-lg" />
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
            @endif

            <!-- Upload / Edit Form -->
            @if (!auth()->user()->isSuperAdmin2())
                <form wire:submit.prevent="saveBuktiFoto" class="space-y-3 pt-2 border-t border-stone-200">
                    <div x-data="{
                        isCompressing: false,
                        isUploading: false,
                        uploadProgress: 0,
                        clientError: null,
                        async handleFileSelect(e) {
                            const file = e.target.files[0];
                            if (!file) return;

                            this.clientError = null;

                            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                            if (!validTypes.includes(file.type)) {
                                this.clientError = 'Format file tidak didukung. Harap pilih foto JPG, JPEG, PNG, atau WEBP.';
                                e.target.value = '';
                                return;
                            }

                            try {
                                let uploadFile = file;

                                // Kompresi otomatis di sisi browser jika file lebih dari 350KB
                                if (file.size > 350 * 1024) {
                                    this.isCompressing = true;
                                    uploadFile = await this.compress(file);
                                    this.isCompressing = false;
                                }

                                this.isUploading = true;
                                this.uploadProgress = 0;

                                $wire.upload('edit_bukti_foto', uploadFile, 
                                    () => {
                                        this.isUploading = false;
                                        this.isCompressing = false;
                                        this.uploadProgress = 100;
                                        this.clientError = null;
                                        e.target.value = '';
                                    },
                                    (error) => {
                                        this.isUploading = false;
                                        this.isCompressing = false;
                                        this.clientError = 'Gagal mengunggah foto. Pastikan koneksi internet stabil.';
                                        e.target.value = '';
                                    },
                                    (event) => {
                                        this.uploadProgress = event.detail.progress;
                                    }
                                );
                            } catch (err) {
                                this.isCompressing = false;
                                this.isUploading = false;
                                this.clientError = 'Gagal memproses gambar: ' + (err.message || 'Format tidak valid');
                                e.target.value = '';
                            }
                        },
                        compress(file) {
                            return new Promise((resolve) => {
                                const img = new Image();
                                const reader = new FileReader();

                                reader.onload = (e) => {
                                    img.onload = () => {
                                        const maxDim = 1600;
                                        let width = img.width;
                                        let height = img.height;

                                        if (width > height && width > maxDim) {
                                            height = Math.round((height * maxDim) / width);
                                            width = maxDim;
                                        } else if (height > maxDim) {
                                            width = Math.round((width * maxDim) / height);
                                            height = maxDim;
                                        }

                                        const canvas = document.createElement('canvas');
                                        canvas.width = width;
                                        canvas.height = height;
                                        const ctx = canvas.getContext('2d');
                                        ctx.drawImage(img, 0, 0, width, height);

                                        canvas.toBlob((blob) => {
                                            if (!blob || blob.size >= file.size) {
                                                resolve(file);
                                            } else {
                                                const cleanName = file.name.replace(/\.[^/.]+$/, '') + '.jpg';
                                                const compressed = new File([blob], cleanName, { type: 'image/jpeg', lastModified: Date.now() });
                                                resolve(compressed);
                                            }
                                        }, 'image/jpeg', 0.82);
                                    };
                                    img.onerror = () => resolve(file);
                                    img.src = e.target.result;
                                };
                                reader.onerror = () => resolve(file);
                                reader.readAsDataURL(file);
                            });
                        }
                    }">
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                            {{ $existingProofs->isNotEmpty() ? 'Ganti Foto Bukti Pembayaran (TF / Struk)' : 'Unggah Foto Bukti Pembayaran (TF / Struk)' }}
                            @if (count($targetInts) > 1)
                                <span class="text-emerald-700 font-semibold lowercase">({{ count($targetInts) }} transaksi sekaligus)</span>
                            @endif
                        </label>
                        
                        @if ($edit_bukti_foto)
                            <div class="p-2.5 bg-emerald-50 border border-emerald-300 rounded-xl flex items-center justify-between gap-2 shadow-2xs">
                                <div class="flex items-center gap-2 min-w-0">
                                    @if (in_array(strtolower($edit_bukti_foto->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'webp']))
                                        <img src="{{ $edit_bukti_foto->temporaryUrl() }}" alt="Preview" class="w-10 h-10 object-cover rounded-lg border border-emerald-200 shadow-2xs shrink-0" />
                                    @endif
                                    <div class="min-w-0">
                                        <span class="text-xs font-bold text-emerald-950 block truncate">{{ $edit_bukti_foto->getClientOriginalName() }}</span>
                                        <span class="text-[10px] text-emerald-700 font-semibold">{{ number_format($edit_bukti_foto->getSize() / 1024, 1) }} KB • Siap Disimpan</span>
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
                                    @change="handleFileSelect($event)"
                                    accept="image/jpeg,image/png,image/jpg,image/webp" 
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                                />
                                <div class="flex items-center justify-center gap-2 pointer-events-none text-stone-600 group-hover:text-emerald-800">
                                    <x-lucide-camera class="w-4 h-4 text-emerald-600" />
                                    <span class="text-xs font-bold">Pilih file foto bukti (Kompresi otomatis, JPG/PNG/WEBP)</span>
                                </div>
                            </div>
                        @endif

                        <!-- Indikator Kompresi & Upload Cepat -->
                        <div x-show="isCompressing" x-cloak class="text-[11px] text-emerald-700 font-bold mt-1.5 flex items-center gap-1.5">
                            <span class="animate-spin inline-block w-3 h-3 border-2 border-emerald-600 border-t-transparent rounded-full"></span>
                            <span>Mengoptimalkan ukuran foto...</span>
                        </div>

                        <div x-show="isUploading" x-cloak class="mt-1.5 space-y-1">
                            <div class="flex items-center justify-between text-[11px] text-emerald-700 font-bold">
                                <span class="flex items-center gap-1">
                                    <span class="animate-spin inline-block w-3 h-3 border-2 border-emerald-600 border-t-transparent rounded-full"></span>
                                    <span>Mengunggah foto...</span>
                                </span>
                                <span x-text="uploadProgress + '%'"></span>
                            </div>
                            <div class="w-full bg-stone-200 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-emerald-600 h-1.5 transition-all duration-150" :style="'width: ' + uploadProgress + '%'"></div>
                            </div>
                        </div>

                        <span x-show="clientError" x-cloak class="text-rose-600 text-[11px] font-bold block mt-1" x-text="clientError"></span>
                        @error('edit_bukti_foto') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-stone-100">
                        <x-button type="button" variant="secondary" size="sm" wire:click="closeBuktiModal">Tutup</x-button>
                        <x-button type="submit" variant="primary" size="sm" icon="check" loadingTarget="saveBuktiFoto" :disabled="!$edit_bukti_foto || count($targetInts) === 0">
                            Simpan Foto Bukti {{ count($targetInts) > 1 ? '(' . count($targetInts) . ' Transaksi)' : '' }}
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
