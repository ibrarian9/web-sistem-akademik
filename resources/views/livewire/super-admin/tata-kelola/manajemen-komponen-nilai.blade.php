<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Komponen & Bobot Nilai</h2>
        <p class="text-xs text-slate-500">Konfigurasi jenis penilaian akademik beserta bobot persentasenya. Pastikan total bobot aktif bernilai 100%.</p>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('warning'))
        <x-alert-banner type="warning" :message="session('warning')" />
    @endif

    <div class="bg-slate-900/40 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6">
        <form wire:submit.prevent="save" class="space-y-6">
            <div class="divide-y divide-slate-800">
                @foreach ($komponens as $index => $komponen)
                    <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex-1 space-y-1">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Komponen #{{ $index + 1 }} ({{ ucfirst($komponen['kategori']) }})</span>
                            <input wire:model="komponens.{{ $index }}.nama" type="text" 
                                class="w-full max-w-md px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 font-semibold" />
                            @error("komponens.{$index}.nama") <span class="text-rose-400 text-[10px] block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Bobot -->
                        <div class="w-48 space-y-1">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Bobot (%)</span>
                            <div class="relative">
                                <input wire:model="komponens.{{ $index }}.bobot" type="number" step="0.1"
                                    class="w-full pr-8 pl-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 text-right font-bold" />
                                <span class="absolute inset-y-0 right-3 flex items-center text-slate-500 text-xs font-semibold pointer-events-none">%</span>
                            </div>
                            @error("komponens.{$index}.bobot") <span class="text-rose-400 text-[10px] block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Action buttons -->
            <div class="flex items-center justify-end border-t border-slate-800 pt-4">
                <button type="submit" class="py-2.5 px-6 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition duration-200 shadow-lg shadow-indigo-600/10">
                    Simpan Perubahan Bobot
                </button>
            </div>
        </form>
    </div>
</div>
