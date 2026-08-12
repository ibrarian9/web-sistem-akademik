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
            0% {
                opacity: 0.85;
                transform: translateY(4px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
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

    <!-- MicroModal Alert / Confirm Component -->
    <div class="modal micromodal-slide" id="modal-alert" aria-hidden="true">
        <div class="modal__overlay fixed inset-0 bg-stone-900/60 backdrop-blur-xs z-[99999] flex items-center justify-center p-4" tabindex="-1" data-micromodal-close>
            <div class="modal__container bg-white border border-stone-200 rounded-3xl p-6 shadow-2xl max-w-md w-full space-y-4" role="dialog" aria-modal="true" aria-labelledby="modal-alert-title">
                <header class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <h2 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2.5" id="modal-alert-title">
                        <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-800 text-xs flex items-center justify-center font-black">i</span>
                        Pemberitahuan Sistem
                    </h2>
                    <button type="button" class="modal__close p-1.5 rounded-xl text-stone-400 hover:text-stone-700 hover:bg-stone-100 font-bold transition" aria-label="Close modal" data-micromodal-close>
                        ✕
                    </button>
                </header>
                <main class="text-xs text-stone-700 leading-relaxed font-medium py-1" id="modal-alert-content">
                    Pesan pemberitahuan sistem.
                </main>
                <footer class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                    <button type="button" id="modal-alert-cancel-btn" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-bold transition" data-micromodal-close>
                        Batal
                    </button>
                    <button type="button" id="modal-alert-confirm-btn" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md transition">
                        Lanjutkan
                    </button>
                </footer>
            </div>
        </div>
    </div>

    <!-- Accessibility Menu -->
    <x-accessibility-menu />

    @livewireScripts

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof MicroModal !== 'undefined') {
                MicroModal.init({
                    awaitCloseAnimation: false,
                    awaitOpenAnimation: false,
                    disableScroll: true,
                });
            }
            checkFlashMessages();
        });

        function checkFlashMessages() {
            @if(session()->has('success'))
                window.showAlert(null, @json(session('success')), null, 'create');
            @elseif(session()->has('message'))
                window.showAlert(null, @json(session('message')), null, 'auto');
            @elseif(session()->has('error'))
                window.showAlert('Peringatan Error', @json(session('error')), null, 'danger');
            @elseif(session()->has('warning'))
                window.showAlert('Peringatan', @json(session('warning')), null, 'danger');
            @elseif(session()->has('info'))
                window.showAlert('Informasi', @json(session('info')), null, 'info');
            @endif
        }

        // Global Alert & Confirm Function via MicroModal (Automated for Create, Edit, Delete Data)
        window.showAlert = function(title, message, onConfirm = null, type = 'auto') {
            const modalElem = document.getElementById('modal-alert');
            if (modalElem) {
                modalElem.setAttribute('aria-hidden', 'true');
                modalElem.classList.remove('is-open');
            }

            const titleElem = document.getElementById('modal-alert-title');
            const contentElem = document.getElementById('modal-alert-content');
            const cancelBtn = document.getElementById('modal-alert-cancel-btn');
            const confirmBtn = document.getElementById('modal-alert-confirm-btn');

            const msgLower = (message || '').toLowerCase();
            const isDelete = type === 'delete' || type === 'danger' || msgLower.includes('hapus') || msgLower.includes('delete') || msgLower.includes('dibatalkan');
            const isEdit = type === 'edit' || msgLower.includes('edit') || msgLower.includes('perbarui') || msgLower.includes('ubah') || msgLower.includes('update') || msgLower.includes('koreksi');
            const isCreate = type === 'create' || msgLower.includes('tambah') || msgLower.includes('buat') || msgLower.includes('simpan') || msgLower.includes('create') || msgLower.includes('tersimpan');

            let badgeHtml = '<span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-800 text-xs flex items-center justify-center font-black shrink-0">✓</span>';
            let defaultTitle = 'Pemberitahuan Sistem';

            if (isDelete) {
                badgeHtml = '<span class="w-7 h-7 rounded-xl bg-rose-100 text-rose-800 text-xs flex items-center justify-center font-black shrink-0">🗑</span>';
                defaultTitle = 'Hapus Data Berhasil';
            } else if (isEdit) {
                badgeHtml = '<span class="w-7 h-7 rounded-xl bg-indigo-100 text-indigo-800 text-xs flex items-center justify-center font-black shrink-0">✏️</span>';
                defaultTitle = 'Perubahan Data Disimpan';
            } else if (isCreate) {
                badgeHtml = '<span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-800 text-xs flex items-center justify-center font-black shrink-0">✨</span>';
                defaultTitle = 'Data Berhasil Dibuat';
            }

            if (titleElem) {
                titleElem.innerHTML = badgeHtml + ` <span>` + (title || defaultTitle) + `</span>`;
            }
            if (contentElem) {
                contentElem.innerText = message || '';
            }
            
            if (typeof onConfirm === 'function') {
                if (cancelBtn) cancelBtn.style.display = 'inline-block';
                if (confirmBtn) {
                    confirmBtn.innerText = 'Lanjutkan';
                    confirmBtn.className = 'px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md transition';
                    confirmBtn.style.display = 'inline-block';
                    confirmBtn.onclick = function() {
                        if (typeof MicroModal !== 'undefined') {
                            MicroModal.close('modal-alert');
                        }
                        onConfirm();
                    };
                }
            } else {
                if (cancelBtn) cancelBtn.style.display = 'none';
                if (confirmBtn) {
                    confirmBtn.innerText = 'Tutup';
                    confirmBtn.className = isDelete 
                        ? 'px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md transition' 
                        : (isEdit ? 'px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md transition' : 'px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md transition');
                    confirmBtn.style.display = 'inline-block';
                    confirmBtn.onclick = function() {
                        if (typeof MicroModal !== 'undefined') {
                            MicroModal.close('modal-alert');
                        }
                    };
                }
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

        // Listen for Livewire custom alert events & page navigations
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('show-alert', (data) => {
                const title = data.title || null;
                const message = data.message || data.text || '';
                const type = data.type || 'auto';
                window.showAlert(title, message, null, type);
            });
        });

        document.addEventListener('livewire:navigated', () => {
            checkFlashMessages();
        });
    </script>
</body>
</html>
