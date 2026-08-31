@props([
    'items' => [],
])

<nav aria-label="Breadcrumb" class="flex items-center text-xs font-semibold text-stone-500 mb-1.5">
    <ol class="inline-flex items-center gap-1.5 flex-wrap">
        <li class="inline-flex items-center">
            <a href="{{ route('finance.dashboard') }}" class="inline-flex items-center gap-1 text-stone-500 hover:text-emerald-700 transition">
                <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Keuangan</span>
            </a>
        </li>

        @foreach ($items as $item)
            <li class="inline-flex items-center gap-1.5">
                <svg class="w-3 h-3 text-stone-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
                
                @if (!empty($item['url']) || !empty($item['href']))
                    <a href="{{ $item['url'] ?? $item['href'] }}" class="text-stone-500 hover:text-emerald-700 transition">
                        {{ $item['label'] ?? $item['title'] }}
                    </a>
                @else
                    <span class="text-emerald-800 font-bold bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                        {{ $item['label'] ?? $item['title'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
