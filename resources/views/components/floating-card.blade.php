@props([
    'show' => true,
    'title' => null,
    'subtitle' => null,
    'badge' => null,
    'badgeVariant' => 'emerald',
    'icon' => null,
    'maxWidth' => 'max-w-lg',
    'closeAction' => null,
    'zIndex' => 'z-[99990]',
])

@php
    $maxWidthClass = match ($maxWidth) {
        'max-w-sm' => 'max-w-sm',
        'max-w-md' => 'max-w-md',
        'max-w-lg' => 'max-w-lg',
        'max-w-xl' => 'max-w-xl',
        'max-w-2xl' => 'max-w-2xl',
        'max-w-3xl' => 'max-w-3xl',
        'max-w-4xl' => 'max-w-4xl',
        'max-w-5xl' => 'max-w-5xl',
        'max-w-6xl' => 'max-w-6xl',
        'max-w-7xl' => 'max-w-7xl',
        'max-w-full' => 'max-w-[96vw]',
        default => $maxWidth ?: 'max-w-lg',
    };
@endphp

@if ($show)
    <div class="fixed inset-0 {{ $zIndex }} flex items-center justify-center lg:pl-64 bg-stone-950/65 backdrop-blur-xs p-4 sm:p-6 lg:p-8 overflow-y-auto animate-fade-in"
         x-data
         @keydown.escape.window="{{ $closeAction ? "\$wire.{$closeAction}()" : '' }}"
         @click.self="{{ $closeAction ? "\$wire.{$closeAction}()" : '' }}">
        
        <div class="w-full {{ $maxWidthClass }} max-h-[92vh] flex flex-col bg-white border border-stone-200 rounded-3xl shadow-2xl p-5 sm:p-7 space-y-5 my-auto relative transform transition-all duration-200 ease-out scale-100 overflow-y-auto">
            
            <!-- Card Header Bar -->
            <div class="flex items-start justify-between gap-4 border-b border-stone-200/80 pb-4 shrink-0">
                <div class="space-y-1">
                    @if ($badge)
                        <div class="flex items-center gap-2 mb-1">
                            <x-badge :variant="$badgeVariant" size="xs">{{ $badge }}</x-badge>
                        </div>
                    @endif

                    @if ($title)
                        <h3 class="text-base sm:text-lg font-extrabold text-stone-900 tracking-tight flex items-center gap-2">
                            @if ($icon)
                                <x-dynamic-component :component="'lucide-' . $icon" class="w-5 h-5 text-emerald-600 shrink-0" />
                            @endif
                            <span>{{ $title }}</span>
                        </h3>
                    @endif

                    @if ($subtitle)
                        <p class="text-xs text-stone-500 font-medium">{{ $subtitle }}</p>
                    @endif
                </div>

                @if ($closeAction)
                    <button 
                        type="button" 
                        wire:click="{{ $closeAction }}" 
                        class="p-1.5 text-stone-400 hover:text-stone-700 hover:bg-stone-100 rounded-xl transition cursor-pointer shrink-0"
                        title="Tutup">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                @endif
            </div>

            <!-- Card Content Body -->
            <div class="space-y-4">
                {{ $slot }}
            </div>
        </div>
    </div>
@endif
