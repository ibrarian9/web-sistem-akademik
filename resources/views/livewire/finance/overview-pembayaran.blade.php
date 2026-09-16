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

    <!-- Info & Tutorial Box (Updated with Relevant System Menus) -->
    <x-info-tutorial-box 
        title="Panduan Monitoring Realisasi Pembayaran SPP & Tagihan Siswa"
        :steps="[
            [
                'title' => '1. Filter Tahun Ajaran & Kelas', 
                'desc' => 'Pilih Tahun Ajaran pada pojok kanan atas serta kelas pada dropdown filter di bawah untuk memantau data realisasi serta rasio pelunasan angkatan siswa secara akurat.'
            ],
            [
                'title' => '2. Filter Interaktif Lewat Kartu Statistik', 
                'desc' => 'Klik langsung kartu Siswa Ada Tunggakan atau Siswa Lunas Semua di bawah untuk menyaring tabel secara instan. Klik kembali pada kartu aktif untuk mereset filter.'
            ],
            [
                'title' => '3. Aksi Kasir Pembayaran Siswa', 
                'desc' => 'Klik tombol hijau Bayar Sekarang (ikon kartu kredit) pada siswa yang menunggak untuk langsung membuka kasir pembayaran dengan data siswa otomatis terpilih.'
            ],
            [
                'title' => '4. Kirim Reminder Notifikasi Tunggakan', 
                'desc' => 'Klik tombol lonceng kuning pada baris siswa untuk mengirimkan notifikasi tagihan in-app resmi ke akun santri/wali murid mengenai nominal tunggakan berjalan.'
            ],
            [
                'title' => '5. Rincian Kartu Kendali & Cetak Resi', 
                'desc' => 'Klik tombol Detail untuk membuka kartu kendali transaksi perorangan, melihat histori cicilan, dan mencetak ulang resi bukti pembayaran sah.'
            ],
            [
                'title' => '6. Integrasi Manajemen Tagihan & Laporan', 
                'desc' => 'Terbitkan tagihan baru via menu Manajemen Tagihan, kelola deposit siswa di Tabungan Siswa, dan ekspor rekapitulasi via menu Laporan Tunggakan.'
            ]
        ]"
        notes="Nominal tunggakan pada kartu statistik dihitung berdasarkan tagihan yang telah jatuh tempo s/d bulan berjalan. Tagihan bulan mendatang dicatat terpisah agar tidak mendistorsi rasio kelunasan riil."
    />

    <!-- Alert / Toast Banner -->
    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif
    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Quick Stats Grid (Vibrant, Informative & Interactive) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Siswa Ada Tunggakan -->
        <div class="relative group">
            <x-stat-card 
                title="Siswa Ada Tunggakan" 
                :value="$tunggakanCount . ' Siswa'" 
                subtitle="Belum melunasi tagihan jatuh tempo"
                icon="alert-circle" 
                variant="soft-rose" 
                :badge="$filterStatus === 'tunggakan' ? 'Filter Aktif ✓' : ($tunggakanCount > 0 ? 'Perlu Follow-up' : 'Nihil')"
                wire:click="filterByStatus('tunggakan')"
                role="button"
                tabindex="0"
                class="cursor-pointer select-none hover:scale-[1.01] active:scale-[0.99] {{ $filterStatus === 'tunggakan' ? 'ring-4 ring-rose-400/80 ring-offset-2 shadow-md' : '' }}"
            />
            @if ($filterStatus === 'tunggakan')
                <span class="absolute top-2 right-2 px-2 py-0.5 bg-rose-600 text-white text-[9px] font-black rounded-full shadow-2xs uppercase tracking-wider">
                    Aktif
                </span>
            @endif
        </div>

        <!-- Card 2: Siswa Lunas Semua -->
        <div class="relative group">
            <x-stat-card 
                title="Siswa Lunas Semua" 
                :value="$lunasCount . ' Siswa'" 
                subtitle="Tertib administrasi s/d bulan ini"
                icon="check-circle" 
                variant="soft-emerald" 
                :badge="$filterStatus === 'lunas' ? 'Filter Aktif ✓' : 'Tertib 100%'"
                wire:click="filterByStatus('lunas')"
                role="button"
                tabindex="0"
                class="cursor-pointer select-none hover:scale-[1.01] active:scale-[0.99] {{ $filterStatus === 'lunas' ? 'ring-4 ring-emerald-400/80 ring-offset-2 shadow-md' : '' }}"
            />
            @if ($filterStatus === 'lunas')
                <span class="absolute top-2 right-2 px-2 py-0.5 bg-emerald-600 text-white text-[9px] font-black rounded-full shadow-2xs uppercase tracking-wider">
                    Aktif
                </span>
            @endif
        </div>

        <!-- Card 3: Nominal Tunggakan -->
        <div class="relative group">
            <x-stat-card 
                title="Nominal Tunggakan" 
                :value="'Rp ' . number_format($nominalTunggakan, 0, ',', '.')" 
                :subtitle="$nominalMendatang > 0 ? 'Jatuh tempo berjalan (+ Rp ' . number_format($nominalMendatang, 0, ',', '.') . ' mendatang)' : 'Total piutang s/d bulan berjalan'"
                icon="wallet" 
                variant="soft-amber" 
                badge="Piutang Aktif"
            />
        </div>

        <!-- Card 4: Realisasi Pembayaran -->
        <div class="relative group">
            <x-stat-card 
                title="Realisasi Pembayaran" 
                :value="$realisasiPersen . '%'" 
                :subtitle="'Rp ' . number_format($totalDibayar, 0, ',', '.') . ' / Rp ' . number_format($totalNominal, 0, ',', '.')"
                icon="trending-up" 
                variant="soft-indigo" 
                :badge="$realisasiPersen >= 80 ? 'Sangat Baik' : ($realisasiPersen >= 50 ? 'Berjalan' : 'Perlu Ditingkatkan')"
                :progress="$realisasiPersen"
            />
        </div>
    </div>

    <!-- Content Table Card (Full Width) -->
    <div class="bg-white border border-stone-200 rounded-2xl p-3.5 sm:p-6 shadow-xs space-y-4">
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
                <option value="lunas">Lunas / Tertib Berjalan</option>
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
                                <div class="text-xs text-rose-600 font-black mt-0.5">Tunggakan: Rp {{ number_format($item['sisa_tunggakan'], 0, ',', '.') }}</div>
                            @else
                                <div class="text-xs text-emerald-600 font-bold mt-0.5">Tunggakan: Nihil (Lunas)</div>
                            @endif
                            @if (!empty($item['sisa_mendatang']) && $item['sisa_mendatang'] > 0)
                                <div class="text-[10px] text-stone-400 font-medium mt-0.5">Mendatang: Rp {{ number_format($item['sisa_mendatang'], 0, ',', '.') }}</div>
                            @endif
                        </td>
                        <!-- Custom Status Badge -->
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @switch($item['status'])
                                @case('Lunas Semua')
                                    <x-badge variant="emerald" size="xs" :dot="true">Lunas Semua</x-badge>
                                    @break
                                @case('Tertib Berjalan')
                                    <x-badge variant="emerald" size="xs" :dot="true">Tertib (Bulan Ini)</x-badge>
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
