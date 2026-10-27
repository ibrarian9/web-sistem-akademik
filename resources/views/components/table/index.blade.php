@props([
    'loadingTarget' => null,
    'loadingText' => null,
    'striped' => false,
    'overflow' => true,
])

@php
    $cleanTarget = $loadingTarget ? implode(',', array_map('trim', explode(',', $loadingTarget))) : null;
    $deleteTargets = 'bulkDeleteTransactions, deleteIncome, deleteExpense, deleteSppPayment, deleteTagihan, bulkDeleteTagihan, deletePembayaran, bulkDeletePembayaran';
@endphp

<div class="relative bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-xs min-h-[120px]">
    @if ($cleanTarget)
        <!-- Livewire Top Indeterminate Progress Line -->
        <div wire:loading.delay.block wire:target="{{ $cleanTarget }}" style="display: none;" class="absolute top-0 left-0 right-0 h-0.5 bg-stone-100 overflow-hidden z-30 pointer-events-none">
            <div wire:loading.remove wire:target="{{ $deleteTargets }}" class="w-full h-full bg-emerald-600 animate-pulse"></div>
            <div wire:loading wire:target="{{ $deleteTargets }}" class="w-full h-full bg-rose-600 animate-pulse"></div>
        </div>

        <!-- Sleek Translucent Overlay with Centered Indicator Card -->
        <div wire:loading.delay.flex wire:target="{{ $cleanTarget }}" style="display: none;" class="absolute inset-0 bg-white/75 backdrop-blur-[2px] z-20 items-center justify-center transition-all">
            <div class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-white border border-stone-200 text-stone-800 rounded-2xl shadow-xl shadow-stone-900/10">
                <svg wire:loading.remove wire:target="{{ $deleteTargets }}" class="animate-spin w-4 h-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-85" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <svg wire:loading wire:target="{{ $deleteTargets }}" class="animate-spin w-4 h-4 text-rose-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-85" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span wire:loading.remove wire:target="{{ $deleteTargets }}" class="text-xs font-bold text-stone-700 tracking-tight">{{ $loadingText ?? 'Memperbarui data...' }}</span>
                <span wire:loading wire:target="{{ $deleteTargets }}" class="text-xs font-bold text-rose-700 tracking-tight">Memproses penghapusan data...</span>
            </div>
        </div>
    @endif

    <div @if($overflow) class="overflow-x-auto custom-scrollbar touch-pan-x" @endif>
        <table {{ $attributes->merge(['class' => 'w-full text-left border-separate border-spacing-0 text-xs text-stone-800']) }}>
            {{ $slot }}
        </table>
    </div>
</div>
