<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pencatatan Presensi Kehadiran Siswa"
        :steps="[
            ['title' => 'Pilih Kelas & Tanggal', 'desc' => 'Tentukan rombel kelas dan tanggal presensi (gunakan tombol cepat Hari Ini atau Kemarin).'],
            ['title' => 'Set Status Masal / Per-Siswa', 'desc' => 'Klik tombol status cepat di bagian atas untuk mengisi seluruh kelas, atau klik tombol status pada tiap baris siswa.'],
            ['title' => 'Simpan Presensi', 'desc' => 'Pastikan menekan tombol Simpan Seluruh Kehadiran di bawah untuk menyimpan data ke database.']
        ]"
        notes="Presensi yang disimpan akan langsung terakumulasi pada Rekap Absensi Siswa dan Rapor Digital."
    />

    <!-- Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                AKADEMIK &amp; PRESENSI
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Presensi Kehadiran Siswa</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Rekam dan perbarui status kehadiran harian siswa di kelas yang Anda ampu.</p>
        </div>
        <div class="flex items-center gap-2">
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
    </div>

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
            @if ($kelas_id && count($attendance) > 0)
                <div class="space-y-1.5 flex flex-col justify-end">
                    <label class="text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Set Masal Seluruh Kelas</label>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" wire:click="setStatusAll('hadir')" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-extrabold transition shadow-xs flex items-center gap-1.5">
                            <x-lucide-check-circle class="w-3.5 h-3.5" />
                            <span>Semua Hadir</span>
                        </button>
                        <button type="button" wire:click="setStatusAll('sakit')" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-extrabold transition shadow-xs flex items-center gap-1.5">
                            <x-lucide-activity class="w-3.5 h-3.5" />
                            <span>Semua Sakit</span>
                        </button>
                        <button type="button" wire:click="setStatusAll('izin')" class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-stone-950 rounded-xl text-xs font-black transition shadow-xs flex items-center gap-1.5">
                            <x-lucide-file-text class="w-3.5 h-3.5" />
                            <span>Semua Izin</span>
                        </button>
                        <button type="button" wire:click="setStatusAll('alpa')" class="px-3 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-extrabold transition shadow-xs flex items-center gap-1.5">
                            <x-lucide-x-circle class="w-3.5 h-3.5" />
                            <span>Semua Alpa</span>
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

        <!-- Attendance Table -->
        <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
            <form wire:submit.prevent="save">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-stone-50 border-b border-stone-200 text-stone-600 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="p-3.5 border-r border-stone-200 w-12 text-center">No</th>
                                <th class="p-3.5 border-r border-stone-200 w-28">NIS</th>
                                <th class="p-3.5 border-r border-stone-200">Nama Siswa</th>
                                <th class="p-3.5 border-r border-stone-200 w-96 text-center">Pilih Status Kehadiran</th>
                                <th class="p-3.5">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @forelse ($attendance as $index => $att)
                                @php
                                    $currentStatus = $att['status'] ?? 'hadir';
                                @endphp
                                <tr class="hover:bg-stone-50/60 transition">
                                    <td class="p-3.5 border-r border-stone-200 text-center font-bold text-stone-400">{{ $index + 1 }}</td>
                                    <td class="p-3.5 border-r border-stone-200 text-stone-600 font-mono font-bold">{{ $att['nis'] }}</td>
                                    <td class="p-3.5 border-r border-stone-200">
                                        <span class="font-extrabold text-stone-900 block text-xs">{{ $att['nama'] }}</span>
                                    </td>
                                    <td class="p-3.5 border-r border-stone-200">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Hadir Button -->
                                            <button type="button" wire:click="setStatus({{ $index }}, 'hadir')"
                                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition uppercase tracking-wider flex items-center gap-1 {{ $currentStatus === 'hadir' ? 'bg-emerald-600 text-white shadow-sm ring-2 ring-emerald-400 font-extrabold' : 'bg-stone-100 text-stone-600 hover:bg-stone-200 border border-stone-200' }}">
                                                <x-lucide-check-circle class="w-3.5 h-3.5" />
                                                <span>Hadir</span>
                                            </button>

                                            <!-- Sakit Button -->
                                            <button type="button" wire:click="setStatus({{ $index }}, 'sakit')"
                                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition uppercase tracking-wider flex items-center gap-1 {{ $currentStatus === 'sakit' ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-400 font-extrabold' : 'bg-stone-100 text-stone-600 hover:bg-stone-200 border border-stone-200' }}">
                                                <x-lucide-activity class="w-3.5 h-3.5" />
                                                <span>Sakit</span>
                                            </button>

                                            <!-- Izin Button -->
                                            <button type="button" wire:click="setStatus({{ $index }}, 'izin')"
                                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition uppercase tracking-wider flex items-center gap-1 {{ $currentStatus === 'izin' ? 'bg-amber-500 text-stone-950 shadow-sm ring-2 ring-amber-300 font-black' : 'bg-stone-100 text-stone-600 hover:bg-stone-200 border border-stone-200' }}">
                                                <x-lucide-file-text class="w-3.5 h-3.5" />
                                                <span>Izin</span>
                                            </button>

                                            <!-- Alpa Button -->
                                            <button type="button" wire:click="setStatus({{ $index }}, 'alpa')"
                                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition uppercase tracking-wider flex items-center gap-1 {{ $currentStatus === 'alpa' ? 'bg-rose-600 text-white shadow-sm ring-2 ring-rose-400 font-extrabold' : 'bg-stone-100 text-stone-600 hover:bg-stone-200 border border-stone-200' }}">
                                                <x-lucide-x-circle class="w-3.5 h-3.5" />
                                                <span>Alpa</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="p-3.5">
                                        <input type="text" wire:model="attendance.{{ $index }}.catatan" 
                                            class="w-full px-3 py-1.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 shadow-xs" placeholder="Keterangan / alasan izin..." />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-stone-500 font-semibold">
                                        Tidak ada siswa aktif terdaftar di kelas ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (count($attendance) > 0)
                    <div class="flex items-center justify-between border-t border-stone-200 p-4 bg-stone-50">
                        <div class="text-xs text-stone-600 font-medium">
                            Pastikan status seluruh siswa telah terisi sebelum menyimpan.
                        </div>
                        <button type="submit" class="py-2.5 px-7 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-extrabold transition shadow-sm flex items-center gap-2">
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
</div>
