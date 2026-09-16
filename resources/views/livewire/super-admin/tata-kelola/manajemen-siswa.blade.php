<div class="space-y-6 font-sans">
    <!-- Quick Switcher Bar -->
    @include('livewire.super-admin.tata-kelola.manajemen-siswa.partials.switcher-bar')

    <!-- Header Title Bar -->
    <x-page-header 
        title="Kelola Data Siswa & Penempatan 2 Kelas" 
        subtitle="Pencatatan biodata siswa, penempatan Kelas Umum (1-6) & Kelas Tahfizh, dan akses portal."
        badge="MANAJEMEN DATA SISWA"
        badgeVariant="emerald"
        icon="users"
    >
        <x-slot:actions>
            @if(!auth()->user()->isSuperAdmin2())
            <x-button type="button" variant="primary" size="md" icon="plus" wire:click.prevent="openCreate">
                Tambah Siswa Baru
            </x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Data Siswa Aktif (Dua Kelas Per Siswa)"
        :steps="[
            ['title' => 'Tambah Siswa Baru', 'desc' => 'Klik Tambah Siswa Baru untuk mendaftarkan NIS, NISN, biodata, serta wali murid.'],
            ['title' => 'Penetapan 2 Kelas Wajib', 'desc' => 'Setiap siswa wajib memilih 1 Kelas Umum (1-6 A/B/C) dan 1 Kelas Tahfizh (Halaqah Ustadz/ah).'],
            ['title' => 'Perubahan Status', 'desc' => 'Ubah status keaktifan menjadi Lulus, Pindah, atau Keluar saat terjadi pembaruan status pendidikan.']
        ]"
        notes="Username & password otomatis dibuatkan untuk akses portal siswa dan wali murid."
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Content Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        @include('livewire.super-admin.tata-kelola.manajemen-siswa.partials.filter-bar')
        @include('livewire.super-admin.tata-kelola.manajemen-siswa.partials.table-students')
    </div>

    <!-- Form Floating Modal (Create / Edit Siswa) -->
    @include('livewire.super-admin.tata-kelola.manajemen-siswa.partials.modal-form')

    <!-- Student Detail Floating Modal -->
    @include('livewire.super-admin.tata-kelola.manajemen-siswa.partials.modal-detail')
</div>
