<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight">Rekap Absensi Siswa</h2>
            <p class="text-sm text-stone-500 font-medium font-medium">Laporan kehadiran siswa per kelas per bulan.</p>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm p-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Kelas Selection -->
            <div>
                <label for="kelasId" class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Kelas</label>
                <select id="kelasId" wire:model.live="kelasId" 
                        class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3.5 py-2.5 text-sm font-semibold text-stone-700 shadow-sm focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Bulan Selection -->
            <div>
                <label for="bulan" class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Bulan</label>
                <select id="bulan" wire:model.live="bulan" 
                        class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3.5 py-2.5 text-sm font-semibold text-stone-700 shadow-sm focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
            </div>

            <!-- Tahun Selection -->
            <div>
                <label for="tahun" class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Tahun</label>
                <select id="tahun" wire:model.live="tahun" 
                        class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3.5 py-2.5 text-sm font-semibold text-stone-700 shadow-sm focus:border-green-500 focus:bg-white focus:ring-1 focus:ring-green-500">
                    @for ($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <!-- Report Table / Matrix Card -->
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
        @if ($kelas)
            <!-- Table Action Panel -->
            <div class="px-6 py-4 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-stone-50/50">
                <div class="text-sm font-bold text-stone-700">
                    Kelas: <span class="text-green-700">{{ $kelas->nama_kelas }}</span> 
                    | Wali Kelas: <span class="text-stone-600 font-semibold">{{ $kelas->guruUmum->user->nama ?? '-' }}</span>
                </div>
                
                <button wire:click="downloadPdf" 
                        class="inline-flex items-center justify-center gap-2 py-2 px-4 rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-sm font-semibold text-stone-700 shadow-sm transition duration-150 shrink-0">
                    <x-lucide-file-text class="w-4 h-4 text-red-600" />
                    <span>Ekspor PDF</span>
                </button>
            </div>

            <!-- Scrollable Matrix Table -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-xs text-stone-600 min-w-[900px]">
                    <thead>
                        <tr class="bg-stone-50 border-b border-stone-200 font-bold text-stone-700">
                            <th class="py-3 px-4 w-12 text-center border-r border-stone-200">No</th>
                            <th class="py-3 px-4 w-52 border-r border-stone-200">Nama Siswa</th>
                            @for ($day = 1; $day <= $daysInMonth; $day++)
                                <th class="py-3 px-1 w-7 text-center border-r border-stone-200 bg-stone-50">{{ $day }}</th>
                            @endfor
                            <th class="py-3 px-2 w-10 text-center border-r border-stone-200 bg-green-50 text-green-800">H</th>
                            <th class="py-3 px-2 w-10 text-center border-r border-stone-200 bg-amber-50 text-amber-800">I</th>
                            <th class="py-3 px-2 w-10 text-center border-r border-stone-200 bg-red-50 text-red-800">A</th>
                            <th class="py-3 px-3 w-16 text-center bg-stone-100 text-stone-800 font-extrabold">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        @forelse ($matrix as $index => $row)
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="py-2.5 px-4 text-center border-r border-stone-100 font-bold text-stone-400">{{ $index + 1 }}</td>
                                <td class="py-2.5 px-4 border-r border-stone-100 font-bold text-stone-800">
                                    {{ $row['siswa']->user->nama }}
                                    <div class="text-[10px] text-stone-400 font-semibold mt-0.5">NIS: {{ $row['siswa']->nis }}</div>
                                </td>
                                @for ($day = 1; $day <= $daysInMonth; $day++)
                                    @php
                                        $status = $row['days'][$day];
                                        $cellClass = 'text-stone-300';
                                        $cellText = '•';
                                        
                                        if ($status === 'hadir') {
                                            $cellClass = 'bg-green-100 text-green-800 font-bold';
                                            $cellText = 'H';
                                        } elseif ($status === 'izin') {
                                            $cellClass = 'bg-amber-100 text-amber-800 font-bold';
                                            $cellText = 'I';
                                        } elseif ($status === 'tidak_hadir') {
                                            $cellClass = 'bg-red-100 text-red-800 font-bold';
                                            $cellText = 'A';
                                        }
                                    @endphp
                                    <td class="py-2 px-0 text-center border-r border-stone-100 {{ $cellClass }}">{{ $cellText }}</td>
                                @endfor
                                <td class="py-2.5 px-2 text-center border-r border-stone-100 bg-green-50/50 text-green-700 font-bold">{{ $row['hadir'] }}</td>
                                <td class="py-2.5 px-2 text-center border-r border-stone-100 bg-amber-50/50 text-amber-700 font-bold">{{ $row['izin'] }}</td>
                                <td class="py-2.5 px-2 text-center border-r border-stone-100 bg-red-50/50 text-red-700 font-bold">{{ $row['tidak_hadir'] }}</td>
                                <td class="py-2.5 px-3 text-center bg-stone-50 font-extrabold text-stone-700">{{ $row['rate'] }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $daysInMonth + 6 }}" class="py-12 text-center text-stone-400 font-medium">
                                    Tidak ada data siswa aktif di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Legend Info Panel -->
            <div class="p-6 border-t border-stone-200 bg-stone-50/30 flex flex-wrap gap-4 text-xs font-semibold text-stone-500">
                <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded bg-green-100 text-green-800 font-bold flex items-center justify-center">H</span>
                    <span>Hadir</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded bg-amber-100 text-amber-800 font-bold flex items-center justify-center">I</span>
                    <span>Izin</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded bg-red-100 text-red-800 font-bold flex items-center justify-center">A</span>
                    <span>Alpa / Tidak Hadir</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-stone-300 font-bold font-mono">•</span>
                    <span>Belum Diinput</span>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="py-16 text-center">
                <div class="w-12 h-12 rounded-full bg-stone-100 flex items-center justify-center mx-auto text-stone-400 mb-3">
                    <x-lucide-clipboard class="w-6 h-6" />
                </div>
                <h3 class="text-sm font-bold text-stone-700">Silakan pilih kelas terlebih dahulu</h3>
                <p class="text-xs text-stone-400 mt-1">Gunakan filter di atas untuk memuat laporan rekap absensi.</p>
            </div>
        @endif
    </div>
</div>
