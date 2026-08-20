<!-- GLOBAL MICROMODAL ALERT DIALOG -->
<div class="modal micromodal-slide" id="global-alert-modal" aria-hidden="true">
    <div class="modal__overlay fixed inset-0 z-[99999] flex items-center justify-center bg-stone-950/70 backdrop-blur-xs p-4 overflow-y-auto" tabindex="-1" data-micromodal-close>
        <div class="modal__container w-full max-w-md bg-white border border-stone-200 rounded-3xl shadow-2xl p-6 space-y-5 my-auto transform transition-all duration-200 scale-100" role="dialog" aria-modal="true" aria-labelledby="global-alert-title">
            
            <div class="flex items-start gap-4">
                <div id="global-alert-icon-container" class="p-3 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="space-y-1 flex-1">
                    <h3 id="global-alert-title" class="text-base font-extrabold text-stone-900 leading-tight">
                        Pemberitahuan
                    </h3>
                    <p id="global-alert-message" class="text-xs text-stone-600 font-medium leading-relaxed">
                        Pesan notifikasi.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end pt-3 border-t border-stone-100">
                <button 
                    type="button" 
                    id="global-alert-ok-btn"
                    onclick="window.closeModalAlert()" 
                    class="px-5 py-2.5 bg-stone-900 hover:bg-stone-800 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                    Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<!-- GLOBAL MICROMODAL CONFIRM DIALOG -->
<div class="modal micromodal-slide" id="global-confirm-modal" aria-hidden="true">
    <div class="modal__overlay fixed inset-0 z-[99999] flex items-center justify-center bg-stone-950/70 backdrop-blur-xs p-4 overflow-y-auto" tabindex="-1" data-micromodal-close>
        <div class="modal__container w-full max-w-md bg-white border border-stone-200 rounded-3xl shadow-2xl p-6 space-y-5 my-auto transform transition-all duration-200 scale-100" role="dialog" aria-modal="true" aria-labelledby="global-confirm-title">
            
            <div class="flex items-start gap-4">
                <div id="global-confirm-icon-container" class="p-3 rounded-2xl bg-amber-50 text-amber-700 border border-amber-200 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="space-y-1 flex-1">
                    <h3 id="global-confirm-title" class="text-base font-extrabold text-stone-900 leading-tight">
                        Konfirmasi Tindakan
                    </h3>
                    <p id="global-confirm-message" class="text-xs text-stone-600 font-medium leading-relaxed">
                        Apakah Anda yakin ingin melanjutkan tindakan ini?
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-stone-100">
                <button 
                    type="button" 
                    id="global-confirm-cancel-btn"
                    onclick="window.cancelModalConfirm()" 
                    class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-bold transition cursor-pointer">
                    Batal
                </button>
                <button 
                    type="button" 
                    id="global-confirm-btn"
                    onclick="window.executeModalConfirm()" 
                    class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .modal {
        display: none;
    }
    .modal.is-open {
        display: block;
    }
    .modal__overlay {
        animation: mmfadeIn 0.2s cubic-bezier(0.0, 0.0, 0.2, 1);
    }
    .modal__container {
        animation: mmslideIn 0.2s cubic-bezier(0, 0, 0.2, 1);
    }
    @keyframes mmfadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes mmslideIn {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
</style>
