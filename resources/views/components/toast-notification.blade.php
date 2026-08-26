<div 
    x-data="{
        toasts: [],
        addToast(toast) {
            const id = Date.now() + Math.random();
            const newToast = {
                id: id,
                type: toast.type || 'info', // 'success', 'error', 'warning', 'info'
                title: toast.title || (toast.type === 'error' ? 'Gagal' : (toast.type === 'success' ? 'Berhasil' : 'Pemberitahuan')),
                message: toast.message || '',
                timeout: toast.timeout || 4500,
                progress: 100
            };
            this.toasts.push(newToast);

            // Animate progress countdown
            const intervalTime = 50;
            const step = (intervalTime / newToast.timeout) * 100;
            const interval = setInterval(() => {
                newToast.progress -= step;
                if (newToast.progress <= 0) {
                    clearInterval(interval);
                    this.removeToast(id);
                }
            }, intervalTime);
        },
        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    x-init="
        @if (session()->has('message') || session()->has('success'))
            addToast({ type: 'success', message: '{{ session('message') ?? session('success') }}' });
        @endif
        @if (session()->has('error'))
            addToast({ type: 'error', message: '{{ session('error') }}' });
        @endif
        @if (session()->has('warning'))
            addToast({ type: 'warning', message: '{{ session('warning') }}' });
        @endif

        // Listen to global JS and Livewire events
        window.addEventListener('notify', (event) => {
            addToast(event.detail || {});
        });
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notify', (data) => {
                const payload = Array.isArray(data) ? data[0] : data;
                addToast(payload || {});
            });
        });
    "
    class="fixed top-4 right-4 z-[9999] flex flex-col gap-2.5 max-w-sm w-full pointer-events-none"
    style="display: none;"
    x-show="toasts.length > 0"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div 
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            class="pointer-events-auto bg-white/95 backdrop-blur-md border rounded-2xl shadow-xl overflow-hidden relative group"
            :class="{
                'border-emerald-200 shadow-emerald-900/10': toast.type === 'success',
                'border-rose-200 shadow-rose-900/10': toast.type === 'error',
                'border-amber-200 shadow-amber-900/10': toast.type === 'warning',
                'border-sky-200 shadow-sky-900/10': toast.type === 'info'
            }"
        >
            <div class="p-3.5 flex items-start gap-3">
                <!-- Icon -->
                <div class="shrink-0 p-2 rounded-xl"
                    :class="{
                        'bg-emerald-50 text-emerald-600': toast.type === 'success',
                        'bg-rose-50 text-rose-600': toast.type === 'error',
                        'bg-amber-50 text-amber-600': toast.type === 'warning',
                        'bg-sky-50 text-sky-600': toast.type === 'info'
                    }"
                >
                    <template x-if="toast.type === 'success'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </template>
                </div>

                <!-- Text -->
                <div class="flex-1 min-w-0 pr-2">
                    <h4 class="text-xs font-black text-stone-900 leading-tight" x-text="toast.title"></h4>
                    <p class="text-xs text-stone-600 font-medium mt-0.5 leading-relaxed break-words" x-text="toast.message"></p>
                </div>

                <!-- Close Button -->
                <button 
                    type="button" 
                    @click="removeToast(toast.id)" 
                    class="text-stone-400 hover:text-stone-700 p-1 rounded-lg transition"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Progress countdown bar -->
            <div class="h-1 w-full bg-stone-100">
                <div class="h-full transition-all duration-75"
                    :style="`width: ${toast.progress}%`"
                    :class="{
                        'bg-emerald-500': toast.type === 'success',
                        'bg-rose-500': toast.type === 'error',
                        'bg-amber-500': toast.type === 'warning',
                        'bg-sky-500': toast.type === 'info'
                    }"
                ></div>
            </div>
        </div>
    </template>
</div>
