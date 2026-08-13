<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-stone-50 text-stone-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name', 'SIAKAD') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <!-- MicroModal CDN -->
    <script src="https://cdn.jsdelivr.net/npm/micromodal/dist/micromodal.min.js"></script>
    <style>
        .modal { font-family: inherit; }
        .modal[aria-hidden="true"] { display: none; }
        .modal[aria-hidden="false"] { display: block; }
        .modal__overlay { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); }

        /* Smooth SPA Navigation Transitions (React/Vue Feel) */
        main {
            animation: fadeInPage 0.22s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeInPage {
            0% { opacity: 0.85; }
            100% { opacity: 1; }
        }

        /* Top Progress Bar Styling for Livewire wire:navigate & Requests */
        [wire\:navigate-progress-bar], .livewire-progress-bar, #nprogress .bar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            height: 3.5px !important;
            background: linear-gradient(90deg, #059669, #10b981, #f59e0b) !important;
            z-index: 99999 !important;
            box-shadow: 0 0 14px rgba(16, 185, 129, 0.9) !important;
        }
    </style>
</head>
<body class="h-full font-sans antialiased selection:bg-green-600 selection:text-white">
    <!-- Glowing Top Loading Bar above Navbar (Active on Livewire Actions & SPA Navigation) -->
    <div id="top-app-loader"
         x-data="{ isNavigating: false, isRequesting: false }"
         x-init="
            document.addEventListener('livewire:navigating', () => { isNavigating = true; });
            document.addEventListener('livewire:navigated', () => { isNavigating = false; });
            Livewire.hook('commit', ({ respond, succeed, fail }) => {
                isRequesting = true;
                succeed(() => { isRequesting = false; });
                fail(() => { isRequesting = false; });
            });
         "
         x-show="isNavigating || isRequesting"
         x-transition:enter="transition-all ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-x-0"
         x-transition:enter-end="opacity-100 scale-x-100"
         x-transition:leave="transition-all ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-x-100"
         x-transition:leave-end="opacity-0 scale-x-100"
         class="fixed top-0 left-0 right-0 z-[99999] h-[3.5px] bg-gradient-to-r from-emerald-500 via-teal-400 to-amber-400 shadow-[0_0_12px_rgba(16,185,129,0.9)] origin-left animate-pulse pointer-events-none"
         style="display: none;"></div>

    <div x-data="{ sidebarOpen: false }" class="flex h-full min-h-screen">
        
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-20 bg-stone-900/60 backdrop-blur-xs lg:hidden"
             style="display: none;"></div>

        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col lg:pl-64 min-w-0 bg-stone-50">
            
            <!-- Topbar / Header -->
            <header class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16 border-b border-stone-200 bg-white/80 backdrop-blur-md sticky top-0 z-10 shadow-sm">
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Hamburger Button (Mobile Only) -->
                    <button @click="sidebarOpen = !sidebarOpen" 
                            type="button" 
                            class="p-2 -ml-2 rounded-xl text-stone-600 hover:text-stone-900 hover:bg-stone-100 lg:hidden focus:outline-none"
                            aria-label="Open sidebar">
                        <x-lucide-menu class="w-6 h-6" />
                    </button>

                    <h1 class="text-sm sm:text-base font-bold text-stone-800 tracking-wide truncate">
                        {{ $title ?? 'Sistem Informasi Akademik' }}
                    </h1>
                </div>

                <div class="flex items-center gap-2 sm:gap-4 shrink-0">
                    <!-- Notification Bell Dropdown -->
                    @livewire('shared.notification-dropdown')

                    <div class="w-px h-6 bg-stone-200"></div>

                    <!-- User Profile -->
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-50 border border-green-200 flex items-center justify-center font-bold text-green-700 text-xs select-none shrink-0">
                            {{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 2)) }}
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-semibold text-stone-800">{{ auth()->user()->nama ?? 'User' }}</p>
                            <p class="text-xs text-stone-500 font-medium capitalize">{{ str_replace('_', ' ', auth()->user()->role->nama ?? 'Role') }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                <div class="mx-auto max-w-7xl">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <!-- MicroModal Alert / Confirm Component (Ultra-Premium Glassmorphism Floating Toast & Modal) -->
    <div class="modal micromodal-slide" id="modal-alert" aria-hidden="true">
        <div id="modal-alert-overlay" class="modal__overlay fixed inset-0 bg-stone-950/50 backdrop-blur-sm z-[99999] flex items-center justify-center p-4 transition-all duration-300" tabindex="-1" data-micromodal-close>
            <div id="modal-alert-container" class="modal__container bg-white/95 backdrop-blur-md border border-stone-200/90 rounded-3xl p-5 shadow-2xl max-w-md w-full relative overflow-hidden transition-all duration-300 shadow-stone-950/15" role="dialog" aria-modal="true" aria-labelledby="modal-alert-title">
                
                <!-- Top Accent Line -->
                <div id="modal-alert-accent" class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 to-teal-400"></div>

                <div class="flex items-start justify-between gap-3 pt-1">
                    <div class="flex items-start gap-3">
                        <div id="modal-alert-badge" class="w-10 h-10 rounded-2xl bg-emerald-50 border border-emerald-200/60 text-emerald-700 flex items-center justify-center text-lg font-black shrink-0 shadow-xs">
                            <x-lucide-check-circle-2 class="w-5 h-5" />
                        </div>
                        <div>
                            <h2 class="text-xs font-black text-stone-900 uppercase tracking-wide flex items-center gap-1.5" id="modal-alert-title">
                                Pemberitahuan Sistem
                            </h2>
                            <p class="text-xs text-stone-600 leading-relaxed font-semibold mt-1" id="modal-alert-content">
                                Pesan pemberitahuan sistem.
                            </p>
                        </div>
                    </div>
                    <button type="button" class="modal__close p-1.5 rounded-xl text-stone-400 hover:text-stone-700 hover:bg-stone-100 font-bold transition text-xs shrink-0" aria-label="Close modal" data-micromodal-close>
                        ✕
                    </button>
                </div>

                <footer id="modal-alert-footer" class="flex items-center justify-end gap-2.5 pt-3.5 mt-2 border-t border-stone-100">
                    <button type="button" id="modal-alert-cancel-btn" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-bold transition" data-micromodal-close>
                        Batal
                    </button>
                    <button type="button" id="modal-alert-confirm-btn" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md transition">
                        Lanjutkan
                    </button>
                </footer>

                <!-- Toast Progress Bar Animation -->
                <div id="modal-alert-progress" class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-500 origin-left transition-all duration-300 hidden"></div>
            </div>
        </div>
    </div>

    <!-- Accessibility Menu -->
    <x-accessibility-menu />

    @livewireScripts

    <script>
        let modalAutoDismissTimer = null;
        let modalProgressInterval = null;

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof MicroModal !== 'undefined') {
                MicroModal.init({
                    awaitCloseAnimation: false,
                    awaitOpenAnimation: false,
                    disableScroll: false,
                });
            }
            checkFlashMessages();
        });

        function checkFlashMessages() {
            @if(session()->has('error'))
                window.showAlert('Peringatan Error', @json(session('error')), null, 'danger');
            @elseif(session()->has('danger'))
                window.showAlert('Gagal Memproses', @json(session('danger')), null, 'danger');
            @elseif(session()->has('success'))
                window.showAlert('Berhasil Ditambahkan', @json(session('success')), null, 'create');
            @elseif(session()->has('message'))
                window.showAlert('Informasi Sistem', @json(session('message')), null, 'auto');
            @elseif(session()->has('warning'))
                window.showAlert('Perhatian', @json(session('warning')), null, 'warning');
            @elseif(session()->has('info'))
                window.showAlert('Informasi', @json(session('info')), null, 'info');
            @endif
        }

        // Premium Floating Toast & Confirm Dialog System via MicroModal
        window.showAlert = function(title, message, onConfirm = null, type = 'auto') {
            if (modalAutoDismissTimer) {
                clearTimeout(modalAutoDismissTimer);
                modalAutoDismissTimer = null;
            }
            if (modalProgressInterval) {
                clearInterval(modalProgressInterval);
                modalProgressInterval = null;
            }

            const modalElem = document.getElementById('modal-alert');
            const overlayElem = document.getElementById('modal-alert-overlay');
            const containerElem = document.getElementById('modal-alert-container');
            const accentElem = document.getElementById('modal-alert-accent');
            const badgeElem = document.getElementById('modal-alert-badge');
            const titleElem = document.getElementById('modal-alert-title');
            const contentElem = document.getElementById('modal-alert-content');
            const footerElem = document.getElementById('modal-alert-footer');
            const cancelBtn = document.getElementById('modal-alert-cancel-btn');
            const confirmBtn = document.getElementById('modal-alert-confirm-btn');
            const progressElem = document.getElementById('modal-alert-progress');

            if (modalElem) {
                modalElem.setAttribute('aria-hidden', 'true');
                modalElem.classList.remove('is-open');
            }

            const msgLower = (message || '').toLowerCase();
            const typeLower = (type || '').toLowerCase();

            const isError = typeLower === 'danger' || typeLower === 'error' || msgLower.includes('gagal') || msgLower.includes('error') || msgLower.includes('salah');
            const isWarning = !isError && (typeLower === 'warning' || msgLower.includes('perhatian') || msgLower.includes('warning') || msgLower.includes('ingat'));
            const isDelete = !isError && !isWarning && (typeLower === 'delete' || msgLower.includes('hapus') || msgLower.includes('delete') || msgLower.includes('dibatalkan'));
            const isEdit = !isError && !isWarning && (typeLower === 'edit' || msgLower.includes('edit') || msgLower.includes('perbarui') || msgLower.includes('ubah') || msgLower.includes('update') || msgLower.includes('koreksi'));
            const isCreate = !isError && !isWarning && (typeLower === 'create' || msgLower.includes('tambah') || msgLower.includes('buat') || msgLower.includes('simpan') || msgLower.includes('create') || msgLower.includes('tersimpan'));

            let badgeHtml = '✓';
            let badgeBgClass = 'bg-emerald-50 text-emerald-700 border-emerald-200/80';
            let accentClass = 'bg-gradient-to-r from-emerald-500 to-teal-400';
            let progressBgClass = 'bg-emerald-500';
            let defaultTitle = 'Pemberitahuan Sistem';

            if (isError) {
                badgeHtml = '⚠️';
                badgeBgClass = 'bg-rose-50 text-rose-700 border-rose-200/80';
                accentClass = 'bg-gradient-to-r from-rose-600 to-pink-500';
                progressBgClass = 'bg-rose-600';
                defaultTitle = 'Gagal Memproses Data';
            } else if (isWarning) {
                badgeHtml = '💡';
                badgeBgClass = 'bg-amber-50 text-amber-800 border-amber-200/80';
                accentClass = 'bg-gradient-to-r from-amber-500 to-yellow-400';
                progressBgClass = 'bg-amber-500';
                defaultTitle = 'Perhatian Sistem';
            } else if (isDelete) {
                badgeHtml = '🗑️';
                badgeBgClass = 'bg-rose-50 text-rose-700 border-rose-200/80';
                accentClass = 'bg-gradient-to-r from-rose-500 to-red-400';
                progressBgClass = 'bg-rose-500';
                defaultTitle = 'Data Berhasil Dihapus';
            } else if (isEdit) {
                badgeHtml = '✏️';
                badgeBgClass = 'bg-indigo-50 text-indigo-700 border-indigo-200/80';
                accentClass = 'bg-gradient-to-r from-indigo-500 to-sky-400';
                progressBgClass = 'bg-indigo-500';
                defaultTitle = 'Perubahan Berhasil Disimpan';
            } else if (isCreate) {
                badgeHtml = '✨';
                badgeBgClass = 'bg-emerald-50 text-emerald-700 border-emerald-200/80';
                accentClass = 'bg-gradient-to-r from-emerald-500 to-teal-400';
                progressBgClass = 'bg-emerald-500';
                defaultTitle = 'Data Berhasil Ditambahkan';
            }

            if (accentElem) accentElem.className = 'absolute top-0 left-0 right-0 h-1.5 ' + accentClass;
            if (badgeElem) {
                badgeElem.className = 'w-10 h-10 rounded-2xl border flex items-center justify-center text-lg font-black shrink-0 shadow-xs ' + badgeBgClass;
                badgeElem.innerHTML = badgeHtml;
            }
            if (titleElem) {
                titleElem.innerText = title || defaultTitle;
            }
            if (contentElem) {
                contentElem.innerText = message || '';
            }

            const isConfirmation = typeof onConfirm === 'function';

            if (isConfirmation) {
                // Confirmation Mode (Centered with Backdrop)
                if (overlayElem) {
                    overlayElem.className = "modal__overlay fixed inset-0 bg-stone-950/60 backdrop-blur-xs z-[99999] flex items-center justify-center p-4 pointer-events-auto";
                }
                if (containerElem) {
                    containerElem.className = "modal__container bg-white/95 backdrop-blur-md border border-stone-200/90 rounded-3xl p-6 shadow-2xl max-w-md w-full relative overflow-hidden transition-all duration-300 shadow-stone-950/20";
                }
                if (footerElem) footerElem.style.display = 'flex';
                if (cancelBtn) cancelBtn.style.display = 'inline-block';
                if (progressElem) progressElem.style.display = 'none';

                if (confirmBtn) {
                    confirmBtn.innerText = 'Lanjutkan';
                    confirmBtn.className = isError || isDelete 
                        ? 'px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-extrabold shadow-md transition'
                        : 'px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-extrabold shadow-md transition';
                    confirmBtn.style.display = 'inline-block';
                    confirmBtn.onclick = function() {
                        if (typeof MicroModal !== 'undefined') MicroModal.close('modal-alert');
                        onConfirm();
                    };
                }
            } else {
                // Toast Notification Mode (Top Right Floating Toast with Progress Bar)
                if (overlayElem) {
                    overlayElem.className = "modal__overlay fixed top-5 right-5 z-[99999] p-0 bg-transparent backdrop-blur-none pointer-events-none";
                }
                if (containerElem) {
                    containerElem.className = "modal__container bg-white/95 backdrop-blur-md border " + (isError ? "border-rose-300 ring-4 ring-rose-500/10" : (isWarning ? "border-amber-300 ring-4 ring-amber-500/10" : "border-stone-200/90 ring-4 ring-emerald-500/10")) + " rounded-2xl p-4.5 shadow-2xl max-w-sm w-full relative overflow-hidden pointer-events-auto transition-all duration-300 shadow-stone-950/15";
                }
                if (footerElem) footerElem.style.display = 'none';

                if (progressElem) {
                    progressElem.className = "absolute bottom-0 left-0 right-0 h-1 origin-left transition-all duration-100 " + progressBgClass;
                    progressElem.style.display = 'block';
                    progressElem.style.transform = 'scaleX(1)';
                }

                // Smooth timer progress countdown over 4 seconds
                const duration = 4000;
                const startTime = Date.now();

                modalProgressInterval = setInterval(function() {
                    const elapsed = Date.now() - startTime;
                    const remaining = Math.max(0, 1 - (elapsed / duration));
                    if (progressElem) {
                        progressElem.style.transform = `scaleX(${remaining})`;
                    }
                    if (elapsed >= duration) {
                        clearInterval(modalProgressInterval);
                        modalProgressInterval = null;
                    }
                }, 40);

                modalAutoDismissTimer = setTimeout(function() {
                    if (typeof MicroModal !== 'undefined') {
                        try { MicroModal.close('modal-alert'); } catch(e) {}
                    }
                }, duration);
            }

            if (typeof MicroModal !== 'undefined') {
                try {
                    MicroModal.show('modal-alert');
                } catch (e) {
                    if (modalElem) {
                        modalElem.setAttribute('aria-hidden', 'false');
                        modalElem.classList.add('is-open');
                    }
                }
            }
        };

        // Override Browser Native alert() with MicroModal
        window.alert = function(message) {
            window.showAlert(null, message);
        };

        // Intercept all wire:confirm or data-confirm clicks to use MicroModal dialogs
        document.addEventListener('click', function(e) {
            const confirmEl = e.target.closest('[wire\\:confirm], [data-confirm]');
            if (confirmEl && !confirmEl.dataset.micromodalConfirmed) {
                e.stopImmediatePropagation();
                e.preventDefault();
                const msg = confirmEl.getAttribute('wire:confirm') || confirmEl.getAttribute('data-confirm');
                window.showAlert('Konfirmasi Tindakan', msg, function() {
                    confirmEl.dataset.micromodalConfirmed = 'true';
                    confirmEl.click();
                    delete confirmEl.dataset.micromodalConfirmed;
                });
            }
        }, true);

        // Listen for Livewire custom alert events & hook into request failures
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('show-alert', (data) => {
                const payload = Array.isArray(data) ? data[0] : data;
                const title = payload?.title || null;
                const message = payload?.message || payload?.text || '';
                const type = payload?.type || 'auto';
                window.showAlert(title, message, null, type);
            });

            Livewire.hook('commit', ({ respond, succeed, fail }) => {
                fail(({ status, content }) => {
                    let errorMsg = 'Terjadi kesalahan sistem (HTTP ' + (status || '500') + ').';
                    try {
                        let parsed = JSON.parse(content);
                        if (parsed.message) errorMsg = parsed.message;
                    } catch(e) {}
                    window.showAlert('Peringatan Error Sistem', errorMsg, null, 'danger');
                });
            });
        });

        document.addEventListener('livewire:navigated', () => {
            checkFlashMessages();
        });;
    </script>
</body>
</html>
