<div class="space-y-6 font-sans">
    <!-- Header Page -->
    <x-page-header 
        title="Manajemen Tabungan Siswa" 
        subtitle="Kelola transaksi setoran & penarikan tabungan siswa serta pantau saldo dan jurnal mutasi secara akurat."
        badge="TABUNGAN & SIMPANAN SISWA"
        badgeVariant="emerald"
        icon="wallet"
    />

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Manajemen Tabungan Siswa"
        :steps="[
            ['title' => 'Tabel Saldo Santri', 'desc' => 'Tabel atas menampilkan saldo terkini setiap santri. Klik Setor atau Tarik untuk mencatat mutasi baru secara instan.'],
            ['title' => 'Jurnal & Riwayat Seluruh Mutasi', 'desc' => 'Tabel bawah menampilkan catatan kronologis seluruh mutasi tabungan yang pernah diinputkan, dilengkapi filter tanggal & ekspor PDF/Excel.'],
            ['title' => 'Koreksi Data', 'desc' => 'Finance dan Founder dapat mengedit atau menghapus entri transaksi tabungan secara langsung pada buku mutasi.']
        ]"
    />

    <!-- Alert Success / Error Notification -->
    @if (session()->has('success'))
        <x-alert-banner type="success" :message="session('success')" />
    @endif
    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Metric Summary Cards -->
    @include('livewire.finance.tabungan-siswa.partials.stats-cards')

    <!-- 1. TABEL UTAMA: SALDO TABUNGAN PER SISWA -->
    @include('livewire.finance.tabungan-siswa.partials.table-student-balances')

    <!-- 2. TABEL BAWAH: JURNAL & RIWAYAT SELURUH MUTASI TRANSAKSI TABUNGAN SISWA -->
    @include('livewire.finance.tabungan-siswa.partials.table-mutation-history')

    <!-- Floating Card Form Transaction (Setor / Tarik) -->
    @include('livewire.finance.tabungan-siswa.partials.modal-transaction')

    <!-- Floating Card History Mutasi 1 Siswa -->
    @include('livewire.finance.tabungan-siswa.partials.modal-student-history')

    <!-- Floating Card Form Edit Transaksi Tabungan (Founder & Finance) -->
    @include('livewire.finance.tabungan-siswa.partials.modal-edit-transaction')
</div>
