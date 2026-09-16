<div class="space-y-6 font-sans pb-16">
    <!-- Header Title Bar -->
    <x-page-header 
        :title="'Riwayat Gaji: ' . ($guru?->user?->nama ?? 'Pegawai')" 
        :subtitle="'NIY: ' . ($guru?->niy ?? ($guru?->nip ?? '-')) . ' • Jabatan: ' . ($guru?->jabatan ?: 'Guru / Pegawai') . ' • Jam Kerja: ' . ($guru?->jam_kerja ?: '07.00 - 14.00') . ' • Status: ' . ucwords(str_replace('_', ' ', $guru?->status_kepegawaian ?? 'Tetap'))"
        badge="RINCIAN PAYROLL PEGAWAI"
        badgeVariant="emerald"
        icon="history"
    >
        <x-slot:actions>
            <x-button variant="secondary" size="md" icon="arrow-left" href="{{ route('finance.gaji-guru') }}">
                Kembali ke Penggajian
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Employee Profile Banner -->
    @include('livewire.finance.detail-gaji-guru.partials.profile-banner')

    <!-- Financial KPI Summary Cards -->
    @include('livewire.finance.detail-gaji-guru.partials.stat-cards')

    <!-- Filters & Salary History Table -->
    <div class="bg-white border border-stone-200 rounded-2xl p-4 sm:p-6 shadow-xs space-y-4">
        @include('livewire.finance.detail-gaji-guru.partials.filter-bar')
        @include('livewire.finance.detail-gaji-guru.partials.table-salary-history')
    </div>

    <!-- Modal Detail Rincian Gaji Pegawai -->
    @include('livewire.finance.detail-gaji-guru.partials.modal-detail-salary')

    <!-- Modal Pratinjau Slip Gaji PDF -->
    @include('livewire.finance.detail-gaji-guru.partials.modal-preview-slip')
</div>
