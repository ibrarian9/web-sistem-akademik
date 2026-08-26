<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Input Absensi Karyawan & Guru" 
        subtitle="Pencatatan presensi terpusat oleh Tata Usaha untuk seluruh staf pendidik & tenaga kependidikan."
        badge="PRESENSI KARYAWAN & GURU"
        badgeVariant="emerald"
        icon="user-check"
    />

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

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Control & Filter Bar -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <!-- Tanggal -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Tanggal Presensi</label>
                <input type="date" wire:model.live="tanggal" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
            </div>

            <!-- Filter Role -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Kategori Role / Staf</label>
                <select wire:model.live="filterRole" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="semua">Semua Karyawan</option>
                    <option value="guru">Guru (Pendidik)</option>
                    <option value="tata_usaha">Tata Usaha</option>
                    <option value="finance">Bendahara / Finance</option>
                    <option value="kepala_sekolah">Kepala Sekolah</option>
                </select>
            </div>

            <!-- Search -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Cari Nama / NIP</label>
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama / NIP..." />
            </div>

            <!-- Set Status Masal -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block">Set Status Masal</label>
                <div class="flex items-center gap-2">
                    <x-button type="button" variant="outline" size="sm" icon="check" wire:click="setStatusAll('hadir')" title="Ubah status seluruh staf di daftar menjadi Hadir">
                        Semua Hadir
                    </x-button>
                    <x-button type="button" variant="warning" size="sm" icon="clock" wire:click="setStatusAll('izin')" title="Ubah status seluruh staf di daftar menjadi Izin">
                        Semua Izin
                    </x-button>
                </div>
            </div>
        </div>

        <!-- Upload CSV Box -->
        <div class="border-t border-stone-100 pt-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <form wire:submit.prevent="uploadCsv" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <span class="text-xs font-bold text-stone-700 shrink-0">Unggah CSV Presensi:</span>
                <input type="file" wire:model="csvFile" accept=".csv,.txt" class="text-xs text-stone-500 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-xl file:border file:border-stone-300 file:text-xs file:font-bold file:bg-stone-50 file:text-stone-700 hover:file:bg-stone-100 cursor-pointer" />
                <x-button type="submit" variant="secondary" size="sm" icon="upload" loadingTarget="uploadCsv">
                    Upload CSV
                </x-button>
            </form>
            <span class="text-[11px] text-stone-400 italic">Format CSV: NIP/Username, Status, JamDatang (HH:MM), JamPulang (HH:MM)</span>
        </div>
    </div>

    <!-- Attendance Form Table Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-6">
        <form wire:submit.prevent="saveAttendance" class="space-y-6">
            <x-table loadingTarget="saveAttendance">
                <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                    <tr>
                        <x-table.th class="w-36">NIP / ID</x-table.th>
                        <x-table.th class="min-w-[180px]">Nama Karyawan</x-table.th>
                        <x-table.th class="w-36">Peran / Jabatan</x-table.th>
                        <x-table.th align="center" class="w-44">Status Kehadiran</x-table.th>
                        <x-table.th align="center" class="w-52">Jam Datang & Pulang</x-table.th>
                        <x-table.th class="min-w-[200px]">Catatan</x-table.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white text-xs">
                    @forelse ($attendanceData as $guruId => $row)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="p-3.5 font-mono font-bold text-stone-600 border-r border-stone-200">{{ $row['nip'] }}</td>
                            <td class="p-3.5 font-extrabold text-stone-900 border-r border-stone-200">{{ $row['nama'] }}</td>
                            <td class="p-3.5 text-stone-500 border-r border-stone-200">
                                <x-badge variant="stone" size="xs">{{ strtoupper(str_replace('_', ' ', $row['role'])) }}</x-badge>
                            </td>
                            <td class="p-3.5 text-center border-r border-stone-200">
                                <select wire:model="attendanceData.{{ $guruId }}.status" class="px-3 py-1.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                                    <option value="hadir">Hadir (Tepat Waktu)</option>
                                    <option value="telat">Terlambat</option>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="alpa">Alpa / Tidak Hadir</option>
                                </select>
                            </td>
                            <td class="p-3.5 text-center border-r border-stone-200">
                                <div class="flex items-center justify-center gap-1.5">
                                    <input type="time" wire:model="attendanceData.{{ $guruId }}.waktu_datang" class="px-2 py-1 bg-white border border-stone-300 rounded-lg text-stone-800 text-xs font-bold text-center w-24 focus:ring-2 focus:ring-emerald-600" />
                                    <span class="text-stone-400 font-bold">-</span>
                                    <input type="time" wire:model="attendanceData.{{ $guruId }}.waktu_pulang" class="px-2 py-1 bg-white border border-stone-300 rounded-lg text-stone-800 text-xs font-bold text-center w-24 focus:ring-2 focus:ring-emerald-600" />
                                </div>
                            </td>
                            <td class="p-3.5">
                                <input type="text" wire:model="attendanceData.{{ $guruId }}.catatan" placeholder="Catatan opsional..." class="w-full px-3 py-1.5 bg-white border border-stone-300 rounded-lg text-stone-800 text-xs focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-stone-400">
                                <x-table.empty title="Tidak ada data karyawan ditemukan" subtitle="Gunakan filter kategori role atau kotak pencarian di atas." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-table>

            @if (count($attendanceData) > 0)
                <div class="flex justify-end border-t border-stone-200 pt-4">
                    <x-button type="submit" variant="primary" size="md" icon="check-circle" loadingTarget="saveAttendance">
                        Simpan Presensi Karyawan
                    </x-button>
                </div>
            @endif
        </form>
    </div>
</div>
