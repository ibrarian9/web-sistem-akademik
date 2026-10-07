<div class="space-y-6 font-sans pb-16">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Monitoring Saldo Deposit Siswa" 
        subtitle="Audit saldo mengendap hasil kelebihan bayar (overpayment), telusuri asal-usul setoran, dan alokasikan ke tagihan aktif santri."
        badge="AUDIT DEPOSIT SANTRI"
        badgeVariant="emerald"
        icon="wallet"
    >
        <x-slot:actions>
            <x-button variant="secondary" size="md" icon="credit-card" href="{{ route('finance.input-pembayaran') }}">
                Kasir Pembayaran
            </x-button>
            <x-button variant="secondary" size="md" icon="eye" href="{{ route('finance.overview-pembayaran') }}">
                Overview Pembayaran
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Stat Cards Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card 
            title="Total Saldo Mengendap" 
            :value="'Rp ' . number_format($statTotalMengendap, 0, ',', '.')" 
            subtitle="Saldo aktif milik santri yang tersimpan di sekolah"
            icon="wallet" 
            variant="soft-emerald" 
            badge="Kas Mengendap"
        />

        <x-stat-card 
            title="Siswa Memiliki Saldo" 
            :value="$statCountSiswaMengendap . ' Siswa'" 
            subtitle="Santri dengan saldo deposit lebih dari nol"
            icon="users" 
            variant="soft-indigo" 
            badge="Total Santri"
        />

        <x-stat-card 
            title="Akumulasi Kelebihan Setoran" 
            :value="'Rp ' . number_format($statTotalAkumulasiOverpayment, 0, ',', '.')" 
            subtitle="Total nominal overpayment dari seluruh transaksi"
            icon="arrow-down-left" 
            variant="soft-teal" 
            badge="Riwayat Masuk"
        />

        <x-stat-card 
            title="Total Terpakai ke Tagihan" 
            :value="'Rp ' . number_format($statTotalDepositTerpakai, 0, ',', '.')" 
            subtitle="Saldo deposit yang telah dialokasikan melunasi tagihan"
            icon="arrow-up-right" 
            variant="soft-amber" 
            badge="Telah Dipotong"
        />
    </div>

    <!-- Info Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Audit & Alokasi Saldo Deposit Siswa" 
        :steps="[
            [
                'title' => '1. Asal-Usul Saldo Deposit', 
                'desc' => 'Saldo deposit terbentuk secara otomatis saat wali murid menyetor uang melebihi nominal tunggakan tagihan di menu Kasir Pembayaran.'
            ],
            [
                'title' => '2. Verifikasi & Audit Kesalahan', 
                'desc' => 'Klik tombol Rincian Mutasi pada baris siswa untuk memeriksa riwayat resi asal setoran dan memastikan apakah kelebihan dana valid atau akibat salah ketik nominal.'
            ],
            [
                'title' => '3. Alokasi ke Tagihan Berikutnya', 
                'desc' => 'Jika santri memiliki tagihan SPP atau ujian yang masih tertunggak, klik Bayar Tagihan lalu gunakan metode bayar Deposit untuk melunasinya.'
            ],
            [
                'title' => '4. Koreksi / Pembatalan Transaksi', 
                'desc' => 'Bila kelebihan setoran disebabkan oleh kekeliruan kasir, buka menu Detail Tagihan Siswa untuk membatalkan (void) kuitansi pembayaran terkait.'
            ]
        ]"
        notes="Saldo deposit tidak tercampur dengan tabungan reguler santri. Saldo deposit khusus berfungsi sebagai kantong uang muka untuk pemotongan tagihan sekolah."
    />

    <!-- Filter & Table Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-4 sm:p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="max-w-md w-full">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama siswa, NIS, atau NISN..." />
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <select wire:model.live="filterStatus" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="aktif">Saldo Mengendap Saja (> Rp 0)</option>
                    <option value="semua">Semua Siswa dengan Riwayat</option>
                </select>

                <select wire:model.live="filterKelas" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Kelas</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}">Kelas {{ $c->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <x-table loadingTarget="search, filterKelas, filterStatus, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="min-w-[200px]">Santri / Siswa</x-table.th>
                    <x-table.th class="w-36">Kelas</x-table.th>
                    <x-table.th align="right" class="w-48">Saldo Deposit Saat Ini</x-table.th>
                    <x-table.th class="w-48">Status Tagihan Aktif</x-table.th>
                    <x-table.th align="center" class="w-52">Aksi Audit</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($students as $s)
                    <tr wire:key="student-deposit-{{ $s->id }}" class="hover:bg-emerald-50/40 transition">
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-xs text-stone-900">{{ $s->user->nama ?? '-' }}</div>
                            <div class="text-[10px] text-stone-500 font-mono">NIS: {{ $s->nis ?? '-' }} | NISN: {{ $s->nisn ?? '-' }}</div>
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-stone-100 text-stone-700 border border-stone-200">
                                {{ $s->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                            </span>
                        </td>
                        <td class="p-3.5 text-right font-black text-sm border-r border-stone-200 {{ $s->saldo_deposit > 0 ? 'text-emerald-800' : 'text-stone-400' }}">
                            Rp {{ number_format($s->saldo_deposit, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 border-r border-stone-200 text-xs">
                            @if ($s->unpaid_bills_count > 0)
                                <div class="font-bold text-rose-700">
                                    {{ $s->unpaid_bills_count }} Tagihan Belum Lunas
                                </div>
                                <span class="text-[10px] text-stone-500">
                                    Total Sisa: Rp {{ number_format($s->total_tunggakan ?? 0, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                    <x-lucide-check-circle class="w-3.5 h-3.5 text-emerald-600" />
                                    Semua Tagihan Lunas
                                </span>
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-2 flex-wrap">
                                <x-button 
                                    variant="secondary" 
                                    size="xs" 
                                    icon="file-search" 
                                    wire:click="openDetail({{ $s->id }})"
                                >
                                    Rincian Mutasi
                                </x-button>

                                @if ($s->unpaid_bills_count > 0 && $s->saldo_deposit > 0)
                                    <x-button 
                                        variant="primary" 
                                        size="xs" 
                                        icon="credit-card" 
                                        href="{{ route('finance.input-pembayaran', ['siswa_id' => $s->id]) }}"
                                    >
                                        Bayar Tagihan
                                    </x-button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-table.empty 
                        :colspan="5" 
                        title="Tidak ada saldo deposit mengendap" 
                        message="Saat ini tidak ada siswa yang memiliki saldo deposit lebih dari nol pada kriteria pencarian ini." 
                    />
                @endforelse
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $students->links() }}
        </div>
    </div>

    <!-- MODAL DETAIL & AUDIT MUTASI SALDO DEPOSIT -->
    @if ($showDetailModal && $selectedSiswa)
        <x-floating-card 
            :show="true" 
            title="Audit Rincian Mutasi Deposit Siswa" 
            :subtitle="($selectedSiswa->user->nama ?? 'Siswa') . ' (NIS: ' . ($selectedSiswa->nis ?? '-') . ' - Kelas ' . ($selectedSiswa->kelas->nama_kelas ?? '-') . ')'"
            badge="AUDIT HISTORI DEPOSIT"
            badgeVariant="emerald"
            icon="wallet"
            maxWidth="max-w-4xl"
            closeAction="closeDetail"
        >
            <div class="space-y-5 font-sans">
                <!-- Current Deposit Balance Banner -->
                <div class="p-4 bg-gradient-to-r from-emerald-900 to-teal-800 text-white rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm border border-emerald-700">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-200 block">Saldo Deposit Aktif Saat Ini</span>
                        <div class="text-2xl sm:text-3xl font-black mt-0.5">
                            Rp {{ number_format($selectedSiswa->saldo_deposit, 0, ',', '.') }}
                        </div>
                        <span class="text-[11px] text-emerald-100/90 font-medium mt-1 block">
                            Dana mengendap yang siap digunakan untuk melunasi tagihan aktif santri bersangkutan.
                        </span>
                    </div>

                    <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto justify-end">
                        <x-button 
                            variant="secondary" 
                            size="sm" 
                            icon="file-text" 
                            href="{{ route('finance.tagihan.detail', $selectedSiswa->id) }}"
                            target="_blank"
                        >
                            Detail Tagihan Siswa
                        </x-button>
                        @if ($selectedSiswa->saldo_deposit > 0 && count($siswaUnpaidBills) > 0)
                            <x-button 
                                variant="primary" 
                                size="sm" 
                                icon="credit-card" 
                                href="{{ route('finance.input-pembayaran', ['siswa_id' => $selectedSiswa->id]) }}"
                            >
                                Lunasi Tagihan via Deposit
                            </x-button>
                        @endif
                    </div>
                </div>

                <!-- Bagian 1: Asal-Usul Kelebihan Setoran (Kredit Saldo) -->
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between border-b border-stone-200 pb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">
                                +
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-stone-900 uppercase tracking-wider">1. Riwayat Asal-Usul Setoran Berlebih (Kredit Masuk)</h4>
                                <span class="text-[10px] text-stone-500 font-medium">Transaksi kasir di mana santri menyetor uang melebihi tagihan</span>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                            {{ count($siswaOverpaymentHistory) }} Catatan
                        </span>
                    </div>

                    @if (count($siswaOverpaymentHistory) > 0)
                        <div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
                            <table class="w-full text-left text-xs text-stone-800">
                                <thead class="bg-stone-50 text-stone-600 font-extrabold uppercase tracking-wider text-[10px] border-b border-stone-200">
                                    <tr>
                                        <th class="p-2.5">Tanggal & Resi</th>
                                        <th class="p-2.5">Tagihan Asal</th>
                                        <th class="p-2.5 text-right">Nominal Tagihan</th>
                                        <th class="p-2.5 text-right">Uang Disetor</th>
                                        <th class="p-2.5 text-right font-black text-emerald-800">Kelebihan (Deposit)</th>
                                        <th class="p-2.5">Metode & Kasir</th>
                                        <th class="p-2.5 text-center">Kuitansi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-100">
                                    @foreach ($siswaOverpaymentHistory as $op)
                                        <tr class="hover:bg-stone-50 transition">
                                            <td class="p-2.5">
                                                <div class="font-bold text-stone-900">{{ $op['tanggal'] }}</div>
                                                <span class="text-[10px] text-stone-500 font-mono">{{ $op['no_resi'] }}</span>
                                            </td>
                                            <td class="p-2.5 font-bold text-stone-800">
                                                {{ $op['tagihan_nama'] }}
                                            </td>
                                            <td class="p-2.5 text-right font-semibold text-stone-600">
                                                Rp {{ number_format($op['nominal_tagihan'], 0, ',', '.') }}
                                            </td>
                                            <td class="p-2.5 text-right font-bold text-stone-800">
                                                Rp {{ number_format($op['nominal_dibayar'], 0, ',', '.') }}
                                            </td>
                                            <td class="p-2.5 text-right font-black text-emerald-700">
                                                + Rp {{ number_format($op['kelebihan_bayar'], 0, ',', '.') }}
                                            </td>
                                            <td class="p-2.5">
                                                <div class="font-bold text-stone-800">{{ $op['metode_bayar'] }}</div>
                                                <span class="text-[10px] text-stone-500">Oleh: {{ $op['petugas'] }}</span>
                                            </td>
                                            <td class="p-2.5 text-center">
                                                <a href="{{ route('finance.cetak-resi', $op['id']) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-2 py-1 rounded-lg transition">
                                                    <x-lucide-printer class="w-3 h-3" />
                                                    <span>Resi</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 bg-stone-50 rounded-xl border border-stone-200 text-center text-xs text-stone-500">
                            Belum ada riwayat transaksi overpayment yang tercatat untuk siswa ini.
                        </div>
                    @endif
                </div>

                <!-- Bagian 2: Riwayat Pemakaian Deposit (Debit Keluar) -->
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between border-b border-stone-200 pb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xs">
                                -
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-stone-900 uppercase tracking-wider">2. Riwayat Pemakaian Saldo Deposit (Debit Keluar)</h4>
                                <span class="text-[10px] text-stone-500 font-medium">Pembayaran tagihan yang dilunasi dengan memotong saldo deposit</span>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                            {{ count($siswaDepositUsageHistory) }} Catatan
                        </span>
                    </div>

                    @if (count($siswaDepositUsageHistory) > 0)
                        <div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
                            <table class="w-full text-left text-xs text-stone-800">
                                <thead class="bg-stone-50 text-stone-600 font-extrabold uppercase tracking-wider text-[10px] border-b border-stone-200">
                                    <tr>
                                        <th class="p-2.5">Tanggal & Resi</th>
                                        <th class="p-2.5">Tagihan Dilunasi</th>
                                        <th class="p-2.5 text-right font-black text-amber-900">Nominal Terpotong</th>
                                        <th class="p-2.5">Petugas Kasir</th>
                                        <th class="p-2.5 text-center">Kuitansi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-100">
                                    @foreach ($siswaDepositUsageHistory as $du)
                                        <tr class="hover:bg-stone-50 transition">
                                            <td class="p-2.5">
                                                <div class="font-bold text-stone-900">{{ $du['tanggal'] }}</div>
                                                <span class="text-[10px] text-stone-500 font-mono">{{ $du['no_resi'] }}</span>
                                            </td>
                                            <td class="p-2.5 font-bold text-stone-800">
                                                {{ $du['tagihan_nama'] }}
                                            </td>
                                            <td class="p-2.5 text-right font-black text-rose-700">
                                                - Rp {{ number_format($du['nominal_dipotong'], 0, ',', '.') }}
                                            </td>
                                            <td class="p-2.5 text-stone-600">
                                                {{ $du['petugas'] }}
                                            </td>
                                            <td class="p-2.5 text-center">
                                                <a href="{{ route('finance.cetak-resi', $du['id']) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-2 py-1 rounded-lg transition">
                                                    <x-lucide-printer class="w-3 h-3" />
                                                    <span>Resi</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 bg-stone-50 rounded-xl border border-stone-200 text-center text-xs text-stone-500">
                            Siswa ini belum pernah menggunakan saldo deposit untuk membayar tagihan.
                        </div>
                    @endif
                </div>

                <!-- Bagian 3: Tagihan Aktif yang Belum Lunas -->
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between border-b border-stone-200 pb-2">
                        <div class="flex items-center gap-2">
                            <x-lucide-alert-circle class="w-5 h-5 text-indigo-600" />
                            <div>
                                <h4 class="text-xs font-black text-stone-900 uppercase tracking-wider">3. Tagihan Santri Belum Lunas</h4>
                                <span class="text-[10px] text-stone-500 font-medium">Tagihan yang dapat langsung dilunasi menggunakan saldo deposit</span>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-indigo-800 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-200">
                            {{ count($siswaUnpaidBills) }} Tagihan
                        </span>
                    </div>

                    @if (count($siswaUnpaidBills) > 0)
                        <div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
                            <table class="w-full text-left text-xs text-stone-800">
                                <thead class="bg-stone-50 text-stone-600 font-extrabold uppercase tracking-wider text-[10px] border-b border-stone-200">
                                    <tr>
                                        <th class="p-2.5">Jenis Tagihan</th>
                                        <th class="p-2.5">Periode / Bulan</th>
                                        <th class="p-2.5 text-right">Total Tagihan</th>
                                        <th class="p-2.5 text-right font-black text-rose-800">Sisa Tunggakan</th>
                                        <th class="p-2.5 text-center">Jatuh Tempo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-100">
                                    @foreach ($siswaUnpaidBills as $ub)
                                        <tr class="hover:bg-stone-50 transition">
                                            <td class="p-2.5 font-bold text-stone-900">{{ $ub['jenis'] }}</td>
                                            <td class="p-2.5 text-stone-600">{{ $ub['bulan'] }}</td>
                                            <td class="p-2.5 text-right font-semibold text-stone-600">Rp {{ number_format($ub['nominal'], 0, ',', '.') }}</td>
                                            <td class="p-2.5 text-right font-black text-rose-700">Rp {{ number_format($ub['sisa'], 0, ',', '.') }}</td>
                                            <td class="p-2.5 text-center text-stone-500 font-mono text-[11px]">{{ $ub['jatuh_tempo'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200 text-center text-xs text-emerald-800 font-bold flex items-center justify-center gap-2">
                            <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
                            <span>Santri ini tidak memiliki tunggakan tagihan aktif saat ini.</span>
                        </div>
                    @endif
                </div>
            </div>
        </x-floating-card>
    @endif
</div>
