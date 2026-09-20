<div 
    x-data="{
        isDirty: false,
        init() {
            Livewire.on('attendance-saved', () => {
                this.isDirty = false;
            });
            window.addEventListener('beforeunload', (e) => {
                if (this.isDirty) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        }
    }" 
    class="space-y-6 font-sans"
>
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        :title="$isReadOnly ? 'Petunjuk Pemantauan Presensi Kehadiran Siswa' : 'Petunjuk Pencatatan Presensi Kehadiran Siswa'"
        :steps="$isReadOnly ? [
            ['title' => 'Pilih Kelas & Tanggal', 'desc' => 'Pilih rombel kelas tempat siswa dampingan berada dan tentukan tanggal yang ingin ditinjau.'],
            ['title' => 'Pantau Status Kehadiran', 'desc' => 'Tinjau status kehadiran siswa bimbingan yang telah diinput oleh Guru Umum.'],
            ['title' => 'Koordinasi Terpadu', 'desc' => 'Gunakan data kehadiran untuk menyelaraskan catatan observasi pendampingan berkala.']
        ] : [
            ['title' => 'Pilih Kelas & Tanggal', 'desc' => 'Tentukan rombel kelas dan tanggal presensi (gunakan tombol cepat Hari Ini atau Kemarin).'],
            ['title' => 'Set Status Masal / Per-Siswa', 'desc' => 'Klik tombol status cepat di bagian atas untuk mengisi seluruh kelas, atau klik tombol status pada tiap baris siswa.'],
            ['title' => 'Simpan Presensi', 'desc' => 'Pastikan menekan tombol Simpan Presensi di bawah untuk menyimpan data ke database.']
        ]"
        :notes="$isReadOnly ? 'Pengisian presensi siswa dikelola oleh Guru Umum. Guru Pendamping memiliki hak akses pantau khusus siswa dampingan.' : 'Presensi yang disimpan akan langsung terakumulasi pada Rekap Absensi Siswa dan Rapor Digital.'"
    />

    <!-- Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                AKADEMIK & PRESENSI
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">{{ $isReadOnly ? 'Pantau Kehadiran Siswa' : 'Presensi Kehadiran Siswa' }}</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">{{ $isReadOnly ? 'Pantau status kehadiran harian siswa dampingan Anda yang dicatat oleh Guru Umum.' : 'Rekam dan perbarui status kehadiran harian siswa di kelas yang Anda ampu.' }}</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <button 
                type="button" 
                id="tour-absen-help-btn"
                onclick="runAbsensiTour()"
                class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 active:bg-emerald-200 text-emerald-800 border border-emerald-300 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer"
                title="Buka panduan langkah demi langkah cara mencatat presensi kehadiran siswa"
            >
                <x-lucide-help-circle class="w-4 h-4 text-emerald-700" />
                <span>Panduan Interaktif</span>
            </button>
            <div id="tour-absen-tanggal" class="flex items-center gap-2">
                <button type="button" wire:click="setPresetDate('today')" 
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5 {{ $tanggal === date('Y-m-d') ? 'bg-emerald-700 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200 border border-stone-300' }}">
                    <x-lucide-calendar class="w-3.5 h-3.5" />
                    <span>Hari Ini</span>
                </button>
                <button type="button" wire:click="setPresetDate('yesterday')" 
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5 {{ $tanggal === date('Y-m-d', strtotime('-1 day')) ? 'bg-emerald-700 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200 border border-stone-300' }}">
                    <x-lucide-clock class="w-3.5 h-3.5" />
                    <span>Kemarin</span>
                </button>
            </div>
            @if ($kelas_id && count($attendance) > 0 && !$isReadOnly)
                <div id="tour-absen-simpan-top">
                    <button type="button" wire:click="save" 
                        class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-extrabold transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <x-lucide-check-circle class="w-3.5 h-3.5" />
                        <span>Simpan Presensi</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    @if ($isReadOnly)
        <!-- Read-Only Banner for Guru Pendamping -->
        <div class="p-4 bg-amber-50 border border-amber-300 text-amber-900 rounded-2xl text-xs flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2.5 font-bold">
                <x-lucide-info class="w-4 h-4 text-amber-700 shrink-0" />
                <span>Mode Pratinjau (Hanya Lihat): Pencatatan presensi kehadiran siswa dilakukan oleh Guru Umum. Guru Pendamping hanya dapat melihat status kehadiran siswa bimbingan.</span>
            </div>
            <span class="px-2.5 py-1 bg-amber-200 text-amber-900 rounded-lg text-[10px] font-black uppercase tracking-wider shrink-0">Hanya Lihat</span>
        </div>
    @endif

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
            <x-lucide-check-circle class="w-4 h-4 text-emerald-600 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-300 text-rose-900 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
            <x-lucide-alert-circle class="w-4 h-4 text-rose-600 shrink-0" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Selection Bar & Quick Actions -->
    <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Kelas -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Pilih Kelas / Rombel</label>
                <select wire:model.live="kelas_id" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 shadow-xs">
                    <option value="">-- Pilih Rombongan Belajar / Kelas --</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal Absensi -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Tanggal Presensi</label>
                <input wire:model.live="tanggal" type="date" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 shadow-xs" />
            </div>

            <!-- Quick Action Set All -->
            @if ($kelas_id && count($attendance) > 0 && !$isReadOnly)
                <div id="tour-absen-masal" class="space-y-1.5 flex flex-col justify-end">
                    <label class="text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Set Masal Seluruh Kelas</label>
                    <div class="flex flex-wrap items-center gap-1.5">
                        <button type="button" wire:click="setStatusAll('hadir')" @click="isDirty = true" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-extrabold transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <x-lucide-check-circle class="w-3.5 h-3.5" />
                            <span>Semua Hadir</span>
                        </button>
                        <button type="button" wire:click="setStatusAll('sakit')" @click="isDirty = true" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-extrabold transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <x-lucide-activity class="w-3.5 h-3.5" />
                            <span>Semua Sakit</span>
                        </button>
                        <button type="button" wire:click="setStatusAll('izin')" @click="isDirty = true" class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-stone-950 rounded-xl text-xs font-black transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <x-lucide-file-text class="w-3.5 h-3.5" />
                            <span>Semua Izin</span>
                        </button>
                        <button type="button" wire:click="setStatusAll('alpa')" @click="isDirty = true" class="px-3 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-extrabold transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <x-lucide-x-circle class="w-3.5 h-3.5" />
                            <span>Semua Alpa</span>
                        </button>
                        <button type="button" wire:click="save" class="px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-extrabold transition shadow-xs flex items-center gap-1.5 cursor-pointer sm:ml-auto">
                            <x-lucide-check-circle class="w-3.5 h-3.5" />
                            <span>Simpan Cepat</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Student Attendance List & Metrics -->
    @if ($kelas_id)
        <!-- Realtime Status Metrics -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Total Siswa</span>
                    <span class="text-xl font-black text-stone-900">{{ $summary['total'] }}</span>
                </div>
                <div class="w-9 h-9 rounded-xl bg-stone-100 flex items-center justify-center text-stone-600 font-bold">
                    <x-lucide-users class="w-4 h-4" />
                </div>
            </div>

            <div class="bg-emerald-50/70 border border-emerald-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider block">Hadir</span>
                    <span class="text-xl font-black text-emerald-900">{{ $summary['hadir'] }}</span>
                </div>
                <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold">
                    <x-lucide-check-circle class="w-4 h-4" />
                </div>
            </div>

            <div class="bg-blue-50/70 border border-blue-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold text-blue-800 uppercase tracking-wider block">Sakit</span>
                    <span class="text-xl font-black text-blue-900">{{ $summary['sakit'] }}</span>
                </div>
                <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                    <x-lucide-activity class="w-4 h-4" />
                </div>
            </div>

            <div class="bg-amber-50/70 border border-amber-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold text-amber-900 uppercase tracking-wider block">Izin</span>
                    <span class="text-xl font-black text-amber-950">{{ $summary['izin'] }}</span>
                </div>
                <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center text-amber-800 font-bold">
                    <x-lucide-file-text class="w-4 h-4" />
                </div>
            </div>

            <div class="bg-rose-50/70 border border-rose-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold text-rose-800 uppercase tracking-wider block">Alpa</span>
                    <span class="text-xl font-black text-rose-900">{{ $summary['alpa'] }}</span>
                </div>
                <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center text-rose-700 font-bold">
                    <x-lucide-x-circle class="w-4 h-4" />
                </div>
            </div>
        </div>

        <!-- Unsaved Changes Alert Banner -->
        <div x-show="isDirty" x-cloak class="p-4 bg-amber-50 border border-amber-300 text-amber-900 rounded-2xl text-xs font-bold flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs animate-pulse">
            <div class="flex items-center gap-2.5">
                <x-lucide-alert-triangle class="w-4 h-4 text-amber-600 shrink-0" />
                <span>Status presensi telah diubah. Pastikan menekan tombol "Simpan Seluruh Kehadiran" di bagian bawah untuk menyimpan ke database.</span>
            </div>
            <button type="button" wire:click="save" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-black text-xs shrink-0 shadow-xs cursor-pointer">
                Simpan Sekarang
            </button>
        </div>

        <!-- Attendance Table -->
        <div class="bg-white border border-stone-200 rounded-2xl shadow-xs overflow-hidden">
            <form wire:submit.prevent="save">
                <x-table loadingTarget="kelas_id, tanggal">
                    <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                        <tr>
                            <x-table.th align="center" class="w-12">No</x-table.th>
                            <x-table.th>Nama Siswa & NIS</x-table.th>
                            <x-table.th align="center" class="w-96">{{ $isReadOnly ? 'Status Kehadiran' : 'Pilih Status Kehadiran' }}</x-table.th>
                            <x-table.th>Catatan</x-table.th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($attendance as $index => $att)
                            @php
                                $currentStatus = $att['status'] ?? 'hadir';
                            @endphp
                            <tr class="hover:bg-stone-50 transition">
                                <td class="p-3.5 border-r border-stone-200 text-center font-bold text-stone-400 text-xs">{{ $index + 1 }}</td>
                                <td class="p-3.5 border-r border-stone-200">
                                    <span class="font-extrabold text-stone-900 block text-xs">{{ $att['nama'] }}</span>
                                    <span class="text-[10px] text-stone-500 font-mono font-medium block mt-0.5">NIS: {{ $att['nis'] }}</span>
                                </td>
                                <td class="p-3.5 border-r border-stone-200">
                                    @if ($isReadOnly)
                                        <div class="flex items-center justify-center">
                                            @if ($currentStatus === 'hadir')
                                                <span class="px-3 py-1.5 rounded-xl text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 uppercase tracking-wider flex items-center gap-1.5 shadow-2xs">
                                                    <x-lucide-check-circle class="w-3.5 h-3.5 text-emerald-600" />
                                                    <span>Hadir</span>
                                                </span>
                                            @elseif ($currentStatus === 'sakit')
                                                <span class="px-3 py-1.5 rounded-xl text-xs font-black bg-blue-100 text-blue-800 border border-blue-300 uppercase tracking-wider flex items-center gap-1.5 shadow-2xs">
                                                    <x-lucide-activity class="w-3.5 h-3.5 text-blue-600" />
                                                    <span>Sakit</span>
                                                </span>
                                            @elseif ($currentStatus === 'izin')
                                                <span class="px-3 py-1.5 rounded-xl text-xs font-black bg-amber-100 text-amber-900 border border-amber-300 uppercase tracking-wider flex items-center gap-1.5 shadow-2xs">
                                                    <x-lucide-file-text class="w-3.5 h-3.5 text-amber-700" />
                                                    <span>Izin</span>
                                                </span>
                                            @elseif ($currentStatus === 'alpa')
                                                <span class="px-3 py-1.5 rounded-xl text-xs font-black bg-rose-100 text-rose-800 border border-rose-300 uppercase tracking-wider flex items-center gap-1.5 shadow-2xs">
                                                    <x-lucide-x-circle class="w-3.5 h-3.5 text-rose-600" />
                                                    <span>Alpa</span>
                                                </span>
                                            @else
                                                <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-stone-100 text-stone-600 border border-stone-200 uppercase tracking-wider flex items-center gap-1.5">
                                                    <x-lucide-clock class="w-3.5 h-3.5 text-stone-400" />
                                                    <span>Belum Ada Data</span>
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <div @if($loop->first) id="tour-absen-status-btn" @endif class="flex items-center justify-center gap-1.5 flex-wrap sm:flex-nowrap">
                                            <!-- Hadir Button -->
                                            <button type="button" wire:click="setStatus({{ $index }}, 'hadir')" @click="isDirty = true"
                                                class="min-h-[38px] px-3.5 py-2 rounded-xl text-xs font-bold transition uppercase tracking-wider flex items-center justify-center gap-1.5 cursor-pointer touch-manipulation {{ $currentStatus === 'hadir' ? 'bg-emerald-700 text-white shadow-xs ring-2 ring-emerald-400 font-extrabold' : 'bg-stone-100 text-stone-700 hover:bg-stone-200 border border-stone-300' }}">
                                                <x-lucide-check-circle class="w-4 h-4 text-white shrink-0" />
                                                <span>Hadir</span>
                                            </button>

                                            <!-- Sakit Button -->
                                            <button type="button" wire:click="setStatus({{ $index }}, 'sakit')" @click="isDirty = true"
                                                class="min-h-[38px] px-3.5 py-2 rounded-xl text-xs font-bold transition uppercase tracking-wider flex items-center justify-center gap-1.5 cursor-pointer touch-manipulation {{ $currentStatus === 'sakit' ? 'bg-blue-700 text-white shadow-xs ring-2 ring-blue-400 font-extrabold' : 'bg-stone-100 text-stone-700 hover:bg-stone-200 border border-stone-300' }}">
                                                <x-lucide-activity class="w-4 h-4 text-white shrink-0" />
                                                <span>Sakit</span>
                                            </button>

                                            <!-- Izin Button -->
                                            <button type="button" wire:click="setStatus({{ $index }}, 'izin')" @click="isDirty = true"
                                                class="min-h-[38px] px-3.5 py-2 rounded-xl text-xs font-bold transition uppercase tracking-wider flex items-center justify-center gap-1.5 cursor-pointer touch-manipulation {{ $currentStatus === 'izin' ? 'bg-amber-500 text-stone-950 shadow-xs ring-2 ring-amber-300 font-black' : 'bg-stone-100 text-stone-700 hover:bg-stone-200 border border-stone-300' }}">
                                                <x-lucide-file-text class="w-4 h-4 text-stone-950 shrink-0" />
                                                <span>Izin</span>
                                            </button>

                                            <!-- Alpa Button -->
                                            <button type="button" wire:click="setStatus({{ $index }}, 'alpa')" @click="isDirty = true"
                                                class="min-h-[38px] px-3.5 py-2 rounded-xl text-xs font-bold transition uppercase tracking-wider flex items-center justify-center gap-1.5 cursor-pointer touch-manipulation {{ $currentStatus === 'alpa' ? 'bg-rose-700 text-white shadow-xs ring-2 ring-rose-400 font-extrabold' : 'bg-stone-100 text-stone-700 hover:bg-stone-200 border border-stone-300' }}">
                                                <x-lucide-x-circle class="w-4 h-4 text-white shrink-0" />
                                                <span>Alpa</span>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td class="p-3.5">
                                    @if ($isReadOnly)
                                        <span class="text-xs font-medium text-stone-700 block px-1">
                                             {{ $att['catatan'] ?: '-' }}
                                        </span>
                                    @else
                                        <input type="text" wire:model="attendance.{{ $index }}.catatan" @input="isDirty = true"
                                            class="w-full px-3 py-1.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 shadow-xs" placeholder="Keterangan / alasan izin..." />
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <x-table.empty :colspan="4" title="Tidak ada data siswa" message="Tidak ada data siswa terdaftar di rombel kelas ini." />
                        @endforelse
                    </tbody>
                </x-table>

                @if (count($attendance) > 0 && !$isReadOnly)
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-stone-200 p-4 bg-stone-50">
                        <div class="flex items-center gap-2 text-xs font-semibold">
                            <span x-show="isDirty" x-cloak class="px-2.5 py-1 bg-amber-100 border border-amber-300 text-amber-900 rounded-lg text-xs font-bold animate-pulse">
                                Belum disimpan
                            </span>
                            <span class="text-stone-600">Pastikan status seluruh siswa telah terisi sebelum menyimpan.</span>
                        </div>
                        <button type="submit" class="w-full sm:w-auto py-2.5 px-7 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-extrabold transition shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                            <x-lucide-check-circle class="w-4 h-4" />
                            <span>Simpan Seluruh Kehadiran</span>
                        </button>
                    </div>
                @endif
            </form>
        </div>
    @else
        <div class="bg-white border border-stone-200 rounded-2xl p-12 text-center text-stone-500 font-medium shadow-sm">
            <x-lucide-clipboard class="w-10 h-10 mx-auto mb-3 text-stone-400" />
            <span class="text-xs font-bold text-stone-600">Pilih kelas di atas untuk menampilkan daftar presensi siswa.</span>
        </div>
    @endif

    <script>
        function getAbsensiTourSteps() {
            const steps = [
                {
                    element: '#tour-absen-tanggal',
                    popover: {
                        title: '1. Pilih Tanggal Cepat',
                        description: 'Gunakan tombol Hari Ini atau Kemarin untuk berpindah tanggal presensi dengan satu kali klik tanpa membuka pemilih tanggal.',
                        side: 'bottom',
                        align: 'start'
                    }
                }
            ];

            if (document.querySelector('#tour-absen-masal')) {
                steps.push({
                    element: '#tour-absen-masal',
                    popover: {
                        title: '2. Set Masal Seluruh Kelas',
                        description: 'Klik tombol Semua Hadir untuk mengisi seluruh status siswa sekaligus dalam sekejap. Anda juga dapat memilih Semua Sakit, Izin, atau Alpa jika diperlukan.',
                        side: 'top',
                        align: 'start'
                    }
                });
            }

            if (document.querySelector('#tour-absen-status-btn')) {
                steps.push({
                    element: '#tour-absen-status-btn',
                    popover: {
                        title: '3. Atur Status Per Siswa',
                        description: 'Klik tombol Hadir, Sakit, Izin, atau Alpa pada siswa yang bersangkutan jika ada yang berhalangan hadir. Anda juga bisa mengisi keterangan alasan izin pada kolom catatan.',
                        side: 'top',
                        align: 'center'
                    }
                });
            }

            const simpanEl = document.querySelector('#tour-absen-simpan-top') ? '#tour-absen-simpan-top' : '#tour-absen-simpan-bottom';
            if (document.querySelector(simpanEl)) {
                steps.push({
                    element: simpanEl,
                    popover: {
                        title: '4. Simpan Data Presensi',
                        description: 'Setelah menyetel kehadiran siswa, pastikan selalu menekan tombol hijau Simpan Presensi agar data tersimpan permanen ke database dan rekapitulasi rapor.',
                        side: 'bottom',
                        align: 'end'
                    }
                });
            }

            return steps;
        }

        function runAbsensiTour() {
            if (typeof window.startSiakadTour === 'function') {
                window.startSiakadTour('siakad_tour_absen_v1', getAbsensiTourSteps());
            }
        }

        document.addEventListener('livewire:navigated', () => {
            if (typeof window.checkAutoTour === 'function') {
                window.checkAutoTour('siakad_tour_absen_v1', getAbsensiTourSteps(), 800);
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            if (typeof window.checkAutoTour === 'function') {
                window.checkAutoTour('siakad_tour_absen_v1', getAbsensiTourSteps(), 800);
            }
        });
    </script>
</div>
