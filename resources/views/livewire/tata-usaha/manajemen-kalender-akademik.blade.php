<div class="space-y-6">
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

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Kalender Akademik &amp; Hari Libur</h2>
            <p class="text-xs text-slate-400">Pengelolaan jadwal libur semester, libur keagamaan, dan kegiatan akademik yayasan.</p>
        </div>
        @if ($canManage)
        <button wire:click="openCreateModal"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-indigo-600/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Agenda / Hari Libur
        </button>
        @endif
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- Filters & Search -->
    <div class="bg-slate-900/40 border border-slate-800 rounded-2xl p-4 flex flex-col md:flex-row gap-3 items-center justify-between">
        <div class="w-full md:w-1/3 relative">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama agenda / keterangan..."
                class="w-full pl-9 pr-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs placeholder-slate-500 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
            <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>

        <div class="w-full md:w-auto flex flex-wrap gap-2 items-center">
            <select wire:model.live="filterJenis"
                class="px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Kategori</option>
                <option value="hari_libur">Hari Libur Resmi</option>
                <option value="libur_semester">Libur Semester</option>
                <option value="kegiatan_akademik">Kegiatan Akademik</option>
                <option value="ujian">Ujian / Evaluasi</option>
            </select>

            <select wire:model.live="filterTahunAjaranId"
                class="px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Tahun Ajaran</option>
                @foreach ($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}">T.A. {{ $ta->nama }} {{ $ta->status_aktif ? '(Aktif)' : '' }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-slate-900/40 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Nama Agenda / Kegiatan</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Rentang Tanggal</th>
                        <th class="py-3 px-4 text-center">Bebas Presensi</th>
                        <th class="py-3 px-4">Tahun Ajaran</th>
                        @if ($canManage)
                        <th class="py-3 px-4 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($events as $event)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-white">{{ $event->nama_kegiatan }}</div>
                                @if ($event->keterangan)
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $event->keterangan }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if ($event->jenis === 'hari_libur')
                                    <span class="px-2.5 py-1 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-lg font-bold text-[10px]">Hari Libur</span>
                                @elseif ($event->jenis === 'libur_semester')
                                    <span class="px-2.5 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-lg font-bold text-[10px]">Libur Semester</span>
                                @elseif ($event->jenis === 'ujian')
                                    <span class="px-2.5 py-1 bg-purple-500/10 text-purple-400 border border-purple-500/20 rounded-lg font-bold text-[10px]">Ujian</span>
                                @else
                                    <span class="px-2.5 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-lg font-bold text-[10px]">Kegiatan</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-200">
                                {{ $event->tanggal_mulai->format('d M Y') }}
                                @if ($event->tanggal_mulai->ne($event->tanggal_selesai))
                                    s.d. {{ $event->tanggal_selesai->format('d M Y') }}
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if ($event->liburkan_presensi)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Ya (Libur)
                                    </span>
                                @else
                                    <span class="text-slate-500 font-medium text-[11px]">Tidak</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-400">
                                {{ $event->tahunAjaran->nama ?? '-' }}
                            </td>
                            @if ($canManage)
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center justify-end gap-2">
                                    <button wire:click="openEditModal({{ $event->id }})" class="px-2.5 py-1.5 bg-amber-500/10 hover:bg-amber-500 border border-amber-500/30 hover:border-amber-500 text-amber-400 hover:text-slate-950 rounded-xl text-[11px] font-bold transition-all duration-150 inline-flex items-center gap-1.5 shadow-sm" title="Edit Agenda">
                                        <x-lucide-edit class="w-3.5 h-3.5" />
                                        <span>Edit</span>
                                    </button>
                                    <button wire:confirm="Apakah Anda yakin ingin menghapus agenda ini?" wire:click="delete({{ $event->id }})" class="px-2.5 py-1.5 bg-rose-500/10 hover:bg-rose-600 border border-rose-500/30 hover:border-rose-600 text-rose-400 hover:text-white rounded-xl text-[11px] font-bold transition-all duration-150 inline-flex items-center gap-1.5 shadow-sm" title="Hapus Agenda">
                                        <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500 text-xs">
                                Belum ada data agenda atau hari libur akademik yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($events->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $events->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl space-y-4 p-6">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-white">
                        {{ $isEditing ? 'Edit Agenda / Hari Libur' : 'Tambah Agenda / Hari Libur Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400">Tahun Ajaran</label>
                        <select wire:model="tahun_ajaran_id" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach ($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}">T.A. {{ $ta->nama }} {{ $ta->status_aktif ? '(Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                        @error('tahun_ajaran_id') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400">Nama Agenda / Kegiatan</label>
                        <input wire:model="nama_kegiatan" type="text" placeholder="misal: Libur Semester Ganjil / Idul Fitri"
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" />
                        @error('nama_kegiatan') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-400">Kategori Agenda</label>
                            <select wire:model="jenis" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                                <option value="hari_libur">Hari Libur Resmi</option>
                                <option value="libur_semester">Libur Semester</option>
                                <option value="kegiatan_akademik">Kegiatan Akademik</option>
                                <option value="ujian">Ujian / Evaluasi</option>
                            </select>
                            @error('jenis') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1 flex flex-col justify-end">
                            <label class="flex items-center gap-2 cursor-pointer pt-3">
                                <input wire:model="liburkan_presensi" type="checkbox" class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-indigo-600 focus:ring-indigo-500" />
                                <span class="text-xs font-bold text-slate-300">Liburkan Presensi</span>
                            </label>
                            <p class="text-[10px] text-slate-500">Tanggal ini diabaikan dari alpa presensi.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-400">Tanggal Mulai</label>
                            <input wire:model="tanggal_mulai" type="date"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" />
                            @error('tanggal_mulai') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-400">Tanggal Selesai</label>
                            <input wire:model="tanggal_selesai" type="date"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" />
                            @error('tanggal_selesai') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400">Keterangan Tambahan (Opsional)</label>
                        <textarea wire:model="keterangan" rows="2" placeholder="Catatan tambahan mengenai kegiatan..."
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/20">
                            Simpan Agenda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
