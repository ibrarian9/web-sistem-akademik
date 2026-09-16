<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Jadwal Pelajaran Per Kelas" 
        subtitle="Kelola dan pantau jam pelajaran yang sudah terisi di setiap hari untuk setiap rombel kelas."
        badge="JADWAL PELAJARAN"
        badgeVariant="emerald"
        icon="calendar"
    >
        <x-slot:actions>
            <div class="bg-stone-100 border border-stone-200 p-1 rounded-xl flex items-center gap-1 shadow-2xs">
                <x-button type="button" :variant="$viewMode === 'grid' ? 'primary' : 'ghost'" size="sm" icon="layout-grid" wire:click="$set('viewMode', 'grid')">
                    Matriks Per Kelas
                </x-button>
                <x-button type="button" :variant="$viewMode === 'table' ? 'primary' : 'ghost'" size="sm" icon="table" wire:click="$set('viewMode', 'table')">
                    Daftar Tabel
                </x-button>
            </div>
            @if (!auth()->user()?->isSuperAdmin2())
                <x-button type="button" variant="primary" size="md" icon="plus" wire:click="openCreate">
                    Tambah Jadwal
                </x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Jadwal Pelajaran"
        :steps="[
            ['title' => 'Pilih Kelas', 'desc' => 'Klik tombol nama kelas untuk langsung melihat matriks jadwal mingguan (Senin–Sabtu) kelas tersebut.'],
            ['title' => 'Lihat Jam Terisi & Tambah Jam', 'desc' => 'Sistem menampilkan jam pelajaran yang sudah terisi di setiap hari. Klik tombol Tambah Jam di bawah hari yang diinginkan.'],
            ['title' => 'Deteksi Bentrok Otomatis', 'desc' => 'Sistem otomatis memvalidasi jadwal agar tidak ada bentrok mengajar guru atau bentrok ruang kelas.']
        ]"
        notes="Pastikan guru pengampu telah dipetakan pada kelas di menu Manajemen Mapel sebelum membuat jadwal."
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif   

    <!-- Tab Type Switcher -->
    <div class="bg-white border border-stone-200 p-2 rounded-2xl shadow-xs flex items-center gap-2">
        <button type="button" wire:click="$set('jadwalType', 'reguler')" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $jadwalType === 'reguler' ? 'bg-emerald-700 text-white shadow-xs' : 'text-stone-600 hover:bg-stone-100' }}">
            <x-lucide-calendar class="w-4 h-4" />
            <span>Jadwal Pelajaran Reguler</span>
        </button>
        <button type="button" wire:click="$set('jadwalType', 'ekstrakurikuler')" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $jadwalType === 'ekstrakurikuler' ? 'bg-emerald-700 text-white shadow-xs' : 'text-stone-600 hover:bg-stone-100' }}">
            <x-lucide-award class="w-4 h-4" />
            <span>Jadwal Ekstrakurikuler</span>
        </button>
    </div>

    @if ($jadwalType === 'reguler')
        @include('livewire.super-admin.tata-kelola.manajemen-jadwal.partials.selector-kelas')

        @if ($viewMode === 'grid')
            @include('livewire.super-admin.tata-kelola.manajemen-jadwal.partials.grid-matriks')
        @else
            @include('livewire.super-admin.tata-kelola.manajemen-jadwal.partials.table-jadwal')
        @endif
    @elseif ($jadwalType === 'ekstrakurikuler')
        @include('livewire.super-admin.tata-kelola.manajemen-jadwal.partials.table-ekskul')
    @endif

    @include('livewire.super-admin.tata-kelola.manajemen-jadwal.partials.modal-ekskul')
    @include('livewire.super-admin.tata-kelola.manajemen-jadwal.partials.modal-jadwal')
</div>
