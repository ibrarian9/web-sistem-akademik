<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Absensi Siswa</h2>
        <p class="text-xs text-slate-500">Rekam dan perbarui status kehadiran harian siswa di kelas yang diampu.</p>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- Selection Bar -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Kelas -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kelas</label>
                <select wire:model.live="kelas_id" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                    <option value="">Pilih Kelas</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal Absensi -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Absensi</label>
                <input wire:model.live="tanggal" type="date" class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
            </div>

            <!-- Quick Action Set All -->
            @if ($kelas_id && count($attendance) > 0)
                <div class="space-y-1.5 flex flex-col justify-end">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Set Status Masal</label>
                    <div class="flex gap-2">
                        <button type="button" wire:click="setStatusAll('hadir')" class="px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 rounded-xl text-[10px] font-bold transition duration-200 uppercase">Set Hadir</button>
                        <button type="button" wire:click="setStatusAll('izin')" class="px-3 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/20 rounded-xl text-[10px] font-bold transition duration-200 uppercase">Set Izin</button>
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
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider w-72">Status Kehadiran</th>
                                <th class="pb-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-850">
                            @forelse ($attendance as $index => $att)
                                <tr class="hover:bg-slate-950/20">
                                    <td class="py-3 text-xs font-semibold text-slate-400">{{ $att['nis'] }}</td>
                                    <td class="py-3 text-xs font-bold text-white">{{ $att['nama'] }}</td>
                                    <td class="py-3">
                                        <div class="flex items-center gap-1.5">
                                            <!-- Hadir -->
                                            <label class="cursor-pointer">
                                                <input type="radio" wire:model="attendance.{{ $index }}.status" value="hadir" class="sr-only peer" />
                                                <span class="px-3 py-1.5 bg-slate-950/80 border border-slate-800 rounded-xl text-[10px] font-bold text-slate-500 peer-checked:bg-emerald-500/10 peer-checked:text-emerald-400 peer-checked:border-emerald-500/30 uppercase tracking-wide block transition duration-200">
                                                    Hadir
                                                </span>
                                            </label>

                                            <!-- Sakit -->
                                            <label class="cursor-pointer">
                                                <input type="radio" wire:model="attendance.{{ $index }}.status" value="sakit" class="sr-only peer" />
                                                <span class="px-3 py-1.5 bg-slate-950/80 border border-slate-800 rounded-xl text-[10px] font-bold text-slate-500 peer-checked:bg-sky-500/10 peer-checked:text-sky-400 peer-checked:border-sky-500/30 uppercase tracking-wide block transition duration-200">
                                                    Sakit
                                                </span>
                                            </label>

                                            <!-- Izin -->
                                            <label class="cursor-pointer">
                                                <input type="radio" wire:model="attendance.{{ $index }}.status" value="izin" class="sr-only peer" />
                                                <span class="px-3 py-1.5 bg-slate-950/80 border border-slate-800 rounded-xl text-[10px] font-bold text-slate-500 peer-checked:bg-amber-500/10 peer-checked:text-amber-400 peer-checked:border-amber-500/30 uppercase tracking-wide block transition duration-200">
                                                    Izin
                                                </span>
                                            </label>

                                            <!-- Alpa -->
                                            <label class="cursor-pointer">
                                                <input type="radio" wire:model="attendance.{{ $index }}.status" value="alpa" class="sr-only peer" />
                                                <span class="px-3 py-1.5 bg-slate-950/80 border border-slate-800 rounded-xl text-[10px] font-bold text-slate-500 peer-checked:bg-rose-500/10 peer-checked:text-rose-400 peer-checked:border-rose-500/30 uppercase tracking-wide block transition duration-200">
                                                    Alpa
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <input type="text" wire:model="attendance.{{ $index }}.catatan" 
                                            class="w-full px-2.5 py-1.5 bg-slate-950/60 border border-slate-800 rounded-lg text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" placeholder="Keterangan (opsional)..." />
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
                        <button type="submit" class="py-2.5 px-6 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition duration-200 shadow-lg shadow-indigo-600/10">
                            Simpan Seluruh Kehadiran
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
