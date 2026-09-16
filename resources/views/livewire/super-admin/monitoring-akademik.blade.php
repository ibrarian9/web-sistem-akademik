<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Monitoring Akademik & Pembelajaran" 
        subtitle="Pusat supervisi terpadu rekapitulasi nilai siswa, pengawasan kurikulum Bab & TP, serta pemantauan presensi harian."
        badge="SUPERVISI AKADEMIK"
        badgeVariant="emerald"
        icon="activity"
    />

    <!-- Quick KPI Summary Cards -->
    @include('livewire.super-admin.monitoring-akademik.partials.kpi-cards')

    <!-- Main Navigation Tabs -->
    @include('livewire.super-admin.monitoring-akademik.partials.tab-navigation')

    <!-- Tab 0: Monitoring Progres Guru -->
    @include('livewire.super-admin.monitoring-akademik.partials.tab-progres-guru')

    <!-- Tab 1: Monitoring Rekap Nilai Siswa -->
    @include('livewire.super-admin.monitoring-akademik.partials.tab-rekap-nilai')

    <!-- Tab 2: Monitoring Bab & TP (Kurikulum Merdeka) -->
    @include('livewire.super-admin.monitoring-akademik.partials.tab-monitoring-kurikulum')

    <!-- Tab 3: Monitoring Absensi (Siswa & Guru) -->
    @include('livewire.super-admin.monitoring-akademik.partials.tab-monitoring-absensi')
</div>
