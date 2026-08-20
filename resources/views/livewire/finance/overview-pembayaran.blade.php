<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Monitoring Status Pembayaran Siswa"
        :steps="[
            ['title' => 'Filter Tahun Ajaran', 'desc' => 'Pilih tahun ajaran aktif pada pojok kanan atas untuk memuat statistik kelunasan siswa.'],
            ['title' => 'Pencarian & Status', 'desc' => 'Cari berdasarkan nama/NIS atau filter status: Lunas, Ada Tunggakan, atau Belum Bayar.'],
            ['title' => 'Detail Transaksi', 'desc' => 'Klik tombol Detail Pembayaran pada siswa untuk melihat riwayat tagihan dan cetak kuitansi resi.']
        ]"
    />

    <!-- Header Title Bar -->
    <x-page-header 
        title="Overview Pembayaran Siswa" 
        subtitle="Pantau rangkuman lunas dan tunggakan tagihan administrasi/SPP siswa per tahun ajaran."
        badge="MONITORING ADMINISTRASI"
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
                                <!-- View details modal trigger -->
                                <x-button variant="secondary" size="xs" icon="list" wire:click="viewDetails({{ $item['id'] }})" title="Rincian Tagihan">
                                    Detail
                                </x-button>

                                <!-- Send reminder notification if there are outstanding arrears -->
                                @if ($item['sisa_tunggakan'] > 0)
                                    <x-button variant="warning" size="xs" icon="bell" wire:click="kirimReminder({{ $item['id'] }})" title="Kirim Reminder" />
                                    <x-button variant="primary" size="xs" icon="plus" href="{{ route('finance.input-pembayaran', ['siswa_id' => $item['id']]) }}" title="Bayar" />
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

    <!-- Details Modal -->
    @if ($selectedSiswaDetails)
        <div class="fixed inset-0 z-[99990] flex items-center justify-center bg-stone-950/65 backdrop-blur-xs p-4 sm:p-6 overflow-y-auto">
            <div class="w-full max-w-3xl bg-white border border-stone-200 rounded-3xl shadow-2xl p-6 space-y-5 my-auto max-h-[90vh] flex flex-col">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <div>
                        <x-badge variant="emerald" size="xs">RINCIAN TAGIHAN SISWA</x-badge>
                        <h3 class="text-base font-extrabold text-stone-900 mt-1">{{ $selectedSiswaDetails->user->nama }}</h3>
                        <p class="text-xs text-stone-500 font-semibold">Kelas: {{ $selectedSiswaDetails->kelas->nama_kelas ?? '-' }} | NIS: {{ $selectedSiswaDetails->nis }}</p>
                    </div>
                    <button wire:click="closeDetails" class="p-1 text-stone-400 hover:text-stone-700 rounded-lg">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="overflow-y-auto space-y-4 flex-1">
                    <x-table>
                        <thead class="bg-stone-900 text-white font-extrabold uppercase tracking-wider text-[10px] sticky top-0">
                            <tr>
                                <th class="p-3 text-left">Kategori Tagihan</th>
                                <th class="p-3 text-left">Periode</th>
                                <th class="p-3 text-right">Nominal</th>
                                <th class="p-3 text-right">Dibayar</th>
                                <th class="p-3 text-right">Sisa</th>
                                <th class="p-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @forelse ($selectedSiswaDetails->tagihans as $t)
                                <tr>
                                    <td class="p-3 text-xs font-bold text-stone-900 border-r border-stone-200">{{ $t->jenisTagihan->nama }}</td>
                                    <td class="p-3 text-xs text-stone-600 border-r border-stone-200">{{ $t->bulan ?: '-' }}</td>
                                    <td class="p-3 text-xs font-bold text-stone-900 text-right border-r border-stone-200">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                                    <td class="p-3 text-xs text-emerald-700 text-right font-bold border-r border-stone-200">Rp {{ number_format($t->total_dibayar, 0, ',', '.') }}</td>
                                    <td class="p-3 text-xs text-rose-600 text-right font-black border-r border-stone-200">Rp {{ number_format($t->nominal - $t->total_dibayar, 0, ',', '.') }}</td>
                                    <td class="p-3 text-center">
                                        @switch($t->status)
                                            @case('lunas')
                                                <x-badge variant="emerald" size="xs">Lunas</x-badge>
                                                @break
                                            @case('sebagian')
                                                <x-badge variant="amber" size="xs">Sebagian</x-badge>
                                                @break
                                            @case('batal')
                                                <x-badge variant="stone" size="xs">Batal</x-badge>
                                                @break
                                            @default
                                                <x-badge variant="rose" size="xs">Belum Bayar</x-badge>
                                        @endswitch
                                    </td>
                                </tr>
                                @if ($t->pembayarans && $t->pembayarans->count() > 0)
                                    <tr>
                                        <td colspan="6" class="bg-stone-50 p-3 text-xs">
                                            <span class="font-bold text-stone-700 block mb-2">Riwayat Pembayaran Kuitansi:</span>
                                            <div class="space-y-1.5">
                                                @foreach ($t->pembayarans as $p)
                                                    <div class="flex items-center justify-between bg-white p-2.5 rounded-xl border border-stone-200 shadow-2xs">
                                                        <div class="flex items-center gap-2.5">
                                                            <span class="font-mono font-bold text-stone-900 text-[11px]">{{ $p->no_resi ?? '-' }}</span>
                                                            <span class="text-stone-500 text-[11px]">{{ date('d/m/Y', strtotime($p->tanggal_bayar)) }}</span>
                                                            <x-badge variant="stone" size="xs">{{ $p->metode_bayar }}</x-badge>
                                                            @if ($p->is_void)
                                                                <x-badge variant="rose" size="xs">VOID</x-badge>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-black text-emerald-800 text-xs">Rp {{ number_format($p->nominal_dibayar, 0, ',', '.') }}</span>
                                                            @if (!$p->is_void)
                                                                <a href="{{ route('finance.pembayaran.resi', $p->id) }}" target="_blank" class="p-1 text-stone-500 hover:text-emerald-700 hover:bg-emerald-50 rounded" title="Cetak Resi">
                                                                    <x-lucide-printer class="w-3.5 h-3.5" />
                                                                </a>
                                                                <button wire:click="voidPayment({{ $p->id }})" wire:confirm="Apakah Anda yakin ingin membatalkan (VOID) transaksi pembayaran ini?" class="p-1 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded" title="Batalkan (VOID) Pembayaran">
                                                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-stone-400 font-medium text-xs">
                                        Tidak ada tagihan yang dirilis untuk tahun ajaran terpilih.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </x-table>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end pt-3 border-t border-stone-200">
                    <x-button variant="secondary" size="md" wire:click="closeDetails">
                        Tutup
                    </x-button>
                </div>
            </div>
        </div>
    @endif
</div>
