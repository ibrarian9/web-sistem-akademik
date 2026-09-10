<div class="space-y-6 font-sans">
    <!-- Header Page -->
    <x-page-header 
        :title="'Tagihan: ' . ($siswa->user->nama ?? 'Siswa')" 
        :subtitle="'NIS: ' . ($siswa->nis ?? '-') . ' • Kelas: ' . ($siswa->kelas->nama_kelas ?? '-') . ' • Wali: ' . ($siswa->nama_wali ?: '-') . ' • Kontak: ' . ($siswa->no_hp_wali ?: ($siswa->user->no_hp ?? '-'))"
        icon="file-text"
        :breadcrumbs="[
            ['label' => 'Manajemen Tagihan', 'url' => route('finance.tagihan')],
            ['label' => 'Rincian: ' . ($siswa->user->nama ?? 'Siswa')]
        ]"
    >
        <x-slot:actions>
            <div class="flex items-center gap-2 flex-wrap">
                <x-button variant="secondary" size="md" icon="arrow-left" href="{{ route('finance.tagihan') }}">
                    Kembali
                </x-button>
                @if (!auth()->user()->isSuperAdmin2())
                    <x-button variant="primary" size="md" icon="credit-card" href="{{ route('finance.input-pembayaran', ['siswa_id' => $siswa->id]) }}">
                        Buka Kasir Siswa Ini
                    </x-button>
                    <x-button variant="primary" size="md" icon="plus-circle" wire:click="openCreateModal">
                        + Tambah Tagihan
                    </x-button>
                @endif
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Alerts Notification -->
    @if (session()->has('success'))
        <x-alert-banner type="success" :message="session('success')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="danger" :message="session('error')" />
    @endif

    <!-- Financial Stat Cards for This Student -->
    @include('livewire.finance.detail-tagihan-siswa.partials.stats-cards')

    <!-- Widget Matriks 12 Bulan SPP Siswa -->
    @include('livewire.finance.detail-tagihan-siswa.partials.spp-matrix-widget')

    <!-- Main Invoices Table Card with Multi-Filter -->
    @include('livewire.finance.detail-tagihan-siswa.partials.table-invoices')

    <!-- Riwayat Transaksi Pembayaran Siswa -->
    @include('livewire.finance.detail-tagihan-siswa.partials.table-payment-history')

    <!-- Modal Create Tagihan -->
    @include('livewire.finance.detail-tagihan-siswa.partials.modal-create-tagihan')

    <!-- Modal Edit Tagihan -->
    @include('livewire.finance.detail-tagihan-siswa.partials.modal-edit-tagihan')

    <!-- Modal Bukti Pembayaran -->
    @include('livewire.finance.detail-tagihan-siswa.partials.modal-bukti-pembayaran')
</div>
