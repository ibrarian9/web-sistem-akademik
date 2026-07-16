<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Jadwal Pelajaran</h2>
            <p class="text-xs text-slate-500">Kelola jadwal mata pelajaran mingguan dengan validasi bentrok kelas dan guru secara real-time.</p>
        </div>
        <button wire:click="openCreate" class="py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold tracking-wide transition duration-200 flex items-center gap-1.5 shadow-lg shadow-indigo-600/10">
            <x-lucide-plus class="w-4 h-4" />
            <span>Tambah Jadwal</span>
        </button>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- Table Section -->
    <div class="space-y-4">
        <!-- Filters -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 w-full sm:max-w-xl">
                <!-- Search bar -->
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                        <x-lucide-search class="w-4 h-4" />
                    </span>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari kelas, mapel, atau guru..."
                        class="w-full pl-9 pr-4 py-2 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition duration-200 text-xs" />
                </div>
                
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
                            <div class="inline-flex gap-2">
                                <button wire:click="openEdit({{ $jadwal->id }})" class="p-1.5 bg-slate-800 border border-slate-750 text-indigo-400 hover:text-white hover:bg-indigo-600 rounded-lg transition duration-200">
                                    <x-lucide-edit class="w-3.5 h-3.5" />
                                </button>
                                <button onclick="confirm('Apakah Anda yakin ingin menghapus jadwal ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $jadwal->id }})" class="p-1.5 bg-slate-800 border border-slate-750 text-rose-400 hover:text-white hover:bg-rose-600 rounded-lg transition duration-200">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
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
                    <!-- Penugasan Mapel & Kelas -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mata Pelajaran & Kelas</label>
                        <select wire:model="guru_mapel_kelas_id" class="w-full px-3 py-2.5 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                            <option value="">Pilih Mapel Kelas</option>
                            @foreach ($assignments as $asg)
                                <option value="{{ $asg->id }}">
                                    Kelas {{ $asg->kelas->nama_kelas }} - {{ $asg->mapel->nama_mapel }} ({{ $asg->guru->user->nama }})
                                </option>
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
                            <select wire:model="hari" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
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
