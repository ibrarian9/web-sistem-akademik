<div class="space-y-6 font-sans" x-data="{ previewOpen: false, previewSrc: '', previewTitle: '' }" x-init="$watch('$wire.showPreviewBuktiModal', val => { if(val) { previewSrc = $wire.previewBuktiUrl; previewTitle = $wire.previewBuktiTitle; previewOpen = true; } })" @keydown.escape.window="previewOpen = false">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Gabungan Arus Kas Keluar" 
        subtitle="Pusat analitik & rekapitulasi seluruh pengeluaran: Operasional Yayasan, Gaji Guru, serta Fasilitas Kasbon (Non-BOS)."
        icon="trending-down"
    >
        <x-slot:actions>
            <x-button variant="outline" size="sm" icon="printer" wire:click="exportPdf" :disabled="$paginatedOutflows->total() === 0" title="{{ $paginatedOutflows->total() === 0 ? 'Tidak ada catatan pengeluaran untuk diekspor' : 'Ekspor Dokumen PDF Sesuai Filter' }}">
                Cetak PDF
            </x-button>
            <x-button variant="outline" size="sm" icon="file-spreadsheet" wire:click="exportExcel" :disabled="$paginatedOutflows->total() === 0" title="{{ $paginatedOutflows->total() === 0 ? 'Tidak ada catatan pengeluaran untuk diekspor' : 'Ekspor Spreadsheet Excel Sesuai Filter' }}">
                Ekspor Excel
            </x-button>
            @if(!auth()->user()->isSuperAdmin2())
            <x-button variant="danger-solid" size="sm" icon="plus" wire:click="openCreateModal">
                Catat Kas Keluar
            </x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Monitoring & Gabungan Arus Kas Keluar"
        :steps="[
            ['title' => 'Grafik Tren Bulanan', 'desc' => 'Visualisasi tren arus kas keluar 6 bulan terakhir merangkum perbandingan beban operasional yayasan, payroll gaji, dan kasbon.'],
            ['title' => 'Filter Multi-Stream', 'desc' => 'Pilih tab stream (Operasional Yayasan, Gaji Guru, Kasbon) atau periode (Hari Ini, Kemarin, Minggu Ini, Bulan Ini) untuk audit spesifik.'],
            ['title' => 'Pencatatan Cepat', 'desc' => 'Klik Catat Kas Keluar untuk mendokumentasikan beban pengeluaran kas non-BOS seperti ATK, sarpras, listrik/air, atau konsumsi.']
        ]"
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    @include('livewire.finance.arus-kas-keluar.partials.metric-cards')

    @include('livewire.finance.arus-kas-keluar.partials.charts-overview')

    <!-- MAIN UNIFIED DATA TABLE PANEL -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        @include('livewire.finance.arus-kas-keluar.partials.filter-bar')

        @include('livewire.finance.arus-kas-keluar.partials.table-outflows')
    </div>

    @include('livewire.finance.arus-kas-keluar.partials.modal-create')

    @include('livewire.finance.arus-kas-keluar.partials.modal-edit')

    @include('livewire.finance.arus-kas-keluar.partials.modal-lightbox')
</div>
