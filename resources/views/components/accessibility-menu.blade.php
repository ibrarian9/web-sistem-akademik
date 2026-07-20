{{-- Accessibility Menu — Floating Button + Panel --}}
<div x-data="accessibilityMenu()" x-cloak class="fixed bottom-6 right-6 z-50">
    {{-- Scoped CSS for Ultra-Sleek Range Slider & Controls --}}
    <style>
        .a11y-slider-track {
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 8px;
            border-radius: 9999px;
            background: linear-gradient(to right, #e2e8f0, #10b981);
            outline: none;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        .a11y-slider-track::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #ffffff;
            border: 3px solid #10b981;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.35);
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .a11y-slider-track::-webkit-slider-thumb:hover {
            transform: scale(1.2);
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.5);
        }

        .a11y-slider-track::-moz-range-thumb {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #ffffff;
            border: 3px solid #10b981;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.35);
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .a11y-slider-track::-moz-range-thumb:hover {
            transform: scale(1.2);
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.5);
        }

        /* High Contrast Class Overrides */
        html.a11y-high-contrast {
            filter: contrast(125%);
        }

        html.a11y-loose-lines body {
            line-height: 1.95 !important;
            letter-spacing: 0.03em !important;
        }
    </style>

    {{-- Toggle Floating Button --}}
    <button @click="open = !open"
        class="w-13 h-13 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white shadow-xl hover:shadow-2xl flex items-center justify-center transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-emerald-300"
        title="Menu Aksesibilitas"
        aria-label="Buka menu aksesibilitas">
        <x-lucide-accessibility class="w-6 h-6" />
    </button>

    {{-- Panel --}}
    <div x-show="open" @click.outside="open = false"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 translate-y-6 scale-90"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-6 scale-90"
        class="absolute bottom-16 right-0 w-80 bg-white/95 backdrop-blur-md border border-stone-200/80 rounded-2xl shadow-2xl p-5 space-y-6"
        style="display: none;">

        <div class="flex items-center justify-between border-b border-stone-100 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <x-lucide-accessibility class="w-5 h-5" />
                </div>
                <div>
                    <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider">Aksesibilitas</h3>
                    <p class="text-[10px] text-stone-400">Sesuaikan kenyamanan tampilan</p>
                </div>
            </div>
            <button @click="resetAll()" class="px-2.5 py-1 text-[11px] font-bold text-emerald-600 hover:bg-emerald-50 rounded-lg transition">
                Reset
            </button>
        </div>

        {{-- Font Zoom Range Slider --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <label class="text-[11px] font-bold text-stone-700 uppercase tracking-wider flex items-center gap-1.5">
                    <x-lucide-zoom-in class="w-3.5 h-3.5 text-emerald-600" />
                    <span>Ukuran Teks</span>
                </label>
                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-md text-[11px] font-extrabold" x-text="fontScale + '%'"></span>
            </div>

            <div class="px-1 space-y-2">
                <input type="range" min="100" max="140" step="10" x-model="fontScale" @input="updateFontScale()"
                    class="a11y-slider-track" />

                {{-- Step Markers --}}
                <div class="flex justify-between text-[9px] font-bold text-stone-400 px-0.5">
                    <span :class="fontScale == 100 ? 'text-emerald-600 font-extrabold' : ''">100%</span>
                    <span :class="fontScale == 110 ? 'text-emerald-600 font-extrabold' : ''">110%</span>
                    <span :class="fontScale == 120 ? 'text-emerald-600 font-extrabold' : ''">120%</span>
                    <span :class="fontScale == 130 ? 'text-emerald-600 font-extrabold' : ''">130%</span>
                    <span :class="fontScale == 140 ? 'text-emerald-600 font-extrabold' : ''">140%</span>
                </div>
            </div>
        </div>

        {{-- High Contrast Toggle --}}
        <div :class="highContrast ? 'bg-emerald-50/90 border-emerald-300/90 shadow-sm' : 'bg-stone-50/70 border-stone-200/70'"
            class="flex items-center justify-between p-3.5 border rounded-2xl transition-all duration-200">
            <div class="flex items-center gap-3">
                <div :class="highContrast ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-amber-100/80 text-amber-600'"
                    class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200 shrink-0">
                    <x-lucide-sun class="w-4 h-4" />
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-bold text-stone-800">Kontras Tinggi</label>
                        <span :class="highContrast ? 'bg-emerald-600 text-white' : 'bg-stone-200 text-stone-600'"
                            class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wider transition-colors"
                            x-text="highContrast ? 'ON' : 'OFF'"></span>
                    </div>
                    <p class="text-[10px] text-stone-500 mt-0.5">Tingkatkan kontras teks &amp; border</p>
                </div>
            </div>
            <button @click="toggleHighContrast()"
                type="button"
                :class="highContrast ? 'bg-emerald-600 shadow-md shadow-emerald-600/30' : 'bg-stone-300 hover:bg-stone-400'"
                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                <span class="sr-only">Toggle Kontras Tinggi</span>
                <span :class="highContrast ? 'translate-x-5' : 'translate-x-0'"
                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out flex items-center justify-center">
                    <template x-if="highContrast">
                        <x-lucide-check class="w-3 h-3 text-emerald-600 stroke-[3]" />
                    </template>
                </span>
            </button>
        </div>

        {{-- Loose Line Spacing Toggle --}}
        <div :class="looseLines ? 'bg-emerald-50/90 border-emerald-300/90 shadow-sm' : 'bg-stone-50/70 border-stone-200/70'"
            class="flex items-center justify-between p-3.5 border rounded-2xl transition-all duration-200">
            <div class="flex items-center gap-3">
                <div :class="looseLines ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-emerald-100/80 text-emerald-600'"
                    class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200 shrink-0">
                    <x-lucide-align-justify class="w-4 h-4" />
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-bold text-stone-800">Spasi Longgar</label>
                        <span :class="looseLines ? 'bg-emerald-600 text-white' : 'bg-stone-200 text-stone-600'"
                            class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wider transition-colors"
                            x-text="looseLines ? 'ON' : 'OFF'"></span>
                    </div>
                    <p class="text-[10px] text-stone-500 mt-0.5">Perlebar jarak baris bacaan</p>
                </div>
            </div>
            <button @click="toggleLooseLines()"
                type="button"
                :class="looseLines ? 'bg-emerald-600 shadow-md shadow-emerald-600/30' : 'bg-stone-300 hover:bg-stone-400'"
                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                <span class="sr-only">Toggle Spasi Longgar</span>
                <span :class="looseLines ? 'translate-x-5' : 'translate-x-0'"
                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out flex items-center justify-center">
                    <template x-if="looseLines">
                        <x-lucide-check class="w-3 h-3 text-emerald-600 stroke-[3]" />
                    </template>
                </span>
            </button>
        </div>
    </div>
</div>

<script>
    function accessibilityMenu() {
        return {
            open: false,
            fontScale: parseInt(localStorage.getItem('a11y-font-scale')) || 100,
            highContrast: localStorage.getItem('a11y-high-contrast') === 'true',
            looseLines: localStorage.getItem('a11y-loose-lines') === 'true',

            init() {
                this.applyAll();
            },

            updateFontScale() {
                localStorage.setItem('a11y-font-scale', this.fontScale);
                this.applyFontScale();
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
                this.fontScale = 100;
                this.highContrast = false;
                this.looseLines = false;
                localStorage.removeItem('a11y-font-scale');
                localStorage.removeItem('a11y-high-contrast');
                localStorage.removeItem('a11y-loose-lines');
                this.applyAll();
            },

            applyAll() {
                this.applyFontScale();
                this.applyHighContrast();
                this.applyLooseLines();
            },

            applyFontScale() {
                if (this.fontScale == 100) {
                    document.documentElement.style.fontSize = '';
                } else {
                    document.documentElement.style.fontSize = this.fontScale + '%';
                }
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
