<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Kalender Akademik &amp; Hari Libur" 
        subtitle="Pengelolaan jadwal libur semester, libur keagamaan, dan kegiatan akademik yayasan."
        badge="KALENDER AKADEMIK &amp; LIBUR"
        badgeVariant="emerald"
        icon="calendar"
    >
        @if ($canManage)
            <x-slot:actions>
                <x-button variant="secondary" size="md" icon="settings" wire:click="openTahunAjaranModal">
                    Kelola Tahun Ajaran &amp; Semester
                </x-button>
                <x-button variant="primary" size="md" icon="plus" wire:click="openCreateModal">
                    Tambah Agenda / Hari Libur
                </x-button>
            </x-slot:actions>
        @endif
    </x-page-header>

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

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- Content Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Toolbar & Filter -->
        <div class="flex flex-col md:flex-row gap-3 items-center justify-between">
            <div class="w-full md:w-1/3">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama agenda / keterangan..." />
            </div>

            <div class="w-full md:w-auto flex flex-wrap gap-2 items-center">
                <select wire:model.live="filterJenis" class="px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Kategori</option>
                    <option value="hari_libur">Hari Libur Resmi</option>
                    <option value="libur_semester">Libur Semester</option>
                    <option value="kegiatan_akademik">Kegiatan Akademik</option>
                    <option value="ujian">Ujian / Evaluasi</option>
                </select>

                <select wire:model.live="filterTahunAjaranId" class="px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach ($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">T.A. {{ $ta->nama }} {{ $ta->status_aktif ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Data Table -->
        <x-table loadingTarget="search, filterJenis, filterTahunAjaranId">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="min-w-[200px]">Nama Agenda / Kegiatan</x-table.th>
                    <x-table.th class="w-32">Kategori</x-table.th>
                    <x-table.th class="min-w-[180px]">Rentang Tanggal</x-table.th>
                    <x-table.th align="center" class="w-32">Bebas Presensi</x-table.th>
                    <x-table.th class="w-36">Tahun Ajaran</x-table.th>
                    @if ($canManage)
                        <x-table.th align="center" class="w-36">Aksi</x-table.th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($events as $event)
                    <tr class="hover:bg-stone-50 transition">
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-xs">{{ $event->nama_kegiatan }}</div>
                            @if ($event->keterangan)
                                <div class="text-[11px] text-stone-500 font-medium mt-0.5">{{ $event->keterangan }}</div>
                            @endif
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            @if ($event->jenis === 'hari_libur')
                                <x-badge variant="rose" size="xs">Hari Libur</x-badge>
                            @elseif ($event->jenis === 'libur_semester')
                                <x-badge variant="amber" size="xs">Libur Semester</x-badge>
                            @elseif ($event->jenis === 'ujian')
                                <x-badge variant="purple" size="xs">Ujian</x-badge>
                            @else
                                <x-badge variant="emerald" size="xs">Kegiatan</x-badge>
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
                                <x-badge variant="emerald" size="xs">Ya (Libur)</x-badge>
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
                                    <x-button type="button" variant="secondary" size="xs" icon="edit" wire:click="openEditModal({{ $event->id }})">
                                        Edit
                                    </x-button>
                                    <x-button variant="danger" size="xs" icon="trash-2" type="button" wire:click="delete({{ $event->id }})" data-confirm="Apakah Anda yakin ingin menghapus agenda {{ $event->nama_kegiatan }} ini?">
                                        Hapus
                                    </x-button>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $canManage ? 6 : 5 }}" class="py-12 text-center text-stone-400">
                            <x-table.empty title="Belum ada data agenda atau hari libur akademik terdaftar" subtitle="Gunakan tombol Tambah Agenda di atas untuk membuat jadwal baru." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        @if ($events->hasPages())
            <div class="pt-2">
                {{ $events->links() }}
            </div>
        @endif
    </div>

    <!-- Floating Modal Form: Tambah/Edit Agenda -->
    <x-floating-card 
        :show="$showModal ? true : false"
        :title="$isEditing ? 'Edit Agenda / Hari Libur' : 'Tambah Agenda / Hari Libur Baru'"
        subtitle="Kelola agenda akademik, hari libur, atau evaluasi ujian."
        badge="AGENDA AKADEMIK"
        badgeVariant="emerald"
        icon="calendar"
        maxWidth="max-w-lg"
        closeAction="closeModal"
    >
        <form wire:submit.prevent="save" class="space-y-4 text-xs">
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Tahun Ajaran <span class="text-rose-600">*</span></label>
                <select wire:model="tahun_ajaran_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" required>
                    <option value="">-- Pilih Tahun Ajaran --</option>
                    @foreach ($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">T.A. {{ $ta->nama }} {{ $ta->status_aktif ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
                @error('tahun_ajaran_id') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Nama Agenda / Kegiatan <span class="text-rose-600">*</span></label>
                <input wire:model="nama_kegiatan" type="text" placeholder="misal: Libur Semester Ganjil / Idul Fitri"
                    class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" required />
                @error('nama_kegiatan') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Kategori Agenda <span class="text-rose-600">*</span></label>
                    <select wire:model="jenis" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="hari_libur">Hari Libur Resmi</option>
                        <option value="libur_semester">Libur Semester</option>
                        <option value="kegiatan_akademik">Kegiatan Akademik</option>
                        <option value="ujian">Ujian / Evaluasi</option>
                    </select>
                    @error('jenis') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1 flex flex-col justify-end">
                    <label class="flex items-center gap-2 cursor-pointer p-2.5 bg-stone-50 border border-stone-200 rounded-xl">
                        <input wire:model="liburkan_presensi" type="checkbox" class="w-4 h-4 rounded text-emerald-700 border-stone-300 focus:ring-emerald-600 cursor-pointer" />
                        <span class="text-xs font-bold text-stone-800">Liburkan Presensi</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Mulai <span class="text-rose-600">*</span></label>
                    <input wire:model="tanggal_mulai" type="date"
                        class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" required />
                    @error('tanggal_mulai') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Selesai <span class="text-rose-600">*</span></label>
                    <input wire:model="tanggal_selesai" type="date"
                        class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" required />
                    @error('tanggal_selesai') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Keterangan Tambahan (Opsional)</label>
                <textarea wire:model="keterangan" rows="2" placeholder="Catatan tambahan mengenai kegiatan..."
                    class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-stone-200 pt-3">
                <x-button variant="secondary" size="md" wire:click="closeModal">
                    Batal
                </x-button>
                <x-button variant="primary" size="md" type="submit" loadingTarget="save" icon="save">
                    Simpan Agenda
                </x-button>
            </div>
        </form>
    </x-floating-card>

    <!-- Floating Modal: Kelola & Hapus / Buat Tahun Ajaran -->
    <x-floating-card 
        :show="$showTahunAjaranModal ? true : false"
        title="Kelola Tahun Ajaran &amp; Aktivasi Semester"
        subtitle="Tambah tahun ajaran baru, atur status aktif 1-click, atau hapus periode kosong."
        badge="PENGATURAN PERIODE"
        badgeVariant="emerald"
        icon="settings"
        maxWidth="max-w-2xl"
        closeAction="$set('showTahunAjaranModal', false)"
    >
        <div class="space-y-4">
            <!-- Form Buat Tahun Ajaran Baru -->
            <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl space-y-3">
                <h4 class="text-xs font-bold text-stone-800 uppercase tracking-wider">Buat Tahun Ajaran &amp; Tentukan Tanggal Semester</h4>
                <form wire:submit.prevent="createTahunAjaran" class="space-y-3">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Nama Tahun Ajaran <span class="text-rose-600">*</span></label>
                        <input wire:model.live.debounce.300ms="newTahunAjaranNama" type="text" placeholder="Contoh: 2026/2027 atau 2027/2028"
                            class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                        @error('newTahunAjaranNama') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Custom Date Ranges -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1 border-t border-stone-200">
                        <!-- Semester Ganjil -->
                        <div class="p-3 bg-white border border-stone-200 rounded-xl space-y-2">
                            <span class="text-xs font-extrabold text-emerald-800 block">Semester Ganjil</span>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-[10px] font-bold text-stone-600">Tgl Mulai</label>
                                    <input wire:model="tglMulaiGanjil" type="date" class="w-full px-2 py-1.5 bg-stone-50 border border-stone-300 rounded-lg text-[11px] font-bold text-stone-900" />
                                    @error('tglMulaiGanjil') <span class="text-rose-600 text-[9px] block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-stone-600">Tgl Selesai</label>
                                    <input wire:model="tglSelesaiGanjil" type="date" class="w-full px-2 py-1.5 bg-stone-50 border border-stone-300 rounded-lg text-[11px] font-bold text-stone-900" />
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
                                    <input wire:model="tglMulaiGenap" type="date" class="w-full px-2 py-1.5 bg-stone-50 border border-stone-300 rounded-lg text-[11px] font-bold text-stone-900" />
                                    @error('tglMulaiGenap') <span class="text-rose-600 text-[9px] block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-stone-600">Tgl Selesai</label>
                                    <input wire:model="tglSelesaiGenap" type="date" class="w-full px-2 py-1.5 bg-stone-50 border border-stone-300 rounded-lg text-[11px] font-bold text-stone-900" />
                                    @error('tglSelesaiGenap') <span class="text-rose-600 text-[9px] block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-1">
                        <x-button type="submit" variant="primary" size="sm" icon="plus" loadingTarget="createTahunAjaran">
                            Simpan Tahun Ajaran &amp; Tanggal Semester
                        </x-button>
                    </div>
                </form>
            </div>

            <!-- Form Edit Tanggal Semester Eksisting -->
            @if ($editingSemesterId)
                <div class="p-4 bg-amber-50 border border-amber-300 rounded-2xl space-y-3">
                    <div class="flex justify-between items-center">
                        <h4 class="text-xs font-extrabold text-amber-900 uppercase tracking-wider">Edit Tanggal Awal &amp; Akhir Semester</h4>
                        <button type="button" wire:click="$set('editingSemesterId', null)" class="text-amber-700 hover:text-amber-950 font-bold text-xs flex items-center gap-1 cursor-pointer">
                            <span>Batal Edit</span>
                            <x-lucide-x class="w-3.5 h-3.5" />
                        </button>
                    </div>
                    <form wire:submit.prevent="saveSemesterDates" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                        <div>
                            <label class="text-[10px] font-bold text-stone-700 uppercase">Tanggal Mulai Semester</label>
                            <input wire:model="editSemesterMulai" type="date" class="w-full px-3 py-2 bg-white border border-amber-300 rounded-xl text-xs font-bold text-stone-900 shadow-2xs" />
                            @error('editSemesterMulai') <span class="text-rose-600 text-[9px] font-bold block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-stone-700 uppercase">Tanggal Selesai Semester</label>
                            <input wire:model="editSemesterSelesai" type="date" class="w-full px-3 py-2 bg-white border border-amber-300 rounded-xl text-xs font-bold text-stone-900 shadow-2xs" />
                            @error('editSemesterSelesai') <span class="text-rose-600 text-[9px] font-bold block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <x-button type="submit" variant="warning" size="sm" icon="save" loadingTarget="saveSemesterDates" class="w-full">
                                Simpan Perubahan
                            </x-button>
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
                                        <x-badge variant="emerald" size="xs">AKTIF</x-badge>
                                    @else
                                        <x-badge variant="stone" size="xs">Nonaktif</x-badge>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    @if (!$ta->status_aktif)
                                        <x-button type="button" variant="outline" size="xs" icon="check-circle" wire:click="setTahunAjaranAktif({{ $ta->id }})">
                                            Set Aktif
                                        </x-button>
                                        <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="deleteTahunAjaran({{ $ta->id }})" data-confirm="Apakah Anda yakin ingin menghapus Tahun Ajaran {{ $ta->nama }}?">
                                            Hapus
                                        </x-button>
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
                                            <x-button type="button" variant="secondary" size="xs" icon="edit" wire:click="openEditSemester({{ $s->id }})" title="Edit Tanggal Semester">
                                                Edit
                                            </x-button>
                                            @if (!$s->status_aktif)
                                                <x-button type="button" variant="outline" size="xs" wire:click="setTahunAjaranAktif({{ $ta->id }}, {{ $s->id }})">
                                                    Set Aktif
                                                </x-button>
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
                <x-button type="button" variant="secondary" size="md" wire:click="$set('showTahunAjaranModal', false)">
                    Tutup
                </x-button>
            </div>
        </div>
    </x-floating-card>
</div>
