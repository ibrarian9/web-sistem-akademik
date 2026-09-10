<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Audit Log Aktivitas Sistem" 
        subtitle="Pantau seluruh riwayat aksi, perubahan data, alamat IP, dan aktivitas user pada sistem secara real-time."
        badge="ACTIVITY TRACKER"
        badgeVariant="emerald"
        icon="activity"
    >
        <x-slot:actions>
            <div class="flex items-center gap-1.5 bg-stone-100 border border-stone-200 p-1 rounded-xl overflow-x-auto shadow-2xs">
                <x-button type="button" :variant="$filterPeriode === '' ? 'primary' : 'ghost'" size="xs" wire:click="$set('filterPeriode', '')">
                    Semua Waktu
                </x-button>
                <x-button type="button" :variant="$filterPeriode === 'today' ? 'primary' : 'ghost'" size="xs" wire:click="setPeriodPreset('today')">
                    Hari Ini
                </x-button>
                <x-button type="button" :variant="$filterPeriode === 'yesterday' ? 'primary' : 'ghost'" size="xs" wire:click="setPeriodPreset('yesterday')">
                    Kemarin
                </x-button>
                <x-button type="button" :variant="$filterPeriode === 'this_week' ? 'primary' : 'ghost'" size="xs" wire:click="setPeriodPreset('this_week')">
                    Minggu Ini
                </x-button>
                <x-button type="button" :variant="$filterPeriode === 'this_month' ? 'primary' : 'ghost'" size="xs" wire:click="setPeriodPreset('this_month')">
                    Bulan Ini
                </x-button>
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Monitoring Audit Log Sistem"
        :steps="[
            ['title' => 'Jejak Audit Real-time', 'desc' => 'Tabel mencatat seluruh aksi entri, update, dan penghapusan data beserta IP pelakunya.'],
            ['title' => 'Filter Periode & Event', 'desc' => 'Gunakan filter periode (Hari Ini, Kemarin, Minggu Ini, Bulan Ini) atau filter event (Created, Updated, Deleted) untuk penelusuran cepat.'],
            ['title' => 'Pencarian Pengguna', 'desc' => 'Cari nama user atau alamat IP tertentu pada kotak pencarian di bagian atas.']
        ]"
    />

    <!-- Content Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        @include('livewire.super-admin.tata-kelola.audit-log.partials.filter-bar')
        @include('livewire.super-admin.tata-kelola.audit-log.partials.table')
    </div>

    <!-- Audit Log Detail Modal -->
    @include('livewire.super-admin.tata-kelola.audit-log.partials.modal-detail')
</div>
