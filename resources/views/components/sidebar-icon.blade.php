@props(['icon' => 'circle'])

@switch($icon)
    @case('home') <x-lucide-home class="w-4 h-4 shrink-0 text-emerald-600 group-hover:text-emerald-700" /> @break
    @case('users') <x-lucide-users class="w-4 h-4 shrink-0 text-blue-600 group-hover:text-blue-700" /> @break
    @case('book-open') <x-lucide-book-open class="w-4 h-4 shrink-0 text-teal-600 group-hover:text-teal-700" /> @break
    @case('calendar') <x-lucide-calendar class="w-4 h-4 shrink-0 text-indigo-600 group-hover:text-indigo-700" /> @break
    @case('graduation-cap') <x-lucide-graduation-cap class="w-4 h-4 shrink-0 text-amber-600 group-hover:text-amber-700" /> @break
    @case('credit-card') <x-lucide-credit-card class="w-4 h-4 shrink-0 text-emerald-600 group-hover:text-emerald-700" /> @break
    @case('file-text') <x-lucide-file-text class="w-4 h-4 shrink-0 text-slate-500 group-hover:text-slate-700" /> @break
    @case('trending-down') <x-lucide-trending-down class="w-4 h-4 shrink-0 text-rose-500 group-hover:text-rose-600" /> @break
    @case('plus-circle') <x-lucide-plus-circle class="w-4 h-4 shrink-0 text-emerald-600" /> @break
    @case('eye') <x-lucide-eye class="w-4 h-4 shrink-0 text-sky-600" /> @break
    @case('settings') <x-lucide-settings class="w-4 h-4 shrink-0 text-stone-500 group-hover:text-stone-700" /> @break
    @case('activity') <x-lucide-activity class="w-4 h-4 shrink-0 text-amber-600" /> @break
    @case('alert-triangle') <x-lucide-alert-triangle class="w-4 h-4 shrink-0 text-rose-500" /> @break
    @case('alert-circle') <x-lucide-alert-circle class="w-4 h-4 shrink-0 text-amber-500" /> @break
    @case('trending-up') <x-lucide-trending-up class="w-4 h-4 shrink-0 text-emerald-600" /> @break
    @case('school') <x-lucide-school class="w-4 h-4 shrink-0 text-indigo-600" /> @break
    @case('user-check') <x-lucide-user-check class="w-4 h-4 shrink-0 text-emerald-600" /> @break
    @case('bell') <x-lucide-bell class="w-4 h-4 shrink-0 text-amber-500" /> @break
    @case('wallet') <x-lucide-wallet class="w-4 h-4 shrink-0 text-emerald-600" /> @break
    @case('box') <x-lucide-box class="w-4 h-4 shrink-0 text-indigo-500" /> @break
    @case('bar-chart-2') <x-lucide-bar-chart-2 class="w-4 h-4 shrink-0 text-cyan-600" /> @break
    @case('link') <x-lucide-link class="w-4 h-4 shrink-0 text-stone-500" /> @break
    @case('banknote') <x-lucide-banknote class="w-4 h-4 shrink-0 text-emerald-600" /> @break
    @case('arrow-down-left') <x-lucide-arrow-down-left class="w-4 h-4 shrink-0 text-emerald-600" /> @break
    @case('heart-handshake') <x-lucide-heart-handshake class="w-4 h-4 shrink-0 text-rose-500" /> @break
    @case('layers') <x-lucide-layers class="w-4 h-4 shrink-0 text-violet-600" /> @break
    @case('clock') <x-lucide-clock class="w-4 h-4 shrink-0 text-stone-500" /> @break
    @case('award') <x-lucide-award class="w-4 h-4 shrink-0 text-amber-500" /> @break
    @case('star') <x-lucide-star class="w-4 h-4 shrink-0 text-amber-400" /> @break
    @case('sliders') <x-lucide-sliders class="w-4 h-4 shrink-0 text-stone-500" /> @break
    @case('edit-3') <x-lucide-edit-3 class="w-4 h-4 shrink-0 text-emerald-600" /> @break
    @case('clipboard') <x-lucide-clipboard class="w-4 h-4 shrink-0 text-blue-500" /> @break
    @case('shield-check') <x-lucide-shield-check class="w-4 h-4 shrink-0 text-emerald-600" /> @break
    @case('check-square') <x-lucide-check-square class="w-4 h-4 shrink-0 text-emerald-600" /> @break
    @case('refresh-cw') <x-lucide-refresh-cw class="w-4 h-4 shrink-0 text-sky-500" /> @break
    @case('help-circle') <x-lucide-help-circle class="w-4 h-4 shrink-0 text-emerald-600" /> @break
    @default
        <div class="w-1.5 h-1.5 rounded-full bg-stone-400 shrink-0"></div>
@endswitch
