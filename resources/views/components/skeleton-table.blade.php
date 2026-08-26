@props([
    'rows' => 5,
    'cols' => 5,
])

<div class="w-full bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-2xs animate-pulse p-4 space-y-4">
    <!-- Header skeleton -->
    <div class="h-8 bg-stone-100 rounded-xl w-full flex items-center justify-between px-3">
        <div class="h-4 bg-stone-200 rounded w-1/4"></div>
        <div class="h-4 bg-stone-200 rounded w-1/6"></div>
    </div>

    <!-- Rows skeleton -->
    <div class="space-y-3">
        @for ($i = 0; $i < $rows; $i++)
            <div class="flex items-center gap-4 py-2 border-b border-stone-100 last:border-0">
                <div class="h-3.5 bg-stone-200 rounded w-8 shrink-0"></div>
                <div class="h-3.5 bg-stone-200 rounded w-1/4"></div>
                <div class="h-3.5 bg-stone-100 rounded w-1/5"></div>
                <div class="h-3.5 bg-stone-100 rounded w-1/6"></div>
                <div class="h-3.5 bg-stone-200 rounded w-1/6 ml-auto"></div>
            </div>
        @endfor
    </div>
</div>
