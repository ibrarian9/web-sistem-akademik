import MicroModal from 'micromodal';
import Chart from 'chart.js/auto';

// Expose globally
window.MicroModal = MicroModal;
window.Chart = Chart;

// Initialize MicroModal
document.addEventListener('DOMContentLoaded', () => {
    MicroModal.init({
        onShow: (modal) => {},
        onClose: (modal) => {},
        openTrigger: 'data-micromodal-trigger',
        closeTrigger: 'data-micromodal-close',
        openClass: 'is-open',
        disableScroll: true,
        disableFocus: false,
        awaitOpenAnimation: true,
        awaitCloseAnimation: true,
        debugMode: false,
    });
});

// Global Helper: Show Alert Dialog via MicroModal
let currentAlertCallback = null;
window.showModalAlert = function ({ title = 'Pemberitahuan', message = '', type = 'info', okText = 'Mengerti', onOk = null }) {
    const modalEl = document.getElementById('global-alert-modal');
    if (!modalEl) {
        alert(message || title);
        if (onOk) onOk();
        return;
    }

    const titleEl = document.getElementById('global-alert-title');
    const messageEl = document.getElementById('global-alert-message');
    const iconContainerEl = document.getElementById('global-alert-icon-container');
    const okBtnEl = document.getElementById('global-alert-ok-btn');

    if (titleEl) titleEl.innerText = title;
    if (messageEl) messageEl.innerText = message;
    if (okBtnEl) okBtnEl.innerText = okText;

    // Set icon & colors based on type
    if (iconContainerEl) {
        let iconHtml = '';
        let colorClasses = '';

        if (type === 'success') {
            colorClasses = 'bg-emerald-100 text-emerald-700 border-emerald-300';
            iconHtml = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        } else if (type === 'error' || type === 'danger') {
            colorClasses = 'bg-rose-100 text-rose-700 border-rose-300';
            iconHtml = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        } else if (type === 'warning') {
            colorClasses = 'bg-amber-100 text-amber-700 border-amber-300';
            iconHtml = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
        } else {
            colorClasses = 'bg-sky-100 text-sky-700 border-sky-300';
            iconHtml = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        }

        iconContainerEl.className = `p-3 rounded-2xl border ${colorClasses} shrink-0`;
        iconContainerEl.innerHTML = iconHtml;
    }

    currentAlertCallback = onOk;
    MicroModal.show('global-alert-modal');
};

window.closeModalAlert = function () {
    MicroModal.close('global-alert-modal');
    if (typeof currentAlertCallback === 'function') {
        currentAlertCallback();
        currentAlertCallback = null;
    }
};

// Global Helper: Show Confirmation Dialog via MicroModal
let currentConfirmCallback = null;
let currentCancelCallback = null;

window.showModalConfirm = function ({
    title = 'Konfirmasi Tindakan',
    message = 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    confirmText = 'Ya, Konfirmasi',
    cancelText = 'Batal',
    type = 'warning',
    onConfirm = null,
    onCancel = null,
}) {
    const modalEl = document.getElementById('global-confirm-modal');
    if (!modalEl) {
        if (confirm(message)) {
            if (onConfirm) onConfirm();
        } else {
            if (onCancel) onCancel();
        }
        return;
    }

    const titleEl = document.getElementById('global-confirm-title');
    const messageEl = document.getElementById('global-confirm-message');
    const confirmBtnEl = document.getElementById('global-confirm-btn');
    const cancelBtnEl = document.getElementById('global-confirm-cancel-btn');
    const iconContainerEl = document.getElementById('global-confirm-icon-container');

    if (titleEl) titleEl.innerText = title;
    if (messageEl) messageEl.innerText = message;
    if (confirmBtnEl) confirmBtnEl.innerText = confirmText;
    if (cancelBtnEl) cancelBtnEl.innerText = cancelText;

    if (iconContainerEl) {
        let colorClasses = type === 'danger' ? 'bg-rose-100 text-rose-700 border-rose-300' : 'bg-amber-100 text-amber-700 border-amber-300';
        let iconHtml = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
        iconContainerEl.className = `p-3 rounded-2xl border ${colorClasses} shrink-0`;
        iconContainerEl.innerHTML = iconHtml;
    }

    currentConfirmCallback = onConfirm;
    currentCancelCallback = onCancel;
    MicroModal.show('global-confirm-modal');
};

window.executeModalConfirm = function () {
    MicroModal.close('global-confirm-modal');
    if (typeof currentConfirmCallback === 'function') {
        currentConfirmCallback();
        currentConfirmCallback = null;
    }
};

window.cancelModalConfirm = function () {
    MicroModal.close('global-confirm-modal');
    if (typeof currentCancelCallback === 'function') {
        currentCancelCallback();
        currentCancelCallback = null;
    }
};

// Listen for Livewire alert dispatch
document.addEventListener('livewire:initialized', () => {
    Livewire.on('modal-alert', (data) => {
        const payload = Array.isArray(data) ? data[0] : data;
        window.showModalAlert(payload || {});
    });

    Livewire.on('modal-confirm', (data) => {
        const payload = Array.isArray(data) ? data[0] : data;
        window.showModalConfirm(payload || {});
    });
});
