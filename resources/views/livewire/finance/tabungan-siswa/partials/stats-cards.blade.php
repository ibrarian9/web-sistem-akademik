<!-- Metric Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <x-stat-card 
        title="Total Saldo Tabungan" 
        :value="'Rp ' . number_format($totalSaldoGlobal, 0, ',', '.')" 
        subtitle="Saldo kumulatif seluruh siswa"
        icon="wallet" 
        variant="white" 
    />
    <x-stat-card 
        title="Total Akumulasi Setor" 
        :value="'Rp ' . number_format($totalSetorAll, 0, ',', '.')" 
        subtitle="Total dana masuk tabungan"
        icon="arrow-down-left" 
        variant="white" 
    />
    <x-stat-card 
        title="Total Akumulasi Tarik" 
        :value="'Rp ' . number_format($totalTarikAll, 0, ',', '.')" 
        subtitle="Total dana ditarik siswa"
        icon="arrow-up-right" 
        variant="white" 
    />
    <x-stat-card 
        title="Siswa Aktif Menabung" 
        :value="number_format($jumlahSiswaMenabung) . ' Siswa'" 
        subtitle="Memiliki transaksi aktif"
        icon="users" 
        variant="white" 
    />
</div>
