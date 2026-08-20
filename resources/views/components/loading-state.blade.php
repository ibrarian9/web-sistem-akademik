@props([
    'target' => null,
    'type' => 'bar',
    'rows' => 5,
])

@if ($type === 'bar')
    <div wire:loading.delay @if($target) wire:target="{{ $target }}" @endif class="w-full h-1 bg-emerald-100 overflow-hidden relative rounded-full">
        <div class="h-full bg-emerald-600 animate-pulse w-full"></div>
    </div>
@elseif ($type === 'spinner')
    <div wire:loading.delay @if($target) wire:target="{{ $target }}" @endif class="inline-flex items-center gap-2 text-xs font-bold text-emerald-800 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200 shadow-2xs">
        <svg class="animate-spin w-3.5 h-3.5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span>Memperbarui data...</span>
    </div>
@elseif ($type === 'skeleton')
    <div wire:loading.delay @if($target) wire:target="{{ $target }}" @endif class="space-y-2.5 p-4 animate-pulse">
        @for ($i = 0; $i < $rows; $i++)
            <div class="flex items-center gap-4 py-2 border-b border-stone-100">
                <div class="h-4 bg-stone-200 rounded w-8"></div>
                <div class="h-4 bg-stone-200 rounded flex-1"></div>
                <div class="h-4 bg-stone-200 rounded w-24"></div>
                <div class="h-4 bg-stone-200 rounded w-16"></div>
            </div>
        @endfor
    </div>
@endif
