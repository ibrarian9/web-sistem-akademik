<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Kalender Akademik & Hari Libur"
        :steps="[
            ['title' => 'Tambah Agenda Libur', 'desc' => 'Klik Tambah Agenda / Hari Libur untuk menginput nama agenda, kategori, serta rentang tanggal pelaksanaan.'],
            ['title' => 'Bebas Presensi', 'desc' => 'Centang opsi Liburkan Presensi jika pada tanggal tersebut seluruh murid dan guru diliburkan dari absensi harian.'],
            ['title' => 'Filter Tahun Ajaran', 'desc' => 'Gunakan dropdown filter untuk meninjau kalender akademik pada semester & tahun ajaran berjalan.']
        ]"
        notes="Tanggal yang ditandai Liburkan Presensi tidak akan dihitung sebagai alpa/tanpa keterangan pada rekap presensi bulanan."
    />

    <!-- Hero Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                KALENDER AKADEMIK &amp; LIBUR
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Kalender Akademik &amp; Hari Libur</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Pengelolaan jadwal libur semester, libur keagamaan, dan kegiatan akademik yayasan.</p>
        </div>
        @if ($canManage)
            <button wire:click="openCreateModal" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-sm">
                <x-lucide-plus class="w-4 h-4" />
                <span>+ Tambah Agenda / Hari Libur</span>
            </button>
        @endif
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- Content Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <!-- Toolbar & Filter -->
        <div class="flex flex-col md:flex-row gap-3 items-center justify-between">
            <div class="w-full md:w-1/3 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400">
                    <x-lucide-search class="w-4 h-4" />
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama agenda / keterangan..."
                    class="w-full pl-9 pr-4 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 placeholder-stone-400 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-xs" />
            </div>

            <div class="w-full md:w-auto flex flex-wrap gap-2 items-center">
                <select wire:model.live="filterJenis" class="px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-xs">
                    <option value="">Semua Kategori</option>
                    <option value="hari_libur">Hari Libur Resmi</option>
                    <option value="libur_semester">Libur Semester</option>
                    <option value="kegiatan_akademik">Kegiatan Akademik</option>
                    <option value="ujian">Ujian / Evaluasi</option>
                </select>

                <select wire:model.live="filterTahunAjaranId" class="px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-xs">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach ($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">T.A. {{ $ta->nama }} {{ $ta->status_aktif ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs text-stone-800">
                <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                    <tr>
                        <th class="p-3.5 border-r border-emerald-700 min-w-[200px]">Nama Agenda / Kegiatan</th>
                        <th class="p-3.5 border-r border-emerald-700 w-32">Kategori</th>
                        <th class="p-3.5 border-r border-emerald-700 min-w-[180px]">Rentang Tanggal</th>
                        <th class="p-3.5 border-r border-emerald-700 w-32 text-center">Bebas Presensi</th>
                        <th class="p-3.5 border-r border-emerald-700 w-36">Tahun Ajaran</th>
                        @if ($canManage)
                            <th class="p-3.5 text-center min-w-[140px]">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse ($events as $event)
                        <tr class="hover:bg-emerald-50/50 transition">
                            <td class="p-3.5 border-r border-stone-200">
                                <div class="font-extrabold text-stone-900 text-xs">{{ $event->nama_kegiatan }}</div>
                                @if ($event->keterangan)
                                    <div class="text-[11px] text-stone-500 font-medium mt-0.5">{{ $event->keterangan }}</div>
                                @endif
                            </td>
                            <td class="p-3.5 border-r border-stone-200">
                                @if ($event->jenis === 'hari_libur')
                                    <span class="px-2.5 py-1 bg-rose-100 text-rose-900 border border-rose-300 rounded-full font-bold text-[10px] uppercase inline-block">Hari Libur</span>
                                @elseif ($event->jenis === 'libur_semester')
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-900 border border-amber-300 rounded-full font-bold text-[10px] uppercase inline-block">Libur Semester</span>
                                @elseif ($event->jenis === 'ujian')
                                    <span class="px-2.5 py-1 bg-purple-100 text-purple-900 border border-purple-300 rounded-full font-bold text-[10px] uppercase inline-block">Ujian</span>
                                @else
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-full font-bold text-[10px] uppercase inline-block">Kegiatan</span>
                                @endif
                            </td>
                            <td class="p-3.5 font-bold text-stone-800 border-r border-stone-200">
                                {{ $event->tanggal_mulai->format('d M Y') }}
                                @if ($event->tanggal_mulai->ne($event->tanggal_selesai))
                                    s.d. {{ $event->tanggal_selesai->format('d M Y') }}
                                @endif
                            </td>
                            <td class="p-3.5 text-center border-r border-stone-200">
                                @if ($event->liburkan_presensi)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300 inline-block uppercase">
                                        ✓ Ya (Libur)
                                    </span>
                                @else
                                    <span class="text-stone-400 font-medium text-[11px] italic">Tidak</span>
                                @endif
                            </td>
                            <td class="p-3.5 font-semibold text-stone-600 border-r border-stone-200">
                                {{ $event->tahunAjaran->nama ?? '-' }}
                            </td>
                            @if ($canManage)
                                <td class="p-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button wire:click="openEditModal({{ $event->id }})" class="px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-900 rounded-lg font-bold text-xs border border-amber-300 transition shadow-xs flex items-center gap-1">
                                            <x-lucide-edit class="w-3.5 h-3.5 text-amber-700" />
                                            <span>Edit</span>
                                        </button>
                                        <button type="button" wire:click="delete({{ $event->id }})" data-confirm="Apakah Anda yakin ingin menghapus agenda {{ $event->nama_kegiatan }} ini?" class="px-2.5 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg font-bold text-xs border border-rose-300 transition shadow-xs flex items-center gap-1">
                                            <x-lucide-trash-2 class="w-3.5 h-3.5 text-rose-600" />
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canManage ? 6 : 5 }}" class="p-8 text-center text-stone-500 font-semibold italic">
                                Belum ada data agenda atau hari libur akademik yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($events->hasPages())
            <div class="pt-2">
                {{ $events->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/60 backdrop-blur-xs p-4">
            <div class="w-full max-w-lg bg-white border border-stone-200 rounded-3xl shadow-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <h3 class="text-sm font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-950 text-xs flex items-center justify-center font-black">★</span>
                        <span>{{ $isEditing ? 'Edit Agenda / Hari Libur' : 'Tambah Agenda / Hari Libur Baru' }}</span>
                    </h3>
                    <button wire:click="closeModal" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 font-bold">✕</button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Tahun Ajaran <span class="text-rose-600">*</span></label>
                        <select wire:model="tahun_ajaran_id" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach ($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}">T.A. {{ $ta->nama }} {{ $ta->status_aktif ? '(Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                        @error('tahun_ajaran_id') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Nama Agenda / Kegiatan <span class="text-rose-600">*</span></label>
                        <input wire:model="nama_kegiatan" type="text" placeholder="misal: Libur Semester Ganjil / Idul Fitri"
                            class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                        @error('nama_kegiatan') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Kategori Agenda <span class="text-rose-600">*</span></label>
                            <select wire:model="jenis" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                <option value="hari_libur">Hari Libur Resmi</option>
                                <option value="libur_semester">Libur Semester</option>
                                <option value="kegiatan_akademik">Kegiatan Akademik</option>
                                <option value="ujian">Ujian / Evaluasi</option>
                            </select>
                            @error('jenis') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1 flex flex-col justify-end">
                            <label class="flex items-center gap-2 cursor-pointer p-2 bg-stone-50 border border-stone-200 rounded-xl">
                                <input wire:model="liburkan_presensi" type="checkbox" class="w-4 h-4 rounded text-emerald-700 border-stone-300 focus:ring-emerald-600 cursor-pointer" />
                                <span class="text-xs font-bold text-stone-800">Liburkan Presensi</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Mulai <span class="text-rose-600">*</span></label>
                            <input wire:model="tanggal_mulai" type="date"
                                class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                            @error('tanggal_mulai') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Selesai <span class="text-rose-600">*</span></label>
                            <input wire:model="tanggal_selesai" type="date"
                                class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                            @error('tanggal_selesai') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Keterangan Tambahan (Opsional)</label>
                        <textarea wire:model="keterangan" rows="2" placeholder="Catatan tambahan mengenai kegiatan..."
                            class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 border-t border-stone-200 pt-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-bold">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md">
                            Simpan Agenda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
