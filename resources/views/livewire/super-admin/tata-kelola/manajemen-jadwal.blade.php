<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Jadwal Pelajaran"
        :steps="[
            ['title' => 'Pilih Kelas', 'desc' => 'Klik tombol nama kelas untuk langsung melihat matriks jadwal mingguan (Senin–Sabtu) kelas tersebut.'],
            ['title' => 'Lihat Jam Terisi & Tambah Jam', 'desc' => 'Sistem menampilkan jam pelajaran yang sudah terisi di setiap hari. Klik tombol + Tambah Jam di bawah hari yang diinginkan.'],
            ['title' => 'Deteksi Bentrok Otomatis', 'desc' => 'Sistem otomatis memvalidasi jadwal agar tidak ada bentrok mengajar guru atau bentrok ruang kelas.']
        ]"
        notes="Pastikan guru pengampu telah dipetakan pada kelas di menu Manajemen Mapel sebelum membuat jadwal."
    />

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Jadwal Pelajaran Per Kelas</h2>
            <p class="text-xs text-slate-500">Kelola dan pantau jam pelajaran yang sudah terisi di setiap hari untuk setiap kelas.</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- View Mode Switcher -->
            <div class="bg-slate-900 border border-slate-800 p-1 rounded-xl flex items-center gap-1">
                <button wire:click="$set('viewMode', 'grid')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition duration-200 flex items-center gap-1.5 {{ $viewMode === 'grid' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white' }}">
                    <x-lucide-layout-grid class="w-3.5 h-3.5" />
                    <span>Matriks Per Kelas</span>
                </button>
                <button wire:click="$set('viewMode', 'table')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition duration-200 flex items-center gap-1.5 {{ $viewMode === 'table' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white' }}">
                    <x-lucide-table class="w-3.5 h-3.5" />
                    <span>Daftar Tabel</span>
                </button>
            </div>

            <button wire:click="openCreate" class="py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold tracking-wide transition duration-200 flex items-center gap-1.5 shadow-lg shadow-indigo-600/10">
                <x-lucide-plus class="w-4 h-4" />
                <span>Tambah Jadwal</span>
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif   
    <div class="bg-white border border-slate-200/80 rounded-2xl p-4 space-y-3 shadow-sm">
        <div class="flex items-center gap-2">
            <span class="w-1.5 h-3.5 bg-indigo-600 rounded-full"></span>
            <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Pilih Kelas Untuk Menampilkan Jadwal</span>
        </div>

        <div class="flex items-center gap-2.5 overflow-x-auto pb-1 pt-0.5 custom-scrollbar">
            @foreach ($kelases as $k)
                <button wire:click="selectKelas({{ $k->id }})" 
                    class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 shrink-0 flex items-center gap-2 border {{ $selectedKelasId == $k->id ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-500/20' : 'bg-slate-50 border-slate-200 text-slate-600 hover:text-indigo-600 hover:bg-slate-100 hover:border-slate-300' }}">
                    <x-lucide-school class="w-4 h-4 text-current shrink-0" />
                    <span>Kelas {{ $k->nama_kelas }}</span>
                </button>
            @endforeach
        </div>
    </div>
    @if ($viewMode === 'grid')
        <!-- GRID TIMETABLE MATRIKS MINGGUAN PER KELAS -->
        <div class="space-y-4">
            <div class="flex items-center justify-between bg-slate-900 border border-slate-800 p-4 rounded-2xl shadow-md">
                <h3 class="text-base font-black text-white flex items-center gap-2.5">
                    <span class="px-3 py-1 bg-indigo-600 text-white rounded-xl text-xs font-black shadow-sm">Kelas {{ $activeKelas->nama_kelas ?? '-' }}</span>
                    <span>Jadwal Pelajaran Mingguan</span>
                </h3>
                <span class="text-xs text-slate-300 font-medium">Total Hari Aktif: <strong class="text-white font-bold">6 Hari (Senin - Sabtu)</strong></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                @foreach ($days as $day)
                    @php
                        $daySchedules = $weeklyGrid[$day] ?? collect();
                    @endphp
                    <div class="bg-slate-900 border border-slate-800 hover:border-slate-700 rounded-2xl p-4 flex flex-col justify-between space-y-4 shadow-xl transition duration-200">
                        <!-- Day Header -->
                        <div class="flex items-center justify-between border-b border-slate-200/80 pb-2.5">
                            <span class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-2">
                                <x-lucide-calendar class="w-4 h-4 text-current shrink-0" />
                                <span>{{ $day }}</span>
                            </span>

                            <!-- Badge Jumlah Jam -->
                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full border {{ count($daySchedules) > 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                {{ count($daySchedules) }} Jam
                            </span>
                        </div>

                        <!-- Schedule Slots List -->
                        <div class="space-y-3 flex-1 min-h-[170px]">
                            @forelse ($daySchedules as $sched)
                                <div class="p-3.5 bg-slate-950 border border-slate-800 hover:border-indigo-500/60 rounded-2xl space-y-2 transition duration-200 shadow-md group">
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-600 text-white rounded-lg text-[11px] font-extrabold shadow-sm">
                                            <x-lucide-clock class="w-3 h-3 text-current shrink-0" />
                                            {{ date('H:i', strtotime($sched->jam_mulai)) }} - {{ date('H:i', strtotime($sched->jam_selesai)) }}
                                        </span>
                                        <div class="inline-flex items-center gap-1 shrink-0">
                                            <button wire:click="openEdit({{ $sched->id }})" class="p-1 bg-amber-500/10 hover:bg-amber-500 border border-amber-500/30 text-amber-400 hover:text-slate-950 rounded-lg transition" title="Edit">
                                                <x-lucide-edit class="w-3.5 h-3.5" />
                                            </button>
                                            <button onclick="confirm('Apakah Anda yakin ingin menghapus jadwal ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $sched->id }})" class="p-1 bg-rose-500/10 hover:bg-rose-600 border border-rose-500/30 text-rose-400 hover:text-white rounded-lg transition" title="Hapus">
                                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </div>
                                    <div class="pt-0.5">
                                        <h4 class="font-extrabold text-white text-xs leading-snug group-hover:text-indigo-300 transition-colors">
                                            {{ $sched->guruMapelKelas->mapel->nama_mapel ?? '-' }}
                                        </h4>
                                        <p class="text-[11px] text-slate-300 font-semibold mt-1 flex items-center gap-1.5">
                                            <x-lucide-user class="w-3.5 h-3.5 text-emerald-400 shrink-0" />
                                            <span class="truncate">{{ $sched->guruMapelKelas->guru->user->nama ?? '-' }}</span>
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="py-10 px-2 text-center bg-slate-950/60 border border-dashed border-slate-800 rounded-2xl space-y-1.5">
                                    <x-lucide-calendar-x class="w-6 h-6 text-slate-500 mx-auto" />
                                    <p class="text-slate-400 text-[11px] font-semibold">Belum Ada Jadwal</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Quick Add Button for this Day -->
                        <button wire:click="openCreateForDay('{{ $day }}', {{ $selectedKelasId }})" class="w-full py-2.5 bg-indigo-600/10 hover:bg-indigo-600 border border-indigo-500/30 hover:border-indigo-500 text-indigo-300 hover:text-white rounded-xl text-xs font-extrabold transition-all duration-200 flex items-center justify-center gap-1.5 shadow-sm">
                            <x-lucide-plus class="w-4 h-4 text-indigo-400 group-hover:text-white" />
                            <span>+ Tambah Jam {{ ucfirst($day) }}</span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- TABLE VIEW DAFTAR SEMUA JADWAL -->
        <div class="space-y-4">
            <!-- Filters -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2 w-full sm:max-w-2xl">
                    <!-- Search bar -->
                    <div class="relative flex-1 min-w-[200px]">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                            <x-lucide-search class="w-4 h-4" />
                        </span>
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari mapel atau nama guru..."
                            class="w-full pl-9 pr-4 py-2 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition duration-200 text-xs" />
                    </div>
                    
                    <!-- Filter Per Kelas -->
                    <select wire:model.live="filterKelasId" class="bg-slate-900 border border-slate-800 rounded-xl text-white text-xs px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 font-semibold">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelases as $k)
                            <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>

                    <!-- Hari selector -->
                    <select wire:model.live="filterHari" class="bg-slate-900 border border-slate-800 rounded-xl text-white text-xs px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
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
                    <span class="text-xs text-slate-500">Tampilkan</span>
                    <select wire:model.live="perPage" class="bg-slate-900 border border-slate-800 rounded-xl text-white text-xs px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                        <option value="15">15</option>
                        <option value="30">30</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <x-data-table>
                <x-slot:thead>
                    <th class="px-6 py-3.5">Hari</th>
                    <th class="px-6 py-3.5">Jam Pelajaran</th>
                    <th class="px-6 py-3.5">Kelas</th>
                    <th class="px-6 py-3.5">Mata Pelajaran</th>
                    <th class="px-6 py-3.5">Guru Pengampu</th>
                    <th class="px-6 py-3.5 text-right">Aksi</th>
                </x-slot:thead>
                <x-slot:tbody>
                    @forelse ($jadwals as $jadwal)
                        <tr class="hover:bg-slate-905 transition-colors">
                            <td class="px-6 py-4 font-bold text-white capitalize">{{ $jadwal->hari }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-xs font-semibold">
                                    <x-lucide-clock class="w-3.5 h-3.5" />
                                    {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-300 font-semibold">Kelas {{ $jadwal->guruMapelKelas->kelas->nama_kelas ?? '-' }}</td>
                            <td class="px-6 py-4 text-white font-medium">{{ $jadwal->guruMapelKelas->mapel->nama_mapel ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-300">{{ $jadwal->guruMapelKelas->guru->user->nama ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center justify-end gap-2">
                                    <button wire:click="openEdit({{ $jadwal->id }})" class="px-2.5 py-1.5 bg-amber-500/10 hover:bg-amber-500 border border-amber-500/30 hover:border-amber-500 text-amber-400 hover:text-slate-950 rounded-xl text-[11px] font-bold transition-all duration-150 inline-flex items-center gap-1.5 shadow-sm" title="Edit Jadwal">
                                        <x-lucide-edit class="w-3.5 h-3.5" />
                                        <span>Edit</span>
                                    </button>
                                    <button onclick="confirm('Apakah Anda yakin ingin menghapus jadwal ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $jadwal->id }})" class="px-2.5 py-1.5 bg-rose-500/10 hover:bg-rose-600 border border-rose-500/30 hover:border-rose-600 text-rose-400 hover:text-white rounded-xl text-[11px] font-bold transition-all duration-150 inline-flex items-center gap-1.5 shadow-sm" title="Hapus Jadwal">
                                        <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500 font-medium">
                                Belum ada jadwal pelajaran terdaftar untuk filter ini
                            </td>
                        </tr>
                    @endforelse
                </x-slot:tbody>
            </x-data-table>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $jadwals->links() }}
            </div>
        </div>
    @endif

    <!-- Form Modal -->
    @if ($isFormOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div class="w-full max-w-lg bg-slate-900 border border-slate-800 rounded-3xl shadow-2xl p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-850 pb-4">
                    <h3 class="text-base font-bold text-white tracking-wide">{{ $jadwalId ? 'Edit Jadwal Pelajaran' : 'Tambah Jadwal Baru' }}</h3>
                    <button wire:click="$set('isFormOpen', false)" class="p-1.5 bg-slate-850 hover:bg-slate-800 rounded-lg text-slate-400 hover:text-white transition duration-200">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <!-- Penugasan Mapel & Kelas (Disusun Per Kelas) -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mata Pelajaran &amp; Kelas</label>
                        <select wire:model.live="guru_mapel_kelas_id" class="w-full px-3 py-2.5 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
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
                            <span class="text-rose-400 text-[10px] leading-relaxed block mt-1">
                                {{ $message }}
                            </span> 
                        @enderror
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <!-- Hari -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Hari</label>
                            <select wire:model.live="hari" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                                <option value="senin">Senin</option>
                                <option value="selasa">Selasa</option>
                                <option value="rabu">Rabu</option>
                                <option value="kamis">Kamis</option>
                                <option value="jumat">Jumat</option>
                                <option value="sabtu">Sabtu</option>
                            </select>
                            @error('hari') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Jam Mulai -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jam Mulai</label>
                            <input wire:model="jam_mulai" type="time" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
                            @error('jam_mulai') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Jam Selesai -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jam Selesai</label>
                            <input wire:model="jam_selesai" type="time" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
                            @error('jam_selesai') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Live Helper Box: Existing Schedules for Chosen Class & Day -->
                    @if (count($formExistingSchedules) > 0)
                        <div class="p-3 bg-indigo-950/60 border border-indigo-500/30 rounded-2xl space-y-1.5 text-xs">
                            <div class="font-bold text-indigo-300 flex items-center gap-1.5">
                                <x-lucide-info class="w-4 h-4 text-indigo-400 shrink-0" />
                                <span>Jadwal Terisi Hari {{ ucfirst($hari) }} pada Kelas Ini:</span>
                            </div>
                            <div class="space-y-1 pl-5 text-[11px] text-slate-300">
                                @foreach ($formExistingSchedules as $ex)
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <strong class="text-white">{{ date('H:i', strtotime($ex->jam_mulai)) }} - {{ date('H:i', strtotime($ex->jam_selesai)) }} WIB</strong>:
                                            <span class="text-indigo-400 font-semibold">{{ $ex->guruMapelKelas->mapel->nama_mapel ?? '-' }}</span>
                                        </div>
                                        <span class="text-slate-400 text-[10px]">({{ $ex->guruMapelKelas->guru->user->nama ?? '-' }})</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif ($guru_mapel_kelas_id)
                        <div class="p-3 bg-emerald-950/40 border border-emerald-500/30 rounded-2xl text-xs text-emerald-300 flex items-center gap-2">
                            <x-lucide-check-circle class="w-4 h-4 text-emerald-400 shrink-0" />
                            <span>Hari <strong>{{ ucfirst($hari) }}</strong> belum ada jadwal terisi untuk kelas ini.</span>
                        </div>
                    @endif

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-3 border-t border-slate-850 pt-4 mt-6">
                        <button type="button" wire:click="$set('isFormOpen', false)" class="py-2 px-4 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded-xl text-xs font-bold transition duration-200">
                            Batal
                        </button>
                        <button type="submit" class="py-2 px-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition duration-200">
                            Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
