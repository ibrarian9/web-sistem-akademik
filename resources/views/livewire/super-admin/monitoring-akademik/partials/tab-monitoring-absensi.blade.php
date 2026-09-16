<!-- ==================== TAB 3: MONITORING ABSENSI ==================== -->
@if ($activeTab === 'absen')
    <div class="space-y-6">
        <!-- Sub-tab & Date Filter Bar -->
        <div class="bg-white border border-stone-200 rounded-2xl shadow-xs p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <button wire:click="setAbsenSubTab('siswa')" 
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $absenSubTab === 'siswa' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                    <x-lucide-users class="w-4 h-4" />
                    <span>Presensi Santri</span>
                </button>
                <button wire:click="setAbsenSubTab('guru')" 
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $absenSubTab === 'guru' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                    <x-lucide-user-check class="w-4 h-4" />
                    <span>Presensi Guru</span>
                </button>
            </div>

            <div class="flex items-center gap-3">
                <label class="text-xs font-bold text-stone-500 uppercase tracking-wider">Tanggal:</label>
                <input type="date" wire:model.live="selectedTanggal" class="rounded-xl border border-stone-200 bg-stone-50 px-3.5 py-2 text-xs font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
            </div>
        </div>

        <!-- Sub-tab 1: Absensi Siswa -->
        @if ($absenSubTab === 'siswa')
            <!-- Status Metrics Banner -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                <div class="bg-emerald-50 border border-emerald-200 p-3.5 rounded-xl text-center">
                    <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 block">Hadir</span>
                    <span class="text-xl font-black text-emerald-900">{{ $absenSiswaData['counts']['hadir'] }}</span>
                </div>
                <div class="bg-sky-50 border border-sky-200 p-3.5 rounded-xl text-center">
                    <span class="text-[10px] font-black uppercase tracking-wider text-sky-700 block">Sakit</span>
                    <span class="text-xl font-black text-sky-900">{{ $absenSiswaData['counts']['sakit'] }}</span>
                </div>
                <div class="bg-amber-50 border border-amber-200 p-3.5 rounded-xl text-center">
                    <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 block">Izin</span>
                    <span class="text-xl font-black text-amber-900">{{ $absenSiswaData['counts']['izin'] }}</span>
                </div>
                <div class="bg-rose-50 border border-rose-200 p-3.5 rounded-xl text-center">
                    <span class="text-[10px] font-black uppercase tracking-wider text-rose-700 block">Alpa</span>
                    <span class="text-xl font-black text-rose-900">{{ $absenSiswaData['counts']['alpa'] }}</span>
                </div>
                <div class="bg-stone-100 border border-stone-200 p-3.5 rounded-xl text-center col-span-2 sm:col-span-1">
                    <span class="text-[10px] font-black uppercase tracking-wider text-stone-500 block">Belum Diabsen</span>
                    <span class="text-xl font-black text-stone-700">{{ $absenSiswaData['counts']['belum'] }}</span>
                </div>
            </div>

            <!-- Secondary Filter: Rombel Kelas & Search Siswa -->
            <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex flex-col sm:flex-row items-center gap-3">
                <div class="w-full sm:w-48">
                    <select wire:model.live="absenKelasId" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                        <option value="">Semua Rombel</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:flex-1 relative">
                    <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3.5 top-2.5" />
                    <input type="text" wire:model.live.debounce.300ms="searchSiswaAbsen" placeholder="Cari santri berdasarkan nama atau NIS..." class="w-full rounded-xl border border-stone-200 bg-stone-50 pl-10 pr-3 py-2 text-xs font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                </div>
            </div>

            <!-- Absensi Siswa Table -->
            <div class="bg-white border border-stone-200 rounded-2xl shadow-xs overflow-hidden">
                <x-table loadingTarget="selectedTanggal, absenKelasId, searchSiswaAbsen">
                    <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                        <tr>
                            <x-table.th align="center" class="w-12">No</x-table.th>
                            <x-table.th class="w-64">Nama Santri</x-table.th>
                            <x-table.th class="w-28">Kelas</x-table.th>
                            <x-table.th align="center" class="w-32">Status Presensi</x-table.th>
                            <x-table.th>Keterangan</x-table.th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($absenSiswaData['list'] as $idx => $item)
                            <tr class="hover:bg-stone-50 transition">
                                <td class="p-3 text-center text-xs font-bold text-stone-400 border-r border-stone-200">{{ $idx + 1 }}</td>
                                <td class="p-3 border-r border-stone-200 text-xs font-bold text-stone-900">
                                    {{ $item['siswa']->user->nama }}
                                    <div class="text-[10px] text-stone-400 font-semibold mt-0.5">NIS: {{ $item['siswa']->nis }}</div>
                                </td>
                                <td class="p-3 border-r border-stone-200 text-xs font-semibold text-stone-700">
                                    {{ $item['siswa']->kelas->nama_kelas ?? '-' }}
                                </td>
                                <td class="p-3 text-center border-r border-stone-200 text-xs">
                                    @php
                                        $badgeVariant = match($item['status']) {
                                            'hadir' => 'emerald',
                                            'sakit' => 'sky',
                                            'izin' => 'amber',
                                            'alpa' => 'rose',
                                            default => 'stone',
                                        };
                                        $label = match($item['status']) {
                                            'hadir' => 'HADIR',
                                            'sakit' => 'SAKIT',
                                            'izin' => 'IZIN',
                                            'alpa' => 'ALPA',
                                            default => 'BELUM DIABSEN',
                                        };
                                    @endphp
                                    <x-badge :variant="$badgeVariant" size="xs">{{ $label }}</x-badge>
                                </td>
                                <td class="p-3 text-xs text-stone-600 font-medium italic">
                                    {{ $item['catatan'] }}
                                </td>
                            </tr>
                        @empty
                            <x-table.empty :colspan="5" title="Tidak ada data presensi santri" message="Tidak ada santri yang cocok dengan filter pencarian ini." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>

        <!-- Sub-tab 2: Absensi Guru -->
        @else
            <!-- Status Metrics Banner Guru -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-emerald-50 border border-emerald-200 p-3.5 rounded-xl text-center">
                    <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 block">Hadir Tepat Waktu</span>
                    <span class="text-xl font-black text-emerald-900">{{ $absenGuruData['counts']['hadir'] }}</span>
                </div>
                <div class="bg-amber-50 border border-amber-200 p-3.5 rounded-xl text-center">
                    <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 block">Terlambat</span>
                    <span class="text-xl font-black text-amber-900">{{ $absenGuruData['counts']['terlambat'] }}</span>
                </div>
                <div class="bg-sky-50 border border-sky-200 p-3.5 rounded-xl text-center">
                    <span class="text-[10px] font-black uppercase tracking-wider text-sky-700 block">Izin / Sakit</span>
                    <span class="text-xl font-black text-sky-900">{{ $absenGuruData['counts']['izin'] + $absenGuruData['counts']['sakit'] }}</span>
                </div>
                <div class="bg-rose-50 border border-rose-200 p-3.5 rounded-xl text-center">
                    <span class="text-[10px] font-black uppercase tracking-wider text-rose-700 block">Alpa / Belum Absen</span>
                    <span class="text-xl font-black text-rose-900">{{ $absenGuruData['counts']['alpa'] + $absenGuruData['counts']['belum'] }}</span>
                </div>
            </div>

            <!-- Search Guru -->
            <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs">
                <div class="relative">
                    <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3.5 top-2.5" />
                    <input type="text" wire:model.live.debounce.300ms="searchGuruAbsen" placeholder="Cari ustadz / guru berdasarkan nama atau NIP..." class="w-full rounded-xl border border-stone-200 bg-stone-50 pl-10 pr-3 py-2 text-xs font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                </div>
            </div>

            <!-- Absensi Guru Table -->
            <div class="bg-white border border-stone-200 rounded-2xl shadow-xs overflow-hidden">
                <x-table loadingTarget="selectedTanggal, searchGuruAbsen">
                    <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                        <tr>
                            <x-table.th align="center" class="w-12">No</x-table.th>
                            <x-table.th class="w-64">Nama Guru</x-table.th>
                            <x-table.th align="center" class="w-28">Waktu Masuk</x-table.th>
                            <x-table.th align="center" class="w-28">Waktu Pulang</x-table.th>
                            <x-table.th align="center" class="w-32">Status</x-table.th>
                            <x-table.th>Keterangan</x-table.th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($absenGuruData['list'] as $idx => $item)
                            <tr class="hover:bg-stone-50 transition">
                                <td class="p-3 text-center text-xs font-bold text-stone-400 border-r border-stone-200">{{ $idx + 1 }}</td>
                                <td class="p-3 border-r border-stone-200 text-xs font-bold text-stone-900">
                                    {{ $item['guru']->user->nama }}
                                    <div class="text-[10px] text-stone-400 font-semibold mt-0.5">NIP: {{ $item['guru']->nip ?? '-' }}</div>
                                </td>
                                <td class="p-3 text-center border-r border-stone-200 text-xs font-bold text-stone-700">
                                    {{ $item['waktu_datang'] }}
                                </td>
                                <td class="p-3 text-center border-r border-stone-200 text-xs font-bold text-stone-700">
                                    {{ $item['waktu_pulang'] }}
                                </td>
                                <td class="p-3 text-center border-r border-stone-200 text-xs">
                                    @php
                                        $badgeVariant = match($item['status']) {
                                            'hadir' => 'emerald',
                                            'terlambat' => 'amber',
                                            'sakit', 'izin' => 'sky',
                                            'alpa' => 'rose',
                                            default => 'stone',
                                        };
                                        $label = match($item['status']) {
                                            'hadir' => 'HADIR',
                                            'terlambat' => 'TERLAMBAT',
                                            'sakit' => 'SAKIT',
                                            'izin' => 'IZIN',
                                            'alpa' => 'ALPA',
                                            default => 'BELUM HADIR',
                                        };
                                    @endphp
                                    <x-badge :variant="$badgeVariant" size="xs">{{ $label }}</x-badge>
                                </td>
                                <td class="p-3 text-xs text-stone-600 font-medium italic">
                                    {{ $item['catatan'] }}
                                </td>
                            </tr>
                        @empty
                            <x-table.empty :colspan="6" title="Tidak ada data presensi guru" message="Tidak ada guru yang cocok dengan filter pencarian ini." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>
        @endif
    </div>
@endif
