<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Master Komponen Nilai"
        :steps="[
            ['title' => 'Pilih Kategori Mapel', 'desc' => 'Gunakan tab filter untuk membedakan komponen nilai Mapel Umum, Mapel Tahfidz, atau Semua Mapel.'],
            ['title' => 'Tambah / Edit Komponen', 'desc' => 'Klik tombol Tambah Komponen atau Edit untuk membuat nama komponen penilaian baru.'],
            ['title' => 'Pembobotan Nilai', 'desc' => 'Tata Usaha hanya mendaftarkan master komponen nilai. Persentase bobot sepenuhnya ditentukan oleh Guru Pengampu pada menu Bobot Penilaian.']
        ]"
        notes="Komponen nilai yang sudah digunakan pada data nilai siswa tidak dapat dihapus demi menjaga integritas rapor."
    />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight">Master Komponen Penilaian Akademik</h2>
            <p class="text-xs text-stone-500">Kelola master komponen penilaian untuk Mapel Umum (Tugas, UH, PTS, PAS) dan Mapel Tahfidz (Hafalan Baru, Muraja'ah, Tajwid).</p>
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
        <div class="divide-y divide-stone-100">
            @forelse ($komponens as $index => $komponen)
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-stone-50 transition">
                    <div class="flex items-center gap-3 flex-1">
                        <span class="w-8 h-8 rounded-xl bg-stone-100 text-stone-600 font-bold text-xs flex items-center justify-center shrink-0">
                            #{{ $index + 1 }}
                        </span>
                        <div class="space-y-1 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-stone-800 text-sm">{{ $komponen['nama'] }}</span>
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $komponen['berlaku_untuk'] === 'tahfidz' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($komponen['berlaku_untuk'] === 'umum' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-purple-100 text-purple-800 border border-purple-200') }}">
                                    {{ $komponen['berlaku_untuk'] === 'tahfidz' ? '📖 Mapel Tahfidz' : ($komponen['berlaku_untuk'] === 'umum' ? '📘 Mapel Umum' : 'Semua Mapel') }}
                                </span>
                            </div>
                            <div class="text-xs text-stone-400 capitalize">
                                Kategori Penilaian: <strong class="text-stone-600 font-semibold">{{ $komponen['kategori'] }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <button wire:click="openEdit({{ $komponen['id'] }})" type="button" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-500 border border-amber-200 hover:border-amber-500 text-amber-700 hover:text-slate-950 rounded-xl text-xs font-bold transition-all duration-150 inline-flex items-center gap-1.5 shadow-xs" title="Edit Detail">
                            <x-lucide-edit class="w-3.5 h-3.5" />
                            <span>Edit Komponen</span>
                        </button>
                        <button wire:click="delete({{ $komponen['id'] }})" wire:confirm="Yakin ingin menghapus komponen nilai ini?" type="button" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-600 border border-rose-200 hover:border-rose-600 text-rose-700 hover:text-white rounded-xl text-xs font-bold transition-all duration-150 inline-flex items-center gap-1.5 shadow-xs" title="Hapus">
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
                <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-xl">
                    ✓ Komponen aktif dan siap digunakan oleh Guru
                </span>
            </div>
        @endif
    </div>

    <!-- Modal Form Create / Edit -->
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm">
            <div class="bg-white border border-stone-200 rounded-2xl p-6 w-full max-w-md shadow-2xl space-y-5">
                <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                    <h3 class="text-sm font-bold text-stone-800">
                        {{ $editingId ? 'Edit Komponen Nilai' : 'Tambah Komponen Nilai Baru' }}
                    </h3>
                    <button wire:click="closeModal" type="button" class="text-stone-400 hover:text-stone-600 text-xs font-bold">✕</button>
                </div>

                <form wire:submit.prevent="saveForm" class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700">Nama Komponen Nilai</label>
                        <input wire:model="nama" type="text" placeholder="Contoh: Hafalan Baru / Ulangan Harian / PTS" 
                            class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-xs text-stone-800 focus:outline-none focus:border-indigo-500 font-semibold" />
                        @error('nama') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700">Tipe Pelajaran</label>
                            <select wire:model="berlaku_untuk" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-xs text-stone-800 focus:outline-none focus:border-indigo-500 font-semibold">
                                <option value="umum">📘 Mapel Umum</option>
                                <option value="tahfidz">📖 Mapel Tahfidz</option>
                                <option value="semua">Semua Mapel</option>
                            </select>
                            @error('berlaku_untuk') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700">Kategori Nilai</label>
                            <select wire:model="kategori" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-xs text-stone-800 focus:outline-none focus:border-indigo-500 font-semibold">
                                <option value="pengetahuan">Pengetahuan</option>
                                <option value="keterampilan">Keterampilan</option>
                                <option value="keagamaan">Keagamaan</option>
                                <option value="sikap">Sikap</option>
                            </select>
                            @error('kategori') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
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
