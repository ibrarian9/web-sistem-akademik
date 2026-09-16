@if($showScoreModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center lg:pl-64 p-3 sm:p-4 lg:p-8 bg-stone-900/60 backdrop-blur-xs overflow-y-auto">
        <div class="bg-white border border-stone-200 rounded-3xl p-5 sm:p-6 shadow-2xl max-w-2xl w-full my-auto max-h-[92vh] flex flex-col space-y-4">
            <div class="flex items-center justify-between border-b border-stone-200 pb-3 shrink-0">
                <h3 class="text-sm font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-900 text-xs flex items-center justify-center font-black">
                        <svg class="w-3.5 h-3.5 text-emerald-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </span>
                    <span>{{ $editingId ? 'Edit Mutaba\'ah Santri' : 'Input Mutaba\'ah Santri (SD TAHFIZH F3)' }}</span>
                </h3>
                <button type="button" wire:click.prevent="closeScoreModal" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 font-bold cursor-pointer">✕</button>
            </div>

            <form wire:submit.prevent="saveScore" class="space-y-4 overflow-y-auto pr-1 flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase mb-1">Pilih Santri Target <span class="text-rose-600">*</span></label>
                        <select wire:model="siswa_id" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-xs">
                            <option value="">-- Pilih Santri --</option>
                            @foreach($siswas as $s)
                                <option value="{{ $s->id }}">{{ strtoupper($s->user->nama ?? $s->nama_panggilan) }} (NISN: {{ $s->nisn }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase mb-1">Tanggal Setoran <span class="text-rose-600">*</span></label>
                        <input type="date" wire:model="tanggal" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-xs">
                    </div>
                </div>

                <!-- Grid Mutaba'ah 4 Kategori -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                    <!-- TAHSIN -->
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-emerald-950 uppercase block">1. Tahsin</span>
                            <span class="text-[9px] font-black uppercase text-emerald-700 bg-emerald-200/70 px-1.5 py-0.5 rounded">Metode Aisar / Tilawah</span>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-stone-600 block mb-1">Materi / Surah / Program Aisar</label>
                            <x-surah-autocomplete wireModel="materi_tahsin" placeholder="Ketik Aisar 1 / Al-Baqarah 1-5..." />
                            
                            <!-- Quick Select Aisar Buttons -->
                            <div class="flex flex-wrap items-center gap-1 mt-1.5">
                                <span class="text-[9px] font-bold text-stone-500">Pilih Cepat:</span>
                                <button type="button" wire:click="$set('materi_tahsin', 'Aisar Jilid 1')" class="px-2 py-0.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-900 rounded text-[10px] font-bold transition">Aisar 1</button>
                                <button type="button" wire:click="$set('materi_tahsin', 'Aisar Jilid 2')" class="px-2 py-0.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-900 rounded text-[10px] font-bold transition">Aisar 2</button>
                                <button type="button" wire:click="$set('materi_tahsin', 'Aisar Jilid 3')" class="px-2 py-0.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-900 rounded text-[10px] font-bold transition">Aisar 3</button>
                                <button type="button" wire:click="$set('materi_tahsin', 'Aisar Jilid 4')" class="px-2 py-0.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-900 rounded text-[10px] font-bold transition">Aisar 4</button>
                                <button type="button" wire:click="$set('materi_tahsin', 'Kitab Aisar')" class="px-2 py-0.5 bg-emerald-200 hover:bg-emerald-300 text-emerald-950 rounded text-[10px] font-black transition">Kitab Aisar</button>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-stone-600 block mb-1">Nilai Tahsin (0-100)</label>
                            <input type="number" step="1" wire:model="nilai_tahsin" placeholder="0-100 (opsional)" class="w-full bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-xs font-bold text-emerald-900 focus:ring-2 focus:ring-emerald-600 shadow-xs">
                        </div>
                    </div>

                    <!-- MURAJA'AH -->
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-2">
                        <span class="text-xs font-black text-emerald-950 uppercase block">2. Muraja'ah</span>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-[10px] font-bold text-stone-600 block mb-1">Bersama</label>
                                <x-surah-autocomplete wireModel="murajaah_bersama" :includeJuz="true" placeholder="Juz 30 / Al-Baqarah" />
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-stone-600 block mb-1">Mandiri</label>
                                <x-surah-autocomplete wireModel="murajaah_mandiri" placeholder="Al-Baqarah 1-30" />
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-stone-600 block mb-1">Nilai Muraja'ah (0-100)</label>
                            <input type="number" step="1" wire:model="nilai_murajaah" placeholder="0-100 (opsional)" class="w-full bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-xs font-bold text-emerald-900 focus:ring-2 focus:ring-emerald-600 shadow-xs">
                        </div>
                    </div>

                    <!-- KITABAH -->
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-2">
                        <span class="text-xs font-black text-emerald-950 uppercase block">3. Tahfizh - Kitabah</span>
                        <div>
                            <label class="text-[10px] font-bold text-stone-600 block mb-1">Materi Kitabah</label>
                            <x-surah-autocomplete wireModel="materi_kitabah" placeholder="Ketik surah, contoh: Al-Baqarah 39-40" />
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-stone-600 block mb-1">Nilai Kitabah (0-100)</label>
                            <input type="number" step="1" wire:model="nilai_kitabah" placeholder="0-100 (opsional)" class="w-full bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-xs font-bold text-emerald-900 focus:ring-2 focus:ring-emerald-600 shadow-xs">
                        </div>
                    </div>

                    <!-- ZIYADAH -->
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-2">
                        <span class="text-xs font-black text-emerald-950 uppercase block">4. Tahfizh - Ziyadah</span>
                        <div>
                            <label class="text-[10px] font-bold text-stone-600 block mb-1">Materi Ziyadah Baru</label>
                            <x-surah-autocomplete wireModel="materi_ziyadah" placeholder="Ketik surah, contoh: Al-Baqarah 11-20" />
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-stone-600 block mb-1">Nilai Ziyadah (0-100)</label>
                            <input type="number" step="1" wire:model="nilai_ziyadah" placeholder="0-100 (opsional)" class="w-full bg-white border border-stone-300 rounded-lg px-2.5 py-1.5 text-xs font-bold text-emerald-900 focus:ring-2 focus:ring-emerald-600 shadow-xs">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase mb-1">Catatan Ustadz Pembimbing</label>
                    <textarea wire:model="catatan_ustadz" rows="2" placeholder="Tuliskan catatan perkembangan hafalan dan motivasi santri..." class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2 text-stone-900 text-xs font-medium resize-none focus:ring-2 focus:ring-emerald-600 shadow-xs"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200 sticky bottom-0 bg-white z-10">
                    <x-button variant="secondary" size="md" wire:click.prevent="closeScoreModal">
                        Batal
                    </x-button>
                    <x-button variant="primary" size="md" type="submit" loadingTarget="saveScore">
                        Simpan Mutaba'ah
                    </x-button>
                </div>
            </form>
        </div>
    </div>
@endif
