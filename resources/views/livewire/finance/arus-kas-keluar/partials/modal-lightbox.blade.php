<!-- ALPINE.JS LIGHTBOX PREVIEW MODAL (CLIENT-SIDE) -->
<div x-show="previewOpen" 
     x-cloak 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-950/80 backdrop-blur-xs"
     @click.self="previewOpen = false">
    <div class="relative max-w-3xl w-full bg-white rounded-2xl shadow-2xl overflow-hidden border border-stone-200"
         @click.outside="previewOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-stone-200 bg-stone-50">
            <div class="flex items-center gap-2 min-w-0">
                <x-lucide-image class="w-4 h-4 text-emerald-600 shrink-0" />
                <h4 class="text-xs font-extrabold text-stone-900 truncate" x-text="previewTitle || 'Foto Bukti Pengeluaran'"></h4>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a :href="previewSrc" target="_blank" download class="p-1.5 bg-stone-200/70 hover:bg-emerald-100 text-stone-700 hover:text-emerald-800 rounded-lg transition cursor-pointer" title="Buka Gambar Asli">
                    <x-lucide-external-link class="w-4 h-4" />
                </a>
                <button type="button" @click="previewOpen = false" class="p-1.5 bg-stone-200/70 hover:bg-rose-100 text-stone-700 hover:text-rose-800 rounded-lg transition cursor-pointer" title="Tutup (ESC)">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        </div>
        <!-- Modal Body Image -->
        <div class="p-4 bg-stone-950 flex items-center justify-center max-h-[75vh] overflow-auto">
            <img :src="previewSrc" alt="Foto Bukti Pengeluaran" class="max-w-full max-h-[70vh] rounded-lg object-contain shadow-xl" />
        </div>
        <!-- Modal Footer -->
        <div class="px-5 py-3 bg-stone-50 border-t border-stone-200 flex items-center justify-between">
            <span class="text-[11px] text-stone-500 font-medium">Tekan <kbd class="px-1.5 py-0.5 bg-stone-200 rounded text-[10px] font-mono">ESC</kbd> atau klik di luar untuk menutup</span>
            <x-button type="button" variant="secondary" size="sm" @click="previewOpen = false">
                Tutup
            </x-button>
        </div>
    </div>
</div>
