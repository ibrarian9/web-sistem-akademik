<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Overview Pembayaran Siswa</h2>
            <p class="text-sm text-stone-500">Pantau rangkuman lunas/tunggakan administrasi siswa per tahun ajaran.</p>
        </div>
        
        <!-- Filter Tahun Ajaran -->
        <div class="flex items-center gap-3 bg-white border border-stone-200 px-4 py-2 rounded-2xl shadow-sm">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Tahun Ajaran:</span>
            <select wire:model.live="filterTahunAjaran" class="bg-transparent border-none text-sm font-semibold text-stone-800 focus:ring-0 p-0 cursor-pointer">
                @foreach ($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Guidance Card -->
    <div x-data="{ openGuide: true }" class="bg-emerald-50/80 border border-emerald-200/80 rounded-2xl p-4 transition-all shadow-sm">
        <div class="flex items-center justify-between cursor-pointer" @click="openGuide = !openGuide">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-sm">
                    <x-lucide-info class="w-5 h-5" />
                </div>
                <div>
                    <h4 class="text-xs font-bold text-emerald-950 uppercase tracking-wider">Petunjuk Overview Pembayaran Siswa</h4>
                    <p class="text-xs text-emerald-800">Cari siswa menunggak, filter per kelas, dan proses pembayaran pelunasan.</p>
                </div>
            </div>
            <button class="text-emerald-700 hover:text-emerald-900 text-xs font-semibold flex items-center gap-1">
                <span x-text="openGuide ? 'Sembunyikan' : 'Tampilkan'"></span>
                <x-lucide-chevron-down class="w-4 h-4 transition-transform" ::class="openGuide ? 'rotate-180' : ''" />
            </button>
        </div>
        <div x-show="openGuide" class="mt-3 pt-3 border-t border-emerald-200/60 grid grid-cols-1 md:grid-cols-3 gap-3 text-xs text-emerald-900">
            <div class="flex items-start gap-2 bg-white/70 p-2.5 rounded-xl border border-emerald-100">
                <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span><strong>Lunas vs Menunggak:</strong> Gunakan tab filter untuk langsung menampilkan daftar siswa yang belum melunasi SPP.</span>
            </div>
            <div class="flex items-start gap-2 bg-white/70 p-2.5 rounded-xl border border-emerald-100">
                <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span><strong>Bayar Cepat:</strong> Klik tombol "Input Bayar" pada baris siswa untuk langsung membuka form kasir pembayaran.</span>
            </div>
            <div class="flex items-start gap-2 bg-white/70 p-2.5 rounded-xl border border-emerald-100">
                <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span><strong>Integritas Data:</strong> Data pembayaran terlindungi dan tidak memiliki tombol hapus demi audit trail.</span>
            </div>
        </div>
    </div>

    <!-- Alert / Toast Banner -->
    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif
    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-stat-card title="Siswa Ada Tunggakan" :value="$tunggakanCount" icon="alert-circle" color="red" />
        <x-stat-card title="Siswa Lunas Semua" :value="$lunasCount" icon="check-circle" color="green" />
        <x-stat-card title="Nominal Tunggakan" :value="'Rp ' . number_format($nominalTunggakan, 0, ',', '.')" icon="wallet" color="amber" />
        <x-stat-card title="Realisasi Pembayaran" :value="$realisasiPersen . '%'" icon="trending-up" color="blue" />
    </div>

    <!-- Filter & Search Controls -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Search -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-500 uppercase tracking-wider">Cari Siswa</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-stone-400">
                        <x-lucide-search class="w-4 h-4" />
                    </span>
                    <input wire:model.live="search" type="text" placeholder="Nama siswa atau NIS..." 
                        class="w-full pl-10 pr-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500 transition duration-200 text-sm" />
                </div>
            </div>

            <!-- Filter Kelas -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-500 uppercase tracking-wider">Filter Kelas</label>
                <select wire:model.live="filterKelas" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500 transition">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelases as $k)
                        <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status Lunas/Tunggakan -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-500 uppercase tracking-wider">Filter Status</label>
                <select wire:model.live="filterStatus" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500 transition">
                    <option value="">Semua Status</option>
                    <option value="lunas">Lunas Semua</option>
                    <option value="tunggakan">Ada Tunggakan</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-stone-50/50 border-b border-stone-200">
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Jumlah Tagihan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Rincian Nominal</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Terakhir Bayar</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($siswas as $item)
                        <tr class="hover:bg-stone-50 transition duration-150">
                            <!-- Student identity -->
                            <td class="px-6 py-4.5 whitespace-nowrap">
                                <div class="font-bold text-stone-800 text-sm">{{ $item['nama'] }}</div>
                                <div class="text-xs text-stone-400 mt-0.5">NIS: {{ $item['nis'] }}</div>
                            </td>
                            <!-- Class -->
                            <td class="px-6 py-4.5 whitespace-nowrap text-stone-600 text-sm font-semibold">
                                {{ $item['kelas'] }}
                            </td>
                            <!-- Invoice Counts -->
                            <td class="px-6 py-4.5 whitespace-nowrap text-center">
                                @if ($item['total_tagihan_count'] > 0)
                                    <span class="text-sm font-semibold text-stone-700">
                                        {{ $item['total_tagihan_count'] }}
                                    </span>
                                    <div class="text-[10px] text-stone-400 mt-0.5">
                                        {{ $item['lunas_count'] }} Lunas / {{ $item['belum_lunas_count'] }} Belum
                                    </div>
                                @else
                                    <span class="text-xs text-stone-400">Tidak ada</span>
                                @endif
                            </td>
                            <!-- In-arrears details -->
                            <td class="px-6 py-4.5 whitespace-nowrap text-right">
                                <div class="text-xs text-stone-500">Tagihan: Rp {{ number_format($item['total_nominal'], 0, ',', '.') }}</div>
                                <div class="text-xs text-green-700 font-medium">Dibayar: Rp {{ number_format($item['total_dibayar'], 0, ',', '.') }}</div>
                                @if ($item['sisa_tunggakan'] > 0)
                                    <div class="text-xs text-red-600 font-bold mt-0.5">Sisa: Rp {{ number_format($item['sisa_tunggakan'], 0, ',', '.') }}</div>
                                @else
                                    <div class="text-xs text-stone-400 mt-0.5">Sisa: -</div>
                                @endif
                            </td>
                            <!-- Custom Status Badge -->
                            <td class="px-6 py-4.5 whitespace-nowrap text-center">
                                @switch($item['status'])
                                    @case('Lunas Semua')
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">Lunas Semua</span>
                                        @break
                                    @case('Ada Tunggakan')
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">Ada Tunggakan</span>
                                        @break
                                    @default
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-stone-100 text-stone-500 border border-stone-200">Belum Ada Tagihan</span>
                                @endswitch
                            </td>
                            <!-- Last Pay Date -->
                            <td class="px-6 py-4.5 whitespace-nowrap text-center text-stone-500 text-xs font-semibold">
                                {{ $item['terakhir_bayar'] }}
                            </td>
                            <!-- Action button triggers -->
                            <td class="px-6 py-4.5 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- View details modal trigger -->
                                    <button wire:click="viewDetails({{ $item['id'] }})" 
                                        class="p-2 text-stone-500 hover:text-green-600 hover:bg-green-50 rounded-xl border border-stone-200 hover:border-green-200 transition" 
                                        title="Rincian Tagihan">
                                        <x-lucide-list class="w-4 h-4" />
                                    </button>

                                    <!-- Send reminder notification if there are outstanding arrears -->
                                    @if ($item['sisa_tunggakan'] > 0)
                                        <button wire:click="kirimReminder({{ $item['id'] }})"
                                            class="p-2 text-stone-500 hover:text-amber-600 hover:bg-amber-50 rounded-xl border border-stone-200 hover:border-amber-200 transition"
                                            title="Kirim Reminder Tunggakan">
                                            <x-lucide-bell class="w-4 h-4" />
                                        </button>

                                        <!-- Go to payment input page -->
                                        <a href="{{ route('finance.input-pembayaran', ['siswa_id' => $item['id']]) }}"
                                            class="p-2 text-stone-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl border border-stone-200 hover:border-blue-200 transition"
                                            title="Input Pembayaran">
                                            <x-lucide-plus-circle class="w-4 h-4" />
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-stone-400 font-medium text-sm">
                                <x-lucide-wallet-cards class="w-8 h-8 text-stone-300 mx-auto mb-2" />
                                Tidak ada siswa terdaftar dengan status tagihan terpilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        <div class="px-6 py-4 border-t border-stone-200">
            {{ $siswas->links() }}
        </div>
    </div>

    <!-- Details Modal -->
    @if ($selectedSiswaDetails)
        <div class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white border border-stone-200 rounded-3xl shadow-2xl max-w-3xl w-full overflow-hidden flex flex-col max-h-[85vh]">
                <!-- Modal Header -->
                <div class="px-6 py-4.5 bg-stone-50/50 border-b border-stone-200 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-stone-800 text-lg">Rincian Tagihan Administrasi</h3>
                        <p class="text-xs text-stone-500 mt-0.5">{{ $selectedSiswaDetails->user->nama }} | Kelas {{ $selectedSiswaDetails->kelas->nama_kelas ?? '-' }} | NIS: {{ $selectedSiswaDetails->nis }}</p>
                    </div>
                    <button wire:click="closeDetails" class="p-1.5 text-stone-400 hover:text-stone-600 rounded-xl hover:bg-stone-150 transition">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto space-y-6 custom-scrollbar flex-1">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-stone-200">
                                <th class="pb-2 text-xs font-semibold text-stone-500 uppercase tracking-wider">Kategori Tagihan</th>
                                <th class="pb-2 text-xs font-semibold text-stone-500 uppercase tracking-wider">Periode / Bulan</th>
                                <th class="pb-2 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Nominal</th>
                                <th class="pb-2 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Telah Dibayar</th>
                                <th class="pb-2 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Sisa</th>
                                <th class="pb-2 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse ($selectedSiswaDetails->tagihans as $t)
                                <tr>
                                    <td class="py-3 text-sm font-semibold text-stone-800">{{ $t->jenisTagihan->nama }}</td>
                                    <td class="py-3 text-sm text-stone-500">{{ $t->bulan ?: '-' }}</td>
                                    <td class="py-3 text-sm text-stone-800 text-right font-semibold">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                                    <td class="py-3 text-sm text-green-700 text-right font-medium">Rp {{ number_format($t->total_dibayar, 0, ',', '.') }}</td>
                                    <td class="py-3 text-sm text-red-600 text-right font-bold">Rp {{ number_format($t->nominal - $t->total_dibayar, 0, ',', '.') }}</td>
                                    <td class="py-3 text-center">
                                        @switch($t->status)
                                            @case('lunas')
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">Lunas</span>
                                                @break
                                            @case('sebagian')
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Sebagian</span>
                                                @break
                                            @case('batal')
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-stone-100 text-stone-500 border border-stone-200">Batal</span>
                                                @break
                                            @default
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">Belum Bayar</span>
                                        @endswitch
                                    </td>
                                </tr>
                                @if ($t->pembayarans && $t->pembayarans->count() > 0)
                                    <tr>
                                        <td colspan="6" class="bg-stone-50/70 p-3 rounded-xl border border-stone-200 text-xs my-1">
                                            <span class="font-bold text-stone-700 block mb-2">Riwayat Setoran Pembayaran:</span>
                                            <div class="space-y-1.5">
                                                @foreach ($t->pembayarans as $p)
                                                    <div class="flex items-center justify-between bg-white p-2 rounded-lg border border-stone-200">
                                                        <div class="flex items-center gap-3">
                                                            <span class="font-mono font-bold text-stone-800 text-[11px]">{{ $p->no_resi ?? '-' }}</span>
                                                            <span class="text-stone-500">{{ date('d/m/Y', strtotime($p->tanggal_bayar)) }}</span>
                                                            <span class="px-2 py-0.5 bg-stone-100 text-stone-600 rounded font-semibold text-[10px] capitalize">{{ $p->metode_bayar }}</span>
                                                            @if ($p->is_void)
                                                                <span class="px-2 py-0.5 bg-rose-100 text-rose-700 rounded font-bold text-[10px]">VOID</span>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center gap-3">
                                                            <span class="font-bold text-emerald-700">Rp {{ number_format($p->nominal_dibayar, 0, ',', '.') }}</span>
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
                                    <td colspan="6" class="py-6 text-center text-stone-400 font-medium text-sm">
                                        Tidak ada tagihan yang dirilis untuk tahun ajaran terpilih.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-stone-50/50 border-t border-stone-200 flex justify-end">
                    <button wire:click="closeDetails" class="px-5 py-2 bg-stone-200 hover:bg-stone-300 text-stone-700 text-sm font-semibold rounded-xl transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
