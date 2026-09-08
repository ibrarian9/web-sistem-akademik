<div class="space-y-6 font-sans">
    <!-- Header Page -->
    <x-page-header 
        title="Ekstrakurikuler & Pengembangan Minat Bakat" 
        subtitle="Kelola dan pantau kegiatan ekstrakurikuler santri serta berikan penilaian capaian akhir semester."
        badge="EKSTRAKURIKULER"
        badgeVariant="amber"
        icon="star"
    />

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Ekstrakurikuler Guru"
        :steps="[
            ['title' => 'Pilih Program Ekskul', 'desc' => 'Pilih kegiatan ekstrakurikuler pada tab pilihan untuk melihat data pembina dan daftar anggota santri.'],
            ['title' => 'Penilaian Anggota', 'desc' => 'Sebagai Guru Pembina, Anda dapat memberikan predikat (A/B/C/D) dan catatan evaluasi per santri.'],
            ['title' => 'Katalog Program', 'desc' => 'Katalog di bawah memuat seluruh ragam ekstrakurikuler aktif yang selaras dengan dasbor wali murid.']
        ]"
    />

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
            <x-lucide-check-circle class="w-4 h-4 text-emerald-600 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Selector / Tabs of Active Extracurriculars -->
    <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-stone-200 pb-3">
            <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                <x-lucide-star class="w-4 h-4 text-amber-500" />
                <span>Pilih Kegiatan Ekstrakurikuler</span>
            </h3>
            <span class="text-xs text-stone-500 font-semibold">{{ count($catalogEkskuls) }} Program Terdaftar</span>
        </div>

        <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar">
            @foreach ($catalogEkskuls as $ekskul)
                @php
                    $isSelected = ($selectedEkskulId === $ekskul->id);
                    $isMyEkskul = $myEkskuls->contains('id', $ekskul->id);
                @endphp
                <button 
                    type="button" 
                    wire:click="selectEkskul({{ $ekskul->id }})"
                    class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition flex items-center gap-2 shrink-0 border cursor-pointer
                    {{ $isSelected 
                        ? 'bg-amber-600 text-white border-amber-600 shadow-sm' 
                        : 'bg-stone-50 hover:bg-stone-100 text-stone-700 border-stone-200' }}"
                >
                    <span>{{ $ekskul->nama }}</span>
                    @if ($isMyEkskul)
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black {{ $isSelected ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">Binaan Saya</span>
                    @endif
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono {{ $isSelected ? 'bg-amber-700 text-amber-100' : 'bg-stone-200 text-stone-600' }}">
                        {{ $ekskul->siswaEkskul->count() }}
                    </span>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Active Ekskul Detail & Roster -->
    @if ($selectedEkskul)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Info Card -->
            <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-4">
                <div class="flex items-start justify-between gap-2 border-b border-stone-100 pb-3">
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                            DETAIL KEGIATAN
                        </span>
                        <h4 class="text-base font-extrabold text-stone-900 mt-1">{{ $selectedEkskul->nama }}</h4>
                    </div>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="p-3 bg-stone-50 rounded-xl border border-stone-200">
                        <span class="text-[10px] text-stone-400 font-bold uppercase block">Guru Pembina</span>
                        <span class="font-extrabold text-stone-800">{{ $selectedEkskul->pembina->user->nama ?? 'Belum Ditentukan' }}</span>
                    </div>

                    <div class="p-3 bg-stone-50 rounded-xl border border-stone-200">
                        <span class="text-[10px] text-stone-400 font-bold uppercase block">Total Santri Terdaftar</span>
                        <span class="font-extrabold text-stone-800">{{ $roster->count() }} Santri</span>
                    </div>

                    <div class="p-3 bg-stone-50 rounded-xl border border-stone-200">
                        <span class="text-[10px] text-stone-400 font-bold uppercase block">Deskripsi Kegiatan</span>
                        <p class="text-stone-700 font-medium mt-1 leading-relaxed">{{ $selectedEkskul->deskripsi ?: 'Tidak ada deskripsi kegiatan khusus.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Right: Student Roster & Grading -->
            <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-stone-100 pb-3">
                    <div class="flex items-center gap-2">
                        <x-lucide-users class="w-4 h-4 text-emerald-600" />
                        <h4 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider">Daftar Santri & Penilaian Capaian</h4>
                    </div>
                    <span class="text-[11px] text-stone-500 font-semibold">{{ $roster->count() }} Santri Aktif</span>
                </div>

                <div class="divide-y divide-stone-100 border border-stone-200 rounded-xl overflow-hidden bg-white">
                    @forelse ($roster as $member)
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-stone-50/70 transition">
                            <div class="space-y-1 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-extrabold text-stone-900">{{ $member->siswa->user->nama ?? $member->siswa->nama_panggilan }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-stone-100 text-stone-600 border border-stone-200">
                                        Kelas {{ $member->siswa->kelas->nama_kelas ?? '-' }}
                                    </span>
                                </div>
                                <span class="text-[10px] text-stone-400 font-mono block">NISN: {{ $member->siswa->nisn ?? '-' }}</span>
                            </div>

                            <!-- Grade & Note Input Controls -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full sm:w-auto">
                                <select 
                                    wire:model.defer="predikatInputs.{{ $member->id }}"
                                    class="w-20 px-2 py-1.5 bg-white border border-stone-300 rounded-lg text-xs font-black text-stone-900 focus:ring-2 focus:ring-amber-500 shadow-2xs"
                                >
                                    <option value="A">A (Sangat Baik)</option>
                                    <option value="B">B (Baik)</option>
                                    <option value="C">C (Cukup)</option>
                                    <option value="D">D (Kurang)</option>
                                </select>

                                <input 
                                    type="text" 
                                    wire:model.defer="catatanInputs.{{ $member->id }}"
                                    placeholder="Catatan capaian..." 
                                    class="w-full sm:w-48 px-2.5 py-1.5 bg-white border border-stone-300 rounded-lg text-xs text-stone-800 focus:ring-2 focus:ring-amber-500 shadow-2xs"
                                />

                                <x-button 
                                    type="button" 
                                    variant="primary" 
                                    size="xs" 
                                    icon="save" 
                                    wire:click="saveScore({{ $member->id }})"
                                >
                                    Simpan
                                </x-button>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-stone-400 text-xs font-medium">
                            Belum ada santri yang terdaftar pada kegiatan ekstrakurikuler ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
