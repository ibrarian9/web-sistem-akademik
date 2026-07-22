<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-900 tracking-tight">Jadwal Pelajaran Saya</h2>
            <p class="text-xs text-stone-500">Jadwal kegiatan belajar mengajar (KBM) mingguan kelas Anda.</p>
        </div>
        <div class="flex items-center gap-3 px-4 py-2.5 bg-white border border-stone-200 rounded-2xl shadow-sm">
            <div class="w-8 h-8 rounded-xl bg-sky-50 border border-sky-200 flex items-center justify-center text-sky-700 font-bold text-xs">
                <x-lucide-calendar class="w-4 h-4" />
            </div>
            <div>
                <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Kelas / Wali Kelas</span>
                <span class="text-xs font-bold text-stone-800">
                    {{ auth()->user()->siswa->kelas->nama_kelas ?? '-' }} (Wali: {{ auth()->user()->siswa->kelas->waliKelas->user->nama ?? '-' }})
                </span>
            </div>
        </div>
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Kegiatan Belajar Mengajar (KBM)"
        :steps="[
            ['title' => 'Jadwal Mingguan', 'desc' => 'Tabel menampilkan daftar mata pelajaran, nama guru pengampu, serta alokasi jam KBM.'],
            ['title' => 'Ruang Kelas', 'desc' => 'Setiap sesi menyantumkan nomor ruang tempat pelaksanaan pembelajaran harian.'],
            ['title' => 'Ketepatan Waktu', 'desc' => 'Siswa diimbau hadir di ruang kelas 15 menit sebelum sesi jam pertama dimulai.']
        ]"
    />

    <!-- Day Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach (['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'] as $hari)
            <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <h3 class="text-sm font-bold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-600"></span>
                        {{ ucfirst($hari) }}
                    </h3>
                    <span class="text-[10px] font-bold text-stone-500 uppercase">
                        {{ count($jadwalByHari[$hari] ?? []) }} Sesi
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse ($jadwalByHari[$hari] ?? [] as $j)
                        <div class="p-3.5 bg-stone-50 border border-stone-200 rounded-xl space-y-1.5 hover:bg-stone-100/80 transition">
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                    {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                                </span>
                                <span class="text-[10px] text-stone-500 font-bold uppercase">Ruang {{ $j->ruangan ?: '-' }}</span>
                            </div>
                            <h4 class="text-xs font-bold text-stone-900">{{ $j->mataPelajaran->nama_mapel ?? '-' }}</h4>
                            <p class="text-[11px] text-stone-600 font-medium flex items-center gap-1">
                                <x-lucide-user class="w-3 h-3 text-stone-400 shrink-0" />
                                {{ $j->guru->user->nama ?? '-' }}
                            </p>
                        </div>
                    @empty
                        <div class="py-6 text-center text-stone-400 text-xs font-medium italic">
                            Tidak ada jadwal pelajaran.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
