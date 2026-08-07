<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Direktori Alumni & Kelulusan"
        :steps="[
            ['title' => 'Filter Tahun Lulus', 'desc' => 'Gunakan dropdown filter tahun lulus untuk memilah daftar angkatan alumni sekolah.'],
            ['title' => 'Pencarian Alumni', 'desc' => 'Ketik nama alumni atau NIS pada kolom pencarian untuk melacak rekap lulusan.'],
            ['title' => 'Edit Studi Lanjut', 'desc' => 'Klik tombol Edit Catatan untuk menginput sekolah tujuan / pesantren kelanjutan studi alumni.']
        ]"
        notes="Data alumni secara otomatis terbentuk ketika proses Kenaikan & Kelulusan Kelas dijalankan oleh Tata Usaha."
    />

    <!-- Hero Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                DIREKTORI ALUMNI
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Data Alumni Lulusan</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Direktori rekap kelulusan siswa yayasan beserta tahun lulus dan catatan pelacakan studi lanjut.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- Content Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <!-- Toolbar & Filter -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="relative max-w-md w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400">
                    <x-lucide-search class="w-4 h-4" />
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau NIS alumni..."
                    class="w-full pl-9 pr-4 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 placeholder-stone-400 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-xs" />
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-stone-600">Filter Angkatan:</span>
                <select wire:model.live="filterTahun" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-1.5 focus:ring-2 focus:ring-emerald-600 shadow-xs">
                    <option value="semua">Semua Tahun Lulus</option>
                    @foreach ($availableYears as $year)
                        <option value="{{ $year }}">Tahun Lulus {{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs text-stone-800">
                <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                    <tr>
                        <th class="p-3.5 border-r border-emerald-700 w-32">NIS / NISN</th>
                        <th class="p-3.5 border-r border-emerald-700 min-w-[180px]">Nama Alumni</th>
                        <th class="p-3.5 border-r border-emerald-700 w-28">Jenis Kelamin</th>
                        <th class="p-3.5 border-r border-emerald-700 w-28 text-center">Tahun Lulus</th>
                        <th class="p-3.5 border-r border-emerald-700 min-w-[220px]">Catatan Studi Lanjut / Prestasi</th>
                        <th class="p-3.5 text-center min-w-[120px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse ($alumnis as $a)
                        <tr class="hover:bg-emerald-50/50 transition">
                            <td class="p-3.5 font-semibold text-stone-600 border-r border-stone-200">
                                <div class="font-bold text-stone-900">{{ $a->nis }}</div>
                                <div class="text-[10px] text-stone-500">NISN: {{ $a->nisn ?: '-' }}</div>
                            </td>
                            <td class="p-3.5 border-r border-stone-200 font-extrabold text-stone-900 text-xs">
                                {{ strtoupper($a->user->nama ?? '-') }}
                            </td>
                            <td class="p-3.5 border-r border-stone-200 font-semibold text-stone-600">
                                {{ $a->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </td>
                            <td class="p-3.5 text-center border-r border-stone-200">
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-full font-black text-xs inline-block">
                                    {{ $a->tahun_lulus ?? '-' }}
                                </span>
                            </td>
                            <td class="p-3.5 border-r border-stone-200 text-stone-700 italic">
                                {{ $a->catatan_alumni ?: '-' }}
                            </td>
                            <td class="p-3.5 text-center">
                                <button wire:click="editAlumni({{ $a->id }})" class="px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-900 rounded-lg font-bold text-xs border border-amber-300 transition shadow-xs inline-flex items-center gap-1">
                                    <x-lucide-edit class="w-3.5 h-3.5 text-amber-700" />
                                    <span>Edit Catatan</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-stone-500 font-semibold italic">
                                Belum ada data alumni kelulusan terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            {{ $alumnis->links() }}
        </div>
    </div>

    <!-- Edit Alumni Modal -->
    @if ($editingSiswaId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/60 backdrop-blur-xs p-4">
            <div class="bg-white border border-stone-200 rounded-3xl p-6 max-w-md w-full space-y-4 shadow-2xl">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <h3 class="text-sm font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-950 text-xs flex items-center justify-center font-black">★</span>
                        <span>Sunting Data Alumni</span>
                    </h3>
                    <button wire:click="cancelEdit" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 font-bold">✕</button>
                </div>
                
                <div class="space-y-4 text-xs">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Tahun Lulus <span class="text-rose-600">*</span></label>
                        <input type="number" wire:model="tahun_lulus" class="w-full px-3.5 py-2 bg-white border border-stone-300 text-stone-900 rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="Contoh: 2026" />
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Catatan Alumni / Studi Lanjut</label>
                        <textarea wire:model="catatan_alumni" rows="3" class="w-full px-3.5 py-2 bg-white border border-stone-300 text-stone-900 rounded-xl text-xs font-medium focus:ring-2 focus:ring-emerald-600 resize-none" placeholder="Melanjutkan ke SMPN 1 / Pesantren X..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-stone-200">
                    <button wire:click="cancelEdit" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-bold">
                        Batal
                    </button>
                    <button wire:click="saveAlumni" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
