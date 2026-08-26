<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Data Alumni Lulusan" 
        subtitle="Direktori rekap kelulusan siswa yayasan beserta tahun lulus dan catatan pelacakan studi lanjut."
        badge="DIREKTORI ALUMNI"
        badgeVariant="emerald"
        icon="graduation-cap"
    />

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

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- Content Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Toolbar & Filter -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="max-w-md w-full">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau NIS alumni..." />
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-stone-600">Filter Angkatan:</span>
                <select wire:model.live="filterTahun" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="semua">Semua Tahun Lulus</option>
                    @foreach ($availableYears as $year)
                        <option value="{{ $year }}">Tahun Lulus {{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Data Table -->
        <x-table loadingTarget="search, filterTahun">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="w-36">NIS / NISN</x-table.th>
                    <x-table.th class="min-w-[180px]">Nama Alumni</x-table.th>
                    <x-table.th class="w-28">Jenis Kelamin</x-table.th>
                    <x-table.th align="center" class="w-28">Tahun Lulus</x-table.th>
                    <x-table.th class="min-w-[220px]">Catatan Studi Lanjut / Prestasi</x-table.th>
                    <x-table.th align="center" class="w-32">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($alumnis as $a)
                    <tr class="hover:bg-stone-50 transition">
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
                            <x-badge variant="emerald" size="xs">{{ $a->tahun_lulus ?? '-' }}</x-badge>
                        </td>
                        <td class="p-3.5 border-r border-stone-200 text-stone-700 italic">
                            {{ $a->catatan_alumni ?: '-' }}
                        </td>
                        <td class="p-3.5 text-center">
                            <x-button variant="warning" size="xs" icon="edit" wire:click="editAlumni({{ $a->id }})">
                                Edit Catatan
                            </x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-stone-400">
                            <x-table.empty title="Belum ada data alumni kelulusan terdaftar" subtitle="Data alumni akan otomatis terbentuk saat proses Kenaikan & Kelulusan Kelas dijalankan." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $alumnis->links() }}
        </div>
    </div>

    <!-- Edit Alumni Floating Modal -->
    <x-floating-card 
        :show="$editingSiswaId ? true : false"
        title="Sunting Data Alumni"
        subtitle="Perbarui tahun lulus dan catatan studi lanjut alumni."
        badge="UPDATE ALUMNI"
        badgeVariant="emerald"
        icon="graduation-cap"
        maxWidth="max-w-md"
        closeAction="cancelEdit"
    >
        <form wire:submit.prevent="saveAlumni" class="space-y-4 text-xs">
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Tahun Lulus <span class="text-rose-600">*</span></label>
                <input type="number" wire:model="tahun_lulus" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 text-stone-900 rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Contoh: 2026" required />
                @error('tahun_lulus') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Catatan Alumni / Studi Lanjut</label>
                <textarea wire:model="catatan_alumni" rows="3" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 text-stone-900 rounded-xl text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none" placeholder="Melanjutkan ke SMPN 1 / Pesantren X..."></textarea>
                @error('catatan_alumni') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button variant="secondary" size="md" wire:click="cancelEdit">
                    Batal
                </x-button>
                <x-button variant="primary" size="md" type="submit" loadingTarget="saveAlumni" icon="save">
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
