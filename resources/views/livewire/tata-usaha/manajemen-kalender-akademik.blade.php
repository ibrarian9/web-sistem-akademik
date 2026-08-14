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
            <div class="flex flex-wrap items-center gap-2">
                <button wire:click="openTahunAjaranModal" class="inline-flex items-center gap-2 px-4 py-2.5 bg-stone-100 hover:bg-stone-200 border border-stone-300 text-stone-800 rounded-xl text-xs font-bold transition shadow-xs">
                    <x-lucide-settings class="w-4 h-4 text-emerald-700" />
                    <span>Kelola Tahun Ajaran &amp; Semester</span>
                </button>
                <button wire:click="openCreateModal" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-sm">
                    <x-lucide-plus class="w-4 h-4" />
                    <span>Tambah Agenda / Hari Libur</span>
                </button>
            </div>
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
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300 inline-flex items-center gap-1 uppercase">
                                        <x-lucide-check-circle class="w-3 h-3 text-emerald-700" />
                                        <span>Ya (Libur)</span>
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
        <div x-data x-init="window.scrollTo({ top: 0, behavior: 'smooth' })" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/60 backdrop-blur-xs p-4">
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

    <!-- Modal Kelola & Hapus / Buat Tahun Ajaran -->
    @if ($showTahunAjaranModal)
        <div x-data x-init="window.scrollTo({ top: 0, behavior: 'smooth' })" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/60 backdrop-blur-xs p-4 overflow-y-auto">
            <div class="w-full max-w-2xl bg-white border border-stone-200 rounded-3xl shadow-2xl p-6 space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-950 text-xs flex items-center justify-center font-black">
                                <x-lucide-settings class="w-3.5 h-3.5 text-emerald-950" />
                            </span>
                            <span>Kelola Tahun Ajaran &amp; Aktivasi Semester</span>
                        </h3>
                        <p class="text-[11px] text-stone-500 font-semibold mt-0.5">Tambah tahun ajaran baru, atur status aktif 1-click, atau hapus periode kosong.</p>
                    </div>
                    <button wire:click="$set('showTahunAjaranModal', false)" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 font-bold">✕</button>
                </div>

                <!-- Form Buat Tahun Ajaran Baru -->
                <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl space-y-3">
                    <h4 class="text-xs font-bold text-stone-800 uppercase tracking-wider">Buat Tahun Ajaran &amp; Tentukan Tanggal Semester</h4>
                    <form wire:submit.prevent="createTahunAjaran" class="space-y-3">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Nama Tahun Ajaran <span class="text-rose-600">*</span></label>
                            <input wire:model.live.debounce.300ms="newTahunAjaranNama" type="text" placeholder="Contoh: 2026/2027 atau 2027/2028"
                                class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                            @error('newTahunAjaranNama') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Custom Date Ranges -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1 border-t border-stone-200">
                            <!-- Semester Ganjil -->
                            <div class="p-3 bg-white border border-stone-200 rounded-xl space-y-2">
                                <span class="text-xs font-extrabold text-emerald-800 block">Semester Ganjil</span>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-[10px] font-bold text-stone-600">Tgl Mulai</label>
                                        <input wire:model="tglMulaiGanjil" type="date" class="w-full px-2 py-1 bg-stone-50 border border-stone-300 rounded-lg text-[11px] font-bold text-stone-900" />
                                        @error('tglMulaiGanjil') <span class="text-rose-600 text-[9px] block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-stone-600">Tgl Selesai</label>
                                        <input wire:model="tglSelesaiGanjil" type="date" class="w-full px-2 py-1 bg-stone-50 border border-stone-300 rounded-lg text-[11px] font-bold text-stone-900" />
                                        @error('tglSelesaiGanjil') <span class="text-rose-600 text-[9px] block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Semester Genap -->
                            <div class="p-3 bg-white border border-stone-200 rounded-xl space-y-2">
                                <span class="text-xs font-extrabold text-indigo-800 block">Semester Genap</span>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-[10px] font-bold text-stone-600">Tgl Mulai</label>
                                        <input wire:model="tglMulaiGenap" type="date" class="w-full px-2 py-1 bg-stone-50 border border-stone-300 rounded-lg text-[11px] font-bold text-stone-900" />
                                        @error('tglMulaiGenap') <span class="text-rose-600 text-[9px] block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-stone-600">Tgl Selesai</label>
                                        <input wire:model="tglSelesaiGenap" type="date" class="w-full px-2 py-1 bg-stone-50 border border-stone-300 rounded-lg text-[11px] font-bold text-stone-900" />
                                        @error('tglSelesaiGenap') <span class="text-rose-600 text-[9px] block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                Simpan Tahun Ajaran &amp; Tanggal Semester
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Form Edit Tanggal Semester Eksisting -->
                @if ($editingSemesterId)
                    <div class="p-4 bg-amber-50 border border-amber-300 rounded-2xl space-y-3">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xs font-extrabold text-amber-900 uppercase tracking-wider">Edit Tanggal Awal &amp; Akhir Semester</h4>
                            <button type="button" wire:click="$set('editingSemesterId', null)" class="text-amber-700 hover:text-amber-950 font-bold text-xs flex items-center gap-1">
                                <span>Batal Edit</span>
                                <x-lucide-x class="w-3.5 h-3.5" />
                            </button>
                        </div>
                        <form wire:submit.prevent="saveSemesterDates" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                            <div>
                                <label class="text-[10px] font-bold text-stone-700 uppercase">Tanggal Mulai Semester</label>
                                <input wire:model="editSemesterMulai" type="date" class="w-full px-3 py-1.5 bg-white border border-amber-300 rounded-xl text-xs font-bold text-stone-900" />
                                @error('editSemesterMulai') <span class="text-rose-600 text-[9px] font-bold block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-stone-700 uppercase">Tanggal Selesai Semester</label>
                                <input wire:model="editSemesterSelesai" type="date" class="w-full px-3 py-1.5 bg-white border border-amber-300 rounded-xl text-xs font-bold text-stone-900" />
                                @error('editSemesterSelesai') <span class="text-rose-600 text-[9px] font-bold block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <button type="submit" class="w-full px-4 py-2 bg-amber-700 hover:bg-amber-800 text-white rounded-xl text-xs font-bold shadow-xs">
                                    Simpan Perubahan Tanggal
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Tabel Daftar Tahun Ajaran & Semester -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-stone-800 uppercase tracking-wider">Daftar Periode Tahun Ajaran &amp; Tanggal Semester</h4>
                    <div class="space-y-2.5 max-h-[320px] overflow-y-auto pr-1">
                        @forelse ($tahunAjarans as $ta)
                            <div class="p-3.5 border {{ $ta->status_aktif ? 'border-emerald-500 bg-emerald-50/50' : 'border-stone-200 bg-white' }} rounded-2xl flex flex-col justify-between gap-3 shadow-xs">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-stone-200/80 pb-2">
                                    <div class="flex items-center gap-2">
                                        <h5 class="text-xs font-extrabold text-stone-900">Tahun Ajaran {{ $ta->nama }}</h5>
                                        @if ($ta->status_aktif)
                                            <span class="px-2 py-0.5 bg-emerald-600 text-white rounded-full text-[9px] font-black uppercase">AKTIF</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-stone-200 text-stone-600 rounded-full text-[9px] font-bold uppercase">Nonaktif</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        @if (!$ta->status_aktif)
                                            <button type="button" wire:click="setTahunAjaranAktif({{ $ta->id }})" class="px-3 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-900 border border-emerald-300 rounded-xl text-xs font-bold transition flex items-center gap-1">
                                                <x-lucide-check-circle class="w-3.5 h-3.5 text-emerald-700" />
                                                <span>Set Aktif</span>
                                            </button>
                                            <button type="button" wire:click="deleteTahunAjaran({{ $ta->id }})" data-confirm="Apakah Anda yakin ingin menghapus Tahun Ajaran {{ $ta->nama }}?" class="px-3 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 border border-rose-300 rounded-xl text-xs font-bold transition flex items-center gap-1">
                                                <x-lucide-trash-2 class="w-3.5 h-3.5 text-rose-600" />
                                                <span>Hapus</span>
                                            </button>
                                        @else
                                            <span class="text-[10px] text-emerald-800 font-extrabold italic bg-emerald-100/80 border border-emerald-300 px-2.5 py-0.5 rounded-xl">
                                                Periode Berjalan
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Detail Semester & Tanggal -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                    @foreach ($ta->semesters as $s)
                                        <div class="p-2.5 rounded-xl border flex items-center justify-between gap-2 {{ $s->status_aktif ? 'bg-emerald-100/80 border-emerald-300 text-emerald-950' : 'bg-stone-50 border-stone-200 text-stone-700' }}">
                                            <div class="space-y-0.5">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="font-black text-xs uppercase">Semester {{ $s->semester }}</span>
                                                    @if ($s->status_aktif)
                                                        <span class="text-[9px] font-extrabold bg-emerald-700 text-white px-1.5 py-0.2 rounded-md">AKTIF</span>
                                                    @endif
                                                </div>
                                                <div class="text-[10px] font-semibold text-stone-500 flex items-center gap-1">
                                                    <x-lucide-calendar class="w-3 h-3 text-stone-400 shrink-0" />
                                                    <span>{{ $s->tanggal_mulai ? $s->tanggal_mulai->format('d M Y') : '-' }} s/d {{ $s->tanggal_selesai ? $s->tanggal_selesai->format('d M Y') : '-' }}</span>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-1">
                                                <button type="button" wire:click="openEditSemester({{ $s->id }})" class="p-1.5 text-amber-700 hover:bg-amber-100 rounded-lg transition" title="Edit Tanggal Semester">
                                                    <x-lucide-edit class="w-3.5 h-3.5 text-amber-700" />
                                                </button>
                                                @if (!$s->status_aktif)
                                                    <button type="button" wire:click="setTahunAjaranAktif({{ $ta->id }}, {{ $s->id }})" class="px-2 py-1 bg-white hover:bg-emerald-200 text-emerald-900 border border-emerald-300 rounded-lg text-[10px] font-bold shadow-xs">
                                                        Set Aktif
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-stone-400 font-semibold text-xs italic bg-stone-50 rounded-2xl border border-stone-200">
                                Belum ada Tahun Ajaran. Silakan buat tahun ajaran baru di atas.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-stone-200">
                    <button type="button" wire:click="$set('showTahunAjaranModal', false)" class="px-5 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold rounded-xl text-xs">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
