<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Komponen Nilai Sekolah"
        :steps="[
            ['title' => 'Pilih Kategori Mapel', 'desc' => 'Gunakan tab filter untuk membedakan komponen nilai Mapel Umum, Mapel Tahfidz, atau Semua Mapel.'],
            ['title' => 'Tambah / Edit Komponen', 'desc' => 'Klik tombol Tambah Komponen atau ikon Edit untuk mengatur nama komponen, kategori nilai, dan bidang mata pelajaran.'],
            ['title' => 'Penetapan Bobot Guru', 'desc' => 'Bobot pada menu ini adalah acuan rekomendasi standar. Guru pengampu mata pelajaran yang menentukan persentase bobot aktual di menu portal guru.']
        ]"
        notes="Komponen nilai yang sudah digunakan pada rekap nilai siswa tidak dapat dihapus demi menjaga integritas data rapor."
    />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight">Komponen &amp; Jenis Penilaian Akademik</h2>
            <p class="text-xs text-stone-500">Kelola master komponen penilaian untuk Mapel Umum (UH, PTS, PAS) dan Mapel Tahfidz (Hafalan, Muraja'ah, Tajwid).</p>
        </div>

        <button wire:click="openCreate" type="button" class="py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm">
            <x-lucide-plus class="w-4 h-4" />
            <span>Tambah Komponen Nilai</span>
        </button>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif
    @if (session()->has('error'))
        <x-alert-banner type="danger" :message="session('error')" />
    @endif

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 border-b border-stone-200 pb-3">
        <button wire:click="$set('filterBerlaku', 'semua')" type="button" 
            class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $filterBerlaku === 'semua' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
            Semua Komponen
        </button>
        <button wire:click="$set('filterBerlaku', 'umum')" type="button" 
            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $filterBerlaku === 'umum' ? 'bg-blue-600 text-white shadow-sm' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
            <span>📘 Mapel Umum</span>
        </button>
        <button wire:click="$set('filterBerlaku', 'tahfidz')" type="button" 
            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $filterBerlaku === 'tahfidz' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
            <span>📖 Mapel Tahfizh</span>
        </button>
    </div>

    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
        <form wire:submit.prevent="saveQuickWeights">
            <div class="divide-y divide-stone-100">
                @forelse ($komponens as $index => $komponen)
                    <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-stone-50 transition">
                        <div class="flex items-center gap-3 flex-1">
                            <span class="w-8 h-8 rounded-xl bg-stone-100 text-stone-600 font-bold text-xs flex items-center justify-center shrink-0">
                                #{{ $index + 1 }}
                            </span>
                            <div class="space-y-1 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-stone-800 text-xs">{{ $komponen['nama'] }}</span>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $komponen['berlaku_untuk'] === 'tahfidz' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($komponen['berlaku_untuk'] === 'umum' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-purple-100 text-purple-800 border border-purple-200') }}">
                                        {{ $komponen['berlaku_untuk'] === 'tahfidz' ? 'Tahfidz' : ($komponen['berlaku_untuk'] === 'umum' ? 'Mapel Umum' : 'Semua Mapel') }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-stone-400 capitalize">
                                    Kategori: <strong class="text-stone-600">{{ $komponen['kategori'] }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Weight Edit & Actions -->
                        <div class="flex items-center gap-3">
                            <div class="w-32">
                                <div class="relative">
                                    <input wire:model="komponens.{{ $index }}.bobot" type="number" step="0.1"
                                        class="w-full pr-7 pl-3 py-1.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 text-right font-bold" />
                                    <span class="absolute inset-y-0 right-2.5 flex items-center text-stone-400 text-xs pointer-events-none">%</span>
                                </div>
                            </div>

                            <button wire:click="openEdit({{ $komponen['id'] }})" type="button" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-500 border border-amber-200 hover:border-amber-500 text-amber-700 hover:text-slate-950 rounded-xl text-[11px] font-bold transition-all duration-150 inline-flex items-center gap-1.5 shadow-xs" title="Edit Detail">
                                <x-lucide-edit class="w-3.5 h-3.5" />
                                <span>Edit</span>
                            </button>
                            <button wire:click="delete({{ $komponen['id'] }})" wire:confirm="Yakin ingin menghapus komponen nilai ini?" type="button" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-600 border border-rose-200 hover:border-rose-600 text-rose-700 hover:text-white rounded-xl text-[11px] font-bold transition-all duration-150 inline-flex items-center gap-1.5 shadow-xs" title="Hapus">
                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-stone-400 text-xs">
                        Belum ada komponen penilaian untuk kategori ini.
                    </div>
                @endforelse
            </div>

            @if (count($komponens) > 0)
                <div class="p-4 bg-stone-50 border-t border-stone-200 flex items-center justify-between">
                    <span class="text-xs text-stone-500">
                        Total Komponen Ditampilkan: <strong>{{ count($komponens) }}</strong>
                    </span>
                    <button type="submit" class="py-2 px-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                        Simpan Rekomendasi Bobot
                    </button>
                </div>
            @endif
        </form>
    </div>

    <!-- Modal Form Create / Edit -->
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm">
            <div class="bg-white border border-stone-200 rounded-2xl p-6 w-full max-w-md shadow-2xl space-y-5">
                <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                    <h3 class="text-sm font-bold text-stone-800">
                        {{ $editingId ? 'Edit Komponen Nilai' : 'Tambah Komponen Nilai Baru' }}
                    </h3>
                    <button wire:click="closeModal" type="button" class="text-stone-400 hover:text-stone-600 text-xs">✕</button>
                </div>

                <form wire:submit.prevent="saveForm" class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700">Nama Komponen Nilai</label>
                        <input wire:model="nama" type="text" placeholder="Contoh: Hafalan Baru / PTS / Ulangan Harian" 
                            class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-xs text-stone-800 focus:outline-none focus:border-indigo-500 font-semibold" />
                        @error('nama') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700">Berlaku Untuk</label>
                            <select wire:model="berlaku_untuk" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-xs text-stone-800 focus:outline-none focus:border-indigo-500 font-semibold">
                                <option value="umum">📘 Mapel Umum</option>
                                <option value="tahfidz">📖 Mapel Tahfidz</option>
                                <option value="semua">Semua Mapel</option>
                            </select>
                            @error('berlaku_untuk') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700">Kategori</label>
                            <select wire:model="kategori" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-xs text-stone-800 focus:outline-none focus:border-indigo-500 font-semibold">
                                <option value="pengetahuan">Pengetahuan</option>
                                <option value="keterampilan">Keterampilan</option>
                                <option value="keagamaan">Keagamaan</option>
                                <option value="sikap">Sikap</option>
                            </select>
                            @error('kategori') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700">Rekomendasi Bobot (%)</label>
                        <input wire:model="bobot" type="number" step="0.1" 
                            class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-xs text-stone-800 focus:outline-none focus:border-indigo-500 font-bold" />
                        @error('bobot') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-100">
                        <button wire:click="closeModal" type="button" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                            Simpan Komponen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
