@if (request()->routeIs('*.dashboard') || request()->routeIs('dashboard'))
    <div 
        x-data="{
            audio: null,
            isPlaying: false,
            isMuted: false,
            isDisabled: false,
            isMinimized: false,
            volume: 0.15,
            surahTitle: 'Surah Al-Fatihah',
            reciter: 'Misyari Rasyid Al-Afasy',
            audioSrc: '{{ asset('audio/murottal.mp3') }}',
            fallbackSrc: 'https://download.quranicaudio.com/quran/mishaari_raashid_al_3afaasee/001.mp3',
            initPlayer() {
                // Check user preference in localStorage
                if (localStorage.getItem('siakad_murottal_disabled') === 'true') {
                    this.isDisabled = true;
                    return;
                }

                this.audio = new Audio(this.audioSrc);
                this.audio.volume = this.volume;
                this.audio.loop = true;

                // Handle loading error by falling back to CDN
                this.audio.addEventListener('error', () => {
                    if (this.audio.src !== this.fallbackSrc) {
                        this.audio.src = this.fallbackSrc;
                        this.audio.load();
                        if (this.isPlaying) this.audio.play().catch(() => {});
                    }
                });

                // Attempt gentle autoplay on dashboard load
                const playPromise = this.audio.play();
                if (playPromise !== undefined) {
                    playPromise.then(() => {
                        this.isPlaying = true;
                    }).catch(() => {
                        // Browser autoplay policy blocked unmuted audio: start on first user interaction
                        this.isPlaying = false;
                        const startOnInteraction = () => {
                            if (!this.isDisabled && !this.isPlaying && this.audio) {
                                this.audio.play().then(() => {
                                    this.isPlaying = true;
                                }).catch(() => {});
                            }
                        };
                        document.addEventListener('click', startOnInteraction, { once: true });
                        document.addEventListener('keydown', startOnInteraction, { once: true });
                    });
                }
            },
            togglePlay() {
                if (!this.audio) {
                    this.initPlayer();
                }
                if (this.isPlaying) {
                    this.audio.pause();
                    this.isPlaying = false;
                } else {
                    this.audio.play().then(() => {
                        this.isPlaying = true;
                        this.isDisabled = false;
                        localStorage.removeItem('siakad_murottal_disabled');
                    }).catch(() => {});
                }
            },
            turnOff() {
                if (this.audio) {
                    this.audio.pause();
                    this.audio.currentTime = 0;
                }
                this.isPlaying = false;
                this.isDisabled = true;
                localStorage.setItem('siakad_murottal_disabled', 'true');
            },
            reEnable() {
                this.isDisabled = false;
                localStorage.removeItem('siakad_murottal_disabled');
                if (!this.audio) {
                    this.initPlayer();
                } else {
                    this.audio.play().then(() => {
                        this.isPlaying = true;
                    }).catch(() => {});
                }
            },
            updateVolume(val) {
                this.volume = parseFloat(val);
                if (this.audio) {
                    this.audio.volume = this.volume;
                }
            }
        }"
        x-init="initPlayer()"
        class="fixed bottom-22 right-6 sm:bottom-6 sm:left-6 lg:left-72 sm:right-auto z-40 select-none"
    >
        <!-- Floating Active Player Widget (Full Card) -->
        <div 
            x-show="!isDisabled && !isMinimized"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="bg-white/95 backdrop-blur-md border border-emerald-200 rounded-2xl shadow-xl p-3 sm:p-3.5 flex items-center gap-3 text-xs max-w-[calc(100vw-3rem)] sm:max-w-sm w-full"
            style="display: none;"
        >
            <!-- Animated Soundwave or Quran Icon -->
            <div 
                @click="togglePlay()" 
                class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center shrink-0 shadow-md cursor-pointer hover:scale-105 transition"
                title="Klik untuk Jeda/Putar"
            >
                <template x-if="isPlaying">
                    <div class="flex items-end gap-0.5 h-4">
                        <span class="w-1 bg-white rounded-full animate-[soundWave_0.8s_ease-in-out_infinite]"></span>
                        <span class="w-1 bg-white rounded-full animate-[soundWave_1.1s_ease-in-out_infinite_0.2s]"></span>
                        <span class="w-1 bg-white rounded-full animate-[soundWave_0.9s_ease-in-out_infinite_0.4s]"></span>
                    </div>
                </template>
                <template x-if="!isPlaying">
                    <x-lucide-volume-x class="w-5 h-5 text-emerald-100" />
                </template>
            </div>

            <!-- Recitation Title & Subtitle -->
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-1.5">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse" x-show="isPlaying"></span>
                    <span class="font-extrabold text-stone-900 text-xs truncate" x-text="surahTitle"></span>
                </div>
                <p class="text-[10px] text-stone-500 font-medium truncate" x-text="reciter"></p>
                
                <!-- Volume Mini Indicator -->
                <div class="flex items-center gap-2 mt-1">
                    <x-lucide-volume-1 class="w-3 h-3 text-stone-400 shrink-0" />
                    <input 
                        type="range" 
                        min="0" 
                        max="0.5" 
                        step="0.01" 
                        :value="volume" 
                        @input="updateVolume($event.target.value)"
                        class="w-20 h-1 bg-stone-200 rounded-lg appearance-none cursor-pointer accent-emerald-600"
                        title="Atur Volume Suara"
                    />
                    <span class="text-[9px] text-stone-400 font-bold" x-text="Math.round(volume * 200) + '%'"></span>
                </div>
            </div>

            <!-- Controls: Play/Pause, Minimize & Turn Off -->
            <div class="flex items-center gap-1 shrink-0 border-l border-stone-200/80 pl-2">
                <!-- Play / Pause Button -->
                <button 
                    type="button" 
                    @click="togglePlay()"
                    class="p-1.5 rounded-lg hover:bg-stone-100 text-stone-700 hover:text-emerald-700 transition"
                    :title="isPlaying ? 'Jeda Lantunan' : 'Putar Lantunan'"
                >
                    <template x-if="isPlaying">
                        <x-lucide-pause class="w-4 h-4" />
                    </template>
                    <template x-if="!isPlaying">
                        <x-lucide-play class="w-4 h-4 fill-current" />
                    </template>
                </button>

                <!-- Minimize Button -->
                <button 
                    type="button" 
                    @click="isMinimized = true"
                    class="p-1.5 rounded-lg hover:bg-stone-100 text-stone-400 hover:text-stone-700 transition"
                    title="Kecilkan Tampilan"
                >
                    <x-lucide-minimize-2 class="w-4 h-4" />
                </button>

                <!-- Matikan Button -->
                <button 
                    type="button" 
                    @click="turnOff()"
                    class="p-1.5 rounded-lg hover:bg-rose-50 text-stone-400 hover:text-rose-600 transition"
                    title="Matikan Lantunan Suci"
                >
                    <x-lucide-power class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Floating Minimized Pill Widget (When playing/paused but minimized) -->
        <div 
            x-show="!isDisabled && isMinimized"
            x-transition:enter="transition ease-out duration-200 transform"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150 transform"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="bg-white/95 backdrop-blur-md border border-emerald-200 rounded-full shadow-lg p-1.5 pl-3 pr-2 flex items-center gap-2 text-xs"
            style="display: none;"
        >
            <div class="flex items-center gap-1.5 cursor-pointer" @click="togglePlay()" title="Klik untuk Jeda/Putar">
                <template x-if="isPlaying">
                    <div class="flex items-end gap-0.5 h-3">
                        <span class="w-0.5 bg-emerald-600 rounded-full animate-[soundWave_0.8s_ease-in-out_infinite]"></span>
                        <span class="w-0.5 bg-emerald-600 rounded-full animate-[soundWave_1.1s_ease-in-out_infinite_0.2s]"></span>
                        <span class="w-0.5 bg-emerald-600 rounded-full animate-[soundWave_0.9s_ease-in-out_infinite_0.4s]"></span>
                    </div>
                </template>
                <template x-if="!isPlaying">
                    <x-lucide-volume-x class="w-3.5 h-3.5 text-stone-400" />
                </template>
                <span class="font-bold text-stone-800 text-[11px] truncate max-w-[110px] sm:max-w-[140px]" x-text="surahTitle"></span>
            </div>
            <button 
                type="button" 
                @click="togglePlay()"
                class="p-1 rounded-full hover:bg-stone-100 text-stone-700 hover:text-emerald-700 transition"
                :title="isPlaying ? 'Jeda' : 'Putar'"
            >
                <template x-if="isPlaying"><x-lucide-pause class="w-3.5 h-3.5" /></template>
                <template x-if="!isPlaying"><x-lucide-play class="w-3.5 h-3.5 fill-current" /></template>
            </button>
            <button 
                type="button" 
                @click="isMinimized = false" 
                class="p-1 rounded-full hover:bg-stone-100 text-stone-400 hover:text-stone-700 transition" 
                title="Perbesar Pemutar"
            >
                <x-lucide-maximize-2 class="w-3.5 h-3.5" />
            </button>
            <button 
                type="button" 
                @click="turnOff()" 
                class="p-1 rounded-full hover:bg-rose-50 text-stone-400 hover:text-rose-600 transition" 
                title="Matikan"
            >
                <x-lucide-power class="w-3.5 h-3.5" />
            </button>
        </div>

        <!-- Re-enable Small Floating Button (When turned off / disabled) -->
        <div 
            x-show="isDisabled"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            style="display: none;"
        >
            <button 
                type="button" 
                @click="reEnable()"
                class="flex items-center gap-1.5 px-3 py-2 bg-emerald-600/90 hover:bg-emerald-700 text-white rounded-full shadow-lg text-[11px] font-bold backdrop-blur-xs transition hover:scale-105"
                title="Buka & Putar Kembali Lantunan Ayat Suci"
            >
                <x-lucide-volume-2 class="w-3.5 h-3.5" />
                <span>Lantunan Al-Qur'an</span>
            </button>
        </div>
    </div>

    <!-- Soundwave animation keyframes -->
    <style>
        @keyframes soundWave {
            0%, 100% { height: 4px; }
            50% { height: 16px; }
        }
    </style>
@endif
