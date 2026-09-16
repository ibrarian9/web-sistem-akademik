<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-stone-200">
        <div>
            <h1 class="text-2xl font-black text-stone-800 tracking-tight flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600 border border-amber-200">
                    <x-lucide-shield-check class="w-6 h-6" />
                </span>
                Persetujuan Aksi Keuangan
            </h1>
            <p class="text-sm text-stone-500 mt-1">
                Persetujuan berjenjang untuk aksi <span class="font-semibold text-stone-700">Edit</span> dan <span class="font-semibold text-stone-700">Hapus</span> data keuangan oleh Super Admin & Super Admin 2.
            </p>
        </div>

        @if ($canApprove)
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-green-50 border border-green-200 text-xs font-semibold text-green-700 shadow-sm self-start sm:self-auto">
                <x-lucide-check-circle class="w-4 h-4 text-green-600" />
                Hak Approval Aktif (Super Admin / Super Admin 2)
            </div>
        @else
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-stone-100 border border-stone-200 text-xs font-semibold text-stone-600 shadow-sm self-start sm:self-auto">
                <x-lucide-info class="w-4 h-4 text-stone-500" />
                Mode Pemantauan Status (Keuangan)
            </div>
        @endif
    </div>

    <!-- Alert Messages -->
    @if (session()->has('success'))
        <div class="p-4 rounded-xl bg-green-50 border border-green-200 flex items-start gap-3 text-green-800 text-sm">
            <x-lucide-check-circle class="w-5 h-5 text-green-600 shrink-0 mt-0.5" />
            <div class="flex-1 font-medium">{{ session('success') }}</div>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3 text-red-800 text-sm">
            <x-lucide-alert-circle class="w-5 h-5 text-red-600 shrink-0 mt-0.5" />
            <div class="flex-1 font-medium">{{ session('error') }}</div>
        </div>
    @endif

    <!-- Metrics Cards -->
    @include('livewire.finance.approval-keuangan.partials.metrics-cards')

    <!-- Filters & Search Toolbar -->
    @include('livewire.finance.approval-keuangan.partials.filter-bar')

    <!-- Bulk Action Floating Bar & Bulk Action Modals -->
    @include('livewire.finance.approval-keuangan.partials.modal-bulk-actions')

    <!-- Approvals Table -->
    @include('livewire.finance.approval-keuangan.partials.table-approvals')

    <!-- DETAIL MODAL -->
    @include('livewire.finance.approval-keuangan.partials.modal-detail')

    <!-- APPROVE MODAL -->
    @include('livewire.finance.approval-keuangan.partials.modal-approve')

    <!-- REJECT MODAL -->
    @include('livewire.finance.approval-keuangan.partials.modal-reject')

    <!-- CANCEL MODAL -->
    @include('livewire.finance.approval-keuangan.partials.modal-cancel')
</div>
