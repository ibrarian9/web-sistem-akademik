<!-- Quick KPI Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <x-stat-card 
        title="Rata-rata Nilai Rapor" 
        :value="$avgNilaiSekolah > 0 ? number_format($avgNilaiSekolah, 1) : '-'"
        subtitle="Semester Aktif Berjalan"
        icon="award"
        variant="emerald"
    />
    <x-stat-card 
        title="Kurikulum Bab & TP" 
        :value="$totalBab . ' Bab'"
        :subtitle="$totalTp . ' Total TP Disusun Guru'"
        icon="layers"
        variant="sky"
    />
    <x-stat-card 
        title="Kehadiran Santri Hari Ini" 
        :value="$persenHadirSiswaToday > 0 ? $persenHadirSiswaToday . '%' : '-'"
        :subtitle="'Tanggal: ' . \Carbon\Carbon::parse($selectedTanggal)->isoFormat('D MMM Y')"
        icon="users"
        variant="teal"
    />
    <x-stat-card 
        title="Kehadiran Guru Hari Ini" 
        :value="$guruHadirToday . ' / ' . $totalGuruAktif"
        subtitle="Guru Hadir di Sekolah"
        icon="user-check"
        variant="amber"
    />
</div>
