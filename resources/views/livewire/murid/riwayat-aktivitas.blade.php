<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-xl font-bold text-stone-900 tracking-tight">Riwayat Aktivitas & Log System</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                    Audit Trail Siswa
                </span>
            </div>
            <p class="text-xs text-stone-500 mt-1">Jejak aktivitas akademik, absensi, dan transaksi keuangan Anda yang tercatat oleh sistem.</p>
        </div>
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pencatatan Log Aktivitas Sistem"
        :steps="[
            ['title' => 'Audit Trail Real-time', 'desc' => 'Setiap perubahan presensi, nilai, pembayaran, dan status akademis tercatat otomatis.'],
            ['title' => 'Kategori Log', 'desc' => 'Guna transparansi, log dipisahkan berdasarkan kategori Kehadiran, Nilai, Keuangan, dan Sistem.'],
            ['title' => 'Informasi Pengubah', 'desc' => 'Setiap riwayat menyantumkan nama petugas atau pengajar yang melakukan entri data.']
        ]"
    />

    <!-- Timeline Wrapper Card -->
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm p-6 md:p-8">
        <div class="flow-root">
            <ul class="-mb-8">
                @forelse ($activityLogs as $index => $log)
                    <li>
                        <div class="relative pb-8">
                            @if (!$loop->last)
                                <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-stone-200" aria-hidden="true"></span>
                            @endif

                            <div class="relative flex items-start space-x-4">
                                <!-- Circle Icon Accent -->
                                <div>
                                    <span class="h-10 w-10 rounded-xl border flex items-center justify-center shadow-sm
                                        {{ $log['type'] === 'kehadiran' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : '' }}
                                        {{ $log['type'] === 'nilai' ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : '' }}
                                        {{ $log['type'] === 'keuangan' ? 'bg-amber-50 border-amber-200 text-amber-700' : '' }}
                                        {{ $log['type'] === 'sistem' ? 'bg-sky-50 border-sky-200 text-sky-700' : '' }}
                                    ">
                                        @if ($log['type'] === 'kehadiran')
                                            <x-lucide-user-check class="w-5 h-5" />
                                        @elseif ($log['type'] === 'nilai')
                                            <x-lucide-award class="w-5 h-5" />
                                        @elseif ($log['type'] === 'keuangan')
                                            <x-lucide-credit-card class="w-5 h-5" />
                                        @else
                                            <x-lucide-activity class="w-5 h-5" />
                                        @endif
                                    </span>
                                </div>

                                <!-- Log Content -->
                                <div class="flex-1 min-w-0 pt-0.5 bg-stone-50 border border-stone-200 rounded-xl p-4 shadow-sm hover:border-stone-300 transition">
                                    <div class="flex items-center justify-between gap-4">
                                        <h4 class="text-xs font-bold text-stone-900">{{ $log['title'] }}</h4>
                                        <span class="text-[10px] font-bold text-stone-500 bg-white px-2.5 py-1 rounded-lg border border-stone-200 shrink-0">
                                            {{ $log['time'] }}
                                        </span>
                                    </div>

                                    <p class="text-xs text-stone-700 font-medium mt-1 leading-relaxed">
                                        {{ $log['description'] }}
                                    </p>

                                    @if ($log['actor'])
                                        <div class="mt-2 pt-2 border-t border-stone-200/80 flex items-center justify-between text-[10px] text-stone-500 font-semibold">
                                            <span class="flex items-center gap-1">
                                                <x-lucide-user class="w-3 h-3 text-stone-400" />
                                                Oleh: {{ $log['actor'] }}
                                            </span>
                                            <span class="capitalize text-stone-600 font-bold bg-white px-2 py-0.5 rounded border border-stone-200">
                                                {{ $log['type'] }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <div class="py-12 text-center text-stone-500 space-y-2">
                        <x-lucide-activity class="w-10 h-10 mx-auto text-stone-400" />
                        <p class="text-xs font-semibold">Belum ada riwayat aktivitas yang dicatat oleh sistem.</p>
                    </div>
                @endforelse
            </ul>
        </div>
    </div>
</div>
