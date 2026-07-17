<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 via-stone-50 to-emerald-50 px-4">
    {{-- Decorative Background --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[20%] left-[10%] w-96 h-96 bg-green-200/30 rounded-full blur-3xl"></div>
        <div class="absolute bottom-[20%] right-[10%] w-96 h-96 bg-emerald-200/30 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-md z-10">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-green-50 border border-green-200 shadow-md mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 019.918 5.842 50.45 50.45 0 00-2.658.814m-15.482 0a50.53 50.53 0 0115.482 0m-15.482 0v3.06c0 5.625 3.338 10.71 8.232 12.839m0-22.742V20.9" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-stone-800 tracking-tight">Sistem Akademik Yayasan</h1>
            <p class="text-sm text-stone-500 mt-1">Silakan masuk untuk mengakses dashboard Anda</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white/80 backdrop-blur-xl border border-stone-200 rounded-3xl p-8 shadow-xl">
            @if (session()->has('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-start gap-3">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form wire:submit.prevent="login" class="space-y-6">
                <!-- Username -->
                <div class="space-y-2">
                    <label for="username" class="block text-sm font-semibold text-stone-700">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-stone-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </span>
                        <input wire:model="username" type="text" id="username" autocomplete="username" placeholder="Masukkan username Anda" 
                            class="w-full pl-11 pr-4 py-3 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500 transition duration-200 text-sm" />
                    </div>
                    @error('username') 
                        <span class="text-red-600 text-xs flex items-center gap-1 mt-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </span> 
                    @enderror
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold text-stone-700">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-stone-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </span>
                        <input wire:model="password" type="password" id="password" autocomplete="current-password" placeholder="••••••••" 
                            class="w-full pl-11 pr-4 py-3 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500 transition duration-200 text-sm" />
                    </div>
                    @error('password') 
                        <span class="text-red-600 text-xs flex items-center gap-1 mt-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </span> 
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input wire:model="remember" type="checkbox" class="w-4 h-4 rounded border-stone-300 bg-stone-50 text-green-600 focus:ring-green-500/50" />
                        <span class="text-sm text-stone-500">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full py-3 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white rounded-xl text-sm font-semibold tracking-wide shadow-lg shadow-green-600/20 hover:shadow-green-700/30 transition duration-200 flex items-center justify-center gap-2 group">
                    <span wire:loading.remove>Masuk ke Aplikasi</span>
                    <span wire:loading class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <svg wire:loading.remove class="w-4 h-4 text-white/70 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </form>
        </div>

        <div class="text-center mt-6">
            <span class="text-xs text-stone-400">© 2026 Yayasan Pendidikan Islam. Hak Cipta Dilindungi.</span>
        </div>
    </div>
</div>
