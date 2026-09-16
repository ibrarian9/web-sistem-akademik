<div class="space-y-6">
    <!-- HERO / PAGE HEADER -->
    <x-page-header 
        title="Catatan Pendampingan Siswa Berkebutuhan Khusus" 
        subtitle="Modul terdedikasi Guru Pendamping untuk mendokumentasikan observasi berkala, standarisasi capaian kualitatif (BB, MB, BSH, BSB), rekapitulasi capaian per aspek, serta penerbitan laporan resmi."
        badge="GURU PENDAMPING & ABK"
        icon="clipboard-list"
    >
        <x-slot:actions>
            @if (!auth()->user()->isSuperAdmin2())
                <x-button type="button" variant="primary" size="md" icon="plus" wire:click="openCreateModal">
                    Tambah Catatan Baru
                </x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    @include('livewire.guru.catatan-pendampingan.partials.tab-navigation')

    <!-- FLASH MESSAGES -->
    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif
    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- TAB 1: JURNAL OBSERVASI BERKALA -->
    @if ($tab === 'daftar')
        @include('livewire.guru.catatan-pendampingan.partials.tab-daftar')
    @endif

    <!-- TAB 2: REKAPITULASI PERKEMBANGAN SISWA -->
    @if ($tab === 'rekap')
        @include('livewire.guru.catatan-pendampingan.partials.tab-rekap')
    @endif

    <!-- TAB 3: PRATINJAU & CETAK LAPORAN -->
    @if ($tab === 'pratinjau')
        @include('livewire.guru.catatan-pendampingan.partials.tab-pratinjau')
    @endif

    <!-- MODAL FORM INPUT / EDIT -->
    @include('livewire.guru.catatan-pendampingan.partials.modal-form')

    <!-- MODAL KONFIRMASI HAPUS -->
    @include('livewire.guru.catatan-pendampingan.partials.modal-delete')
</div>
