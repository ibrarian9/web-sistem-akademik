<div class="space-y-8 font-sans pb-12">
    <!-- Hero Header Card with Rich Banner Aesthetic -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-900 via-emerald-800 to-teal-950 p-6 md:p-8 text-white shadow-xl border border-emerald-700/50">
        <!-- Decorative Glow Blur Elements -->
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-950/80 border border-emerald-500/40 rounded-full text-emerald-300 text-[11px] font-extrabold uppercase tracking-wider shadow-inner">
                    <x-lucide-help-circle class="w-3.5 h-3.5 text-emerald-400" />
                    <span>PUSAT PANDUAN AKADEMIK &amp; FAQ</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-black !text-white tracking-tight leading-tight">
                    Tutorial Sistem &amp; Pusat Bantuan FAQ
                </h1>
                <p class="text-emerald-100 text-xs md:text-sm font-medium leading-relaxed">
                    Panduan lengkap alur kerja sistem akademik, tata cara pembuatan Mata Pelajaran, Setup Kurikulum Merdeka, Tahfizh, Keuangan, serta solusi kendala operasional.
                </p>
            </div>

            <!-- Search Bar inside Header -->
            <div class="w-full lg:w-96 bg-white/10 backdrop-blur-md border border-white/20 p-2 rounded-2xl shadow-lg">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-emerald-200">
                        <x-lucide-search class="w-4 h-4" />
                    </span>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Cari kata kunci (contoh: mapel, nilai, spp, rapor)..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white text-stone-900 placeholder-stone-400 rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-400 shadow-inner" />
                </div>
            </div>
        </div>
    </div>

    <!-- Category Filter Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 custom-scrollbar">
        <button wire:click="selectCategory('semua')"
            class="px-4 py-2.5 rounded-2xl text-xs font-bold transition whitespace-nowrap border flex items-center gap-2 {{ $selectedCategory === 'semua' ? 'bg-emerald-700 text-white border-emerald-700 shadow-md' : 'bg-white text-stone-700 border-stone-200 hover:bg-stone-50' }}">
            <x-lucide-grid class="w-4 h-4" />
            <span>Semua Panduan</span>
        </button>

        <button wire:click="selectCategory('tata_usaha')"
            class="px-4 py-2.5 rounded-2xl text-xs font-bold transition whitespace-nowrap border flex items-center gap-2 {{ $selectedCategory === 'tata_usaha' ? 'bg-emerald-700 text-white border-emerald-700 shadow-md' : 'bg-white text-stone-700 border-stone-200 hover:bg-stone-50' }}">
            <x-lucide-layers class="w-4 h-4 text-emerald-600" />
            <span>Tata Usaha &amp; Admin</span>
        </button>

        <button wire:click="selectCategory('guru')"
            class="px-4 py-2.5 rounded-2xl text-xs font-bold transition whitespace-nowrap border flex items-center gap-2 {{ $selectedCategory === 'guru' ? 'bg-emerald-700 text-white border-emerald-700 shadow-md' : 'bg-white text-stone-700 border-stone-200 hover:bg-stone-50' }}">
            <x-lucide-book-open class="w-4 h-4 text-amber-600" />
            <span>Guru &amp; Pengajar</span>
        </button>

        <button wire:click="selectCategory('finance')"
            class="px-4 py-2.5 rounded-2xl text-xs font-bold transition whitespace-nowrap border flex items-center gap-2 {{ $selectedCategory === 'finance' ? 'bg-emerald-700 text-white border-emerald-700 shadow-md' : 'bg-white text-stone-700 border-stone-200 hover:bg-stone-50' }}">
            <x-lucide-wallet class="w-4 h-4 text-blue-600" />
            <span>Finance &amp; Bendahara</span>
        </button>

        <button wire:click="selectCategory('murid')"
            class="px-4 py-2.5 rounded-2xl text-xs font-bold transition whitespace-nowrap border flex items-center gap-2 {{ $selectedCategory === 'murid' ? 'bg-emerald-700 text-white border-emerald-700 shadow-md' : 'bg-white text-stone-700 border-stone-200 hover:bg-stone-50' }}">
            <x-lucide-user-check class="w-4 h-4 text-purple-600" />
            <span>Murid &amp; Orang Tua</span>
        </button>
    </div>

    <!-- Featured Section: Step-by-Step Interactive Guides -->
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-stone-200 pb-3">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-6 bg-emerald-700 rounded-full"></span>
                <h2 class="text-lg font-extrabold text-stone-900 tracking-tight">Panduan Utama &amp; Alur Operasional</h2>
            </div>
            <span class="text-xs text-stone-500 font-semibold">Ditemukan {{ count($tutorials) }} Panduan Praktis</span>
        </div>

        <div class="grid grid-cols-1 gap-6">
            @forelse ($tutorials as $item)
                <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-sm hover:shadow-md transition space-y-5">
                    <!-- Tutorial Header -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-stone-100 pb-4">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-0.5 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-[10px] font-extrabold uppercase">
                                    {{ $item['category_label'] }}
                                </span>
                                <span class="px-2.5 py-0.5 bg-amber-100 border border-amber-300 text-amber-900 rounded-full text-[10px] font-extrabold uppercase">
                                    {{ $item['role_badge'] }}
                                </span>
                            </div>
                            <h3 class="text-base font-extrabold text-stone-900 tracking-tight">{{ $item['title'] }}</h3>
                        </div>

                        @if (!empty($item['action_route']) && Route::has($item['action_route']))
                            <a href="{{ route($item['action_route']) }}" wire:navigate
                                class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center justify-center gap-2 shrink-0">
                                <x-lucide-external-link class="w-3.5 h-3.5" />
                                <span>{{ $item['action_label'] }}</span>
                            </a>
                        @endif
                    </div>

                    <!-- Problem Explanation Banner (Important Callout) -->
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl space-y-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-amber-950 uppercase tracking-wider">
                            <x-lucide-alert-triangle class="w-4 h-4 text-amber-600 shrink-0" />
                            <span>{{ $item['problem_desc'] }}</span>
                        </div>
                        <ul class="space-y-1 pl-6 list-disc text-xs text-amber-900 font-medium">
                            @foreach ($item['consequences'] as $cq)
                                <li>{{ $cq }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Steps Timeline -->
                    <div class="space-y-3 pt-2">
                        <h4 class="text-xs font-extrabold text-stone-800 uppercase tracking-wider">Langkah-Langkah Pelaksanaan:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                            @foreach ($item['steps'] as $st)
                                <div class="bg-stone-50 border border-stone-200 p-4 rounded-2xl space-y-2 relative">
                                    <div class="flex items-center justify-between">
                                        <span class="w-7 h-7 rounded-xl bg-emerald-700 text-white text-xs font-black flex items-center justify-center shadow-xs">
                                            {{ $st['step'] }}
                                        </span>
                                        <span class="text-[10px] font-bold text-stone-400 uppercase">Langkah {{ $st['step'] }}</span>
                                    </div>
                                    <h5 class="text-xs font-extrabold text-stone-900 leading-snug">{{ $st['title'] }}</h5>
                                    <p class="text-[11px] text-stone-600 font-medium leading-relaxed">{{ $st['desc'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center bg-white border border-stone-200 rounded-3xl space-y-2">
                    <x-lucide-help-circle class="w-10 h-10 text-stone-300 mx-auto" />
                    <h3 class="text-sm font-bold text-stone-800">Tidak ada tutorial yang cocok</h3>
                    <p class="text-xs text-stone-500">Coba ubah kata kunci pencarian atau pilih kategori lain.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- FAQ Accordion Section -->
    <div class="space-y-6 pt-4">
        <div class="flex items-center justify-between border-b border-stone-200 pb-3">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-6 bg-teal-700 rounded-full"></span>
                <h2 class="text-lg font-extrabold text-stone-900 tracking-tight">Pertanyaan Sering Diajukan (FAQ)</h2>
            </div>
            <span class="text-xs text-stone-500 font-semibold">Jawaban Singkat Kendala Teknis</span>
        </div>

        <div class="space-y-3">
            @forelse ($faqs as $faq)
                <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-xs transition">
                    <button wire:click="toggleFaq('{{ $faq['id'] }}')"
                        class="w-full p-4 text-left flex items-center justify-between gap-4 hover:bg-stone-50 transition">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-900 flex items-center justify-center shrink-0 font-bold text-xs">
                                Q
                            </span>
                            <span class="text-xs md:text-sm font-extrabold text-stone-900 leading-snug">
                                {{ $faq['question'] }}
                            </span>
                        </div>
                        <x-lucide-chevron-down class="w-4 h-4 text-stone-500 shrink-0 transition-transform duration-200 {{ $openFaqId === $faq['id'] ? 'rotate-180 text-emerald-700' : '' }}" />
                    </button>

                    @if ($openFaqId === $faq['id'])
                        <div class="p-4 bg-emerald-50/50 border-t border-stone-100 text-xs text-stone-800 leading-relaxed font-semibold space-y-2 animate-in fade-in duration-150">
                            <div class="flex items-start gap-2">
                                <span class="px-2 py-0.5 bg-emerald-700 text-white rounded text-[10px] font-bold uppercase shrink-0 mt-0.5">Solusi</span>
                                <p>{{ $faq['answer'] }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center bg-white border border-stone-200 rounded-2xl text-xs text-stone-500 font-semibold">
                    Tidak ada FAQ yang sesuai dengan pencarian Anda.
                </div>
            @endforelse
        </div>
    </div>
</div>
