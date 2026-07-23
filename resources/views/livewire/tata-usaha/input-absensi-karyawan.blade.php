<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Input & Unggah Presensi Karyawan"
        :steps="[
            ['title' => 'Pilih Tanggal Presensi', 'desc' => 'Tentukan tanggal presensi kerja yang ingin diinput atau diperbarui.'],
            ['title' => 'Set Status Harian', 'desc' => 'Pilih status Kehadiran (Hadir, Terlambat, Sakit, Izin, Alpa) dan waktu jam masuk/pulang.'],
            ['title' => 'Unggah File CSV Batch', 'desc' => 'Gunakan fitur Unggah File CSV untuk mengunggah rekap presensi seluruh karyawan sekaligus.']
        ]"
        notes="Seluruh karyawan (Guru, TU, Finance) kecuali Super Admin dan Pengawas wajib didata absensinya secara terpusat oleh Tata Usaha."
    />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight">Input Absensi Karyawan &amp; Guru</h2>
            <p class="text-xs text-stone-500">Pencatatan presensi terpusat oleh Tata Usaha untuk seluruh staf pendidik &amp; tenaga kependidikan.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Control & Filter Bar -->
    <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <!-- Tanggal -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Tanggal Presensi</label>
                <input type="date" wire:model.live="tanggal" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-green-500" />
            </div>

            <!-- Filter Role -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Kategori Role / Staf</label>
                <select wire:model.live="filterRole" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-green-500">
                    <option value="semua">Semua Karyawan</option>
                    <option value="guru">Guru (Pendidik)</option>
                    <option value="tata_usaha">Tata Usaha</option>
                    <option value="finance">Bendahara / Finance</option>
                    <option value="kepala_sekolah">Kepala Sekolah</option>
                </select>
            </div>

            <!-- Search -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Cari Nama / NIP</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari..." class="w-full pl-8 pr-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-green-500" />
                    <x-lucide-search class="w-3.5 h-3.5 text-stone-400 absolute left-2.5 top-2.5" />
                </div>
            </div>

            <!-- Set Status Masal -->
            <div class="space-y-1.5 flex flex-col justify-end">
                <label class="text-[10px] font-bold text-stone-400 uppercase tracking-wider mb-1">Set Status Masal</label>
                <div class="flex gap-2">
                    <button type="button" wire:click="setStatusAll('hadir')" class="px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 border border-emerald-300 rounded-xl text-[10px] font-bold transition uppercase">Semua Hadir</button>
                    <button type="button" wire:click="setStatusAll('izin')" class="px-3 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-800 border border-amber-300 rounded-xl text-[10px] font-bold transition uppercase">Semua Izin</button>
                </div>
            </div>
        </div>

        <!-- Upload CSV Box -->
        <div class="border-t border-stone-100 pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <form wire:submit.prevent="uploadCsv" class="flex items-center gap-3 w-full sm:w-auto">
                <span class="text-xs font-bold text-stone-600 shrink-0">Unggah CSV:</span>
                <input type="file" wire:model="csvFile" accept=".csv,.txt" class="text-xs text-stone-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" />
                <button type="submit" class="px-4 py-1.5 bg-stone-800 hover:bg-stone-900 text-white rounded-xl text-xs font-bold transition flex items-center gap-1 shrink-0">
                    <x-lucide-upload class="w-3.5 h-3.5" />
                    <span>Upload CSV</span>
                </button>
            </form>
            <span class="text-[11px] text-stone-400 italic">Format CSV: NIP/Username, Status, JamDatang (HH:MM), JamPulang (HH:MM)</span>
        </div>
    </div>

    <!-- Attendance Form Table -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6">
        <form wire:submit.prevent="saveAttendance" class="space-y-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-stone-200 text-xs font-bold text-stone-500 uppercase tracking-wider">
                            <th class="pb-3">NIP / ID</th>
                            <th class="pb-3">Nama Karyawan</th>
                            <th class="pb-3">Peran / Jabatan</th>
                            <th class="pb-3 text-center">Status Kehadiran</th>
                            <th class="pb-3 text-center">Jam Datang &amp; Pulang</th>
                            <th class="pb-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-xs">
                        @forelse ($attendanceData as $guruId => $row)
                            <tr class="hover:bg-stone-50/80">
                                <td class="py-3 font-mono font-semibold text-stone-600">{{ $row['nip'] }}</td>
                                <td class="py-3 font-bold text-stone-900">{{ $row['nama'] }}</td>
                                <td class="py-3 text-stone-500">
                                    <span class="px-2 py-0.5 bg-stone-100 rounded border border-stone-200 text-[10px] font-semibold">
                                        {{ $row['role'] }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    <select wire:model="attendanceData.{{ $guruId }}.status" class="px-3 py-1.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs font-bold focus:ring-2 focus:ring-green-500">
                                        <option value="hadir">Hadir (Tepat Waktu)</option>
                                        <option value="telat">Terlambat</option>
                                        <option value="izin">Izin</option>
                                        <option value="sakit">Sakit</option>
                                        <option value="alpa">Alpa / Tidak Hadir</option>
                                    </select>
                                </td>
                                <td class="py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <input type="time" wire:model="attendanceData.{{ $guruId }}.waktu_datang" class="px-2 py-1 bg-stone-50 border border-stone-300 rounded-lg text-stone-800 text-xs text-center w-24" />
                                        <span class="text-stone-400">-</span>
                                        <input type="time" wire:model="attendanceData.{{ $guruId }}.waktu_pulang" class="px-2 py-1 bg-stone-50 border border-stone-300 rounded-lg text-stone-800 text-xs text-center w-24" />
                                    </div>
                                </td>
                                <td class="py-3">
                                    <input type="text" wire:model="attendanceData.{{ $guruId }}.catatan" placeholder="Catatan..." class="w-full px-2.5 py-1 bg-stone-50 border border-stone-300 rounded-lg text-stone-800 text-xs" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-stone-400 font-medium">
                                    Tidak ada data karyawan yang ditemukan pada filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (count($attendanceData) > 0)
                <div class="flex justify-end border-t border-stone-200 pt-4">
                    <button type="submit" class="py-2.5 px-6 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold transition duration-200 shadow-md shadow-green-600/10 flex items-center gap-2">
                        <x-lucide-check-circle class="w-4 h-4" />
                        <span>Simpan Presensi Karyawan</span>
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>
