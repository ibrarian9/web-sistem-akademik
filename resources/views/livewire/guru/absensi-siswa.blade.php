<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pencatatan Presensi Kehadiran Siswa"
        :steps="[
            ['title' => 'Pilih Kelas & Tanggal', 'desc' => 'Tentukan rombel kelas dan tanggal presensi pada bar seleksi di bawah.'],
            ['title' => 'Set Status Masal', 'desc' => 'Gunakan tombol status cepat (Hadir, Sakit, Izin, Alpa) untuk menandai seluruh siswa kelas sekaligus.'],
            ['title' => 'Pilih Status Berwarna', 'desc' => 'Klik tombol status yang ber-kontras tinggi pada tiap baris siswa (Hijau=Hadir, Biru=Sakit, Kuning=Izin, Merah=Alpa).']
        ]"
        notes="Pastikan menekan tombol 'Simpan Seluruh Kehadiran' di bagian bawah tabel setelah selesai menginput data."
    />

    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Absensi Siswa</h2>
        <p class="text-xs text-slate-400">Rekam dan perbarui status kehadiran harian siswa di kelas yang diampu.</p>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- Selection Bar & Quick Actions -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Kelas -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pilih Kelas</label>
                <select wire:model.live="kelas_id" class="w-full px-3.5 py-2.5 bg-slate-950/70 border border-slate-800 rounded-xl text-white text-xs font-semibold focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                    <option value="">-- Pilih Rombongan Belajar / Kelas --</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal Absensi -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Presensi</label>
                <input wire:model.live="tanggal" type="date" class="w-full px-3.5 py-2.5 bg-slate-950/70 border border-slate-800 rounded-xl text-white text-xs font-semibold focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
            </div>

            <!-- Quick Action Set All -->
            @if ($kelas_id && count($attendance) > 0)
                <div class="space-y-1.5 flex flex-col justify-end">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Set Status Masal Seluruh Siswa</label>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" wire:click="setStatusAll('hadir')" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-[10px] font-bold transition duration-200 uppercase tracking-wider shadow-sm flex items-center gap-1">
                            <x-lucide-check-circle class="w-3 h-3" />
                            <span>Set Hadir</span>
                        </button>
                        <button type="button" wire:click="setStatusAll('sakit')" class="px-2.5 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-[10px] font-bold transition duration-200 uppercase tracking-wider shadow-sm flex items-center gap-1">
                            <x-lucide-activity class="w-3 h-3" />
                            <span>Set Sakit</span>
                        </button>
                        <button type="button" wire:click="setStatusAll('izin')" class="px-2.5 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-[10px] font-black transition duration-200 uppercase tracking-wider shadow-sm flex items-center gap-1">
                            <x-lucide-file-text class="w-3 h-3" />
                            <span>Set Izin</span>
                        </button>
                        <button type="button" wire:click="setStatusAll('alpa')" class="px-2.5 py-1.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-[10px] font-bold transition duration-200 uppercase tracking-wider shadow-sm flex items-center gap-1">
                            <x-lucide-x-circle class="w-3 h-3" />
                            <span>Set Alpa</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Student Attendance List -->
    @if ($kelas_id)
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6">
            <form wire:submit.prevent="save" class="space-y-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800">
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">NIS</th>
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Siswa</th>
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-96">Pilih Status Kehadiran (Kontras Tinggi)</th>
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-850">
                            @forelse ($attendance as $index => $att)
                                <tr class="hover:bg-slate-950/30 transition-colors">
                                    <td class="py-3.5 text-xs font-mono font-semibold text-slate-400">{{ $att['nis'] }}</td>
                                    <td class="py-3.5 text-xs font-bold text-white">{{ $att['nama'] }}</td>
                                    <td class="py-3.5">
                                        <div class="flex items-center gap-2">
                                            <!-- Hadir Button (High-Contrast Emerald) -->
                                            <label class="cursor-pointer">
                                                <input type="radio" wire:model="attendance.{{ $index }}.status" value="hadir" class="sr-only peer" />
                                                <span class="px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-[11px] font-bold text-slate-300 peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-400 peer-checked:font-extrabold peer-checked:shadow-md peer-checked:shadow-emerald-600/30 peer-checked:ring-2 peer-checked:ring-emerald-400/50 uppercase tracking-wider flex items-center gap-1 transition-all duration-150">
                                                    <x-lucide-check-circle class="w-3.5 h-3.5" />
                                                    <span>Hadir</span>
                                                </span>
                                            </label>

                                            <!-- Sakit Button (High-Contrast Blue) -->
                                            <label class="cursor-pointer">
                                                <input type="radio" wire:model="attendance.{{ $index }}.status" value="sakit" class="sr-only peer" />
                                                <span class="px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-[11px] font-bold text-slate-300 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-400 peer-checked:font-extrabold peer-checked:shadow-md peer-checked:shadow-blue-600/30 peer-checked:ring-2 peer-checked:ring-blue-400/50 uppercase tracking-wider flex items-center gap-1 transition-all duration-150">
                                                    <x-lucide-activity class="w-3.5 h-3.5" />
                                                    <span>Sakit</span>
                                                </span>
                                            </label>

                                            <!-- Izin Button (High-Contrast Amber) -->
                                            <label class="cursor-pointer">
                                                <input type="radio" wire:model="attendance.{{ $index }}.status" value="izin" class="sr-only peer" />
                                                <span class="px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-[11px] font-bold text-slate-300 peer-checked:bg-amber-500 peer-checked:text-slate-950 peer-checked:border-amber-300 peer-checked:font-black peer-checked:shadow-md peer-checked:shadow-amber-500/30 peer-checked:ring-2 peer-checked:ring-amber-300/50 uppercase tracking-wider flex items-center gap-1 transition-all duration-150">
                                                    <x-lucide-file-text class="w-3.5 h-3.5" />
                                                    <span>Izin</span>
                                                </span>
                                            </label>

                                            <!-- Alpa Button (High-Contrast Rose) -->
                                            <label class="cursor-pointer">
                                                <input type="radio" wire:model="attendance.{{ $index }}.status" value="alpa" class="sr-only peer" />
                                                <span class="px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-[11px] font-bold text-slate-300 peer-checked:bg-rose-600 peer-checked:text-white peer-checked:border-rose-400 peer-checked:font-extrabold peer-checked:shadow-md peer-checked:shadow-rose-600/30 peer-checked:ring-2 peer-checked:ring-rose-400/50 uppercase tracking-wider flex items-center gap-1 transition-all duration-150">
                                                    <x-lucide-x-circle class="w-3.5 h-3.5" />
                                                    <span>Alpa</span>
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="py-3.5">
                                        <input type="text" wire:model="attendance.{{ $index }}.catatan" 
                                            class="w-full px-3 py-1.5 bg-slate-950/70 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="Catatan tambahan (opsional)..." />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-500 font-semibold">
                                        Tidak ada siswa aktif terdaftar di kelas ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (count($attendance) > 0)
                    <div class="flex justify-end border-t border-slate-800 pt-4">
                        <button type="submit" class="py-3 px-7 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition duration-200 shadow-lg shadow-indigo-600/20 flex items-center gap-2">
                            <x-lucide-check-circle class="w-4 h-4" />
                            <span>Simpan Seluruh Kehadiran</span>
                        </button>
                    </div>
                @endif
            </form>
        </div>
    @else
        <div class="bg-slate-900/40 border border-slate-800 rounded-3xl p-12 text-center text-slate-500 font-medium">
            <x-lucide-clipboard class="w-8 h-8 mx-auto mb-3 text-slate-600" />
            <span>Pilih kelas dan tanggal absensi untuk menampilkan data kehadiran siswa.</span>
        </div>
    @endif
</div>
