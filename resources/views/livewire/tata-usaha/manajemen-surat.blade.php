<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Layanan Generator & PDF Surat Resmi" 
        subtitle="Pembuatan, pengeditan langsung, pratinjau, dan unduh PDF resmi SD TAHFIZH F3 Pekanbaru."
        badge="PERSURATAN & ARSIP TATA USAHA"
        badgeVariant="emerald"
        icon="file-text"
    >
        <x-slot:actions>
            <div class="flex items-center gap-1.5 bg-stone-100 p-1.5 rounded-2xl border border-stone-200 shadow-2xs">
                <x-button type="button" :variant="$activeTab === 'buat' ? 'primary' : 'ghost'" size="sm" icon="file-plus" wire:click="$set('activeTab', 'buat')">
                    Buat Surat Baru
                </x-button>
                <x-button type="button" :variant="$activeTab === 'riwayat' ? 'primary' : 'ghost'" size="sm" icon="history" wire:click="$set('activeTab', 'riwayat')">
                    Riwayat Surat
                </x-button>
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Layanan Persuratan & Cetak Dokumen Resmi Tata Usaha"
        :steps="[
            ['title' => 'Pilih Template & Penerima', 'desc' => 'Pilih jenis surat dan pilih nama Siswa / Guru untuk pengisian data otomatis.'],
            ['title' => 'Edit Live & Pratinjau PDF', 'desc' => 'Periksa isian surat. Pada modal pratinjau, Anda dapat mengedit teks secara langsung (Live Interactive Editor).'],
            ['title' => 'Unduh PDF / Cetak Resmi', 'desc' => 'Klik tombol Unduh File PDF untuk mengunduh berkas .pdf resmi atau Cetak Dokumen untuk mencetak Kop YFI/SD Tahfizh F3.']
        ]"
        notes="Format Kop & susunan surat 100% disesuaikan dengan dokumen resmi Yayasan F3 / SD Tahfizh F3 Pekanbaru."
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if ($activeTab === 'buat')
        @include('livewire.tata-usaha.manajemen-surat.partials.template-cards')
        @include('livewire.tata-usaha.manajemen-surat.partials.form-builder')
    @else
        @include('livewire.tata-usaha.manajemen-surat.partials.table-riwayat')
    @endif

    @include('livewire.tata-usaha.manajemen-surat.partials.modal-preview-print')
</div>
