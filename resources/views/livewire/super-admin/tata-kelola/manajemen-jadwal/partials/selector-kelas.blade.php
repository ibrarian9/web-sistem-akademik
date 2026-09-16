<!-- Selector Kelas Dropdown -->
<div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="space-y-0.5">
        <div class="flex items-center gap-2 text-xs font-extrabold text-stone-800 uppercase tracking-wider">
            <x-lucide-school class="w-4 h-4 text-emerald-700" />
            <span>Pilih Kelas / Rombel</span>
        </div>
        <p class="text-xs text-stone-500 font-medium">Pilih rombongan belajar untuk menampilkan matriks jadwal pelajaran mingguan.</p>
    </div>

    <div class="w-full sm:w-80">
        <select wire:model.live="selectedKelasId" 
                class="w-full bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-extrabold px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-600 focus:bg-white transition shadow-2xs">
            @foreach ($kelases as $k)
                <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }} (Tingkat {{ $k->tingkat }})</option>
            @endforeach
        </select>
    </div>
</div>
