<!-- Alpine.js Auto-Complete Component Logic -->
<script>
    document.addEventListener('alpine:init', () => {
        if (!window.QURAN_AISAR_DATA) {
            window.QURAN_AISAR_DATA = [
                { no: 'A1', name: "Aisar Jilid 1", info: "Program Aisar Jilid 1", isAisar: true },
                { no: 'A2', name: "Aisar Jilid 2", info: "Program Aisar Jilid 2", isAisar: true },
                { no: 'A3', name: "Aisar Jilid 3", info: "Program Aisar Jilid 3", isAisar: true },
                { no: 'A4', name: "Aisar Jilid 4", info: "Program Aisar Jilid 4", isAisar: true },
                { no: 'AK', name: "Kitab Aisar", info: "Kitab Panduan Tajwid Aisar", isAisar: true },
            ];
        }

        if (!window.QURAN_SURAHS_DATA) {
            window.QURAN_SURAHS_DATA = [
                { no: 1, name: "Al-Fatihah", info: "7 Ayat" },
                { no: 2, name: "Al-Baqarah", info: "286 Ayat" },
                { no: 3, name: "Ali 'Imran", info: "200 Ayat" },
                { no: 4, name: "An-Nisa'", info: "176 Ayat" },
                { no: 5, name: "Al-Ma'idah", info: "120 Ayat" },
                { no: 6, name: "Al-An'am", info: "165 Ayat" },
                { no: 7, name: "Al-A'raf", info: "206 Ayat" },
                { no: 8, name: "Al-Anfal", info: "75 Ayat" },
                { no: 9, name: "At-Taubah", info: "129 Ayat" },
                { no: 10, name: "Yunus", info: "109 Ayat" },
                { no: 11, name: "Hud", info: "123 Ayat" },
                { no: 12, name: "Yusuf", info: "111 Ayat" },
                { no: 13, name: "Ar-Ra'd", info: "43 Ayat" },
                { no: 14, name: "Ibrahim", info: "52 Ayat" },
                { no: 15, name: "Al-Hijr", info: "99 Ayat" },
                { no: 16, name: "An-Nahl", info: "128 Ayat" },
                { no: 17, name: "Al-Isra'", info: "111 Ayat" },
                { no: 18, name: "Al-Kahf", info: "110 Ayat" },
                { no: 19, name: "Maryam", info: "98 Ayat" },
                { no: 20, name: "Ta-Ha", info: "135 Ayat" },
                { no: 21, name: "Al-Anbiya'", info: "112 Ayat" },
                { no: 22, name: "Al-Hajj", info: "78 Ayat" },
                { no: 23, name: "Al-Mu'minun", info: "118 Ayat" },
                { no: 24, name: "An-Nur", info: "64 Ayat" },
                { no: 25, name: "Al-Furqan", info: "77 Ayat" },
                { no: 26, name: "Asy-Syu'ara'", info: "227 Ayat" },
                { no: 27, name: "An-Naml", info: "93 Ayat" },
                { no: 28, name: "Al-Qasas", info: "88 Ayat" },
                { no: 29, name: "Al-'Ankabut", info: "69 Ayat" },
                { no: 30, name: "Ar-Rum", info: "60 Ayat" },
                { no: 31, name: "Luqman", info: "34 Ayat" },
                { no: 32, name: "As-Sajdah", info: "30 Ayat" },
                { no: 33, name: "Al-Ahzab", info: "73 Ayat" },
                { no: 34, name: "Saba'", info: "54 Ayat" },
                { no: 35, name: "Fatir", info: "45 Ayat" },
                { no: 36, name: "Ya-Sin", info: "83 Ayat" },
                { no: 37, name: "As-Saffat", info: "182 Ayat" },
                { no: 38, name: "Sad", info: "88 Ayat" },
                { no: 39, name: "Az-Zumar", info: "75 Ayat" },
                { no: 40, name: "Ghafir", info: "85 Ayat" },
                { no: 41, name: "Fussilat", info: "54 Ayat" },
                { no: 42, name: "Asy-Syura", info: "53 Ayat" },
                { no: 43, name: "Az-Zukhruf", info: "89 Ayat" },
                { no: 44, name: "Ad-Dukhan", info: "59 Ayat" },
                { no: 45, name: "Al-Jasiyah", info: "37 Ayat" },
                { no: 46, name: "Al-Ahqaf", info: "35 Ayat" },
                { no: 47, name: "Muhammad", info: "38 Ayat" },
                { no: 48, name: "Al-Fath", info: "29 Ayat" },
                { no: 49, name: "Al-Hujurat", info: "18 Ayat" },
                { no: 50, name: "Qaf", info: "45 Ayat" },
                { no: 51, name: "Az-Zariyat", info: "60 Ayat" },
                { no: 52, name: "At-Tur", info: "49 Ayat" },
                { no: 53, name: "An-Najm", info: "62 Ayat" },
                { no: 54, name: "Al-Qamar", info: "55 Ayat" },
                { no: 55, name: "Ar-Rahman", info: "78 Ayat" },
                { no: 56, name: "Al-Waqi'ah", info: "96 Ayat" },
                { no: 57, name: "Al-Hadid", info: "29 Ayat" },
                { no: 58, name: "Al-Mujadilah", info: "22 Ayat" },
                { no: 59, name: "Al-Hasyr", info: "24 Ayat" },
                { no: 60, name: "Al-Mumtahanah", info: "13 Ayat" },
                { no: 61, name: "As-Saff", info: "14 Ayat" },
                { no: 62, name: "Al-Jumu'ah", info: "11 Ayat" },
                { no: 63, name: "Al-Munafiqun", info: "11 Ayat" },
                { no: 64, name: "At-Taghabun", info: "18 Ayat" },
                { no: 65, name: "At-Talaq", info: "12 Ayat" },
                { no: 66, name: "At-Tahrim", info: "12 Ayat" },
                { no: 67, name: "Al-Mulk", info: "30 Ayat" },
                { no: 68, name: "Al-Qalam", info: "52 Ayat" },
                { no: 69, name: "Al-Haqqah", info: "52 Ayat" },
                { no: 70, name: "Al-Ma'arij", info: "44 Ayat" },
                { no: 71, name: "Nuh", info: "28 Ayat" },
                { no: 72, name: "Al-Jinn", info: "28 Ayat" },
                { no: 73, name: "Al-Muzzammil", info: "20 Ayat" },
                { no: 74, name: "Al-Muddassir", info: "56 Ayat" },
                { no: 75, name: "Al-Qiyamah", info: "40 Ayat" },
                { no: 76, name: "Al-Insan", info: "31 Ayat" },
                { no: 77, name: "Al-Mursalat", info: "50 Ayat" },
                { no: 78, name: "An-Naba'", info: "40 Ayat" },
                { no: 79, name: "An-Nazi'at", info: "46 Ayat" },
                { no: 80, name: "'Abasa", info: "42 Ayat" },
                { no: 81, name: "At-Takwir", info: "29 Ayat" },
                { no: 82, name: "Al-Infitar", info: "19 Ayat" },
                { no: 83, name: "Al-Mutaffifin", info: "36 Ayat" },
                { no: 84, name: "Al-Insyiqaq", info: "25 Ayat" },
                { no: 85, name: "Al-Buruj", info: "22 Ayat" },
                { no: 86, name: "At-Tariq", info: "17 Ayat" },
                { no: 87, name: "Al-A'la", info: "19 Ayat" },
                { no: 88, name: "Al-Ghasyiyah", info: "26 Ayat" },
                { no: 89, name: "Al-Fajr", info: "30 Ayat" },
                { no: 90, name: "Al-Balad", info: "20 Ayat" },
                { no: 91, name: "Asy-Syams", info: "15 Ayat" },
                { no: 92, name: "Al-Lail", info: "21 Ayat" },
                { no: 93, name: "Ad-Duha", info: "11 Ayat" },
                { no: 94, name: "Asy-Syarh", info: "8 Ayat" },
                { no: 95, name: "At-Tin", info: "8 Ayat" },
                { no: 96, name: "Al-'Alaq", info: "19 Ayat" },
                { no: 97, name: "Al-Qadr", info: "5 Ayat" },
                { no: 98, name: "Al-Bayyinah", info: "8 Ayat" },
                { no: 99, name: "Az-Zalzalah", info: "8 Ayat" },
                { no: 100, name: "Al-'Adiyat", info: "11 Ayat" },
                { no: 101, name: "Al-Qari'ah", info: "11 Ayat" },
                { no: 102, name: "At-Takasur", info: "8 Ayat" },
                { no: 103, name: "Al-'Asr", info: "3 Ayat" },
                { no: 104, name: "Al-Humazah", info: "9 Ayat" },
                { no: 105, name: "Al-Fil", info: "5 Ayat" },
                { no: 106, name: "Quraisy", info: "4 Ayat" },
                { no: 107, name: "Al-Ma'un", info: "7 Ayat" },
                { no: 108, name: "Al-Kausar", info: "3 Ayat" },
                { no: 109, name: "Al-Kafirun", info: "6 Ayat" },
                { no: 110, name: "An-Nasr", info: "3 Ayat" },
                { no: 111, name: "Al-Lahab", info: "5 Ayat" },
                { no: 112, name: "Al-Ikhlas", info: "4 Ayat" },
                { no: 113, name: "Al-Falaq", info: "5 Ayat" },
                { no: 114, name: "An-Nas", info: "6 Ayat" }
            ];
            window.QURAN_JUZS_DATA = Array.from({ length: 30 }, (_, i) => ({
                no: i + 1,
                name: `Juz ${i + 1}`,
                info: `Juz ${i + 1}`,
                isJuz: true
            }));
        }

        Alpine.data('surahAutocomplete', (config) => ({
            wireField: config.wireField,
            includeJuz: config.includeJuz || false,
            open: false,
            highlightedIndex: 0,

            get currentVal() {
                return (this.$wire && this.wireField) ? (this.$wire.get(this.wireField) || '') : '';
            },

            get allItems() {
                let list = [...window.QURAN_AISAR_DATA, ...window.QURAN_SURAHS_DATA];
                if (this.includeJuz) {
                    list = [...window.QURAN_JUZS_DATA, ...list];
                }
                return list;
            },

            get filteredList() {
                const val = (this.currentVal || '').trim();
                if (!val) {
                    if (this.includeJuz) {
                        return [...window.QURAN_JUZS_DATA.slice(27, 30), ...window.QURAN_AISAR_DATA, ...window.QURAN_SURAHS_DATA.slice(77, 85)];
                    }
                    return [...window.QURAN_AISAR_DATA, window.QURAN_SURAHS_DATA[0], window.QURAN_SURAHS_DATA[1], ...window.QURAN_SURAHS_DATA.slice(77, 85)];
                }

                const q = val.toLowerCase().replace(/[^a-z0-9]/g, '');

                return this.allItems.filter(item => {
                    const normName = item.name.toLowerCase().replace(/[^a-z0-9]/g, '');
                    return normName.includes(q) || item.name.toLowerCase().includes(val.toLowerCase());
                }).slice(0, 10);
            },

            onInput(e) {
                this.open = true;
                this.highlightedIndex = 0;
            },

            onFocus() {
                this.open = true;
                this.highlightedIndex = 0;
            },

            toggleDropdown() {
                this.open = !this.open;
                this.highlightedIndex = 0;
            },

            navigateNext() {
                if (!this.open) { this.open = true; return; }
                if (this.highlightedIndex < this.filteredList.length - 1) {
                    this.highlightedIndex++;
                } else {
                    this.highlightedIndex = 0;
                }
            },

            navigatePrev() {
                if (!this.open) { this.open = true; return; }
                if (this.highlightedIndex > 0) {
                    this.highlightedIndex--;
                } else {
                    this.highlightedIndex = this.filteredList.length - 1;
                }
            },

            selectHighlighted() {
                if (this.open && this.filteredList.length > 0) {
                    this.chooseItem(this.filteredList[this.highlightedIndex].name);
                }
            },

            chooseItem(name) {
                if (this.$wire && this.wireField) {
                    this.$wire.set(this.wireField, name + ' ');
                }
                this.open = false;
                this.$nextTick(() => {
                    if (this.$refs.inputField) {
                        this.$refs.inputField.focus();
                        const len = this.$refs.inputField.value.length;
                        this.$refs.inputField.setSelectionRange(len, len);
                    }
                });
            }
        }));
    });
</script>
