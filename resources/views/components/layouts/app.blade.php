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
        main > div {
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
        <div class="flex-1 flex flex-col lg:pl-64 min-w-0 bg-stone-50 relative">
            
            <!-- Topbar / Header (Non-sticky: stays at the top and scrolls naturally) -->
            <header class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16 border-b border-stone-200 bg-white shrink-0">
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
                    <!-- Command Palette Search Button -->
                    <button 
                        type="button" 
                        @click="$dispatch('open-command-palette')" 
                        class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-stone-100 hover:bg-stone-200/80 text-stone-500 rounded-xl text-xs font-semibold border border-stone-200 transition cursor-pointer shadow-2xs"
                        title="Pencarian Cepat Menu dan Fitur (Ctrl + K)"
                    >
                        <x-lucide-search class="w-3.5 h-3.5 text-stone-400" />
                        <span>Cari menu...</span>
                        <kbd class="text-[10px] bg-white border border-stone-300 px-1.5 py-0.5 rounded shadow-2xs font-mono font-bold text-stone-500">Ctrl K</kbd>
                    </button>

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
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <div class="mx-auto max-w-7xl page-container">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <!-- Command Palette (Ctrl+K) -->
    <x-command-palette />

    <!-- Global Floating Toast Notification -->
    <x-toast-notification />

    <!-- Global Universal Export & Download Loading Modal -->
    <x-export-loading-modal />

    <!-- MicroModal Alert / Confirm Component -->
    <x-micromodal-notification rounded="rounded-2xl" badgeRounded="rounded-xl" categoryRounded="rounded-lg" />

    <!-- Accessibility Menu -->
    <x-accessibility-menu />

    @livewireScripts
</body>
</html>
