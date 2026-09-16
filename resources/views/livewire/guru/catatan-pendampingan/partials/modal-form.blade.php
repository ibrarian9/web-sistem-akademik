@if ($showFormModal)
    <x-floating-card 
        title="{{ $editingId ? 'Sunting Catatan Pendampingan' : 'Tambah Catatan Pendampingan Baru' }}" 
        badge="GURU PENDAMPING"
        showClose="true"
        onClose="closeFormModal"
    >
        <form wire:submit.prevent="saveRecord" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tanggal Observasi -->
                <x-input 
                    type="date" 
                    label="Tanggal Observasi" 
                    name="form_tanggal" 
                    wire:model="form_tanggal" 
                    required 
                />

                <!-- Periode Evaluasi -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Periode Evaluasi <span class="text-rose-600">*</span></label>
                    <select wire:model="form_periode" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="tengah_semester">Tengah Semester (PTS)</option>
                        <option value="akhir_semester">Akhir Semester (PAS / PAT)</option>
                    </select>
                    @error('form_periode') <span class="text-[11px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Pilih Siswa Berkebutuhan Khusus -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                    Peserta Didik Berkebutuhan Khusus <span class="text-rose-600">*</span>
                </label>
                <select wire:model="form_siswa_id" class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">-- Pilih Siswa Dampingan --</option>
                    @foreach ($allStudents as $st)
                        <option value="{{ $st->id }}">
                            {{ $st->user->nama ?? 'Siswa' }} | Kelas {{ $st->kelas->nama_kelas ?? '-' }} (NIS: {{ $st->nis ?: '-' }})
                        </option>
                    @endforeach
                </select>
                @error('form_siswa_id') <span class="text-[11px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Aspek Pengamatan (7 Pilihan Wajib) -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                    Aspek Pengamatan Perkembangan <span class="text-rose-600">*</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach ($aspekList as $aspItem)
                        <label class="cursor-pointer flex items-center gap-2 p-2.5 rounded-xl border text-xs font-bold transition {{ $form_aspek === $aspItem ? 'bg-emerald-50 border-emerald-500 text-emerald-900 ring-2 ring-emerald-500/20' : 'bg-stone-50 border-stone-200 text-stone-700 hover:bg-stone-100' }}">
                            <input type="radio" wire:model.live="form_aspek" value="{{ $aspItem }}" class="sr-only" />
                            <span class="w-2 h-2 rounded-full {{ $form_aspek === $aspItem ? 'bg-emerald-600' : 'bg-stone-300' }}"></span>
                            <span class="truncate">{{ $aspItem }}</span>
                        </label>
                    @endforeach
                </div>
                @error('form_aspek') <span class="text-[11px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Standarisasi Penilaian Kualitatif (BB, MB, BSH, BSB) - ZERO NUMBER INPUT -->
            <div class="space-y-2 p-4 bg-stone-50 border border-stone-200 rounded-2xl">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-extrabold text-stone-800 uppercase tracking-wider">
                        Hasil Capaian Perkembangan Kualitatif <span class="text-rose-600">*</span>
                    </label>
                    <span class="text-[10px] font-bold text-emerald-800 uppercase bg-emerald-100 px-2 py-0.5 rounded-md">Standarisasi Bebas Angka</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <!-- BB -->
                    <label class="cursor-pointer p-3 rounded-xl border transition flex items-start gap-3 {{ $form_hasil_perkembangan === 'BB' ? 'bg-rose-50 border-rose-500 ring-2 ring-rose-500/20 text-rose-950' : 'bg-white border-stone-200 text-stone-700 hover:bg-stone-50' }}">
                        <input type="radio" wire:model.live="form_hasil_perkembangan" value="BB" class="sr-only" />
                        <span class="px-2 py-0.5 rounded-md font-black text-xs {{ $form_hasil_perkembangan === 'BB' ? 'bg-rose-600 text-white' : 'bg-rose-100 text-rose-800' }}">BB</span>
                        <div>
                            <span class="text-xs font-bold block">Belum Berkembang</span>
                            <span class="text-[10px] text-stone-500 leading-tight block">Memerlukan bantuan dan bimbingan penuh dari pendamping.</span>
                        </div>
                    </label>

                    <!-- MB -->
                    <label class="cursor-pointer p-3 rounded-xl border transition flex items-start gap-3 {{ $form_hasil_perkembangan === 'MB' ? 'bg-amber-50 border-amber-500 ring-2 ring-amber-500/20 text-amber-950' : 'bg-white border-stone-200 text-stone-700 hover:bg-stone-50' }}">
                        <input type="radio" wire:model.live="form_hasil_perkembangan" value="MB" class="sr-only" />
                        <span class="px-2 py-0.5 rounded-md font-black text-xs {{ $form_hasil_perkembangan === 'MB' ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-800' }}">MB</span>
                        <div>
                            <span class="text-xs font-bold block">Mulai Berkembang</span>
                            <span class="text-[10px] text-stone-500 leading-tight block">Mulai tampak inisiatif namun masih perlu diingatkan/diarahkan.</span>
                        </div>
                    </label>

                    <!-- BSH -->
                    <label class="cursor-pointer p-3 rounded-xl border transition flex items-start gap-3 {{ $form_hasil_perkembangan === 'BSH' ? 'bg-emerald-50 border-emerald-500 ring-2 ring-emerald-500/20 text-emerald-950' : 'bg-white border-stone-200 text-stone-700 hover:bg-stone-50' }}">
                        <input type="radio" wire:model.live="form_hasil_perkembangan" value="BSH" class="sr-only" />
                        <span class="px-2 py-0.5 rounded-md font-black text-xs {{ $form_hasil_perkembangan === 'BSH' ? 'bg-emerald-600 text-white' : 'bg-emerald-100 text-emerald-800' }}">BSH</span>
                        <div>
                            <span class="text-xs font-bold block">Berkembang Sesuai Harapan</span>
                            <span class="text-[10px] text-stone-500 leading-tight block">Menunjukkan kemampuan secara konsisten sesuai target.</span>
                        </div>
                    </label>

                    <!-- BSB -->
                    <label class="cursor-pointer p-3 rounded-xl border transition flex items-start gap-3 {{ $form_hasil_perkembangan === 'BSB' ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-500/20 text-blue-950' : 'bg-white border-stone-200 text-stone-700 hover:bg-stone-50' }}">
                        <input type="radio" wire:model.live="form_hasil_perkembangan" value="BSB" class="sr-only" />
                        <span class="px-2 py-0.5 rounded-md font-black text-xs {{ $form_hasil_perkembangan === 'BSB' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-800' }}">BSB</span>
                        <div>
                            <span class="text-xs font-bold block">Berkembang Sangat Baik</span>
                            <span class="text-[10px] text-stone-500 leading-tight block">Menguasai secara mandiri dan dapat memandu teman sebaya.</span>
                        </div>
                    </label>
                </div>
                @error('form_hasil_perkembangan') <span class="text-[11px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Deskripsi / Catatan Pengamatan -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                    Deskripsi / Catatan Pengamatan Guru <span class="text-rose-600">*</span>
                </label>
                <textarea wire:model="form_catatan" rows="3" placeholder="Tuliskan deskripsi objektif perilaku, respon belajar, atau kemajuan ananda pada aspek ini..." class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs"></textarea>
                @error('form_catatan') <span class="text-[11px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Rekomendasi / Tindak Lanjut -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                    Rekomendasi / Rencana Tindak Lanjut (Opsional)
                </label>
                <textarea wire:model="form_rekomendasi" rows="2" placeholder="Contoh: Stimulasi motorik halus melalui latihan menggunting kertas, atau pendampingan interaksi saat jam istirahat..." class="w-full bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs"></textarea>
                @error('form_rekomendasi') <span class="text-[11px] text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
                <x-button type="button" variant="secondary" size="sm" wire:click="closeFormModal">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="sm" icon="check" loadingTarget="saveRecord">
                    Simpan Catatan
                </x-button>
            </div>
        </form>
    </x-floating-card>
@endif
