<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Pengaturan Sekolah & Parameter Sistem" 
        subtitle="Konfigurasi data resmi instansi sekolah/lembaga dan identitas pejabat penandatangan dokumen."
        badge="PENGATURAN RESMI"
        badgeVariant="emerald"
        icon="settings"
    />

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengaturan Resmi Sekolah & Instansi"
        :steps="[
            ['title' => 'Satu Sumber Data', 'desc' => 'Identitas nama, telepon, dan alamat sekolah dikelola terpusat sebagai satu sumber data tunggal untuk seluruh sistem dan cetak laporan.'],
            ['title' => 'Nama Pejabat Resmi', 'desc' => 'Kelola identitas Kepala Sekolah, Bendahara Keuangan, dan Kepala Tata Usaha untuk pengesahan dokumen resmi.'],
            ['title' => 'Verifikasi Otomatis', 'desc' => 'Seluruh dokumen resmi terbit dengan identitas yang sinkron beserta QR Code verifikasi publik.']
        ]"
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <form wire:submit.prevent="save" class="space-y-6 max-w-4xl">
        <!-- 1. Identitas Resmi Sekolah / Lembaga (Satu Sumber Data Tunggal) -->
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-stone-200">
                <div class="flex items-center gap-2 text-stone-900 font-extrabold text-xs uppercase tracking-wider">
                    <x-lucide-building-2 class="w-4 h-4 text-emerald-700" />
                    <span>Identitas Resmi Sekolah / Lembaga (Satu Sumber Data)</span>
                </div>
                <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-lg">
                    Sumber Data Tunggal
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                @foreach ($settings as $index => $setting)
                    @if ($setting['group'] === 'sekolah')
                        <div class="space-y-1.5 {{ $setting['type'] === 'textarea' ? 'md:col-span-2' : '' }}">
                            <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">
                                {{ $setting['keterangan'] }}
                            </label>
                            
                            @if ($setting['type'] === 'textarea')
                                <textarea wire:model="settings.{{ $index }}.value" rows="2"
                                    @if(auth()->user()?->isSuperAdmin2()) readonly disabled @endif
                                    class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none @if(auth()->user()?->isSuperAdmin2()) bg-stone-100 cursor-not-allowed @endif"></textarea>
                            @else
                                <input wire:model="settings.{{ $index }}.value" type="text"
                                    @if(auth()->user()?->isSuperAdmin2()) readonly disabled @endif
                                    class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs @if(auth()->user()?->isSuperAdmin2()) bg-stone-100 cursor-not-allowed @endif" />
                            @endif
                            
                            @error("settings.{$index}.value") <span class="text-rose-600 text-[10px] font-bold block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- 2. Pejabat Resmi Pengesah Dokumen -->
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-stone-200 text-stone-900 font-extrabold text-xs uppercase tracking-wider">
                <x-lucide-user-check class="w-4 h-4 text-emerald-700" />
                <span>Identitas Pejabat Resmi Penandatangan Dokumen</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                @foreach ($settings as $index => $setting)
                    @if ($setting['group'] === 'pejabat')
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">
                                {{ $setting['keterangan'] }}
                            </label>
                            
                            <input wire:model="settings.{{ $index }}.value" type="text"
                                @if(auth()->user()?->isSuperAdmin2()) readonly disabled @endif
                                class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs @if(auth()->user()?->isSuperAdmin2()) bg-stone-100 cursor-not-allowed @endif" />
                            
                            @error("settings.{$index}.value") <span class="text-rose-600 text-[10px] font-bold block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- 3. Konfigurasi Jam Kerja & Presensi -->
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-stone-200 text-stone-900 font-extrabold text-xs uppercase tracking-wider">
                <x-lucide-clock class="w-4 h-4 text-emerald-700" />
                <span>Konfigurasi Jam Kerja & Presensi Standar</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                @foreach ($settings as $index => $setting)
                    @if ($setting['group'] === 'presensi')
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">
                                {{ $setting['keterangan'] }}
                            </label>
                            
                            <input wire:model="settings.{{ $index }}.value" type="text"
                                @if(auth()->user()?->isSuperAdmin2()) readonly disabled @endif
                                class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs @if(auth()->user()?->isSuperAdmin2()) bg-stone-100 cursor-not-allowed @endif" />
                            
                            @error("settings.{$index}.value") <span class="text-rose-600 text-[10px] font-bold block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Save Button Footer -->
        <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="text-xs text-emerald-800 font-bold flex items-center gap-1.5 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
                <x-lucide-shield-check class="w-4 h-4 text-emerald-700 shrink-0" />
                <span>Seluruh dokumen resmi & QR Code otomatis menggunakan sumber data ini</span>
            </div>
            @if (!auth()->user()?->isSuperAdmin2())
                <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="save">
                    Simpan Pengaturan
                </x-button>
            @endif
        </div>
    </form>
</div>
