<div class="space-y-6 font-sans">
    <!-- Navigation Tabs Menu (Top of Page) -->
    <x-finance.tagihan-nav-tabs active="overview" />

    <!-- Header Title Bar -->
    <x-page-header 
        title="Overview Pembayaran Siswa" 
        subtitle="Pantau rangkuman realisasi lunas dan sisa tunggakan tagihan administrasi/SPP siswa per tahun ajaran."
        badge="MONITORING REALISASI SPP"
        badgeVariant="emerald"
        icon="eye"
    >
        <x-slot:actions>
            <!-- Filter Tahun Ajaran -->
            <div class="flex items-center gap-3 bg-stone-50 border border-stone-200 px-4 py-2.5 rounded-2xl shadow-2xs">
                <span class="text-xs font-bold text-stone-600 uppercase tracking-wider">Tahun Ajaran:</span>
                <select wire:model.live="filterTahunAjaran" class="bg-transparent border-none text-xs font-bold text-stone-900 focus:ring-0 p-0 cursor-pointer">
                    @foreach ($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                    @endforeach
                </select>
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Monitoring Status Pembayaran Siswa"
        :steps="[
            ['title' => 'Filter Tahun Ajaran', 'desc' => 'Pilih tahun ajaran aktif pada pojok kanan atas untuk memuat statistik kelunasan siswa.'],
            ['title' => 'Pencarian & Status', 'desc' => 'Cari berdasarkan nama/NIS atau filter status: Lunas, Ada Tunggakan, atau Belum Bayar.'],
            ['title' => 'Detail Transaksi', 'desc' => 'Klik tombol Detail Pembayaran pada siswa untuk melihat riwayat tagihan dan cetak kuitansi resi.']
        ]"
    />

    <!-- Alert / Toast Banner -->
    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif
    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card 
            title="Siswa Ada Tunggakan" 
            :value="$tunggakanCount . ' Siswa'" 
            subtitle="Belum melunasi tagihan aktif"
            icon="alert-circle" 
            variant="white" 
        />

        <x-stat-card 
            title="Siswa Lunas Semua" 
            :value="$lunasCount . ' Siswa'" 
            subtitle="Tertib administrasi 100%"
            icon="check-circle" 
            variant="white" 
        />

        <x-stat-card 
            title="Nominal Tunggakan" 
            :value="'Rp ' . number_format($nominalTunggakan, 0, ',', '.')" 
            subtitle="Piutang SPP belum tertagih"
            icon="wallet" 
            variant="white" 
        />

        <x-stat-card 
            title="Realisasi Pembayaran" 
            :value="$realisasiPersen . '%'" 
            subtitle="Rasio pembayaran selesai"
            icon="trending-up" 
            variant="white" 
        />
    </div>

    <!-- Content Table Card (Full Width) -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Filter & Search Controls -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Search -->
            <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama siswa atau NIS..." />

            <!-- Filter Kelas -->
            <select wire:model.live="filterKelas" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="">Semua Kelas</option>
                @foreach ($kelases as $k)
                    <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                @endforeach
            </select>

            <!-- Filter Status -->
            <select wire:model.live="filterStatus" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="">Semua Status Tagihan</option>
                <option value="lunas">Lunas Semua</option>
                <option value="tunggakan">Ada Tunggakan</option>
            </select>
        </div>

        <!-- Data Table Card -->
        <x-table loadingTarget="search, filterKelas, filterStatus, filterTahunAjaran, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="min-w-[180px]">Siswa</x-table.th>
                    <x-table.th class="w-32">Kelas</x-table.th>
                    <x-table.th align="center" class="w-36">Jml Tagihan</x-table.th>
                    <x-table.th align="right" class="w-48">Rincian Nominal</x-table.th>
                    <x-table.th align="center" class="w-36">Status</x-table.th>
                    <x-table.th align="center" class="w-36">Terakhir Bayar</x-table.th>
                    <x-table.th align="center" class="w-36">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($siswas as $item)
                    <tr class="hover:bg-emerald-50/40 transition">
                        <!-- Student identity -->
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-xs">{{ $item['nama'] }}</div>
                            <div class="text-[10px] text-stone-400 font-mono">NIS: {{ $item['nis'] }}</div>
                        </td>
                        <!-- Class -->
                        <td class="p-3.5 border-r border-stone-200">
                            <x-badge variant="stone" size="xs">
                                {{ $item['kelas'] }}
                            </x-badge>
                        </td>
                        <!-- Invoice Counts -->
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($item['total_tagihan_count'] > 0)
                                <span class="text-xs font-bold text-stone-800">
                                    {{ $item['total_tagihan_count'] }} Tagihan
                                </span>
                                <div class="text-[10px] text-stone-400 font-medium">
                                    {{ $item['lunas_count'] }} Lunas / {{ $item['belum_lunas_count'] }} Belum
                                </div>
                            @else
                                <span class="text-xs text-stone-400 font-medium">-</span>
                            @endif
                        </td>
                        <!-- In-arrears details -->
                        <td class="p-3.5 text-right border-r border-stone-200">
                            <div class="text-[11px] text-stone-500 font-medium">Tagihan: Rp {{ number_format($item['total_nominal'], 0, ',', '.') }}</div>
                            <div class="text-[11px] text-emerald-700 font-bold">Dibayar: Rp {{ number_format($item['total_dibayar'], 0, ',', '.') }}</div>
                            @if ($item['sisa_tunggakan'] > 0)
                                <div class="text-xs text-rose-600 font-black mt-0.5">Sisa: Rp {{ number_format($item['sisa_tunggakan'], 0, ',', '.') }}</div>
                            @else
                                <div class="text-xs text-emerald-600 font-bold mt-0.5">Sisa: Lunas</div>
                            @endif
                        </td>
                        <!-- Custom Status Badge -->
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @switch($item['status'])
                                @case('Lunas Semua')
                                    <x-badge variant="emerald" size="xs" :dot="true">Lunas Semua</x-badge>
                                    @break
                                @case('Ada Tunggakan')
                                    <x-badge variant="rose" size="xs" :dot="true">Ada Tunggakan</x-badge>
                                    @break
                                @default
                                    <x-badge variant="stone" size="xs">Belum Ada Tagihan</x-badge>
                            @endswitch
                        </td>
                        <!-- Last Pay Date -->
                        <td class="p-3.5 text-center text-stone-600 text-xs font-semibold border-r border-stone-200">
                            {{ $item['terakhir_bayar'] }}
                        </td>
                        <!-- Action button triggers -->
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <!-- Link to full student billing ledger & history -->
                                <x-button variant="secondary" size="xs" icon="file-text" href="{{ route('finance.tagihan.detail', $item['id']) }}" title="Buka Rincian Lengkap Tagihan Siswa">
                                    Detail
                                </x-button>

                                <!-- Send reminder notification if there are outstanding arrears -->
                                @if ($item['sisa_tunggakan'] > 0)
                                    <x-button variant="warning" size="xs" icon="bell" wire:click="kirimReminder({{ $item['id'] }})" title="Kirim Reminder" />
                                    <x-button variant="primary" size="xs" icon="credit-card" href="{{ route('finance.input-pembayaran', ['siswa_id' => $item['id']]) }}" title="Bayar Sekarang" />
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="7" title="Tidak ada siswa terdaftar" message="Tidak ada data siswa ditemukan dengan filter status yang dipilih." />
                @endforelse
            </tbody>
        </x-table>
        
        <!-- Pagination Links -->
        <div class="pt-2">
            {{ $siswas->links() }}
        </div>
    </div>
</div>
