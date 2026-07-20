<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight">Pengaturan Bobot Penilaian Guru</h2>
            <p class="text-xs text-stone-500">Tentukan sendiri porsi persentase bobot tiap komponen nilai (UH, UTS, UAS, dll.) untuk kelas &amp; mata pelajaran yang Anda ampu.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-xs font-bold flex items-center gap-2">
            <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl text-xs font-bold flex items-center gap-2">
            <x-lucide-alert-triangle class="w-4 h-4 text-amber-600" />
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6">
        <!-- Class & Subject Selection -->
        <div class="max-w-md space-y-1.5">
            <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Pilih Penugasan Mengajar (Kelas &amp; Mapel)</label>
            <select wire:model.live="selectedGmkId" class="w-full bg-stone-50 border border-stone-300 text-stone-800 rounded-xl px-3 py-2.5 text-xs font-bold focus:outline-none focus:border-indigo-500">
                @forelse ($assignments as $a)
                    <option value="{{ $a['id'] }}">
                        Kelas {{ $a['kelas']['nama_kelas'] ?? '-' }} — {{ $a['mapel']['nama_mapel'] ?? '-' }} ({{ strtoupper($a['mapel']['jenis'] ?? 'UMUM') }})
                    </option>
                @empty
                    <option value="">-- Belum ada penugasan mengajar --</option>
                @endforelse
            </select>
        </div>

        @if ($selectedGmkId)
            <form wire:submit.prevent="saveBobot" class="space-y-6 pt-4 border-t border-stone-200">
                <div class="space-y-3">
                    <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider">Persentase Bobot Per Komponen Nilai</h3>
                    <p class="text-xs text-stone-500">Nilai akhir murid dihitung berdasarkan persentase bobot yang Anda tetapkan di bawah ini.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($komponens as $k)
                        <div class="bg-stone-50 border border-stone-200 rounded-2xl p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-stone-800">{{ $k['nama'] }}</span>
                                <span class="px-2 py-0.5 bg-stone-200 text-stone-700 rounded text-[10px] font-mono font-bold">{{ strtoupper($k['kategori'] ?? 'NILAI') }}</span>
                            </div>
                            <div class="relative">
                                <input type="number" step="0.1" min="0" max="100" wire:model="bobotInputs.{{ $k['id'] }}" 
                                    class="w-full bg-white border border-stone-300 text-stone-900 rounded-xl px-3 py-2 text-xs font-bold focus:outline-none focus:border-indigo-500 pr-8" placeholder="0" />
                                <span class="absolute right-3 top-2 text-xs font-bold text-stone-400">%</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-stone-200">
                    <div class="text-xs text-stone-500 flex items-center gap-2">
                        <span>Total Akumulasi Bobot:</span>
                        @php $totalCurrent = array_sum(array_map('floatval', $bobotInputs)); @endphp
                        <span class="px-3 py-1 rounded-xl text-xs font-bold font-mono border {{ abs($totalCurrent - 100) < 0.01 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200' }}">
                            {{ $totalCurrent }}% {{ abs($totalCurrent - 100) < 0.01 ? '(Sempurna 100%)' : '(Disarankan 100%)' }}
                        </span>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-md flex items-center gap-2">
                        <x-lucide-save class="w-4 h-4" />
                        <span>Simpan Bobot Penilaian</span>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
