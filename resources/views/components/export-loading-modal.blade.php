@props([
    'title' => 'Sedang Menyiapkan Dokumen...',
    'subtitle' => 'Sistem sedang memproses dan mengompilasi data sesuai filter aktif. Berkas akan terunduh otomatis sesaat lagi.',
])

<!-- Universal Export & Download Loading Modal with Cancel Option -->
<div 
    x-data="exportLoadingModal()"
    x-show="isExporting"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    @keydown.escape.window="if(isExporting) cancelExport()"
    class="fixed inset-0 z-[999999] flex items-center justify-center bg-stone-900/60 backdrop-blur-sm p-4 font-sans select-none"
    style="display: none;"
>
    <div 
        @click.outside="cancelExport()"
        class="bg-white rounded-3xl shadow-2xl border border-stone-200 p-6 sm:p-8 max-w-md w-full text-center space-y-5 animate-in fade-in zoom-in-95 duration-200"
    >
        <!-- Animated Icon Ring -->
        <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
            <!-- Pulsing outer ring -->
            <div class="absolute inset-0 rounded-full bg-emerald-100 animate-ping opacity-60"></div>
            <!-- Spinning gradient border ring -->
            <div class="w-20 h-20 rounded-full border-4 border-emerald-100 border-t-emerald-600 animate-spin"></div>
            <!-- Center icon -->
            <div class="absolute inset-0 flex items-center justify-center text-emerald-700">
                <x-lucide-file-down class="w-8 h-8 animate-bounce" />
            </div>
        </div>

        <!-- Text Description -->
        <div class="space-y-1.5">
            <h3 class="text-base font-extrabold text-stone-900" x-text="exportTitle">
                {{ $title }}
            </h3>
            <p class="text-xs text-stone-500 leading-relaxed" x-text="exportSubtitle">
                {{ $subtitle }}
            </p>
        </div>

        <!-- Progress Bar Indicator -->
        <div class="w-full bg-stone-100 h-2 rounded-full overflow-hidden relative">
            <div class="h-full bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-600 w-full animate-pulse"></div>
        </div>

        <!-- Cancel Button -->
        <div class="pt-2 flex items-center justify-center gap-2">
            <button 
                type="button" 
                @click="cancelExport()" 
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-stone-100 hover:bg-rose-50 text-stone-700 hover:text-rose-700 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer border border-stone-300 hover:border-rose-300 w-full sm:w-auto"
            >
                <x-lucide-x class="w-4 h-4 text-rose-500" />
                <span>Batalkan Proses</span>
            </button>
        </div>
    </div>
</div>

<script>
    (function() {
        const registerExportModal = () => {
            if (window.Alpine) {
                Alpine.data('exportLoadingModal', () => ({
                    isExporting: false,
                    exportTitle: '{{ $title }}',
                    exportSubtitle: '{{ $subtitle }}',
                    timer: null,

                    init() {
                        // Global click listener for export actions
                        document.addEventListener('click', (e) => {
                            const target = e.target.closest('a, button');
                            if (!target) return;

                            // If link opens in a new tab, the new tab handles its own loading.
                            // We do NOT show a lingering loading modal in the main window.
                            if (target.getAttribute('target') === '_blank') {
                                return;
                            }

                            const href = (target.getAttribute('href') || '').toLowerCase();
                            const wireClick = (target.getAttribute('wire:click') || '').toLowerCase();
                            const text = (target.textContent || '').toLowerCase();

                            const isPdf = href.includes('pdf') || wireClick.includes('pdf') || text.includes('pdf');
                            const isExcel = href.includes('excel') || href.includes('csv') || wireClick.includes('excel') || text.includes('excel');
                            const isSlip = href.includes('slip') || wireClick.includes('slip');

                            if (!isPdf && !isExcel && !isSlip) return;
                            if (href === '#' || href.startsWith('javascript:')) return;
                            if (target.hasAttribute('data-no-loading')) return;

                            if (isPdf) {
                                this.exportTitle = 'Sedang Menyusun Dokumen PDF...';
                            } else if (isExcel) {
                                this.exportTitle = 'Sedang Mengompilasi Berkas Excel...';
                            } else {
                                this.exportTitle = 'Sedang Menyiapkan Berkas Unduhan...';
                            }
                            this.exportSubtitle = 'Sistem sedang memproses data sesuai filter aktif. Pengunduhan akan dimulai secara otomatis.';

                            this.isExporting = true;

                            // Safety timeout fallback
                            clearTimeout(this.timer);
                            this.timer = setTimeout(() => {
                                this.isExporting = false;
                            }, 5000);
                        }, true);

                        // If user switches tab or window, dismiss modal
                        window.addEventListener('blur', () => {
                            this.isExporting = false;
                        });

                        // Livewire 3 commit lifecycle listener
                        if (window.Livewire) {
                            Livewire.hook('commit', ({ succeed, fail }) => {
                                succeed(() => {
                                    setTimeout(() => { this.isExporting = false; }, 300);
                                });
                                fail(() => {
                                    this.isExporting = false;
                                });
                            });
                        }
                    },

                    cancelExport() {
                        this.isExporting = false;
                        clearTimeout(this.timer);
                        if (window.stop) {
                            window.stop();
                        }
                    }
                }));
            }
        };

        if (window.Alpine) {
            registerExportModal();
        } else {
            document.addEventListener('alpine:init', registerExportModal);
        }
    })();
</script>
