<!-- 3-Stat Metric Cards Row (Non-BOS) -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <x-stat-card 
        title="Total Seluruh Kas Keluar" 
        :value="'Rp ' . number_format($totalOutflowAll, 0, ',', '.')" 
        subtitle="Akumulasi operasional, gaji, & kasbon terpilih"
        icon="trending-down" 
        variant="rose" 
        badge="Total Beban"
    />
    <x-stat-card 
        title="Beban Operasional Yayasan" 
        :value="'Rp ' . number_format($totalOperasional, 0, ',', '.')" 
        subtitle="ATK, sarpras, utilitas, konsumsi, dsb."
        icon="building" 
        variant="soft-amber" 
        badge="Operasional"
    />
    <x-stat-card 
        title="Realisasi Gaji & Honor Guru" 
        :value="'Rp ' . number_format($totalGaji, 0, ',', '.')" 
        subtitle="Gaji pokok & insentif terbayar"
        icon="wallet" 
        variant="soft-purple" 
        badge="SDM Guru"
    />
</div>
