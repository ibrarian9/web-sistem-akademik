@if (request()->routeIs('*.dashboard') || request()->routeIs('dashboard'))
    <div 
        x-data="{
            audio: null,
            isPlaying: false,
            isMuted: false,
            isDisabled: false,
            isMinimized: true,
            volume: 0.7,
            surahTitle: 'Surah Al-Fatihah',
            surahName: 'Al-Fatihah',
            ayahNumber: 1,
            reciter: 'Misyari Rasyid Al-Afasy',
            arabicText: 'بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ',
            translation: 'Dengan nama Allah Yang Maha Pengasih, Maha Penyayang.',
            audioSrc: '{{ asset('audio/murottal.mp3') }}',
            fallbackSrc: 'https://download.quranicaudio.com/quran/mishaari_raashid_al_3afaasee/001.mp3',
            isLoadingAyah: false,

            // Koleksi ayat pilihan dari berbagai surat Al-Qur'an (Acak Surat & Acak Ayat)
            curatedAyahs: [
                {
                    surahNumber: 1,
                    surahName: 'Al-Fatihah',
                    ayahNumber: 1,
                    arabic: 'بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ',
                    translation: 'Dengan nama Allah Yang Maha Pengasih, Maha Penyayang.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/1.mp3'
                },
                {
                    surahNumber: 2,
                    surahName: 'Al-Baqarah',
                    ayahNumber: 255,
                    arabic: 'ٱللَّهُ لَآ إِلَٰهَ إِلَّا هُوَ ٱلْحَىُّ ٱلْقَيُّومُ ۚ لَا تَأْخُذُهُۥ سِنَةٌۭ وَلَا نَوْمٌۭ',
                    translation: 'Allah, tidak ada tuhan selain Dia. Yang Mahahidup, Yang terus-menerus mengurus makhluk-Nya.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/262.mp3'
                },
                {
                    surahNumber: 2,
                    surahName: 'Al-Baqarah',
                    ayahNumber: 286,
                    arabic: 'لَا يُكَلِّفُ ٱللَّهُ نَفْسًا إِلَّا وُسْعَهَا ۚ لَهَا مَا كَسَبَتْ وَعَلَيْهَا مَا ٱكْتَسَبَتْ',
                    translation: 'Allah tidak membebani seseorang melainkan sesuai dengan kesanggupannya.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/293.mp3'
                },
                {
                    surahNumber: 3,
                    surahName: 'Ali \'Imran',
                    ayahNumber: 159,
                    arabic: 'فَبِمَا رَحْمَةٍۢ مِّنَ ٱللَّهِ لِنتَ لَهُمْ ۖ وَلَوْ كُنتَ فَظًّا غَلِيظَ ٱلْقَلْبِ لَٱنفَضُّوا۟ مِنْ حَوْلِكَ',
                    translation: 'Maka berkat rahmat Allah engkau (Muhammad) berlaku lemah lembut terhadap mereka.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/452.mp3'
                },
                {
                    surahNumber: 14,
                    surahName: 'Ibrahim',
                    ayahNumber: 7,
                    arabic: 'وَإِذْ تَأَذَّنَ رَبُّكُمْ لَئِن شَكَرْتُمْ لَأَزِيدَنَّكُمْ ۖ وَلَئِن كَفَرْتُمْ إِنَّ عَذَابِى لَشَدِيدٌ',
                    translation: 'Jika kamu bersyukur, pasti Kami akan menambah (nikmat) kepadamu.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/1757.mp3'
                },
                {
                    surahNumber: 17,
                    surahName: 'Al-Isra\'',
                    ayahNumber: 82,
                    arabic: 'وَنُنَزِّلُ مِنَ ٱلْقُرْءَانِ مَا هُوَ شِفَآءٌۭ وَرَحْمَةٌۭ لِّلْمُؤْمِنِينَ',
                    translation: 'Dan Kami turunkan dari Al-Qur\'an (suatu) yang menjadi penawar dan rahmat bagi orang-orang yang beriman.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/2111.mp3'
                },
                {
                    surahNumber: 18,
                    surahName: 'Al-Kahf',
                    ayahNumber: 10,
                    arabic: 'إِذْ أَوَى ٱلْفِتْيَةُ إِلَى ٱلْكَهْفِ فَقَالُوا۟ رَبَّنَآ ءَاتِنَا مِن لَّدُنكَ رَحْمَةًۭ وَهَيِّئْ لَنَا مِنْ أَمْرِنَا رَشَدًۭا',
                    translation: 'Wahai Tuhan kami, berikanlah rahmat kepada kami dari sisi-Mu dan sempurnakanlah petunjuk bagi kami.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/2150.mp3'
                },
                {
                    surahNumber: 20,
                    surahName: 'Ta-Ha',
                    ayahNumber: 114,
                    arabic: 'فَتَعَٰلَى ٱللَّهُ ٱلْمَلِكُ ٱلْحَقُّ ۗ وَقُل رَّبِّ زِدْنِى عِلْمًۭا',
                    translation: 'Maka Mahatinggi Allah, Raja yang sebenar-benarnya. Dan katakanlah: Ya Tuhanku, tambahkanlah ilmu kepadaku.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/2462.mp3'
                },
                {
                    surahNumber: 24,
                    surahName: 'An-Nur',
                    ayahNumber: 35,
                    arabic: 'ٱللَّهُ نُورُ ٱلسَّمَٰوَٰتِ وَٱلْأَرْضِ ۚ مَثَلُ نُورِهِۦ كَمِشْكَوٰةٍۢ فِيهَا مِصْبَاحٌ',
                    translation: 'Allah (pemberi) cahaya kepada langit dan bumi. Perumpamaan cahaya-Nya seperti sebuah lubang pelita.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/2826.mp3'
                },
                {
                    surahNumber: 25,
                    surahName: 'Al-Furqan',
                    ayahNumber: 74,
                    arabic: 'وَٱلَّذِينَ يَقُولُونَ رَبَّنَا هَبْ لَنَا مِنْ أَزْوَٰجِنَا وَذُرِّيَّٰتِنَا قُرَّةَ أَعْيُنٍۢ وَٱجْعَلْنَا لِلْمُتَّقِينَ إِمَامًا',
                    translation: 'Ya Tuhan kami, anugerahkanlah kepada kami pasangan dan keturunan kami sebagai penyenang hati.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/2929.mp3'
                },
                {
                    surahNumber: 31,
                    surahName: 'Luqman',
                    ayahNumber: 17,
                    arabic: 'يَٰبُنَىَّ أَقِمِ ٱلصَّلَوٰةَ وَأْمُرْ بِٱلْمَعْرُوفِ وَٱنْهَ عَنِ ٱلْمُنكَرِ وَٱصْبِرْ عَلَىٰ مَآ أَصَابَكَ',
                    translation: 'Wahai anakku, laksanakanlah salat, suruhlah berbuat yang makruf dan cegahlah dari yang mungkar.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/3506.mp3'
                },
                {
                    surahNumber: 36,
                    surahName: 'Ya-Sin',
                    ayahNumber: 58,
                    arabic: 'سَلَٰمٌۭ قَوْلًۭا مِّن رَّبٍّۢ رَّحِيمٍۢ',
                    translation: 'Salam, sebagai ucapan selamat dari Tuhan Yang Maha Penyayang.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/3763.mp3'
                },
                {
                    surahNumber: 39,
                    surahName: 'Az-Zumar',
                    ayahNumber: 53,
                    arabic: 'قُلْ يَٰعِبَادِىَ ٱلَّذِينَ أَسْرَفُوا۟ عَلَىٰٓ أَنفُسِهِمْ لَا تَقْنَطُوا۟ مِن رَّحْمَةِ ٱللَّهِ',
                    translation: 'Wahai hamba-hamba-Ku yang melampaui batas! Janganlah kamu berputus asa dari rahmat Allah.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/4111.mp3'
                },
                {
                    surahNumber: 49,
                    surahName: 'Al-Hujurat',
                    ayahNumber: 10,
                    arabic: 'إِنَّمَا ٱلْمُؤْمِنُونَ إِخْوَةٌۭ فَأَصْلِحُوا۟ بَيْنَ أَخَوَيْكُمْ ۚ وَٱتَّقُوا۟ ٱللَّهَ لَعَلَّكُمْ تُرْحَمُونَ',
                    translation: 'Sesungguhnya orang-orang mukmin itu bersaudara, karena itu damaikanlah antara kedua saudaramu.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/4622.mp3'
                },
                {
                    surahNumber: 55,
                    surahName: 'Ar-Rahman',
                    ayahNumber: 13,
                    arabic: 'فَبِأَىِّ ءَالَآءِ رَبِّكُمَا تُكَذِّبَانِ',
                    translation: 'Maka nikmat Tuhanmu yang manakah yang kamu dustakan?',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/4914.mp3'
                },
                {
                    surahNumber: 58,
                    surahName: 'Al-Mujadilah',
                    ayahNumber: 11,
                    arabic: 'يَرْفَعِ ٱللَّهُ ٱلَّذِينَ ءَامَنُوا۟ مِنكُمْ وَٱلَّذِينَ أُوتُوا۟ ٱلْعِلْمَ دَرَجَٰتٍۢ',
                    translation: 'Allah akan meninggikan orang-orang yang beriman di antaramu dan orang-orang yang diberi ilmu pengetahuan beberapa derajat.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/5115.mp3'
                },
                {
                    surahNumber: 67,
                    surahName: 'Al-Mulk',
                    ayahNumber: 1,
                    arabic: 'تَبَٰرَكَ ٱلَّذِى بِيَدِهِ ٱلْمُلْكُ وَهُوَ عَلَىٰ كُلِّ شَىْءٍۢ قَدِيرٌ',
                    translation: 'Mahasuci Allah yang menguasai (segala) kerajaan, dan Dia Mahakuasa atas segala sesuatu.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/5242.mp3'
                },
                {
                    surahNumber: 93,
                    surahName: 'Adh-Dhuha',
                    ayahNumber: 5,
                    arabic: 'وَلَسَوْفَ يُعْطِيكَ رَبُّكَ فَتَرْضَىٰٓ',
                    translation: 'Dan kelak Tuhanmu pasti memberikan karunia-Nya kepadamu, sehingga engkau menjadi puas.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/6083.mp3'
                },
                {
                    surahNumber: 94,
                    surahName: 'Al-Insyirah',
                    ayahNumber: 5,
                    arabic: 'فَإِنَّ مَعَ ٱلْعُسْرِ يُسْرًا',
                    translation: 'Maka sesungguhnya beserta kesulitan ada kemudahan.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/6091.mp3'
                },
                {
                    surahNumber: 95,
                    surahName: 'At-Tin',
                    ayahNumber: 4,
                    arabic: 'لَقَدْ خَلَقْنَا ٱلْإِنسَٰنَ فِىٓ أَحْسَنِ تَقْوِيمٍۢ',
                    translation: 'Sungguh, Kami telah menciptakan manusia dalam bentuk yang sebaik-baiknya.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/6102.mp3'
                },
                {
                    surahNumber: 97,
                    surahName: 'Al-Qadr',
                    ayahNumber: 1,
                    arabic: 'إِنَّآ أَنزَلْنَٰهُ فِى لَيْلَةِ ٱلْقَدْرِ',
                    translation: 'Sesungguhnya Kami telah menurunkannya (Al-Qur\'an) pada malam qadar.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/6126.mp3'
                },
                {
                    surahNumber: 103,
                    surahName: 'Al-\'Asr',
                    ayahNumber: 1,
                    arabic: 'وَٱلْعَصْرِ ۙ إِنَّ ٱلْإِنسَٰنَ لَفِى خُسْرٍ',
                    translation: 'Demi masa. Sungguh, manusia berada dalam kerugian, kecuali orang-orang yang beriman.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/6171.mp3'
                },
                {
                    surahNumber: 108,
                    surahName: 'Al-Kautsar',
                    ayahNumber: 1,
                    arabic: 'إِنَّآ أَعْطَيْنَٰكَ ٱلْكَوْثَرَ ۙ فَصَلِّ لِرَبِّكَ وَٱنْحَرْ',
                    translation: 'Sungguh, Kami telah memberimu (Muhammad) nikmat yang banyak.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/6191.mp3'
                },
                {
                    surahNumber: 112,
                    surahName: 'Al-Ikhlas',
                    ayahNumber: 1,
                    arabic: 'قُلْ هُوَ ٱللَّهُ أَحَدٌ ۚ ٱللَّهُ ٱلصَّمَدُ',
                    translation: 'Katakanlah (Muhammad): Dialah Allah, Yang Maha Esa. Allah tempat meminta segala sesuatu.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/6221.mp3'
                },
                {
                    surahNumber: 113,
                    surahName: 'Al-Falaq',
                    ayahNumber: 1,
                    arabic: 'قُلْ أَعُوذُ بِرَبِّ ٱلْفَلَقِ',
                    translation: 'Katakanlah: Aku berlindung kepada Tuhan yang menguasai subuh (fajar).',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/6225.mp3'
                },
                {
                    surahNumber: 114,
                    surahName: 'An-Nas',
                    ayahNumber: 1,
                    arabic: 'قُلْ أَعُوذُ بِرَبِّ ٱلنَّاسِ ۙ مَلِكِ ٱلنَّاسِ',
                    translation: 'Katakanlah: Aku berlindung kepada Tuhannya manusia, Raja manusia.',
                    audio: 'https://cdn.islamic.network/quran/audio/128/ar.alafasy/6231.mp3'
                }
            ],

            initPlayer() {
                // Pilih surat dan ayat acak saat komponen dimuat (tampilan teks saja, tanpa putar audio otomatis)
                this.pickRandomAyah(false);

                // Check user preference in localStorage
                if (localStorage.getItem('siakad_murottal_disabled') === 'true') {
                    this.isDisabled = true;
                    return;
                }

                this.setupAudio();
                this.isPlaying = false;
                this.broadcastState();
            },

            setupAudio() {
                if (this.audio) {
                    this.audio.pause();
                }

                this.audio = new Audio(this.audioSrc);
                this.audio.volume = this.volume;

                // Handle loading error by falling back to CDN or local audio
                this.audio.addEventListener('error', () => {
                    if (this.audio.src !== this.fallbackSrc) {
                        this.audio.src = this.fallbackSrc;
                        this.audio.load();
                        if (this.isPlaying) this.audio.play().catch(() => {});
                    }
                });

                // Saat ayat selesai dilantunkan, hentikan pemutaran (tidak berputar terus-menerus tanpa henti)
                this.audio.addEventListener('ended', () => {
                    this.isPlaying = false;
                    this.broadcastState();
                });
            },

            pickRandomAyah(shouldPlay = false) {
                // 1. Pilih acak dari koleksi kurasi internal
                const randomIndex = Math.floor(Math.random() * this.curatedAyahs.length);
                const item = this.curatedAyahs[randomIndex];

                this.surahName = item.surahName;
                this.ayahNumber = item.ayahNumber;
                this.surahTitle = 'Surah ' + item.surahName + ' : Ayat ' + item.ayahNumber;
                this.arabicText = item.arabic;
                this.translation = item.translation;
                this.audioSrc = item.audio;

                if (this.audio) {
                    this.audio.src = this.audioSrc;
                    this.audio.load();
                    if (shouldPlay) {
                        this.audio.play().then(() => {
                            this.isPlaying = true;
                            this.broadcastState();
                        }).catch(() => {});
                    } else if (!this.isPlaying) {
                        this.isPlaying = false;
                    }
                }

                this.broadcastState();

                // 2. Coba fetch acak dinamis dari Quran Cloud API (6236 ayat) secara asynchronous jika online
                this.fetchRandomOnline(shouldPlay);
            },

            async fetchRandomOnline(shouldPlay = false) {
                try {
                    const randomGlobal = Math.floor(Math.random() * 6236) + 1;
                    const res = await fetch(`https://api.alquran.cloud/v1/ayah/${randomGlobal}/editions/ar.alafasy,id.indonesian`);
                    if (!res.ok) return;
                    const json = await res.json();
                    if (json.code === 200 && json.data && json.data.length >= 2) {
                        const arData = json.data[0];
                        const idData = json.data[1];

                        this.surahName = arData.surah.englishName;
                        this.ayahNumber = arData.numberInSurah;
                        this.surahTitle = 'Surah ' + arData.surah.englishName + ' : Ayat ' + arData.numberInSurah;
                        this.arabicText = arData.text;
                        this.translation = idData.text;
                        this.audioSrc = arData.audio || `https://cdn.islamic.network/quran/audio/128/ar.alafasy/${randomGlobal}.mp3`;

                        if (this.audio) {
                            const wasPlaying = this.isPlaying;
                            this.audio.src = this.audioSrc;
                            this.audio.load();
                            if (shouldPlay || wasPlaying) {
                                this.audio.play().then(() => {
                                    this.isPlaying = true;
                                    this.broadcastState();
                                }).catch(() => {});
                            }
                        }
                        this.broadcastState();
                    }
                } catch (e) {
                    // Gunakan curated fallback yang sudah aktif
                }
            },

            nextRandomAyah(shouldPlay = null) {
                const play = (shouldPlay !== null) ? shouldPlay : this.isPlaying;
                this.pickRandomAyah(play);
            },

            broadcastState() {
                window.dispatchEvent(new CustomEvent('quran-audio-state', {
                    detail: {
                        surahTitle: this.surahTitle,
                        surahName: this.surahName,
                        ayahNumber: this.ayahNumber,
                        arabicText: this.arabicText,
                        translation: this.translation,
                        isPlaying: this.isPlaying,
                        isDisabled: this.isDisabled
                    }
                }));
            },

            togglePlay() {
                if (!this.audio) {
                    this.setupAudio();
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
                this.broadcastState();
            },

            turnOff() {
                if (this.audio) {
                    this.audio.pause();
                    this.audio.currentTime = 0;
                }
                this.isPlaying = false;
                this.isDisabled = true;
                localStorage.setItem('siakad_murottal_disabled', 'true');
                this.broadcastState();
            },

            reEnable() {
                this.isDisabled = false;
                localStorage.removeItem('siakad_murottal_disabled');
                if (!this.audio) {
                    this.setupAudio();
                }
                this.audio.play().then(() => {
                    this.isPlaying = true;
                    this.broadcastState();
                }).catch(() => {});
            },

            updateVolume(val) {
                this.volume = parseFloat(val);
                if (this.audio) {
                    this.audio.volume = this.volume;
                }
            }
        }"
        x-init="initPlayer()"
        @quran-play-toggle.window="togglePlay()"
        @quran-play-random.window="nextRandomAyah(isPlaying)"
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
            class="bg-white/95 backdrop-blur-md border border-emerald-200 rounded-2xl shadow-xl p-3 sm:p-3.5 flex items-center gap-3 text-xs max-w-[calc(100vw-3rem)] sm:max-w-md w-full"
            style="display: none;"
        >
            <!-- Animated Soundwave or Quran Icon -->
            <div 
                @click="togglePlay()" 
                class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center shrink-0 shadow-xs cursor-pointer transition"
                title="Klik untuk Jeda atau Putar"
            >
                <template x-if="isPlaying">
                    <div class="flex items-end gap-0.5 h-4">
                        <span class="w-1 bg-white rounded-full animate-[soundWave_0.8s_ease-in-out_infinite]"></span>
                        <span class="w-1 bg-white rounded-full animate-[soundWave_1.1s_ease-in-out_infinite_0.2s]"></span>
                        <span class="w-1 bg-white rounded-full animate-[soundWave_0.9s_ease-in-out_infinite_0.4s]"></span>
                    </div>
                </template>
                <template x-if="!isPlaying">
                    <x-lucide-volume-2 class="w-4 h-4 text-emerald-100" />
                </template>
            </div>

            <!-- Recitation Title & Subtitle -->
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-1.5">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse" x-show="isPlaying"></span>
                    <span class="font-extrabold text-stone-900 text-xs truncate" x-text="surahTitle">Surah Al-Fatihah</span>
                </div>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <p class="text-[10px] text-stone-500 font-medium truncate" x-text="reciter"></p>
                    <span class="text-[9px] px-1 py-0.2 bg-emerald-100 text-emerald-800 font-bold rounded">Acak</span>
                </div>
                
                <!-- Volume Mini Indicator -->
                <div class="flex items-center gap-2 mt-1">
                    <x-lucide-volume-2 class="w-3.5 h-3.5 text-stone-400 shrink-0" />
                    <input 
                        type="range" 
                        min="0" 
                        max="1" 
                        step="0.05" 
                        :value="volume" 
                        @input="updateVolume($event.target.value)"
                        class="w-16 sm:w-20 h-1 bg-stone-200 rounded-lg appearance-none cursor-pointer accent-emerald-600"
                        title="Atur Volume Suara"
                    />
                    <span class="text-[9px] text-stone-400 font-bold" x-text="Math.round(volume * 100) + '%'"></span>
                </div>
            </div>

            <!-- Controls: Play/Pause, Shuffle Next Random, Minimize & Turn Off -->
            <div class="flex items-center gap-1 shrink-0 border-l border-stone-200/80 pl-2">
                <!-- Play / Pause Button -->
                <button 
                    type="button" 
                    @click="togglePlay()"
                    class="p-1.5 rounded-lg hover:bg-stone-100 text-stone-700 hover:text-emerald-700 transition cursor-pointer"
                    :title="isPlaying ? 'Jeda Lantunan' : 'Putar Lantunan'"
                >
                    <template x-if="isPlaying">
                        <x-lucide-pause class="w-4 h-4" />
                    </template>
                    <template x-if="!isPlaying">
                        <x-lucide-play class="w-4 h-4 fill-current" />
                    </template>
                </button>

                <!-- Shuffle / Next Random Ayah & Surah Button -->
                <button 
                    type="button" 
                    @click="nextRandomAyah(isPlaying)"
                    class="p-1.5 rounded-lg hover:bg-emerald-50 text-stone-500 hover:text-emerald-700 transition cursor-pointer"
                    title="Ganti Ayat & Surat Acak Lainnya"
                >
                    <x-lucide-shuffle class="w-4 h-4" />
                </button>

                <!-- Minimize Button -->
                <button 
                    type="button" 
                    @click="isMinimized = true"
                    class="p-1.5 rounded-lg hover:bg-stone-100 text-stone-400 hover:text-stone-700 transition cursor-pointer"
                    title="Kecilkan Tampilan"
                >
                    <x-lucide-minimize-2 class="w-4 h-4" />
                </button>

                <!-- Matikan Button -->
                <button 
                    type="button" 
                    @click="turnOff()"
                    class="p-1.5 rounded-lg hover:bg-rose-50 text-stone-400 hover:text-rose-600 transition cursor-pointer"
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
            <div class="flex items-center gap-1.5 cursor-pointer" @click="togglePlay()" title="Klik untuk Jeda atau Putar">
                <template x-if="isPlaying">
                    <div class="flex items-end gap-0.5 h-3">
                        <span class="w-0.5 bg-emerald-600 rounded-full animate-[soundWave_0.8s_ease-in-out_infinite]"></span>
                        <span class="w-0.5 bg-emerald-600 rounded-full animate-[soundWave_1.1s_ease-in-out_infinite_0.2s]"></span>
                        <span class="w-0.5 bg-emerald-600 rounded-full animate-[soundWave_0.9s_ease-in-out_infinite_0.4s]"></span>
                    </div>
                </template>
                <template x-if="!isPlaying">
                    <x-lucide-volume-2 class="w-3.5 h-3.5 text-emerald-600" />
                </template>
                <span class="font-bold text-stone-800 text-[11px] truncate max-w-[110px] sm:max-w-[140px]" x-text="surahTitle">Surah Al-Fatihah</span>
            </div>
            <button 
                type="button" 
                @click="togglePlay()"
                class="p-1 rounded-full hover:bg-stone-100 text-stone-700 hover:text-emerald-700 transition cursor-pointer"
                :title="isPlaying ? 'Jeda' : 'Putar'"
            >
                <template x-if="isPlaying"><x-lucide-pause class="w-3.5 h-3.5" /></template>
                <template x-if="!isPlaying"><x-lucide-play class="w-3.5 h-3.5 fill-current" /></template>
            </button>
            <button 
                type="button" 
                @click="nextRandomAyah(isPlaying)" 
                class="p-1 rounded-full hover:bg-emerald-50 text-stone-500 hover:text-emerald-700 transition cursor-pointer" 
                title="Ganti Ayat & Surat Acak"
            >
                <x-lucide-shuffle class="w-3.5 h-3.5" />
            </button>
            <button 
                type="button" 
                @click="isMinimized = false" 
                class="p-1 rounded-full hover:bg-stone-100 text-stone-400 hover:text-stone-700 transition cursor-pointer" 
                title="Perbesar Pemutar"
            >
                <x-lucide-maximize-2 class="w-3.5 h-3.5" />
            </button>
            <button 
                type="button" 
                @click="turnOff()" 
                class="p-1 rounded-full hover:bg-rose-50 text-stone-400 hover:text-rose-600 transition cursor-pointer" 
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
                class="flex items-center gap-1.5 px-3 py-2 bg-emerald-600/90 hover:bg-emerald-700 text-white rounded-full shadow-lg text-[11px] font-bold backdrop-blur-xs transition cursor-pointer"
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
