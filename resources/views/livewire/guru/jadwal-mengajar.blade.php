<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Jadwal Mengajar Anda</h2>
        <p class="text-xs text-slate-500">Berikut adalah jadwal mengajar mingguan Anda pada tahun ajaran aktif.</p>
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
                        <div class="p-3 bg-slate-950/45 border border-slate-850 rounded-xl space-y-1">
                            <h4 class="text-xs font-bold text-white">{{ $session['mapel'] }}</h4>
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="text-indigo-400 font-semibold">{{ $session['kelas'] }}</span>
                                <span class="text-slate-400 font-semibold">{{ $session['jam'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-600 text-xs font-medium">
                            Tidak ada jadwal mengajar.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
