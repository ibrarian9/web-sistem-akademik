<!-- Student Detail Floating Modal -->
<x-floating-card 
    :show="($showDetailModal && $selectedSiswaDetail) ? true : false"
    :title="$selectedSiswaDetail ? ('Biodata: ' . strtoupper($selectedSiswaDetail->user?->nama ?? '-')) : 'Detail Siswa'"
    :subtitle="$selectedSiswaDetail ? ('NIS: ' . ($selectedSiswaDetail->nis ?? '-') . ' | NISN: ' . ($selectedSiswaDetail->nisn ?: '-')) : ''"
    badge="PROFIL SISWA"
    badgeVariant="emerald"
    icon="user-check"
    maxWidth="max-w-3xl"
    closeAction="closeDetail"
>
    @if ($selectedSiswaDetail)
        <div class="space-y-4 text-xs">
            <!-- Tri-Card: Wali Kelas, Wali Tahfizh, Shadow Teacher -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Kelas Umum -->
                <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl space-y-1">
                    <div class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-950">
                        <x-lucide-book-open class="w-4 h-4 text-emerald-700 shrink-0" />
                        <span>1. Kelas Umum</span>
                    </div>
                    @if($selectedSiswaDetail->kelas)
                        <div class="text-sm font-black text-emerald-900 pt-0.5">
                            {{ $selectedSiswaDetail->kelas->nama_kelas }}
                        </div>
                        <div class="text-[11px] text-emerald-700 font-medium">
                            Wali Kelas: <strong>{{ $selectedSiswaDetail->kelas->guruUmum->user->nama ?? 'Belum Ditentukan' }}</strong>
                        </div>
                    @else
                        <div class="text-xs text-stone-400 italic pt-1">- Belum Ditempatkan -</div>
                    @endif
                </div>

                <!-- Kelas Tahfizh -->
                <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl space-y-1">
                    <div class="flex items-center gap-1.5 text-xs font-extrabold text-amber-950">
                        <x-lucide-bookmark class="w-4 h-4 text-amber-700 shrink-0" />
                        <span>2. Kelas Tahfizh</span>
                    </div>
                    @if($selectedSiswaDetail->kelasTahfidz)
                        <div class="text-sm font-black text-amber-900 pt-0.5">
                            {{ $selectedSiswaDetail->kelasTahfidz->nama_kelas }}
                        </div>
                        <div class="text-[11px] text-amber-800 font-medium">
                            Pengampu: <strong>{{ $selectedSiswaDetail->kelasTahfidz->guruTahfidz->user->nama ?? 'Belum Ditentukan' }}</strong>
                        </div>
                    @else
                        <div class="text-xs text-stone-400 italic pt-1">- Belum Ditempatkan -</div>
                    @endif
                </div>

                <!-- Guru Pendamping Khusus (Shadow Teacher) -->
                <div class="p-3.5 bg-indigo-50 border border-indigo-200 rounded-xl space-y-1">
                    <div class="flex items-center gap-1.5 text-xs font-extrabold text-indigo-950">
                        <x-lucide-user-check class="w-4 h-4 text-indigo-700 shrink-0" />
                        <span>3. Guru Pendamping</span>
                    </div>
                    @if($selectedSiswaDetail->shadowTeacher)
                        <div class="text-sm font-black text-indigo-900 pt-0.5">
                            {{ $selectedSiswaDetail->shadowTeacher->user->nama ?? '-' }}
                        </div>
                        <div class="text-[11px] text-indigo-700 font-medium">
                            Status: <strong>Shadow Teacher / GPK</strong> ({{ $selectedSiswaDetail->shadowTeacher->jenis_guru === 'pendamping' ? 'Guru Pendamping' : ($selectedSiswaDetail->shadowTeacher->jenis_guru === 'tahfidz' ? 'Guru Tahfizh' : ($selectedSiswaDetail->shadowTeacher->jenis_guru === 'keduanya' ? 'Guru Umum & Tahfizh' : 'Guru Umum')) }})
                        </div>
                    @else
                        <div class="text-xs text-stone-400 italic pt-1">- Tidak Ada Guru Pendamping -</div>
                    @endif
                </div>
            </div>

            <!-- Detail Information Grid -->
            <div class="space-y-2">
                <div class="text-xs font-extrabold text-stone-800 uppercase tracking-wider">Informasi Identitas & Akun Login</div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5">
                        <div class="text-[10px] uppercase font-bold text-stone-500">NIS (Nomor Induk Siswa)</div>
                        <div class="font-mono font-bold text-stone-900 text-sm">{{ $selectedSiswaDetail->nis }}</div>
                    </div>

                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5">
                        <div class="text-[10px] uppercase font-bold text-stone-500">NISN (Nomor Induk Siswa Nasional)</div>
                        <div class="font-mono font-bold text-stone-900 text-sm">{{ $selectedSiswaDetail->nisn ?: '-' }}</div>
                    </div>

                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5">
                        <div class="text-[10px] uppercase font-bold text-stone-500">Username Portal</div>
                        <div class="font-mono font-bold text-stone-900">{{ $selectedSiswaDetail->user->username ?? '-' }}</div>
                    </div>

                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5">
                        <div class="text-[10px] uppercase font-bold text-stone-500">Alamat Email</div>
                        <div class="font-medium text-stone-900">{{ $selectedSiswaDetail->user->email ?: '-' }}</div>
                    </div>

                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5">
                        <div class="text-[10px] uppercase font-bold text-stone-500">Jenis Kelamin</div>
                        <div class="font-bold text-stone-900">
                            {{ $selectedSiswaDetail->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </div>
                    </div>

                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5">
                        <div class="text-[10px] uppercase font-bold text-stone-500">Tanggal Masuk Sekolah</div>
                        <div class="font-mono font-bold text-stone-900">
                            {{ $selectedSiswaDetail->tanggal_masuk ? $selectedSiswaDetail->tanggal_masuk->format('d F Y') : '-' }}
                        </div>
                    </div>

                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5 sm:col-span-2">
                        <div class="text-[10px] uppercase font-bold text-stone-500">Tempat & Tanggal Lahir</div>
                        <div class="font-bold text-stone-900">
                            {{ $selectedSiswaDetail->tempat_lahir ?: '-' }}, {{ $selectedSiswaDetail->tanggal_lahir ? $selectedSiswaDetail->tanggal_lahir->format('d F Y') : '-' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Wali Murid & Alamat -->
            <div class="space-y-2">
                <div class="text-xs font-extrabold text-stone-800 uppercase tracking-wider">Data Orang Tua / Wali & Alamat</div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5">
                        <div class="text-[10px] uppercase font-bold text-stone-500">Nama Wali Murid</div>
                        <div class="font-bold text-stone-900">{{ $selectedSiswaDetail->nama_wali ?: '-' }}</div>
                    </div>

                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5">
                        <div class="text-[10px] uppercase font-bold text-stone-500">No HP / WhatsApp Wali</div>
                        <div class="font-mono font-bold text-emerald-800">{{ $selectedSiswaDetail->no_hp_wali ?: '-' }}</div>
                    </div>

                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-0.5 sm:col-span-2">
                        <div class="text-[10px] uppercase font-bold text-stone-500">Alamat Tempat Tinggal</div>
                        <div class="font-medium text-stone-800 leading-relaxed">{{ $selectedSiswaDetail->alamat ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t border-stone-200">
                <x-button type="button" variant="secondary" size="md" wire:click="closeDetail">
                    Tutup Detail
                </x-button>
            </div>
        </div>
    @endif
</x-floating-card>
