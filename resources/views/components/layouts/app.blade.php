<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-stone-50 text-stone-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name', 'SIAKAD') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased selection:bg-green-600 selection:text-white">
    <div class="flex h-full min-h-screen">
        
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col pl-64 min-w-0 bg-stone-50">
            
            <!-- Topbar / Header -->
            <header class="flex items-center justify-between px-8 h-16 border-b border-stone-200 bg-white/80 backdrop-blur-md sticky top-0 z-10 shadow-sm">
                <div class="flex items-center gap-4">
                    <h1 class="text-base font-bold text-stone-800 tracking-wide">
                        {{ $title ?? 'Sistem Informasi Akademik' }}
                    </h1>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Notification Bell Dropdown -->
                    @livewire('shared.notification-dropdown')

                    <div class="w-px h-6 bg-stone-200"></div>

                    <!-- User Profile -->
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-50 border border-green-200 flex items-center justify-center font-bold text-green-700 text-xs select-none">
                            {{ substr(auth()->user()->nama ?? 'U', 0, 2) }}
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-semibold text-stone-800">{{ auth()->user()->nama ?? 'User' }}</p>
                            <p class="text-xs text-stone-500 font-medium capitalize">{{ str_replace('_', ' ', auth()->user()->role->nama ?? 'Role') }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-8 overflow-y-auto">
                <div class="mx-auto max-w-7xl">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <!-- Accessibility Menu -->
    <x-accessibility-menu />

    @livewireScripts
</body>
</html>
