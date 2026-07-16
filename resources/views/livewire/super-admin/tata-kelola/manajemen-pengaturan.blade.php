<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Pengaturan Sistem</h2>
        <p class="text-xs text-slate-500">Konfigurasi nama sekolah, jam absensi masuk, toleransi keterlambatan, dan preferensi lainnya.</p>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <div class="bg-slate-900/40 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6 max-w-2xl">
        <form wire:submit.prevent="save" class="space-y-5">
            @foreach ($settings as $index => $setting)
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                        {{ str_replace('_', ' ', $setting['key']) }}
                    </label>
                    
                    @if ($setting['key'] === 'alamat_sekolah')
                        <textarea wire:model="settings.{{ $index }}.value" rows="3"
                            class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500"></textarea>
                    @elseif ($setting['key'] === 'jam_masuk')
                        <input wire:model="settings.{{ $index }}.value" type="time"
                            class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
                    @elseif ($setting['key'] === 'toleransi_keterlambatan')
                        <div class="relative">
                            <input wire:model="settings.{{ $index }}.value" type="number"
                                class="w-full pr-16 pl-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
                            <span class="absolute inset-y-0 right-3 flex items-center text-slate-500 text-xs font-semibold pointer-events-none">Menit</span>
                        </div>
                    @else
                        <input wire:model="settings.{{ $index }}.value" type="text"
                            class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
                    @endif
                    
                    @error("settings.{$index}.value") <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                </div>
            @endforeach

            <!-- Save Button -->
            <div class="flex items-center justify-end border-t border-slate-800 pt-4 mt-6">
                <button type="submit" class="py-2.5 px-6 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition duration-200 shadow-lg shadow-indigo-600/10">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
