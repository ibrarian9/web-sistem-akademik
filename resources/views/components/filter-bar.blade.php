@props([
    'searchModel' => 'search',
    'searchPlaceholder' => 'Cari data...',
    'selectedCount' => 0,
    'selectedLabel' => 'item dipilih',
])

<div class="space-y-3">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        @if ($searchModel)
            <div class="max-w-md w-full">
                <x-search-input :model="$searchModel" :placeholder="$searchPlaceholder" />
            </div>
        @endif

        @if ($slot->isNotEmpty())
            <div class="flex items-center gap-2.5 flex-wrap">
                {{ $slot }}
            </div>
        @endif
    </div>

    @if (isset($secondary) || $selectedCount > 0)
        <div class="flex items-center justify-between gap-4 border-t border-stone-100 pt-3 flex-wrap">
            <div class="flex items-center gap-2.5 flex-wrap">
                {{ $secondary ?? '' }}
            </div>

            @if ($selectedCount > 0)
                <span class="text-xs font-bold text-emerald-800 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200 shadow-2xs">
                    {{ $selectedCount }} {{ $selectedLabel }}
                </span>
            @endif
        </div>
    @endif
</div>
