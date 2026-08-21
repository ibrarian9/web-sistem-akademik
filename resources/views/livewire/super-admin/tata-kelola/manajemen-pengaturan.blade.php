<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Pengaturan Global Sistem &amp; Dokumen" 
        subtitle="Konfigurasi data instansi yayasan dan identitas pejabat resmi pengesah dokumen."
        badge="PENGATURAN SISTEM"
        badgeVariant="emerald"
        icon="settings"
    />

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengaturan Global Instansi"
        :steps="[
            ['title' => 'Profil Lembaga', 'desc' => 'Kelola nama yayasan, logo sekolah, alamat resmi, serta nomor telepon instansi.'],
            ['title' => 'Nama Pejabat Resmi', 'desc' => 'Kelola nama dan NIP/NIY Kepala Sekolah, Bendahara Keuangan, serta Kepala Tata Usaha.'],
            ['title' => 'Verifikasi Otomatis', 'desc' => 'Seluruh dokumen PDF yang dicetak akan terbit dengan QR Code Verifikasi Publik resmi.']
        ]"
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-6 max-w-4xl">
        <form wire:submit.prevent="save" class="space-y-6">
            <div class="space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-stone-200 text-stone-900 font-extrabold text-xs uppercase tracking-wider">
                    <x-lucide-building class="w-4 h-4 text-emerald-700" />
                    <span>Daftar Parameter Konfigurasi Instansi</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    @foreach ($settings as $index => $setting)
                        <div class="space-y-1.5 {{ in_array($setting['key'], ['alamat_instansi', 'alamat_sekolah']) ? 'md:col-span-2' : '' }}">
                            <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">
                                {{ $setting['keterangan'] ?: str_replace('_', ' ', $setting['key']) }}
                            </label>
                            
                            @if (in_array($setting['key'], ['alamat_instansi', 'alamat_sekolah']))
                                <textarea wire:model="settings.{{ $index }}.value" rows="2"
                                    class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none"></textarea>
                            @else
                                <input wire:model="settings.{{ $index }}.value" type="text"
                                    class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                            @endif
                            
                            @error("settings.{$index}.value") <span class="text-rose-600 text-[10px] font-bold block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-t border-stone-200 pt-4 gap-3">
                <div class="text-xs text-emerald-800 font-bold flex items-center gap-1.5 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
                    <x-lucide-shield-check class="w-4 h-4 text-emerald-700 shrink-0" />
                    <span>QR Code Verifikasi Publik Aktif Otomatis di Seluruh Dokumen</span>
                </div>
                <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="save">
                    Simpan Pengaturan
                </x-button>
            </div>
        </form>
    </div>
</div>
