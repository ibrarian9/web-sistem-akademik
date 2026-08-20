@props([
    'placeholder' => 'Cari data...',
    'model' => 'search',
    'debounce' => '300ms',
])

<div class="relative w-full" x-data="{ query: @entangle($model).live }">
    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
        <x-lucide-search class="w-4 h-4" />
    </div>

    <input
        type="text"
        x-model.debounce.{{ $debounce }}="query"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'w-full pl-10 pr-9 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 placeholder-stone-400 text-xs font-semibold focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 transition shadow-2xs'
        ]) }}
    />

    <button
        type="button"
        x-show="query && query.length > 0"
        x-cloak
        @click="query = ''; $wire.set('{{ $model }}', '')"
        class="absolute inset-y-0 right-0 pr-3 flex items-center text-stone-400 hover:text-stone-700 transition"
        title="Bersihkan Pencarian">
        <x-lucide-x class="w-3.5 h-3.5" />
    </button>
</div>
