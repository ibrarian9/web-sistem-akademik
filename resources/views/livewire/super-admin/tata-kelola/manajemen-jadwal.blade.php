<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Jadwal Pelajaran Per Kelas" 
        subtitle="Kelola dan pantau jam pelajaran yang sudah terisi di setiap hari untuk setiap rombel kelas."
        badge="JADWAL PELAJARAN"
        badgeVariant="emerald"
        icon="calendar"
    >
        <x-slot:actions>
            <div class="bg-stone-100 border border-stone-200 p-1 rounded-xl flex items-center gap-1 shadow-2xs">
                <x-button type="button" :variant="$viewMode === 'grid' ? 'primary' : 'ghost'" size="sm" icon="layout-grid" wire:click="$set('viewMode', 'grid')">
                    Matriks Per Kelas
                </x-button>
                <x-button type="button" :variant="$viewMode === 'table' ? 'primary' : 'ghost'" size="sm" icon="table" wire:click="$set('viewMode', 'table')">
                    Daftar Tabel
                </x-button>
            </div>
            <x-button type="button" variant="primary" size="md" icon="plus" wire:click="openCreate">
                Tambah Jadwal
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Jadwal Pelajaran"
        :steps="[
            ['title' => 'Pilih Kelas', 'desc' => 'Klik tombol nama kelas untuk langsung melihat matriks jadwal mingguan (Senin–Sabtu) kelas tersebut.'],
            ['title' => 'Lihat Jam Terisi & Tambah Jam', 'desc' => 'Sistem menampilkan jam pelajaran yang sudah terisi di setiap hari. Klik tombol Tambah Jam di bawah hari yang diinginkan.'],
            ['title' => 'Deteksi Bentrok Otomatis', 'desc' => 'Sistem otomatis memvalidasi jadwal agar tidak ada bentrok mengajar guru atau bentrok ruang kelas.']
        ]"
        notes="Pastikan guru pengampu telah dipetakan pada kelas di menu Manajemen Mapel sebelum membuat jadwal."
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif   

    <!-- Selector Kelas Dropdown -->
    <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-0.5">
            <div class="flex items-center gap-2 text-xs font-extrabold text-stone-800 uppercase tracking-wider">
                <x-lucide-school class="w-4 h-4 text-emerald-700" />
                <span>Pilih Kelas / Rombel</span>
            </div>
            <p class="text-xs text-stone-500 font-medium">Pilih rombongan belajar untuk menampilkan matriks jadwal pelajaran mingguan.</p>
        </div>

        <div class="w-full sm:w-80">
            <select wire:model.live="selectedKelasId" 
                    class="w-full bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-extrabold px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
                @foreach ($kelases as $k)
                    <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }} (Tingkat {{ $k->tingkat }})</option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($viewMode === 'grid')
        <!-- GRID TIMETABLE MATRIKS MINGGUAN PER KELAS -->
        <div class="space-y-4">
            <div class="flex items-center justify-between bg-emerald-900 border border-emerald-950 p-4 rounded-2xl shadow-xs text-white">
                <h3 class="text-base font-black flex items-center gap-2.5">
                    <span class="px-3 py-1 bg-white text-emerald-950 rounded-xl text-xs font-extrabold shadow-2xs">Kelas {{ $activeKelas->nama_kelas ?? '-' }}</span>
                    <span>Jadwal Pelajaran Mingguan</span>
                </h3>
                <span class="text-xs text-emerald-100 font-semibold">Total Hari Aktif: <strong>6 Hari (Senin - Sabtu)</strong></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                @foreach ($days as $day)
                    @php
                        $daySchedules = $weeklyGrid[$day] ?? collect();
                    @endphp
                    <div class="bg-white border border-stone-200 rounded-2xl p-4 flex flex-col justify-between space-y-4 shadow-xs hover:border-emerald-500 transition">
                        <!-- Day Header -->
                        <div class="flex items-center justify-between border-b border-stone-200 pb-2.5">
                            <span class="font-extrabold text-stone-900 text-xs uppercase tracking-wider flex items-center gap-2">
                                <x-lucide-calendar class="w-4 h-4 text-emerald-700 shrink-0" />
                                <span>{{ ucfirst($day) }}</span>
                            </span>

                            <!-- Badge Jumlah Jam -->
                            <x-badge :variant="count($daySchedules) > 0 ? 'emerald' : 'stone'" size="xs">
                                {{ count($daySchedules) }} Jam
                            </x-badge>
                        </div>

                        <!-- Schedule Slots List -->
                        <div class="space-y-3 flex-1 min-h-[170px]">
                            @forelse ($daySchedules as $sched)
                                <div class="p-3 bg-emerald-50/60 border border-emerald-200 hover:border-emerald-400 rounded-xl space-y-2 transition shadow-2xs group">
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-700 text-white rounded text-[10px] font-extrabold">
                                            <x-lucide-clock class="w-3 h-3 text-current shrink-0" />
                                            {{ date('H:i', strtotime($sched->jam_mulai)) }} - {{ date('H:i', strtotime($sched->jam_selesai)) }}
                                        </span>
                                        <div class="inline-flex items-center gap-1 shrink-0">
                                            <button type="button" wire:click="openEdit({{ $sched->id }})" class="p-1 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-lg border border-stone-300 transition cursor-pointer" title="Edit">
                                                <x-lucide-edit class="w-3.5 h-3.5" />
                                            </button>
                                            <button type="button" wire:click="delete({{ $sched->id }})" data-confirm="Apakah Anda yakin ingin menghapus jadwal ini?" class="p-1 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg border border-rose-200 transition cursor-pointer" title="Hapus">
                                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </div>
                                    <div class="pt-0.5">
                                        <h4 class="font-extrabold text-stone-900 text-xs leading-snug group-hover:text-emerald-800 transition">
                                            {{ $sched->guruMapelKelas->mapel->nama_mapel ?? '-' }}
                                        </h4>
                                        <p class="text-[11px] text-stone-600 font-medium mt-1 flex items-center gap-1.5">
                                            <x-lucide-user class="w-3.5 h-3.5 text-emerald-700 shrink-0" />
                                            <span class="truncate">{{ $sched->guruMapelKelas->guru->user->nama ?? '-' }}</span>
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="py-8 px-2 text-center bg-stone-50 border border-dashed border-stone-200 rounded-xl space-y-1">
                                    <x-lucide-calendar-x class="w-6 h-6 text-stone-400 mx-auto" />
                                    <p class="text-stone-500 text-[11px] font-semibold">Belum Ada Jadwal</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Quick Add Button for this Day -->
                        <x-button type="button" variant="outline" size="sm" icon="plus" wire:click="openCreateForDay('{{ $day }}', {{ $selectedKelasId }})" class="w-full justify-center">
                            Tambah {{ ucfirst($day) }}
                        </x-button>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- TABLE VIEW DAFTAR SEMUA JADWAL -->
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
            <!-- Filters -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2 w-full sm:max-w-2xl">
                    <!-- Search bar -->
                    <div class="flex-1 min-w-[200px]">
                        <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari mapel atau nama guru..." />
                    </div>
                    
                    <!-- Filter Per Kelas -->
                    <select wire:model.live="filterKelasId" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelases as $k)
                            <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>

                    <!-- Hari selector -->
                    <select wire:model.live="filterHari" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="">Semua Hari</option>
                        <option value="senin">Senin</option>
                        <option value="selasa">Selasa</option>
                        <option value="rabu">Rabu</option>
                        <option value="kamis">Kamis</option>
                        <option value="jumat">Jumat</option>
                        <option value="sabtu">Sabtu</option>
                    </select>
                </div>
                
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs font-bold text-stone-600">Tampilkan:</span>
                    <select wire:model.live="perPage" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs px-3 py-2 font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="15">15 Baris</option>
                        <option value="30">30 Baris</option>
                        <option value="50">50 Baris</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <x-table loadingTarget="filterKelasId, filterHari, search, perPage">
                <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                    <tr>
                        <x-table.th class="w-32">Hari</x-table.th>
                        <x-table.th class="w-40">Jam Pelajaran</x-table.th>
                        <x-table.th class="w-32">Kelas</x-table.th>
                        <x-table.th class="min-w-[180px]">Mata Pelajaran</x-table.th>
                        <x-table.th class="min-w-[200px]">Guru Pengampu</x-table.th>
                        <x-table.th align="center" class="w-36">Aksi</x-table.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse ($jadwals as $jadwal)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="p-3.5 font-bold text-stone-900 border-r border-stone-200 capitalize">{{ $jadwal->hari }}</td>
                            <td class="p-3.5 border-r border-stone-200">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-900 border border-emerald-300 text-xs font-extrabold">
                                    <x-lucide-clock class="w-3.5 h-3.5" />
                                    {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                                </span>
                            </td>
                            <td class="p-3.5 border-r border-stone-200 font-extrabold text-stone-900">Kelas {{ $jadwal->guruMapelKelas->kelas->nama_kelas ?? '-' }}</td>
                            <td class="p-3.5 border-r border-stone-200 font-extrabold text-stone-900">{{ $jadwal->guruMapelKelas->mapel->nama_mapel ?? '-' }}</td>
                            <td class="p-3.5 border-r border-stone-200 font-bold text-stone-700">{{ $jadwal->guruMapelKelas->guru->user->nama ?? '-' }}</td>
                            <td class="p-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <x-button type="button" variant="secondary" size="xs" icon="edit" wire:click="openEdit({{ $jadwal->id }})">
                                        Edit
                                    </x-button>
                                    <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="delete({{ $jadwal->id }})" data-confirm="Apakah Anda yakin ingin menghapus jadwal ini?">
                                        Hapus
                                    </x-button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-stone-400">
                                <x-table.empty title="Belum ada jadwal pelajaran ditemukan" subtitle="Pilih kelas atau tambahkan slot jadwal baru di atas." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-table>

            <!-- Pagination -->
            <div class="pt-2">
                {{ $jadwals->links() }}
            </div>
        </div>
    @endif

    <!-- Form Floating Modal -->
    <x-floating-card 
        :show="$isFormOpen ? true : false"
        :title="$jadwalId ? 'Edit Jadwal Pelajaran' : 'Tambah Jadwal Baru'"
        subtitle="Atur mata pelajaran, guru pengampu, hari, dan slot waktu."
        badge="JADWAL"
        badgeVariant="emerald"
        icon="calendar"
        maxWidth="max-w-lg"
        closeAction="$set('isFormOpen', false)"
    >
        <form wire:submit.prevent="save" class="space-y-4 text-xs">
            <!-- Penugasan Mapel & Kelas (Disusun Per Kelas) -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Mata Pelajaran & Kelas <span class="text-rose-600">*</span></label>
                <select wire:model.live="guru_mapel_kelas_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" required>
                    <option value="">-- Pilih Penugasan Kelas & Mapel --</option>
                    @foreach ($assignmentsGrouped as $namaKelas => $group)
                        <optgroup label="KELAS {{ strtoupper($namaKelas) }}">
                            @foreach ($group as $asg)
                                <option value="{{ $asg->id }}">
                                    Kelas {{ $namaKelas }} — {{ $asg->mapel->nama_mapel ?? '-' }} (Pengampu: {{ $asg->guru->user->nama ?? '-' }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('guru_mapel_kelas_id') 
                    <span class="text-rose-600 text-[10px] font-bold block mt-1">
                        {{ $message }}
                    </span> 
                @enderror
            </div>

            <div class="grid grid-cols-3 gap-3">
                <!-- Hari -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Hari <span class="text-rose-600">*</span></label>
                    <select wire:model.live="hari" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="senin">Senin</option>
                        <option value="selasa">Selasa</option>
                        <option value="rabu">Rabu</option>
                        <option value="kamis">Kamis</option>
                        <option value="jumat">Jumat</option>
                        <option value="sabtu">Sabtu</option>
                    </select>
                    @error('hari') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Jam Mulai -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Jam Mulai <span class="text-rose-600">*</span></label>
                    <input wire:model="jam_mulai" type="time" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" required />
                    @error('jam_mulai') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Jam Selesai -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Jam Selesai <span class="text-rose-600">*</span></label>
                    <input wire:model="jam_selesai" type="time" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" required />
                    @error('jam_selesai') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Live Helper Box: Existing Schedules for Chosen Class & Day -->
            @if (count($formExistingSchedules) > 0)
                <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl space-y-1.5 text-xs">
                    <div class="font-extrabold text-emerald-950 flex items-center gap-1.5">
                        <x-lucide-info class="w-4 h-4 text-emerald-700 shrink-0" />
                        <span>Jadwal Terisi Hari {{ ucfirst($hari) }} pada Kelas Ini:</span>
                    </div>
                    <div class="space-y-1 pl-5 text-[11px] text-stone-700 font-medium">
                        @foreach ($formExistingSchedules as $ex)
                            <div class="flex items-center justify-between">
                                <div>
                                    <strong class="text-stone-900">{{ date('H:i', strtotime($ex->jam_mulai)) }} - {{ date('H:i', strtotime($ex->jam_selesai)) }} WIB</strong>:
                                    <span class="text-emerald-800 font-bold">{{ $ex->guruMapelKelas->mapel->nama_mapel ?? '-' }}</span>
                                </div>
                                <span class="text-stone-500 text-[10px]">({{ $ex->guruMapelKelas->guru->user->nama ?? '-' }})</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif ($guru_mapel_kelas_id)
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-950 flex items-center gap-2 font-bold">
                    <x-lucide-check-circle class="w-4 h-4 text-emerald-700 shrink-0" />
                    <span>Hari <strong>{{ ucfirst($hari) }}</strong> belum ada jadwal terisi untuk kelas ini.</span>
                </div>
            @endif

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-2 border-t border-stone-200 pt-3">
                <x-button type="button" variant="secondary" size="md" wire:click="$set('isFormOpen', false)">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="save">
                    Simpan Jadwal
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
