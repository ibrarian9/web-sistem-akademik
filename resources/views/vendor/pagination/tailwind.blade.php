@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
        <!-- Mobile Pagination View -->
        <div class="flex items-center justify-between w-full sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-bold text-stone-400 bg-stone-100 border border-stone-200 cursor-not-allowed rounded-xl shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    <span>Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-bold text-stone-700 bg-white border border-stone-300 rounded-xl shadow-2xs hover:bg-emerald-50 hover:text-emerald-800 hover:border-emerald-300 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    <span>Sebelumnya</span>
                </a>
            @endif

            <span class="text-xs font-bold text-stone-600 bg-stone-100/80 px-3 py-1.5 rounded-lg border border-stone-200 font-mono">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-bold text-stone-700 bg-white border border-stone-300 rounded-xl shadow-2xs hover:bg-emerald-50 hover:text-emerald-800 hover:border-emerald-300 transition">
                    <span>Berikutnya</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-bold text-stone-400 bg-stone-100 border border-stone-200 cursor-not-allowed rounded-xl shadow-2xs">
                    <span>Berikutnya</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        </div>

        <!-- Desktop Pagination View -->
        <div class="hidden sm:flex sm:items-center sm:justify-between w-full">
            <div>
                <p class="text-xs text-stone-500 font-medium">
                    Menampilkan <span class="font-bold text-stone-800">{{ $paginator->firstItem() ?? 0 }}</span> - <span class="font-bold text-stone-800">{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="font-extrabold text-stone-900 font-mono">{{ $paginator->total() }}</span> data
                </p>
            </div>

            <div>
                <div class="inline-flex items-center gap-1 p-1 bg-stone-50 border border-stone-200 rounded-xl shadow-2xs">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Sebelumnya" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-stone-300 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-stone-600 hover:bg-white hover:text-emerald-700 hover:shadow-2xs transition font-bold" aria-label="Sebelumnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- Dots Separator --}}
                        @if (is_string($element))
                            <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-stone-400 cursor-default">
                                {{ $element }}
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-extrabold text-white bg-emerald-700 rounded-lg shadow-2xs" aria-current="page">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-stone-700 hover:bg-white hover:text-emerald-700 hover:shadow-2xs rounded-lg transition" aria-label="Halaman {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-stone-600 hover:bg-white hover:text-emerald-700 hover:shadow-2xs transition font-bold" aria-label="Berikutnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Berikutnya" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-stone-300 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </nav>
@endif
