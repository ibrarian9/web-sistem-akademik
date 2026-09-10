<div class="space-y-6 font-sans">
    <!-- Navigation Tabs Menu (Top of Page) -->
    <x-finance.tagihan-nav-tabs active="tagihan" />

    <!-- Header Title Bar -->
    <x-page-header 
        title="Manajemen Tagihan Siswa" 
        subtitle="Buat, filter, edit, dan pantau status tagihan operasional/SPP siswa sesuai nominal masing-masing anak."
        badge="MANAJEMEN TAGIHAN & SPP"
        badgeVariant="emerald"
        icon="file-text"
    >
        <x-slot:actions>
            <x-button variant="primary" size="md" icon="plus" wire:click="openCreateModal">
                Rilis Tagihan Siswa
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Manajemen Tagihan & SPP Siswa" 
        :steps="[
            [
                'title' => '1. Rilis Tagihan Massal & Mandiri', 
                'desc' => 'Klik tombol Rilis Tagihan Siswa untuk menerbitkan tagihan sekaligus per kelas, seluruh siswa, atau memilih siswa tertentu. Berlaku untuk SPP bulanan maupun tagihan non-SPP (Uang Gedung, Seragam, dll).'
            ],
            [
                'title' => '2. Dukungan SPP Rp 0 (Beasiswa)', 
                'desc' => 'Untuk siswa penerima beasiswa / bebas biaya, masukkan nominal Rp 0. Sistem secara otomatis menandai tagihan sebagai LUNAS dan tidak mencatatnya sebagai tunggakan.'
            ],
            [
                'title' => '3. Proteksi Duplikasi Tagihan', 
                'desc' => 'Sistem mencegah pembuatan tagihan ganda pada periode dan kategori yang sama. Jika data sudah ada, sistem akan melewati tagihan tersebut dan menampilkan ringkasan.'
            ],
            [
                'title' => '4. Akses Detail & Kasir Langsung', 
                'desc' => 'Klik tombol Detail pada baris siswa untuk melihat rincian riwayat tagihan & kwitansi perorangan, atau klik tombol Input Bayar untuk langsung membuka kasir pembayaran.'
            ],
            [
                'title' => '5. Wewenang & Audit Trail', 
                'desc' => 'Role Finance, Super Admin, dan Founder memiliki akses penuh untuk menerbitkan, mengedit nominal/jatuh tempo, dan menghapus tagihan. Seluruh aksi terekam dalam Log Audit.'
            ]
        ]"
        notes="Gunakan filter kelas, kategori tagihan, status pelunasan, dan kolom pencarian untuk memantau rekapitulasi tunggakan siswa secara instan."
    />

    @if (session()->has('warning'))
        <div class="p-4 bg-amber-50 border border-amber-300 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-2xs" role="alert" data-alert-message="{{ session('warning') }}" data-alert-type="warning">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-amber-600 text-white rounded-xl shadow-xs shrink-0">
                    <x-lucide-alert-triangle class="w-5 h-5" />
                </div>
                <div>
                    <span class="text-xs font-black text-amber-950 block">{{ session('warning') }}</span>
                    <span class="text-[11px] text-amber-800 font-medium">Tagihan yang sudah ada sebelumnya tidak diduplikasi untuk menjaga keakuratan data pembukuan.</span>
                </div>
            </div>
            <x-button variant="primary" size="sm" icon="credit-card" href="{{ route('finance.input-pembayaran') }}">
                Buka Kasir Pembayaran
            </x-button>
        </div>
    @endif

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Content Table Card (Full Width) -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        @include('livewire.finance.manajemen-tagihan.partials.filter-bar')
        @include('livewire.finance.manajemen-tagihan.partials.table-students')
    </div>

    <!-- Floating Card: Form Edit Tagihan Siswa -->
    @include('livewire.finance.manajemen-tagihan.partials.modal-edit-tagihan')

    <!-- Floating Card: Form Rilis Tagihan Siswa (Single & Bulk) -->
    @include('livewire.finance.manajemen-tagihan.partials.modal-release-tagihan')

    <!-- Floating Card: Modal Rincian Cepat Seluruh Tagihan 1 Siswa -->
    @include('livewire.finance.manajemen-tagihan.partials.modal-quick-detail')
</div>
