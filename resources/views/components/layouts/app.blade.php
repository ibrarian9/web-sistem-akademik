<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name', 'SIAKAD') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased selection:bg-indigo-500 selection:text-white">
    <div class="flex h-full min-h-screen">
        
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col pl-64 min-w-0 bg-slate-950">
            
            <!-- Topbar / Header -->
            <header class="flex items-center justify-between px-8 h-16 border-b border-slate-900 bg-slate-950/70 backdrop-blur-md sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <h1 class="text-base font-bold text-white tracking-wide">
                        {{ $title ?? 'Sistem Informasi Akademik' }}
                    </h1>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Notification Bell Dropdown -->
                    @livewire('shared.notification-dropdown')

                    <div class="w-px h-6 bg-slate-800"></div>

                    <!-- User Profile Dropdown -->
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-600/10 border border-indigo-500/20 flex items-center justify-center font-bold text-indigo-400 text-xs select-none">
                            {{ substr(auth()->user()->nama ?? 'U', 0, 2) }}
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-xs font-semibold text-white">{{ auth()->user()->nama ?? 'User' }}</p>
                            <p class="text-[9px] text-slate-500 font-medium uppercase tracking-wider">{{ auth()->user()->role->nama ?? 'Role' }}</p>
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

    @livewireScripts
</body>
</html>
