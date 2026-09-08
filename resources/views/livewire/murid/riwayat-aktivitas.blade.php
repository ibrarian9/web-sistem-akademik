<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="p-2 bg-indigo-50 border border-indigo-100 rounded-xl text-indigo-700">
                    <x-lucide-activity class="w-5 h-5" />
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-bold text-stone-900 tracking-tight">Riwayat Aktivitas Santri</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                            Linimasa Aktivitas
                        </span>
                    </div>
                    <p class="text-xs text-stone-500 mt-0.5">Catatan perkembangan akademik, presensi harian, dan administrasi santri secara transparan.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Informasi Linimasa Aktivitas Santri"
        :steps="[
            ['title' => 'Pencatatan Otomatis', 'desc' => 'Setiap nilai baru, presensi kelas, dan verifikasi pembayaran otomatis terdata pada linimasa.'],
            ['title' => 'Kategori Terstruktur', 'desc' => 'Gunakan tab filter untuk melihat khusus catatan Presensi, Nilai & Rapor, atau Keuangan.'],
            ['title' => 'Petugas Terkait', 'desc' => 'Menampilkan nama ustadz/ustadzah atau petugas yang memperbarui informasi akun Anda.']
        ]"
    />

    <!-- Filter & Search Bar -->
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm p-4 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <!-- Category Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none text-xs">
            <button 
                type="button"
                wire:click="setFilter('all')" 
                class="px-3.5 py-2 rounded-xl font-semibold transition shrink-0 {{ $filterType === 'all' ? 'bg-indigo-700 text-white shadow-sm' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}"
            >
                Semua Catatan
            </button>
            <button 
                type="button"
                wire:click="setFilter('kehadiran')" 
                class="px-3.5 py-2 rounded-xl font-semibold transition shrink-0 flex items-center gap-1.5 {{ $filterType === 'kehadiran' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}"
            >
                <x-lucide-user-check class="w-3.5 h-3.5" />
                Presensi
            </button>
            <button 
                type="button"
                wire:click="setFilter('nilai')" 
                class="px-3.5 py-2 rounded-xl font-semibold transition shrink-0 flex items-center gap-1.5 {{ $filterType === 'nilai' ? 'bg-indigo-700 text-white shadow-sm' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}"
            >
                <x-lucide-award class="w-3.5 h-3.5" />
                Nilai & Rapor
            </button>
            <button 
                type="button"
                wire:click="setFilter('keuangan')" 
                class="px-3.5 py-2 rounded-xl font-semibold transition shrink-0 flex items-center gap-1.5 {{ $filterType === 'keuangan' ? 'bg-amber-600 text-white shadow-sm' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}"
            >
                <x-lucide-credit-card class="w-3.5 h-3.5" />
                Keuangan
            </button>
            <button 
                type="button"
                wire:click="setFilter('sistem')" 
                class="px-3.5 py-2 rounded-xl font-semibold transition shrink-0 flex items-center gap-1.5 {{ $filterType === 'sistem' ? 'bg-sky-700 text-white shadow-sm' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}"
            >
                <x-lucide-user class="w-3.5 h-3.5" />
                Profil & Sistem
            </button>
        </div>

        <!-- Search Input -->
        <div class="relative w-full md:w-64">
            <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3 top-1/2 -translate-y-1/2" />
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search"
                placeholder="Cari aktivitas..." 
                class="w-full pl-9 pr-3 py-2 text-xs bg-stone-50 border border-stone-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition"
            >
        </div>
    </div>

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
                                <!-- Category Icon Badge -->
                                <div class="shrink-0">
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
                                            <x-lucide-info class="w-5 h-5" />
                                        @endif
                                    </span>
                                </div>

                                <!-- Log Item Box -->
                                <div class="flex-1 min-w-0 bg-stone-50/70 border border-stone-200 rounded-2xl p-4 shadow-sm hover:border-stone-300 hover:bg-stone-50 transition">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-xs font-bold text-stone-900">{{ $log['title'] }}</h4>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border
                                                {{ $log['type'] === 'kehadiran' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : '' }}
                                                {{ $log['type'] === 'nilai' ? 'bg-indigo-100 text-indigo-800 border-indigo-200' : '' }}
                                                {{ $log['type'] === 'keuangan' ? 'bg-amber-100 text-amber-800 border-amber-200' : '' }}
                                                {{ $log['type'] === 'sistem' ? 'bg-sky-100 text-sky-800 border-sky-200' : '' }}
                                            ">
                                                {{ $log['category'] }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] font-semibold text-stone-500 flex items-center gap-1 shrink-0">
                                            <x-lucide-clock class="w-3 h-3 text-stone-400" />
                                            {{ $log['time'] }} ({{ $log['time_ago'] }})
                                        </span>
                                    </div>

                                    <p class="text-xs text-stone-700 font-medium mt-1.5 leading-relaxed">
                                        {{ $log['description'] }}
                                    </p>

                                    @if ($log['actor'])
                                        <div class="mt-2.5 pt-2 border-t border-stone-200/80 flex items-center justify-between text-[11px] text-stone-500">
                                            <span class="flex items-center gap-1.5 font-medium">
                                                <x-lucide-user class="w-3.5 h-3.5 text-stone-400" />
                                                Dicatat oleh: <strong class="text-stone-700">{{ $log['actor'] }}</strong>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <div class="py-12 text-center text-stone-500 space-y-2">
                        <div class="w-12 h-12 rounded-2xl bg-stone-100 border border-stone-200 flex items-center justify-center mx-auto text-stone-400">
                            <x-lucide-activity class="w-6 h-6" />
                        </div>
                        <p class="text-xs font-bold text-stone-700">Belum ada riwayat aktivitas</p>
                        <p class="text-[11px] text-stone-500 max-w-sm mx-auto">
                            Catatan presensi, nilai ujian, atau pembayaran santri akan otomatis muncul di linimasa ini saat dicatat oleh pihak sekolah.
                        </p>
                    </div>
                @endforelse
            </ul>
        </div>
    </div>
</div>
