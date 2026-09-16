<div class="space-y-6 font-sans" x-data="{ previewOpen: false, previewSrc: '', previewTitle: '' }" x-init="$watch('$wire.showPreviewBuktiModal', val => { if(val) { previewSrc = $wire.previewBuktiUrl; previewTitle = $wire.previewBuktiTitle; previewOpen = true; } })" @keydown.escape.window="previewOpen = false">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Arus Kas (Cash Flow)" 
        subtitle="Buku kas & jurnal terpadu arus masuk (SPP, Infaq, Tabungan) serta arus keluar (Operasional, Gaji, Kasbon)."
        badge="BUKU KAS UTAMA"
        badgeVariant="emerald"
        icon="layers"
    >
        <x-slot:actions>
            @if(!auth()->user()->isSuperAdmin2())
            <x-button variant="primary" size="sm" icon="plus" wire:click="openIncomeModal">
                Catat Kas Masuk
            </x-button>
            <x-button variant="danger-solid" size="sm" icon="minus" wire:click="openExpenseModal">
                Catat Kas Keluar
            </x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Manajemen Arus Kas (Cash Flow)"
        :steps="[
            ['title' => 'Grafik Komparasi Arus Kas', 'desc' => 'Tinjau perbandingan kas masuk (hijau) vs kas keluar (merah) 6 bulan terakhir untuk memantau surplus/defisit likuiditas.'],
            ['title' => 'Tab Filter Cepat', 'desc' => 'Gunakan tab Semua Arus Kas, Kas Masuk Saja, atau Kas Keluar Saja untuk memfilter jurnal pembukuan.'],
            ['title' => 'Pencatatan Cepat', 'desc' => 'Gunakan tombol di atas untuk mendokumentasikan penerimaan infaq/donasi yayasan atau pengeluaran operasional sekolah.']
        ]"
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- 3 Core Metric Cards (Cash Inflow, Cash Outflow, Net Balance) -->
    @include('livewire.finance.arus-kas.partials.stats-overview')

    <!-- VISUAL ANALYTICS: DUAL BAR MONTHLY INFLOW VS OUTFLOW CHART & BREAKDOWN -->
    @include('livewire.finance.arus-kas.partials.analytics-charts')

    <!-- MAIN JURNAL BUKU KAS TABLE PANEL -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        @include('livewire.finance.arus-kas.partials.filter-bar')
        @include('livewire.finance.arus-kas.partials.table-transactions')
    </div>

    <!-- MODAL 1: Catat Kas Masuk Yayasan -->
    @include('livewire.finance.arus-kas.partials.modal-create-income')

    <!-- MODAL 2: Catat Kas Keluar Operasional -->
    @include('livewire.finance.arus-kas.partials.modal-create-expense')

    <!-- MODAL 3: Edit Pengeluaran & Bukti Pembayaran -->
    @include('livewire.finance.arus-kas.partials.modal-edit-expense')

    <!-- ALPINE.JS LIGHTBOX PREVIEW MODAL (CLIENT-SIDE) -->
    @include('livewire.finance.arus-kas.partials.modal-lightbox-bukti')
</div>
