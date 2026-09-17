@props([
    'rounded' => 'rounded-2xl',
    'badgeRounded' => 'rounded-xl',
    'categoryRounded' => 'rounded-lg',
])

<!-- 1. MicroModal Centered Confirmation Dialog Component -->
<div class="modal micromodal-slide" id="modal-confirm" aria-hidden="true">
    <div id="modal-confirm-overlay" class="modal__overlay fixed inset-0 bg-stone-950/60 backdrop-blur-xs z-[99999] flex items-center justify-center p-4 transition-all duration-300" tabindex="-1" data-micromodal-close>
        <div id="modal-confirm-container" class="modal__container bg-white text-stone-900 border border-stone-200 {{ $rounded }} p-6 shadow-2xl max-w-md w-full relative overflow-hidden transition-all duration-300 shadow-stone-950/20" role="dialog" aria-modal="true" aria-labelledby="modal-confirm-title">
            
            <!-- Top Accent Line -->
            <div id="modal-confirm-accent" class="absolute top-0 left-0 right-0 h-1.5 bg-rose-600"></div>

            <div class="flex items-start justify-between gap-3.5 pt-1">
                <div class="flex items-start gap-3.5">
                    <div id="modal-confirm-badge" class="w-11 h-11 {{ $badgeRounded }} bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-rose-600/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span id="modal-confirm-category" class="px-2.5 py-0.5 {{ $categoryRounded }} bg-rose-100 text-rose-950 text-[10px] font-extrabold tracking-wider uppercase border border-rose-300">HAPUS DATA</span>
                            <span class="text-[10px] text-stone-500 font-bold">• Baru saja</span>
                        </div>
                        <h2 class="text-sm font-black text-stone-900 tracking-tight leading-snug" id="modal-confirm-title">
                            Konfirmasi Tindakan
                        </h2>
                        <p class="text-xs text-stone-700 leading-relaxed font-semibold pt-0.5" id="modal-confirm-content">
                            Apakah Anda yakin ingin melanjutkan tindakan ini?
                        </p>
                    </div>
                </div>
                <button type="button" class="modal__close p-1.5 {{ $badgeRounded }} text-stone-400 hover:text-stone-800 hover:bg-stone-100 font-bold transition text-xs shrink-0 cursor-pointer" aria-label="Close modal" data-micromodal-close>
                    ✕
                </button>
            </div>

            <footer id="modal-confirm-footer" class="flex items-center justify-end gap-2.5 pt-4 mt-4 border-t border-stone-200">
                <button type="button" id="modal-confirm-cancel-btn" class="px-4.5 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 {{ $badgeRounded }} text-xs font-bold transition border border-stone-300 cursor-pointer" data-micromodal-close>
                    Batal
                </button>
                <button type="button" id="modal-confirm-action-btn" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white {{ $badgeRounded }} text-xs font-extrabold shadow-md transition cursor-pointer">
                    Ya, Hapus
                </button>
            </footer>
        </div>
    </div>
</div>

<!-- 2. Floating Toast Notification Container (Top Right Floating Corner, Independent & Bulletproof) -->
<div id="modal-alert" class="fixed top-6 right-6 z-[99998] p-0 pointer-events-none transition-all duration-300 transform translate-x-full opacity-0 hidden">
    <div id="modal-alert-container" class="bg-white border border-stone-200 {{ $rounded }} p-5 shadow-[0_20px_50px_rgba(0,0,0,0.15)] max-w-md w-full sm:w-[400px] relative overflow-hidden pointer-events-auto transition-all duration-300" role="alert" aria-labelledby="modal-alert-title">
        
        <!-- Top Accent Line -->
        <div id="modal-alert-accent" class="absolute top-0 left-0 right-0 h-1.5 bg-emerald-600"></div>

        <div class="flex items-start justify-between gap-3.5 pt-1">
            <div class="flex items-start gap-3.5">
                <div id="modal-alert-badge" class="w-11 h-11 {{ $badgeRounded }} bg-emerald-700 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-700/30">
                    <x-lucide-check-circle-2 class="w-6 h-6" />
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span id="modal-alert-category" class="px-2.5 py-0.5 {{ $categoryRounded }} bg-emerald-100 text-emerald-950 text-[10px] font-extrabold tracking-wider uppercase border border-emerald-300">Pemberitahuan</span>
                        <span class="text-[10px] text-stone-500 font-bold">• Baru saja</span>
                    </div>
                    <h2 class="text-sm font-black text-stone-900 tracking-tight leading-snug" id="modal-alert-title">
                        Pemberitahuan Sistem
                    </h2>
                    <p class="text-xs text-stone-700 leading-relaxed font-semibold pt-0.5" id="modal-alert-content">
                        Pesan pemberitahuan sistem.
                    </p>
                </div>
            </div>
            <button type="button" onclick="window.closeToastNotification()" class="p-1.5 {{ $badgeRounded }} text-stone-400 hover:text-stone-800 hover:bg-stone-100 font-bold transition text-xs shrink-0 cursor-pointer" aria-label="Close notification">
                ✕
            </button>
        </div>

        <!-- Toast Progress Bar Animation -->
        <div id="modal-alert-progress" class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-600 origin-left transition-all duration-100"></div>
    </div>
</div>

<script>
    var modalAutoDismissTimer = window.modalAutoDismissTimer || null;
    var modalProgressInterval = window.modalProgressInterval || null;
    var toastHideTimer = window.toastHideTimer || null;
    var toastRemainingTime = 4500;
    var toastStartTime = 0;
    var toastDuration = 4500;
    var toastIsPaused = false;

    // Override browser native window.confirm to prevent ugly default popups
    window.confirm = function() { return true; };

    // Configurable Rounded Corner Classes from Blade Props
    var MICRO_MODAL_ROUNDED_CARD = @json($rounded);
    var MICRO_MODAL_ROUNDED_BADGE = @json($badgeRounded);
    var MICRO_MODAL_ROUNDED_CATEGORY = @json($categoryRounded);

    // Dedicated helper to safely and cleanly close MicroModal confirmation dialog
    window.closeConfirmModal = function() {
        const modalElem = document.getElementById('modal-confirm');
        if (typeof MicroModal !== 'undefined') {
            try { MicroModal.close('modal-confirm'); } catch(e) {}
        }
        if (modalElem) {
            modalElem.classList.remove('is-open');
            modalElem.setAttribute('aria-hidden', 'true');
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof MicroModal !== 'undefined') {
            MicroModal.init({
                awaitCloseAnimation: false,
                awaitOpenAnimation: false,
                disableScroll: false,
            });
        }
        checkFlashMessages();

        // Setup hover pause on toast container
        const container = document.getElementById('modal-alert-container');
        if (container) {
            container.addEventListener('mouseenter', function() {
                toastIsPaused = true;
            });
            container.addEventListener('mouseleave', function() {
                toastIsPaused = false;
            });
        }

        // Setup explicit close handlers for confirmation modal
        document.querySelectorAll('#modal-confirm [data-micromodal-close]').forEach(function(el) {
            el.addEventListener('click', function() {
                window.closeConfirmModal();
            });
        });
    });

    // Close Floating Toast Notification
    window.closeToastNotification = function() {
        const toastEl = document.getElementById('modal-alert');
        if (!toastEl) return;
        if (modalAutoDismissTimer) {
            clearTimeout(modalAutoDismissTimer);
            modalAutoDismissTimer = null;
        }
        if (modalProgressInterval) {
            clearInterval(modalProgressInterval);
            modalProgressInterval = null;
        }
        if (toastHideTimer) {
            clearTimeout(toastHideTimer);
            toastHideTimer = null;
        }
        toastEl.classList.remove('translate-x-0', 'opacity-100');
        toastEl.classList.add('translate-x-full', 'opacity-0');
        toastHideTimer = setTimeout(() => {
            toastEl.classList.add('hidden');
            toastHideTimer = null;
        }, 300);
    };

    // Show Floating Toast Notification
    function showToastNotification(title, message, type) {
        if (modalAutoDismissTimer) {
            clearTimeout(modalAutoDismissTimer);
            modalAutoDismissTimer = null;
        }
        if (modalProgressInterval) {
            clearInterval(modalProgressInterval);
            modalProgressInterval = null;
        }
        if (toastHideTimer) {
            clearTimeout(toastHideTimer);
            toastHideTimer = null;
        }

        const toastEl = document.getElementById('modal-alert');
        const containerElem = document.getElementById('modal-alert-container');
        const accentElem = document.getElementById('modal-alert-accent');
        const badgeElem = document.getElementById('modal-alert-badge');
        const categoryElem = document.getElementById('modal-alert-category');
        const titleElem = document.getElementById('modal-alert-title');
        const contentElem = document.getElementById('modal-alert-content');
        const progressElem = document.getElementById('modal-alert-progress');

        if (!toastEl) return;

        const msgLower = (message || '').toLowerCase();
        const typeLower = (type || '').toLowerCase();

        const isError = typeLower === 'danger' || typeLower === 'error' || msgLower.includes('gagal') || msgLower.includes('error') || msgLower.includes('salah') || msgLower.includes('ditolak');
        const isWarning = !isError && (typeLower === 'warning' || msgLower.includes('perhatian') || msgLower.includes('warning') || msgLower.includes('ingat'));
        const isDelete = !isError && !isWarning && (typeLower === 'delete' || msgLower.includes('hapus') || msgLower.includes('delete') || msgLower.includes('dibatalkan'));
        const isEdit = !isError && !isWarning && (typeLower === 'edit' || msgLower.includes('edit') || msgLower.includes('perbarui') || msgLower.includes('diperbarui') || msgLower.includes('ubah') || msgLower.includes('update') || msgLower.includes('koreksi'));
        const isCreate = !isError && !isWarning && (typeLower === 'create' || msgLower.includes('tambah') || msgLower.includes('buat') || msgLower.includes('simpan') || msgLower.includes('tersimpan') || msgLower.includes('rilis') || msgLower.includes('dicatat'));

        let badgeSvg = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
        let badgeBgClass = 'bg-emerald-700 text-white shadow-md shadow-emerald-700/30';
        let categoryClass = 'bg-emerald-100 text-emerald-950 border-emerald-300';
        let categoryText = 'BERHASIL';
        let accentClass = 'bg-emerald-600';
        let progressBgClass = 'bg-emerald-600';
        let defaultTitle = 'Pemberitahuan Sistem';

        if (isError) {
            badgeSvg = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
            badgeBgClass = 'bg-red-700 text-white shadow-md shadow-red-700/30';
            categoryClass = 'bg-red-100 text-red-950 border-red-300';
            categoryText = 'ERROR SISTEM';
            accentClass = 'bg-red-700';
            progressBgClass = 'bg-red-700';
            defaultTitle = 'Gagal Memproses Data';
        } else if (isWarning) {
            badgeSvg = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            badgeBgClass = 'bg-amber-500 text-white shadow-md shadow-amber-500/30';
            categoryClass = 'bg-amber-100 text-amber-950 border-amber-300';
            categoryText = 'PERHATIAN';
            accentClass = 'bg-amber-500';
            progressBgClass = 'bg-amber-500';
            defaultTitle = 'Perhatian Sistem';
        } else if (isDelete) {
            badgeSvg = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';
            badgeBgClass = 'bg-rose-600 text-white shadow-md shadow-rose-600/30';
            categoryClass = 'bg-rose-100 text-rose-950 border-rose-300';
            categoryText = 'HAPUS DATA';
            accentClass = 'bg-rose-600';
            progressBgClass = 'bg-rose-600';
            defaultTitle = 'Data Berhasil Dihapus';
        } else if (isEdit) {
            badgeSvg = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>';
            badgeBgClass = 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30';
            categoryClass = 'bg-indigo-100 text-indigo-950 border-indigo-300';
            categoryText = 'PERBARUI';
            accentClass = 'bg-indigo-600';
            progressBgClass = 'bg-indigo-600';
            defaultTitle = 'Perubahan Berhasil Disimpan';
        } else if (isCreate) {
            badgeSvg = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>';
            badgeBgClass = 'bg-emerald-700 text-white shadow-md shadow-emerald-700/30';
            categoryClass = 'bg-emerald-100 text-emerald-950 border-emerald-300';
            categoryText = 'TAMBAH DATA';
            accentClass = 'bg-emerald-600';
            progressBgClass = 'bg-emerald-600';
            defaultTitle = 'Data Berhasil Ditambahkan';
        }

        if (containerElem) {
            containerElem.className = "modal__container bg-white border " + (isError ? "border-red-300 ring-4 ring-red-500/10" : (isWarning ? "border-amber-300 ring-4 ring-amber-500/10" : (isDelete ? "border-rose-300 ring-4 ring-rose-500/10" : (isEdit ? "border-indigo-300 ring-4 ring-indigo-500/10" : "border-emerald-300 ring-4 ring-emerald-500/10")))) + " " + MICRO_MODAL_ROUNDED_CARD + " p-5 shadow-[0_20px_50px_rgba(0,0,0,0.15)] max-w-md w-full sm:w-[400px] relative overflow-hidden pointer-events-auto transition-all duration-300";
        }
        if (accentElem) accentElem.className = 'absolute top-0 left-0 right-0 h-1.5 ' + accentClass;
        if (badgeElem) {
            badgeElem.className = 'w-11 h-11 ' + MICRO_MODAL_ROUNDED_BADGE + ' flex items-center justify-center text-xl font-black shrink-0 ' + badgeBgClass;
            badgeElem.innerHTML = badgeSvg;
        }
        if (categoryElem) {
            categoryElem.className = 'px-2.5 py-0.5 ' + MICRO_MODAL_ROUNDED_CATEGORY + ' text-[10px] font-extrabold tracking-wider uppercase border ' + categoryClass;
            categoryElem.innerText = categoryText;
        }
        if (titleElem) titleElem.innerText = title || defaultTitle;
        if (contentElem) contentElem.innerText = message || '';

        if (progressElem) {
            progressElem.className = "absolute bottom-0 left-0 right-0 h-1 origin-left transition-all duration-100 " + progressBgClass;
            progressElem.style.display = 'block';
            progressElem.style.transform = 'scaleX(1)';
        }

        // Show toast animation
        toastEl.classList.remove('hidden');
        void toastEl.offsetWidth; // Trigger DOM reflow for CSS transition
        toastEl.classList.remove('translate-x-full', 'opacity-0');
        toastEl.classList.add('translate-x-0', 'opacity-100');

        const duration = 4500;
        toastDuration = duration;
        toastRemainingTime = duration;
        toastIsPaused = false;
        let lastTick = Date.now();

        modalProgressInterval = setInterval(function() {
            const now = Date.now();
            const delta = now - lastTick;
            lastTick = now;

            if (!toastIsPaused) {
                toastRemainingTime = Math.max(0, toastRemainingTime - delta);
                const progressRatio = toastRemainingTime / toastDuration;
                if (progressElem) {
                    progressElem.style.transform = `scaleX(${progressRatio})`;
                }
                if (toastRemainingTime <= 0) {
                    clearInterval(modalProgressInterval);
                    modalProgressInterval = null;
                    window.closeToastNotification();
                }
            }
        }, 30);
    }

    // Listen for Livewire v3 dispatched browser events ('show-alert', 'modal-alert', 'toast')
    window.addEventListener('show-alert', function(event) {
        const detail = Array.isArray(event.detail) ? event.detail[0] : (event.detail || {});
        window.showAlert(
            detail.title || null,
            detail.message || detail.text || '',
            null,
            detail.type || 'auto'
        );
    });

    window.addEventListener('modal-alert', function(event) {
        const detail = Array.isArray(event.detail) ? event.detail[0] : (event.detail || {});
        window.showAlert(
            detail.title || null,
            detail.message || detail.text || '',
            null,
            detail.type || 'auto'
        );
    });

    window.addEventListener('toast', function(event) {
        const detail = Array.isArray(event.detail) ? event.detail[0] : (event.detail || {});
        window.showAlert(
            detail.title || null,
            detail.message || detail.text || '',
            null,
            detail.type || 'auto'
        );
    });

    // Also register Livewire.on listener if available
    document.addEventListener('livewire:init', function() {
        if (window.Livewire) {
            Livewire.on('show-alert', function(data) {
                const payload = Array.isArray(data) ? data[0] : (data || {});
                window.showAlert(
                    payload.title || null,
                    payload.message || payload.text || '',
                    null,
                    payload.type || 'auto'
                );
            });

            Livewire.on('modal-alert', function(data) {
                const payload = Array.isArray(data) ? data[0] : (data || {});
                window.showAlert(
                    payload.title || null,
                    payload.message || payload.text || '',
                    null,
                    payload.type || 'auto'
                );
            });

            Livewire.on('toast', function(data) {
                const payload = Array.isArray(data) ? data[0] : (data || {});
                window.showAlert(
                    payload.title || null,
                    payload.message || payload.text || '',
                    null,
                    payload.type || 'auto'
                );
            });

            Livewire.on('modal-confirm', function(data) {
                const payload = Array.isArray(data) ? data[0] : (data || {});
                window.showAlert(
                    payload.title || 'Konfirmasi Tindakan',
                    payload.message || payload.text || 'Apakah Anda yakin ingin melanjutkan?',
                    payload.onConfirm || null,
                    payload.type || 'warning'
                );
            });

            // Automatically catch flash messages / banners rendered inside Livewire component AJAX updates
            Livewire.hook('morph.updated', ({ el }) => {
                const alertElements = document.querySelectorAll('[data-alert-message]:not(#modal-alert-container)');
                alertElements.forEach(alertEl => {
                    if (!alertEl.dataset.alertDispatched) {
                        alertEl.dataset.alertDispatched = 'true';
                        const msg = alertEl.getAttribute('data-alert-message') || alertEl.innerText.trim();
                        const type = alertEl.getAttribute('data-alert-type') || 'auto';
                        if (msg) {
                            window.showAlert(null, msg, null, type);
                        }
                    }
                });
            });

            Livewire.hook('commit', ({ respond, succeed, fail }) => {
                fail(({ status, content }) => {
                    let errorMsg = 'Terjadi kendala sistem (HTTP ' + (status || '500') + ').';
                    try {
                        let parsed = JSON.parse(content);
                        if (parsed.message) errorMsg = parsed.message;
                    } catch(e) {}
                    window.showAlert('Peringatan Error Sistem', errorMsg, null, 'danger');
                });
            });
        }
    });

    document.addEventListener('livewire:navigated', function() {
        checkFlashMessages();
    });

    // Universal data-confirm Click Interceptor for MicroModal Confirmation Dialog (No Double Alert)
    document.addEventListener('click', function(e) {
        const targetBtn = e.target.closest('[data-confirm], [wire\\:confirm]');
        if (targetBtn) {
            if (targetBtn.dataset.micromodalConfirmed === 'true') {
                return; // Let confirmed click proceed to Livewire / event handlers
            }
            
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const message = targetBtn.getAttribute('data-confirm') || targetBtn.getAttribute('wire:confirm') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
            const isDelete = message.toLowerCase().includes('hapus') || message.toLowerCase().includes('batal') || message.toLowerCase().includes('void') || message.toLowerCase().includes('kosongkan');

            if (targetBtn.hasAttribute('wire:confirm')) {
                targetBtn.setAttribute('data-confirm', targetBtn.getAttribute('wire:confirm'));
                targetBtn.removeAttribute('wire:confirm');
            }

            window.showAlert('Konfirmasi Tindakan', message, function() {
                targetBtn.dataset.micromodalConfirmed = 'true';
                
                try {
                    targetBtn.click();
                } catch (err) {
                    targetBtn.dispatchEvent(new MouseEvent('click', {
                        bubbles: true,
                        cancelable: true,
                        composed: true,
                        view: window
                    }));
                }

                // Keep flag alive for this event cycle then clean up
                setTimeout(function() {
                    delete targetBtn.dataset.micromodalConfirmed;
                }, 400);
            }, isDelete ? 'delete' : 'warning');
        }
    }, true);

    function checkFlashMessages() {
        @if(session()->has('error'))
            window.showAlert('Peringatan Error', @json(session('error')), null, 'danger');
        @elseif(session()->has('danger'))
            window.showAlert('Gagal Memproses', @json(session('danger')), null, 'danger');
        @elseif(session()->has('success'))
            window.showAlert('Operasi Berhasil', @json(session('success')), null, 'create');
        @elseif(session()->has('message'))
            window.showAlert('Informasi Sistem', @json(session('message')), null, 'auto');
        @elseif(session()->has('warning'))
            window.showAlert('Perhatian Sistem', @json(session('warning')), null, 'warning');
        @elseif(session()->has('info'))
            window.showAlert('Informasi', @json(session('info')), null, 'info');
        @endif
    }

    // Global Modal Alert and Confirm Aliases
    window.showModalAlert = function({ title = 'Pemberitahuan', message = '', type = 'auto', okText = 'Mengerti', onOk = null }) {
        window.showAlert(title, message, onOk, type);
    };

    window.showModalConfirm = function({ title = 'Konfirmasi Tindakan', message = 'Apakah Anda yakin?', confirmText = 'Konfirmasi', type = 'warning', onConfirm = null }) {
        window.showAlert(title, message, onConfirm, type);
    };

    // Global Floating Toast & Centered Confirm Dialog System
    window.showAlert = function(title, message, onConfirm = null, type = 'auto') {
        const isConfirmation = typeof onConfirm === 'function';

        if (isConfirmation) {
            // === MODE 1: CONFIRMATION DIALOG (#modal-confirm via MicroModal) ===
            const modalElem = document.getElementById('modal-confirm');
            if (modalElem) {
                modalElem.classList.remove('is-open');
                modalElem.setAttribute('aria-hidden', 'true');
            }

            const accentElem = document.getElementById('modal-confirm-accent');
            const badgeElem = document.getElementById('modal-confirm-badge');
            const categoryElem = document.getElementById('modal-confirm-category');
            const titleElem = document.getElementById('modal-confirm-title');
            const contentElem = document.getElementById('modal-confirm-content');
            const actionBtn = document.getElementById('modal-confirm-action-btn');

            const msgLower = (message || '').toLowerCase();
            const typeLower = (type || '').toLowerCase();
            const isDelete = typeLower === 'delete' || msgLower.includes('hapus') || msgLower.includes('batal') || msgLower.includes('void') || msgLower.includes('kosongkan');

            let badgeSvg = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            let badgeBgClass = 'bg-amber-500 text-white shadow-md shadow-amber-500/30';
            let categoryClass = 'bg-amber-100 text-amber-950 border-amber-300';
            let categoryText = 'KONFIRMASI';
            let accentClass = 'bg-amber-500';
            let btnClass = 'bg-emerald-700 hover:bg-emerald-800 text-white';
            let btnText = 'Konfirmasi';

            if (isDelete) {
                badgeSvg = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';
                badgeBgClass = 'bg-rose-600 text-white shadow-md shadow-rose-600/30';
                categoryClass = 'bg-rose-100 text-rose-950 border-rose-300';
                categoryText = 'HAPUS DATA';
                accentClass = 'bg-rose-600';
                btnClass = 'bg-rose-600 hover:bg-rose-700 text-white';
                btnText = 'Ya, Hapus';
            }

            if (accentElem) accentElem.className = 'absolute top-0 left-0 right-0 h-1.5 ' + accentClass;
            if (badgeElem) {
                badgeElem.className = 'w-11 h-11 ' + MICRO_MODAL_ROUNDED_BADGE + ' flex items-center justify-center text-xl font-black shrink-0 ' + badgeBgClass;
                badgeElem.innerHTML = badgeSvg;
            }
            if (categoryElem) {
                categoryElem.className = 'px-2.5 py-0.5 ' + MICRO_MODAL_ROUNDED_CATEGORY + ' text-[10px] font-extrabold tracking-wider uppercase border ' + categoryClass;
                categoryElem.innerText = categoryText;
            }
            if (titleElem) titleElem.innerText = title || 'Konfirmasi Tindakan';
            if (contentElem) contentElem.innerText = message || 'Apakah Anda yakin ingin melanjutkan?';
            if (actionBtn) {
                actionBtn.innerText = btnText;
                actionBtn.className = 'px-6 py-2.5 ' + btnClass + ' ' + MICRO_MODAL_ROUNDED_BADGE + ' text-xs font-extrabold shadow-md transition cursor-pointer';
                actionBtn.onclick = function() {
                    window.closeConfirmModal();
                    if (typeof onConfirm === 'function') {
                        onConfirm();
                    }
                };
            }

            if (typeof MicroModal !== 'undefined') {
                try {
                    MicroModal.show('modal-confirm', {
                        awaitCloseAnimation: false,
                        awaitOpenAnimation: false,
                        disableScroll: false,
                    });
                } catch (e) {}
            }
            if (modalElem) {
                modalElem.classList.add('is-open');
                modalElem.setAttribute('aria-hidden', 'false');
            }
        } else {
            // === MODE 2: FLOATING TOAST NOTIFICATION ===
            showToastNotification(title, message, type);
        }
    };
</script>
