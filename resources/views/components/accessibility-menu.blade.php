{{-- Accessibility Menu — Floating Button + Panel --}}
<div x-data="accessibilityMenu()" x-cloak class="fixed bottom-6 right-6 z-50">
    {{-- Toggle Button --}}
    <button @click="open = !open"
        class="w-12 h-12 rounded-full bg-green-600 text-white shadow-lg hover:bg-green-700 hover:shadow-xl flex items-center justify-center transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-green-300"
        title="Menu Aksesibilitas"
        aria-label="Buka menu aksesibilitas">
        <x-lucide-accessibility class="w-6 h-6" />
    </button>

    {{-- Panel --}}
    <div x-show="open" @click.outside="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="absolute bottom-16 right-0 w-72 bg-white border border-stone-200 rounded-2xl shadow-2xl p-5 space-y-5"
        style="display: none;">

        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-stone-800">Aksesibilitas</h3>
            <button @click="resetAll()" class="text-xs font-semibold text-green-600 hover:text-green-700 transition">
                Reset Semua
            </button>
        </div>

        {{-- Font Size Control --}}
        <div class="space-y-2">
            <label class="text-xs font-semibold text-stone-600 uppercase tracking-wider">Ukuran Teks</label>
            <div class="flex gap-2">
                <button @click="setFontSize('normal')"
                    :class="fontSize === 'normal' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-stone-600 border-stone-300 hover:border-green-400'"
                    class="flex-1 py-2.5 rounded-xl border text-sm font-semibold transition duration-150">
                    A
                </button>
                <button @click="setFontSize('lg')"
                    :class="fontSize === 'lg' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-stone-600 border-stone-300 hover:border-green-400'"
                    class="flex-1 py-2.5 rounded-xl border text-base font-semibold transition duration-150">
                    A+
                </button>
                <button @click="setFontSize('xl')"
                    :class="fontSize === 'xl' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-stone-600 border-stone-300 hover:border-green-400'"
                    class="flex-1 py-2.5 rounded-xl border text-lg font-bold transition duration-150">
                    A++
                </button>
            </div>
        </div>

        {{-- High Contrast Toggle --}}
        <div class="flex items-center justify-between">
            <div>
                <label class="text-xs font-semibold text-stone-600 uppercase tracking-wider block">Kontras Tinggi</label>
                <p class="text-xs text-stone-400 mt-0.5">Teks lebih gelap, border lebih tebal</p>
            </div>
            <button @click="toggleHighContrast()"
                :class="highContrast ? 'bg-green-600' : 'bg-stone-300'"
                class="relative w-11 h-6 rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-400">
                <span :class="highContrast ? 'translate-x-5' : 'translate-x-0.5'"
                    class="inline-block w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-200"></span>
            </button>
        </div>

        {{-- Loose Line Spacing Toggle --}}
        <div class="flex items-center justify-between">
            <div>
                <label class="text-xs font-semibold text-stone-600 uppercase tracking-wider block">Spasi Longgar</label>
                <p class="text-xs text-stone-400 mt-0.5">Jarak antar baris lebih lebar</p>
            </div>
            <button @click="toggleLooseLines()"
                :class="looseLines ? 'bg-green-600' : 'bg-stone-300'"
                class="relative w-11 h-6 rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-400">
                <span :class="looseLines ? 'translate-x-5' : 'translate-x-0.5'"
                    class="inline-block w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-200"></span>
            </button>
        </div>
    </div>
</div>

<script>
    function accessibilityMenu() {
        return {
            open: false,
            fontSize: localStorage.getItem('a11y-font-size') || 'normal',
            highContrast: localStorage.getItem('a11y-high-contrast') === 'true',
            looseLines: localStorage.getItem('a11y-loose-lines') === 'true',

            init() {
                this.applyAll();
            },

            setFontSize(size) {
                this.fontSize = size;
                localStorage.setItem('a11y-font-size', size);
                this.applyFontSize();
            },

            toggleHighContrast() {
                this.highContrast = !this.highContrast;
                localStorage.setItem('a11y-high-contrast', this.highContrast);
                this.applyHighContrast();
            },

            toggleLooseLines() {
                this.looseLines = !this.looseLines;
                localStorage.setItem('a11y-loose-lines', this.looseLines);
                this.applyLooseLines();
            },

            resetAll() {
                this.fontSize = 'normal';
                this.highContrast = false;
                this.looseLines = false;
                localStorage.removeItem('a11y-font-size');
                localStorage.removeItem('a11y-high-contrast');
                localStorage.removeItem('a11y-loose-lines');
                this.applyAll();
            },

            applyAll() {
                this.applyFontSize();
                this.applyHighContrast();
                this.applyLooseLines();
            },

            applyFontSize() {
                document.documentElement.classList.remove('a11y-font-lg', 'a11y-font-xl');
                if (this.fontSize === 'lg') document.documentElement.classList.add('a11y-font-lg');
                if (this.fontSize === 'xl') document.documentElement.classList.add('a11y-font-xl');
            },

            applyHighContrast() {
                document.documentElement.classList.toggle('a11y-high-contrast', this.highContrast);
            },

            applyLooseLines() {
                document.documentElement.classList.toggle('a11y-loose-lines', this.looseLines);
            },
        };
    }
</script>
