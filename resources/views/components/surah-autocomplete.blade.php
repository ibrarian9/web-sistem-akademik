@props([
    'wireModel' => '',
    'placeholder' => 'Ketik nama surah...',
    'includeJuz' => false,
])

<div 
    class="relative" 
    x-data="surahAutocomplete({ wireField: '{{ $wireModel }}', includeJuz: {{ $includeJuz ? 'true' : 'false' }} })"
    @click.outside="open = false"
>
    <div class="relative">
        <input 
            type="text" 
            x-ref="inputField"
            wire:model="{{ $wireModel }}"
            @input="onInput($event)"
            @focus="onFocus()"
            @keydown.down.prevent="navigateNext()"
            @keydown.up.prevent="navigatePrev()"
            @keydown.enter.prevent="selectHighlighted()"
            @keydown.escape="open = false"
            placeholder="{{ $placeholder }}" 
            autocomplete="off"
            class="w-full bg-white border border-stone-300 rounded-lg pl-2.5 pr-7 py-1.5 text-xs font-semibold text-stone-900 focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 shadow-xs transition"
        >
        <button 
            type="button" 
            @click="toggleDropdown()" 
            class="absolute right-2 top-1/2 -translate-y-1/2 text-stone-400 hover:text-emerald-700 transition"
            title="Pilih Surah"
        >
            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180 text-emerald-700' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
    </div>

    <!-- Floating Dropdown Suggestions Card -->
    <div 
        x-show="open && filteredList.length > 0" 
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-1 scale-98"
        class="absolute left-0 right-0 top-full mt-1.5 z-50 bg-white border border-stone-200 rounded-xl shadow-2xl max-h-52 overflow-y-auto custom-scrollbar divide-y divide-stone-100 ring-1 ring-black/5"
        style="display: none;"
    >
        <div class="px-2.5 py-1 bg-emerald-950 text-white flex items-center justify-between text-[10px] font-bold uppercase tracking-wider sticky top-0 z-10">
            <span class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                <span>Pilih Surah / Juz</span>
            </span>
            <span class="text-[9px] text-emerald-300 font-normal">Tekan Enter atau klik</span>
        </div>

        <template x-for="(item, index) in filteredList" :key="item.name">
            <div 
                @click="chooseItem(item.name)"
                @mouseenter="highlightedIndex = index"
                class="w-full px-3 py-2 flex items-center justify-between gap-2 text-xs transition cursor-pointer"
                :class="highlightedIndex === index ? 'bg-emerald-50 text-emerald-950 font-bold border-l-3 border-emerald-600 pl-2.5' : 'text-stone-700 hover:bg-stone-50'"
            >
                <div class="flex items-center gap-2.5 min-w-0">
                    <span 
                        class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-black shrink-0"
                        :class="item.isJuz ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-emerald-100 text-emerald-900 border border-emerald-300'"
                        x-text="item.no"
                    ></span>
                    <span class="font-bold text-stone-900 truncate" x-text="item.name"></span>
                </div>
                <span class="text-[10px] text-stone-400 font-medium shrink-0" x-text="item.info"></span>
            </div>
        </template>
    </div>
</div>
