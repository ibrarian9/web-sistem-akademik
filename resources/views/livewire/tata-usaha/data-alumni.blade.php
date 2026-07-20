<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight">Data Alumni Lulusan</h2>
            <p class="text-xs text-stone-500">Direktori rekap kelulusan siswa yayasan beserta tahun lulus dan catatan pelacakan studi lanjut.</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama / NIS..." 
                    class="bg-stone-50 border border-stone-300 text-stone-800 placeholder-stone-400 rounded-xl pl-9 pr-4 py-2 text-xs focus:outline-none focus:border-indigo-500 w-64" />
                <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" />
            </div>

            <select wire:model.live="filterTahun" class="bg-stone-50 border border-stone-300 text-stone-800 rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:border-indigo-500">
                <option value="semua">Semua Tahun Lulus</option>
                @foreach ($availableYears as $year)
                    <option value="{{ $year }}">Tahun {{ $year }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-xs font-bold flex items-center gap-2">
            <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-stone-200 text-stone-500 text-xs uppercase tracking-wider">
                    <th class="pb-3 font-bold">NIS / NISN</th>
                    <th class="pb-3 font-bold">Nama Alumni</th>
                    <th class="pb-3 font-bold">Jenis Kelamin</th>
                    <th class="pb-3 font-bold text-center">Tahun Lulus</th>
                    <th class="pb-3 font-bold">Catatan Studi Lanjut / Prestasi</th>
                    <th class="pb-3 font-bold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100 text-xs">
                @forelse ($alumnis as $a)
                    <tr class="hover:bg-stone-50/50">
                        <td class="py-3.5 font-mono text-indigo-600 font-semibold">{{ $a->nis }} / {{ $a->nisn ?? '-' }}</td>
                        <td class="py-3.5 text-stone-800 font-bold">{{ $a->user->nama ?? '-' }}</td>
                        <td class="py-3.5 text-stone-600">{{ $a->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        <td class="py-3.5 text-center font-black text-emerald-700 bg-emerald-50 rounded-lg">{{ $a->tahun_lulus ?? '-' }}</td>
                        <td class="py-3.5 text-stone-600 italic max-w-xs">{{ $a->catatan_alumni ?: '-' }}</td>
                        <td class="py-3.5 text-center">
                            <button wire:click="editAlumni({{ $a->id }})" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white border border-indigo-200 rounded-xl font-bold transition">
                                Edit Catatan
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-stone-400 text-xs">
                            Belum ada data alumni kelulusan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $alumnis->links() }}
        </div>
    </div>

    @if ($editingSiswaId)
        <!-- EDIT ALUMNI MODAL -->
        <div class="fixed inset-0 bg-stone-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white border border-stone-200 rounded-2xl p-6 max-w-md w-full space-y-4 shadow-xl">
                <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider">Sunting Data Alumni</h3>
                
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block text-stone-600 font-semibold mb-1">Tahun Lulus</label>
                        <input type="number" wire:model="tahun_lulus" class="w-full bg-stone-50 border border-stone-300 text-stone-800 rounded-xl p-2.5 focus:outline-none focus:border-indigo-500 font-bold" placeholder="Contoh: 2026" />
                    </div>

                    <div>
                        <label class="block text-stone-600 font-semibold mb-1">Catatan Alumni / Studi Lanjut</label>
                        <textarea wire:model="catatan_alumni" rows="3" class="w-full bg-stone-50 border border-stone-300 text-stone-800 rounded-xl p-2.5 focus:outline-none focus:border-indigo-500" placeholder="Melanjutkan ke SMPN 1 / Pesantren X..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-stone-100">
                    <button wire:click="cancelEdit" class="px-4 py-2 bg-stone-100 text-stone-700 hover:bg-stone-200 rounded-xl text-xs font-bold transition">
                        Batal
                    </button>
                    <button wire:click="saveAlumni" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
