<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengaturan Profil Pengguna & TTD Pribadi"
        :steps="[
            ['title' => 'Biodata & Keamanan', 'desc' => 'Perbarui nama lengkap, email resmi, serta ubah password login akun Anda.'],
            ['title' => 'Input TTD Digital', 'desc' => 'Guru dan Staf dapat mengunggah gambar TTD transparan PNG atau menggambar TTD di kanvas web.'],
            ['title' => 'Pengesahan Otomatis', 'desc' => 'TTD yang disimpan di profil ini akan dipakai otomatis untuk mengesahkan Rapor dan Laporan.']
        ]"
    />

    <!-- Header -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-700 font-black text-xl flex items-center justify-center border border-emerald-200">
                {{ strtoupper(substr($nama ?: 'U', 0, 2)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-stone-800">{{ $nama }}</h2>
                <p class="text-xs text-stone-500 font-medium">@ {{ $username }} • <span class="capitalize bg-stone-100 px-2 py-0.5 rounded text-stone-700 font-semibold">{{ str_replace('_', ' ', Auth::user()->role->nama ?? 'User') }}</span></p>
            </div>
        </div>
        <div class="text-xs text-stone-500">
            NIP: <span class="font-bold text-stone-700">{{ $nip ?: '-' }}</span> • Jabatan: <span class="font-bold text-stone-700">{{ $jabatan ?: '-' }}</span>
        </div>
    </div>

    <!-- Alert Banners -->
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold flex items-center justify-between">
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('password_message'))
        <div class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl text-sm font-semibold flex items-center justify-between">
            <span>{{ session('password_message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form: Profil & TTD Digital -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6">
            <div class="border-b border-stone-200 pb-3">
                <h3 class="text-base font-bold text-stone-800">Informasi Akun & Tanda Tangan Digital</h3>
                <p class="text-xs text-stone-500">Kelola data pribadi, NIP, Jabatan, dan TTD Elektronik resmi Anda.</p>
            </div>

            <form wire:submit.prevent="saveProfile" class="space-y-6" x-data="{ ttdMode: 'draw' }">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Lengkap -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-600 uppercase tracking-wider">Nama Lengkap & Gelar</label>
                        <input wire:model="nama" type="text" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 font-medium" />
                        @error('nama') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-600 uppercase tracking-wider">Alamat Email</label>
                        <input wire:model="email" type="email" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500" />
                        @error('email') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- NIP / NIK -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-600 uppercase tracking-wider">NIP / NIK / ID Resmi</label>
                        <input wire:model="nip" type="text" placeholder="Contoh: 19820415 200801 2 004" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 font-mono" />
                        @error('nip') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jabatan -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-600 uppercase tracking-wider">Jabatan Resmi</label>
                        <input wire:model="jabatan" type="text" placeholder="Contoh: Bendahara Keuangan / Guru Pengajar" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500" />
                        @error('jabatan') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- SECTION TTD DIGITAL (2 PILIHAN) -->
                <div class="pt-4 border-t border-stone-200 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h4 class="text-sm font-bold text-stone-800">Tanda Tangan Digital (TTD Elektronik)</h4>
                            <p class="text-xs text-stone-500">Pilih metode pembuatan TTD Elektronik Anda:</p>
                        </div>
                        @if ($current_ttd)
                            <button type="button" wire:click="removeTtd" wire:confirm="Hapus TTD Digital Anda saat ini?" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg text-xs font-bold transition flex items-center gap-1 w-fit">
                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                Hapus TTD Aktif
                            </button>
                        @endif
                    </div>

                    <!-- Opsi Selector Tabs -->
                    <div class="flex bg-stone-100 p-1 rounded-xl text-xs font-bold gap-1 w-fit">
                        <button type="button" @click="ttdMode = 'draw'" :class="ttdMode === 'draw' ? 'bg-white text-stone-800 shadow-sm' : 'text-stone-500 hover:text-stone-800'" class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                            <x-lucide-pen-tool class="w-3.5 h-3.5" />
                            <span>1. Gambar Coretan Langsung</span>
                        </button>
                        <button type="button" @click="ttdMode = 'upload'" :class="ttdMode === 'upload' ? 'bg-white text-stone-800 shadow-sm' : 'text-stone-500 hover:text-stone-800'" class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                            <x-lucide-upload-cloud class="w-3.5 h-3.5" />
                            <span>2. Unggah File Gambar (PNG/JPG)</span>
                        </button>
                    </div>

                    <!-- TAB 1: CANVAS SIGNATURE PAD -->
                    <div x-show="ttdMode === 'draw'" x-data="{
                        drawing: false,
                        hasDrawn: false,
                        initCanvas() {
                            const canvas = this.$refs.canvas;
                            if (!canvas) return;
                            const ctx = canvas.getContext('2d');
                            ctx.strokeStyle = '#0f172a';
                            ctx.lineWidth = 2.5;
                            ctx.lineCap = 'round';
                            ctx.lineJoin = 'round';

                            const getPos = (e) => {
                                const rect = canvas.getBoundingClientRect();
                                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                                return {
                                    x: (clientX - rect.left) * (canvas.width / rect.width),
                                    y: (clientY - rect.top) * (canvas.height / rect.height)
                                };
                            };

                            const startDraw = (e) => {
                                this.drawing = true;
                                this.hasDrawn = true;
                                const pos = getPos(e);
                                ctx.beginPath();
                                ctx.moveTo(pos.x, pos.y);
                            };

                            const draw = (e) => {
                                if (!this.drawing) return;
                                e.preventDefault();
                                const pos = getPos(e);
                                ctx.lineTo(pos.x, pos.y);
                                ctx.stroke();
                            };

                            const stopDraw = () => {
                                if (this.drawing) {
                                    this.drawing = false;
                                    ctx.closePath();
                                    @this.set('drawn_ttd', canvas.toDataURL('image/png'));
                                }
                            };

                            canvas.addEventListener('mousedown', startDraw);
                            canvas.addEventListener('mousemove', draw);
                            canvas.addEventListener('mouseup', stopDraw);
                            canvas.addEventListener('mouseleave', stopDraw);

                            canvas.addEventListener('touchstart', startDraw, { passive: false });
                            canvas.addEventListener('touchmove', draw, { passive: false });
                            canvas.addEventListener('touchend', stopDraw);
                        },
                        clearCanvas() {
                            const canvas = this.$refs.canvas;
                            if (!canvas) return;
                            const ctx = canvas.getContext('2d');
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            this.hasDrawn = false;
                            @this.set('drawn_ttd', null);
                        }
                    }" x-init="$nextTick(() => initCanvas())" class="space-y-3 p-4 bg-stone-50 border border-stone-200 rounded-xl">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-stone-700">Coret Tanda Tangan Anda Pada Kotak Di Bawah Ini (Gunakan Mouse / Touchscreen HP/Tablet):</span>
                            <button type="button" @click="clearCanvas()" class="px-2.5 py-1 bg-white border border-stone-300 hover:bg-stone-100 text-stone-700 rounded-lg text-xs font-bold transition flex items-center gap-1">
                                <x-lucide-eraser class="w-3.5 h-3.5 text-stone-500" />
                                <span>Bersihkan Canvas</span>
                            </button>
                        </div>

                        <div class="relative bg-white border-2 border-dashed border-stone-300 rounded-xl overflow-hidden touch-none flex items-center justify-center">
                            <canvas x-ref="canvas" width="500" height="150" class="w-full h-36 cursor-crosshair bg-white"></canvas>
                            <span x-show="!hasDrawn && !$wire.drawn_ttd" class="absolute pointer-events-none text-xs text-stone-400 font-medium select-none">
                                ✍️ Silakan membubuhkan coretan TTD di sini...
                            </span>
                        </div>
                        <p class="text-[11px] text-stone-500">Coretan akan otomatis terkonversi menjadi TTD digital transparan saat Anda menekan tombol Simpan Perubahan.</p>
                    </div>

                    <!-- TAB 2: UNGGAH FILE GAMBAR -->
                    <div x-show="ttdMode === 'upload'" class="space-y-4 p-4 bg-stone-50 border border-stone-200 rounded-xl">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                            <!-- Preview Box -->
                            <div class="p-4 bg-white border border-dashed border-stone-300 rounded-xl flex flex-col items-center justify-center min-h-[120px] text-center">
                                @if ($new_ttd)
                                    <img src="{{ $new_ttd->temporaryUrl() }}" class="max-h-24 object-contain mb-2" />
                                    <span class="text-[10px] text-emerald-600 font-bold">Pratinjau File Baru (Belum Disimpan)</span>
                                @elseif ($current_ttd && file_exists(public_path($current_ttd)))
                                    <img src="{{ asset($current_ttd) }}" class="max-h-24 object-contain mb-2" />
                                    <span class="text-[10px] text-stone-500 font-medium">TTD Digital Aktif</span>
                                @else
                                    <div class="text-stone-400 text-xs font-medium space-y-1">
                                        <x-lucide-signature class="w-8 h-8 mx-auto text-stone-300" />
                                        <span>Belum ada gambar TTD tersimpan</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Upload Input -->
                            <div class="space-y-2">
                                <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider">Pilih File Gambar TTD</label>
                                <input wire:model="new_ttd" type="file" accept="image/png,image/jpeg,image/svg+xml" class="block w-full text-xs text-stone-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer" />
                                <p class="text-[11px] text-stone-400">Rekomendasi: Gambar latar putih/transparan dengan crop mepet pada coretan TTD.</p>
                                @error('new_ttd') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-stone-200 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition shadow-md shadow-emerald-600/10 flex items-center gap-2">
                        <x-lucide-save class="w-4 h-4" />
                        Simpan Perubahan Profil & TTD
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar: Preview Card & Change Password -->
        <div class="space-y-6">
            <!-- TTD Preview Card in Document -->
            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-stone-800 border-b border-stone-200 pb-2">Simulasi TTD Pada Dokumen PDF</h3>
                <p class="text-xs text-stone-500">Begini tampilan TTD Elektronik Anda di dokumen kuitansi / rapor / surat resmi:</p>

                <div class="p-4 bg-emerald-50/50 border border-emerald-200 rounded-xl text-center font-sans space-y-2">
                    <div class="text-[10px] text-stone-500 font-bold uppercase tracking-wider">{{ $jabatan ?: 'Pejabat Berwenang' }}</div>
                    
                    <div class="my-2 p-2 bg-white border border-dashed border-emerald-400 rounded-lg inline-block w-full max-w-[200px] text-center">
                        @if ($current_ttd && file_exists(public_path($current_ttd)))
                            <img src="{{ asset($current_ttd) }}" class="h-10 mx-auto object-contain mb-1" />
                        @endif
                        <div class="flex items-center justify-center gap-1.5 text-[9px] font-bold text-emerald-800">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                            <span>TTD ELEKTRONIK SAH</span>
                        </div>
                        <div class="text-[8px] text-stone-500 font-mono">TTD-VERIFIED-SYSTEM</div>
                    </div>

                    <div class="text-xs font-bold text-stone-800 underline">{{ $nama }}</div>
                    @if ($nip)
                        <div class="text-[10px] text-stone-600">NIP: {{ $nip }}</div>
                    @endif
                </div>
            </div>

            <!-- Form Change Password -->
            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-stone-800 border-b border-stone-200 pb-2">Ganti Password Akun</h3>
                
                <form wire:submit.prevent="updatePassword" class="space-y-3">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-stone-600">Password Saat Ini</label>
                        <input wire:model="current_password" type="password" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500" />
                        @error('current_password') <span class="text-red-600 text-[11px] block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-stone-600">Password Baru</label>
                        <input wire:model="new_password" type="password" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500" />
                        @error('new_password') <span class="text-red-600 text-[11px] block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-stone-600">Konfirmasi Password Baru</label>
                        <input wire:model="new_password_confirmation" type="password" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500" />
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-2.5 bg-stone-800 hover:bg-stone-900 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                            <x-lucide-key class="w-3.5 h-3.5" />
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
