<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Jadwal Pelajaran Kelas</h2>
            <p class="text-xs text-slate-500">Lihat seluruh jadwal sesi belajar mengajar mingguan kelas Anda.</p>
        </div>
        <div class="px-4 py-2 bg-slate-900 border border-slate-800 rounded-2xl">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Kelas Terdaftar</span>
            <span class="text-xs font-bold text-white">Kelas {{ auth()->user()->siswa->kelas->nama_kelas ?? '-' }}</span>
        </div>
    </div>

    <!-- Day Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach (['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'] as $hari)
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-850 pb-2">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">{{ $hari }}</h3>
                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">
                        {{ count($schedules[$hari]) }} Sesi
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse ($schedules[$hari] as $session)
                        <div class="p-3 bg-slate-950/45 border border-slate-850 rounded-xl space-y-1.5">
                            <h4 class="text-xs font-bold text-white">{{ $session['mapel'] }}</h4>
                            <span class="text-[9px] text-slate-400 block">Guru: {{ $session['guru'] }}</span>
                            <div class="flex justify-end pt-1">
                                <span class="text-indigo-400 text-[10px] font-semibold">{{ $session['jam'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-650 text-xs font-medium">
                            Tidak ada jadwal belajar.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
