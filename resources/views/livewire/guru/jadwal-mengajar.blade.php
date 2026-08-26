<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Informasi Jadwal Mengajar Guru"
        :steps="[
            ['title' => 'Jadwal Per Hari', 'desc' => 'Tabel menampilkan alokasi waktu jam tatap muka pengajaran dari Senin hingga Sabtu.'],
            ['title' => 'Ruang & Rombel', 'desc' => 'Setiap kartu sesi menyantumkan rombel kelas pengampuan dan waktu pelaksanaan pelajaran.'],
            ['title' => 'Perubahan Jadwal', 'desc' => 'Apabila terdapat bentrok jam atau perubahan ruang, hubungi Bagian Tata Usaha.']
        ]"
    />

    <!-- Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                AKADEMIK & JADWAL
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Jadwal Mengajar Guru</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Jadwal mengajar mingguan Anda pada tahun ajaran aktif.</p>
        </div>
        <div class="bg-stone-50 border border-stone-200 px-4 py-2.5 rounded-xl text-right">
            <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Total Sesi Mingguan</span>
            <span class="text-sm font-black text-stone-900 block">
                {{ array_sum(array_map('count', $schedules)) }} Sesi Tatap Muka
            </span>
        </div>
    </div>

    <!-- Day Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach (['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'] as $hari)
            <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-4 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-stone-200 pb-2.5">
                        <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-1.5">
                            <x-lucide-calendar class="w-3.5 h-3.5 text-emerald-700" />
                            <span>{{ $hari }}</span>
                        </h3>
                        <span class="text-[10px] bg-stone-100 border border-stone-200 text-stone-700 font-extrabold px-2 py-0.5 rounded-md uppercase">
                            {{ count($schedules[$hari]) }} Sesi
                        </span>
                    </div>

                    <div class="space-y-2.5 mt-3">
                        @forelse ($schedules[$hari] as $session)
                            <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1.5 hover:border-emerald-300 transition">
                                <h4 class="text-xs font-extrabold text-stone-900">{{ $session['mapel'] }}</h4>
                                <div class="flex justify-between items-center text-[10px]">
                                    <span class="px-2 py-0.5 bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold rounded-md">
                                        Kelas {{ $session['kelas'] }}
                                    </span>
                                    <span class="text-stone-600 font-bold flex items-center gap-1">
                                        <x-lucide-clock class="w-3 h-3 text-stone-400" />
                                        <span>{{ $session['jam'] }}</span>
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-stone-400 text-xs font-semibold">
                                <x-lucide-coffee class="w-6 h-6 mx-auto mb-1 text-stone-300" />
                                <span>Tidak ada jadwal mengajar</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
