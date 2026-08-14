<div class="space-y-6 font-sans">
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

    <!-- Hero Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                JADWAL PELAJARAN
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Jadwal Pelajaran Per Kelas</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Kelola dan pantau jam pelajaran yang sudah terisi di setiap hari untuk setiap kelas.</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- View Mode Switcher -->
            <div class="bg-stone-100 border border-stone-200 p-1 rounded-xl flex items-center gap-1">
                <button wire:click="$set('viewMode', 'grid')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $viewMode === 'grid' ? 'bg-emerald-700 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900' }}">
                    <x-lucide-layout-grid class="w-3.5 h-3.5" />
                    <span>Matriks Per Kelas</span>
                </button>
                <button wire:click="$set('viewMode', 'table')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $viewMode === 'table' ? 'bg-emerald-700 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900' }}">
                    <x-lucide-table class="w-3.5 h-3.5" />
                    <span>Daftar Tabel</span>
                </button>
            </div>

            <button wire:click="openCreate" class="py-2.5 px-4 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold tracking-wide transition flex items-center gap-1.5 shadow-sm">
                <x-lucide-plus class="w-4 h-4" />
                <span>Tambah Jadwal</span>
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif   

    <!-- Selector Kelas -->
    <div class="bg-white border border-stone-200 rounded-2xl p-4 space-y-3 shadow-sm">
        <div class="flex items-center gap-2">
            <span class="w-1.5 h-3.5 bg-emerald-700 rounded-full"></span>
            <span class="text-xs font-bold text-stone-800 uppercase tracking-wider">Pilih Kelas Untuk Menampilkan Jadwal</span>
        </div>

        <div class="flex items-center gap-2.5 overflow-x-auto pb-1 pt-0.5 custom-scrollbar">
            @foreach ($kelases as $k)
                <button wire:click="selectKelas({{ $k->id }})" 
                    class="px-4 py-2 rounded-xl text-xs font-bold transition shrink-0 flex items-center gap-2 border {{ $selectedKelasId == $k->id ? 'bg-emerald-700 text-white border-emerald-700 shadow-xs' : 'bg-stone-50 border-stone-200 text-stone-700 hover:bg-stone-100' }}">
                    <x-lucide-school class="w-4 h-4 text-current shrink-0" />
                    <span>Kelas {{ $k->nama_kelas }}</span>
                </button>
            @endforeach
        </div>
    </div>

    @if ($viewMode === 'grid')
        <!-- GRID TIMETABLE MATRIKS MINGGUAN PER KELAS -->
        <div class="space-y-4">
            <div class="flex items-center justify-between bg-emerald-900 border border-emerald-950 p-4 rounded-2xl shadow-sm text-white">
                <h3 class="text-base font-black flex items-center gap-2.5">
                    <span class="px-3 py-1 bg-white text-emerald-950 rounded-xl text-xs font-extrabold shadow-xs">Kelas {{ $activeKelas->nama_kelas ?? '-' }}</span>
                    <span>Jadwal Pelajaran Mingguan</span>
                </h3>
                <span class="text-xs text-emerald-100 font-semibold">Total Hari Aktif: <strong>6 Hari (Senin - Sabtu)</strong></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                @foreach ($days as $day)
                    @php
                        $daySchedules = $weeklyGrid[$day] ?? collect();
                    @endphp
                    <div class="bg-white border border-stone-200 rounded-2xl p-4 flex flex-col justify-between space-y-4 shadow-sm hover:border-emerald-500 transition">
                        <!-- Day Header -->
                        <div class="flex items-center justify-between border-b border-stone-200 pb-2.5">
                            <span class="font-extrabold text-stone-900 text-xs uppercase tracking-wider flex items-center gap-2">
                                <x-lucide-calendar class="w-4 h-4 text-emerald-700 shrink-0" />
                                <span>{{ ucfirst($day) }}</span>
                            </span>

                            <!-- Badge Jumlah Jam -->
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ count($daySchedules) > 0 ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : 'bg-stone-100 text-stone-500 border-stone-200' }}">
                                {{ count($daySchedules) }} Jam
                            </span>
                        </div>

                        <!-- Schedule Slots List -->
                        <div class="space-y-3 flex-1 min-h-[170px]">
                            @forelse ($daySchedules as $sched)
                                <div class="p-3 bg-emerald-50/60 border border-emerald-200 hover:border-emerald-400 rounded-xl space-y-2 transition shadow-xs group">
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-700 text-white rounded text-[10px] font-extrabold">
                                            <x-lucide-clock class="w-3 h-3 text-current shrink-0" />
                                            {{ date('H:i', strtotime($sched->jam_mulai)) }} - {{ date('H:i', strtotime($sched->jam_selesai)) }}
                                        </span>
                                        <div class="inline-flex items-center gap-1 shrink-0">
                                            <button wire:click="openEdit({{ $sched->id }})" class="p-1 bg-amber-100 hover:bg-amber-200 text-amber-900 rounded border border-amber-300 transition" title="Edit">
                                                <x-lucide-edit class="w-3.5 h-3.5" />
                                            </button>
                                            <button type="button" wire:click="delete({{ $sched->id }})" data-confirm="Apakah Anda yakin ingin menghapus jadwal ini?" class="p-1 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded border border-rose-300 transition" title="Hapus">
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
                        <button wire:click="openCreateForDay('{{ $day }}', {{ $selectedKelasId }})" class="w-full py-2 bg-emerald-100 hover:bg-emerald-700 text-emerald-900 hover:text-white border border-emerald-300 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-xs">
                            <x-lucide-plus class="w-3.5 h-3.5" />
                            <span>Tambah {{ ucfirst($day) }}</span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- TABLE VIEW DAFTAR SEMUA JADWAL -->
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
            <!-- Filters -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2 w-full sm:max-w-2xl">
                    <!-- Search bar -->
                    <div class="relative flex-1 min-w-[200px]">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400">
                            <x-lucide-search class="w-4 h-4" />
                        </span>
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari mapel atau nama guru..."
                            class="w-full pl-9 pr-4 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 placeholder-stone-400 focus:ring-2 focus:ring-emerald-600 text-xs font-medium" />
                    </div>
                    
                    <!-- Filter Per Kelas -->
                    <select wire:model.live="filterKelasId" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs px-3 py-2 font-bold focus:ring-2 focus:ring-emerald-600">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelases as $k)
                            <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>

                    <!-- Hari selector -->
                    <select wire:model.live="filterHari" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs px-3 py-2 font-bold focus:ring-2 focus:ring-emerald-600">
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
                    <select wire:model.live="perPage" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs px-2.5 py-1.5 font-bold focus:ring-2 focus:ring-emerald-600">
                        <option value="15">15 Baris</option>
                        <option value="30">30 Baris</option>
                        <option value="50">50 Baris</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs text-stone-800">
                    <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                        <tr>
                            <th class="p-3.5 border-r border-emerald-700 w-28">Hari</th>
                            <th class="p-3.5 border-r border-emerald-700 w-36">Jam Pelajaran</th>
                            <th class="p-3.5 border-r border-emerald-700 w-28">Kelas</th>
                            <th class="p-3.5 border-r border-emerald-700 min-w-[180px]">Mata Pelajaran</th>
                            <th class="p-3.5 border-r border-emerald-700 min-w-[200px]">Guru Pengampu</th>
                            <th class="p-3.5 text-center min-w-[140px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($jadwals as $jadwal)
                            <tr class="hover:bg-emerald-50/50 transition">
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
                                        <button wire:click="openEdit({{ $jadwal->id }})" class="px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-900 rounded-lg font-bold text-xs border border-amber-300 transition shadow-xs flex items-center gap-1">
                                            <x-lucide-edit class="w-3.5 h-3.5 text-amber-700" />
                                            <span>Edit</span>
                                        </button>
                                        <button type="button" wire:click="delete({{ $jadwal->id }})" data-confirm="Apakah Anda yakin ingin menghapus jadwal ini?" class="px-2.5 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg font-bold text-xs border border-rose-300 transition shadow-xs flex items-center gap-1">
                                            <x-lucide-trash-2 class="w-3.5 h-3.5 text-rose-600" />
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-stone-500 font-semibold italic">
                                    Belum ada jadwal pelajaran terdaftar untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pt-2">
                {{ $jadwals->links() }}
            </div>
        </div>
    @endif

    <!-- Form Modal -->
    @if ($isFormOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/60 backdrop-blur-xs p-4">
            <div class="w-full max-w-lg bg-white border border-stone-200 rounded-3xl shadow-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <h3 class="text-sm font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-950 text-xs flex items-center justify-center font-black">★</span>
                        <span>{{ $jadwalId ? 'Edit Jadwal Pelajaran' : 'Tambah Jadwal Baru' }}</span>
                    </h3>
                    <button wire:click="$set('isFormOpen', false)" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 font-bold">✕</button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <!-- Penugasan Mapel & Kelas (Disusun Per Kelas) -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Mata Pelajaran &amp; Kelas <span class="text-rose-600">*</span></label>
                        <select wire:model.live="guru_mapel_kelas_id" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                            <option value="">-- Pilih Penugasan Kelas &amp; Mapel --</option>
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
                            <select wire:model.live="hari" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                <option value="senin">Senin</option>
                                <option value="selasa">Selasa</option>
                                <option value="rabu">Rabu</option>
                                <option value="kamis">Kamis</option>
                                <option value="jumat">Jumat</option>
                                <option value="sabtu">Sabtu</option>
                            </select>
                            @error('hari') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Jam Mulai -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Jam Mulai <span class="text-rose-600">*</span></label>
                            <input wire:model="jam_mulai" type="time" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                            @error('jam_mulai') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Jam Selesai -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Jam Selesai <span class="text-rose-600">*</span></label>
                            <input wire:model="jam_selesai" type="time" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                            @error('jam_selesai') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Live Helper Box: Existing Schedules for Chosen Class & Day -->
                    @if (count($formExistingSchedules) > 0)
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-1 text-xs">
                            <div class="font-bold text-emerald-950 flex items-center gap-1.5">
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
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-950 flex items-center gap-2">
                            <x-lucide-check-circle class="w-4 h-4 text-emerald-700 shrink-0" />
                            <span>Hari <strong>{{ ucfirst($hari) }}</strong> belum ada jadwal terisi untuk kelas ini.</span>
                        </div>
                    @endif

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-2 border-t border-stone-200 pt-3">
                        <button type="button" wire:click="$set('isFormOpen', false)" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-bold">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md">
                            Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
