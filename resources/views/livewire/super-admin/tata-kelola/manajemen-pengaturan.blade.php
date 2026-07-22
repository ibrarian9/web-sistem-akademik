<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengaturan Global & TTD Digital"
        :steps="[
            ['title' => 'Profil Instansi', 'desc' => 'Kelola nama yayasan, logo sekolah, alamat, serta nomor kontak resmi lembaga.'],
            ['title' => 'TTD Digital Pejabat', 'desc' => 'Upload berkas gambar TTD transparan PNG atau buat TTD digital langsung di kanvas web.'],
            ['title' => 'Simpan Preferensi', 'desc' => 'Klik Simpan Pengaturan untuk memberlakukan TTD pada slip gaji, resi, dan rapor siswa.']
        ]"
    />

    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Pengaturan Sistem & TTD Elektronik</h2>
        <p class="text-xs text-slate-500">Konfigurasi data instansi, tanda tangan elektronik resmi (Kepala Sekolah & Bendahara), dan preferensi sistem.</p>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <div class="bg-slate-900/40 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6 max-w-3xl">
        <form wire:submit.prevent="save" class="space-y-6">
            
            <div class="space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-800 text-indigo-400 font-bold text-xs uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Daftar Pengaturan Sistem
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($settings as $index => $setting)
                        <div class="space-y-1.5 {{ in_array($setting['key'], ['alamat_instansi', 'alamat_sekolah']) ? 'md:col-span-2' : '' }}">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                {{ $setting['keterangan'] ?: str_replace('_', ' ', $setting['key']) }}
                            </label>
                            
                            @if (in_array($setting['key'], ['alamat_instansi', 'alamat_sekolah']))
                                <textarea wire:model="settings.{{ $index }}.value" rows="2"
                                    class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500"></textarea>
                            @else
                                <input wire:model="settings.{{ $index }}.value" type="text"
                                    class="w-full px-3 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500" />
                            @endif
                            
                            @error("settings.{$index}.value") <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex items-center justify-between border-t border-slate-800 pt-4 mt-6">
                <div class="text-[11px] text-emerald-400 font-medium flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    TTD Elektronik & QR Code Verifikasi Aktif Otomatis di Seluruh Dokumen
                </div>
                <button type="submit" class="py-2.5 px-6 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition duration-200 shadow-lg shadow-indigo-600/10">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
