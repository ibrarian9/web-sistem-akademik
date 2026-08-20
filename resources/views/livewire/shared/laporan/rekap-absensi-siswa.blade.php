<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Laporan Rekap Presensi Siswa"
        :steps="[
            ['title' => 'Pilih Filter Kelas & Periode', 'desc' => 'Pilih nama kelas, bulan, dan tahun untuk memuat matriks kehadiran harian seluruh siswa.'],
            ['title' => 'Matriks Kehadiran', 'desc' => 'Tabel menampilkan kode H (Hadir), I (Izin), A (Alpa) per tanggal beserta kalkulasi persentase kehadiran.'],
            ['title' => 'Cetak PDF Resmi', 'desc' => 'Klik tombol Ekspor PDF untuk mengunduh dokumen resmi lengkap dengan tanda tangan elektronik sekolah.']
        ]"
    />

    <!-- Page Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                LAPORAN AKADEMIK
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Rekap Absensi Siswa</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Laporan rekapitulasi kehadiran siswa per kelas per bulan.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" wire:click="setPeriodPreset('this_month')" 
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5 {{ intval($bulan) === intval(date('m')) && intval($tahun) === intval(date('Y')) ? 'bg-emerald-700 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200 border border-stone-300' }}">
                <x-lucide-calendar class="w-3.5 h-3.5" />
                <span>Bulan Ini</span>
            </button>
            <button type="button" wire:click="setPeriodPreset('last_month')" 
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5 {{ intval($bulan) === intval(date('m', strtotime('-1 month'))) ? 'bg-emerald-700 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200 border border-stone-300' }}">
                <x-lucide-history class="w-3.5 h-3.5" />
                <span>Bulan Lalu</span>
            </button>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm p-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Kelas Selection -->
            <div>
                <label for="kelasId" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Pilih Kelas</label>
                <select id="kelasId" wire:model.live="kelasId" 
                        class="w-full rounded-xl border border-stone-300 bg-stone-50 px-3.5 py-2.5 text-xs font-bold text-stone-900 shadow-xs focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}">Kelas {{ $c->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Bulan Selection -->
            <div>
                <label for="bulan" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Bulan</label>
                <select id="bulan" wire:model.live="bulan" 
                        class="w-full rounded-xl border border-stone-300 bg-stone-50 px-3.5 py-2.5 text-xs font-bold text-stone-900 shadow-xs focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
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
                <label for="tahun" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Tahun</label>
                <select id="tahun" wire:model.live="tahun" 
                        class="w-full rounded-xl border border-stone-300 bg-stone-50 px-3.5 py-2.5 text-xs font-bold text-stone-900 shadow-xs focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
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
            <div class="px-6 py-4 border-b border-stone-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-stone-50">
                <div class="text-xs font-bold text-stone-700">
                    Kelas: <span class="text-emerald-800 font-extrabold">{{ $kelas->nama_kelas }}</span> 
                    | Wali Kelas: <span class="text-stone-900 font-bold">{{ $kelas->guruUmum->user->nama ?? '-' }}</span>
                </div>
                
                <button wire:click="downloadPdf" 
                        class="inline-flex items-center justify-center gap-2 py-2 px-4 rounded-xl border border-stone-300 bg-white hover:bg-stone-50 text-xs font-bold text-stone-700 shadow-xs transition shrink-0">
                    <x-lucide-file-text class="w-4 h-4 text-rose-600" />
                    <span>Ekspor PDF</span>
                </button>
            </div>

            <!-- Scrollable Matrix Table -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-xs text-stone-700 min-w-[900px]">
                    <thead>
                        <tr class="bg-stone-50 border-b border-stone-200 font-bold text-stone-600">
                            <th class="py-3 px-3 w-10 text-center border-r border-stone-200">No</th>
                            <th class="py-3 px-4 w-52 border-r border-stone-200">Nama Siswa</th>
                            @for ($day = 1; $day <= $daysInMonth; $day++)
                                <th class="py-3 px-1 w-7 text-center border-r border-stone-200 bg-stone-50 text-[11px] font-bold">{{ $day }}</th>
                            @endfor
                            <th class="py-3 px-2 w-10 text-center border-r border-stone-200 bg-emerald-100 text-emerald-900 font-extrabold">H</th>
                            <th class="py-3 px-2 w-10 text-center border-r border-stone-200 bg-amber-100 text-amber-950 font-extrabold">I</th>
                            <th class="py-3 px-2 w-10 text-center border-r border-stone-200 bg-rose-100 text-rose-900 font-extrabold">A</th>
                            <th class="py-3 px-3 w-16 text-center bg-stone-100 text-stone-900 font-black">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($matrix as $index => $row)
                            <tr class="hover:bg-stone-50/60 transition">
                                <td class="py-2.5 px-3 text-center border-r border-stone-200 font-bold text-stone-400">{{ $index + 1 }}</td>
                                <td class="py-2.5 px-4 border-r border-stone-200 font-extrabold text-stone-900">
                                    {{ $row['siswa']->user->nama ?? '-' }}
                                    <div class="text-[10px] text-stone-500 font-mono font-bold mt-0.5">NIS: {{ $row['siswa']->nis }}</div>
                                </td>
                                @for ($day = 1; $day <= $daysInMonth; $day++)
                                    @php
                                        $status = $row['days'][$day];
                                        $cellClass = 'text-stone-300 font-mono';
                                        $cellText = '•';
                                        
                                        if ($status === 'hadir') {
                                            $cellClass = 'bg-emerald-100 text-emerald-900 font-black';
                                            $cellText = 'H';
                                        } elseif ($status === 'izin') {
                                            $cellClass = 'bg-amber-100 text-amber-950 font-black';
                                            $cellText = 'I';
                                        } elseif ($status === 'tidak_hadir') {
                                            $cellClass = 'bg-rose-100 text-rose-900 font-black';
                                            $cellText = 'A';
                                        } elseif ($status === 'libur') {
                                            $cellClass = 'bg-stone-100 text-stone-400 font-bold';
                                            $cellText = '-';
                                        }
                                    @endphp
                                    <td class="py-2 px-0 text-center border-r border-stone-200 text-xs {{ $cellClass }}">{{ $cellText }}</td>
                                @endfor
                                <td class="py-2.5 px-2 text-center border-r border-stone-200 bg-emerald-50 text-emerald-900 font-extrabold">{{ $row['hadir'] }}</td>
                                <td class="py-2.5 px-2 text-center border-r border-stone-200 bg-amber-50 text-amber-950 font-extrabold">{{ $row['izin'] }}</td>
                                <td class="py-2.5 px-2 text-center border-r border-stone-200 bg-rose-50 text-rose-900 font-extrabold">{{ $row['tidak_hadir'] }}</td>
                                <td class="py-2.5 px-3 text-center bg-stone-50 font-black text-stone-900">{{ $row['rate'] }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $daysInMonth + 6 }}" class="py-12 text-center text-stone-500 font-medium">
                                    Tidak ada data siswa aktif di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Legend Info Panel -->
            <div class="p-6 border-t border-stone-200 bg-stone-50/50 flex flex-wrap gap-4 text-xs font-bold text-stone-600">
                <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded bg-emerald-100 border border-emerald-300 text-emerald-900 font-black flex items-center justify-center">H</span>
                    <span>Hadir</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded bg-amber-100 border border-amber-300 text-amber-950 font-black flex items-center justify-center">I</span>
                    <span>Izin / Sakit</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded bg-rose-100 border border-rose-300 text-rose-900 font-black flex items-center justify-center">A</span>
                    <span>Alpa / Tidak Hadir</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded bg-stone-100 border border-stone-300 text-stone-500 font-bold flex items-center justify-center">-</span>
                    <span>Hari Libur</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-stone-400 font-black font-mono text-base">•</span>
                    <span>Belum Diinput</span>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="py-16 text-center">
                <div class="w-12 h-12 rounded-full bg-stone-100 flex items-center justify-center mx-auto text-stone-400 mb-3">
                    <x-lucide-clipboard class="w-6 h-6" />
                </div>
                <h3 class="text-sm font-bold text-stone-900">Silakan pilih kelas terlebih dahulu</h3>
                <p class="text-xs text-stone-500 mt-1">Gunakan filter di atas untuk memuat laporan rekap absensi.</p>
            </div>
        @endif
    </div>
</div>
