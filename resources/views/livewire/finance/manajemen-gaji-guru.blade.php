<div class="space-y-6 font-sans pb-16">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Honorarium & Gaji Pegawai" 
        subtitle="Kelola honorarium bulanan pegawai Yayasan F3, gaji pokok, berkala, insentif, ekskul, potongan sosial, kasbon, dan slip gaji digital."
        badge="PAYROLL YAYASAN"
        badgeVariant="emerald"
        icon="wallet"
    >
        <x-slot:actions>
            @if(!auth()->user()->isSuperAdmin2())
            <x-button variant="secondary" size="md" icon="user-plus" wire:click="openCreateModal">
                Buat Gaji Manual
            </x-button>

            <x-button variant="primary" size="md" icon="calendar-plus" wire:click="openGenerateModal">
                Generate Draf Gaji
            </x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Honorarium & Gaji Pegawai" 
        :steps="[
            [
                'title' => '1. Generate Draf Gaji Otomatis', 
                'desc' => 'Klik tombol Generate Draf Gaji untuk menghitung honorarium seluruh pegawai pada bulan tertentu secara otomatis berdasarkan formula tunjangan dan potongan kasbon.'
            ],
            [
                'title' => '2. Buat Gaji Manual Satuan', 
                'desc' => 'Gunakan tombol Buat Gaji Manual jika ingin menambahkan data penggajian pegawai secara perorangan di luar jadwal generate massal.'
            ],
            [
                'title' => '3. Tinjau & Edit Komponen Gaji', 
                'desc' => 'Klik tombol Detail / Edit pada baris pegawai untuk meninjau atau menyesuaikan komponen gaji pokok, tunjangan berkala/jabatan, honor mengajar, insentif, dan potongan.'
            ],
            [
                'title' => '4. Pembayaran & Sinkronisasi Kas', 
                'desc' => 'Klik tombol Bayar pada baris draf atau bayar massal via centang checkbox. Transaksi otomatis membukukan pengeluaran ke Arus Kas Yayasan.'
            ],
            [
                'title' => '5. Cetak Slip Gaji Satuan & Massal', 
                'desc' => 'Cetak slip gaji resmi pegawai perorangan melalui tombol Slip Gaji, atau cetak seluruh slip gaji dalam satu file PDF melalui tombol Cetak Massal Slip.'
            ],
            [
                'title' => '6. Riwayat Gaji & Koreksi Pembatalan', 
                'desc' => 'Klik tombol Riwayat untuk melihat histori penggajian pegawai. Jika ada koreksi, pembayaran dapat dibatalkan kembali ke draf dan kas keluar otomatis disesuaikan.'
            ]
        ]"
        notes="Gaji berstatus Draf belum memotong arus kas yayasan. Kas keluar baru tercatat otomatis saat status gaji diubah menjadi Dibayar."
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif
    @if (session()->has('error'))
        <x-alert-banner type="danger" :message="session('error')" />
    @endif

    <!-- Summary KPI Cards -->
    @include('livewire.finance.manajemen-gaji-guru.partials.stats-cards')

    <!-- Search, Filters, Bulk Actions & Salary Table Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-4 sm:p-6 shadow-xs space-y-4">
        @include('livewire.finance.manajemen-gaji-guru.partials.filter-bar')
        @include('livewire.finance.manajemen-gaji-guru.partials.table-salary')
    </div>

    <!-- Modals -->
    @include('livewire.finance.manajemen-gaji-guru.partials.modal-generate-massal')
    @include('livewire.finance.manajemen-gaji-guru.partials.modal-create-salary')
    @include('livewire.finance.manajemen-gaji-guru.partials.modal-edit-salary')
    @include('livewire.finance.manajemen-gaji-guru.partials.modal-detail-salary')
    @include('livewire.finance.manajemen-gaji-guru.partials.modal-preview-slip')
    @include('livewire.finance.manajemen-gaji-guru.partials.modal-pay-salary')

    <script>
        window.formatRupiahInput = window.formatRupiahInput || function(v) {
            if (v === null || v === undefined || v === '') return '0';
            if (typeof v === 'number') return Math.round(v).toLocaleString('id-ID');
            let s = v.toString().trim();
            if (/^-?\d+\.\d{1,2}$/.test(s)) return Math.round(parseFloat(s)).toLocaleString('id-ID');
            let clean = s.replace(/[^0-9]/g, '');
            return clean ? Number(clean).toLocaleString('id-ID') : '0';
        };
    </script>
</div>
