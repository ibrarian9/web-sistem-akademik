<div class="space-y-6 font-sans pb-16">
    <!-- Header Title Bar -->
    <x-page-header 
        :title="'Riwayat Gaji: ' . ($guru->user->nama ?? 'Pegawai')" 
        :subtitle="'NIY: ' . ($guru->niy ?? ($guru->nip ?? '-')) . ' • Jabatan: ' . ($guru->jabatan ?: 'Guru / Pegawai') . ' • Jam Kerja: ' . ($guru->jam_kerja ?: '07.00 - 14.00') . ' • Status: ' . ucwords(str_replace('_', ' ', $guru->status_kepegawaian ?? 'Tetap'))"
        badge="RINCIAN PAYROLL PEGAWAI"
        badgeVariant="emerald"
        icon="history"
    >
        <x-slot:actions>
            <x-button variant="secondary" size="md" icon="arrow-left" href="{{ route('finance.gaji-guru') }}">
                Kembali ke Penggajian
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Employee Profile Banner -->
    <div class="bg-gradient-to-r from-emerald-900 to-emerald-800 text-white rounded-3xl p-5 sm:p-6 shadow-md border border-emerald-700/50 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/10 text-white font-black flex items-center justify-center text-xl border border-white/20 shadow-inner shrink-0">
                {{ strtoupper(substr($guru->user->nama ?? 'G', 0, 2)) }}
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-base sm:text-lg font-black tracking-tight">{{ $guru->user->nama ?? '-' }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-700 text-emerald-100 border border-emerald-500/40">
                        {{ $guru->jenis_guru === 'tahfidz' ? 'Wali Tahfizh' : ($guru->jabatan ?: 'Guru Pengajar') }}
                    </span>
                    @if ($activeLoan)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500/80 text-white border border-rose-400/40 flex items-center gap-1">
                            <x-lucide-alert-circle class="w-3 h-3" />
                            Kasbon Aktif: Rp {{ number_format($activeLoan->sisa_pinjaman, 0, ',', '.') }}
                        </span>
                    @endif
                </div>
                <div class="text-xs text-emerald-100/80 font-medium mt-1 flex items-center gap-2 flex-wrap">
                    <span>NIY: {{ $guru->niy ?? ($guru->nip ?? '-') }}</span>
                    <span>&bull;</span>
                    <span>Email: {{ $guru->user->email ?? '-' }}</span>
                    <span>&bull;</span>
                    <span>Pendidikan: {{ $guru->pendidikan ?: 'S1' }}</span>
                    <span>&bull;</span>
                    <span>TMT: {{ $guru->tanggal_masuk ? \Carbon\Carbon::parse($guru->tanggal_masuk)->format('d M Y') : '-' }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 self-end lg:self-center">
            @if ($guru->no_hp)
                <a 
                    href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $guru->no_hp) }}" 
                    target="_blank" 
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-white/10 hover:bg-white/20 text-white border border-white/20 transition cursor-pointer"
                >
                    <x-lucide-phone class="w-3.5 h-3.5 text-emerald-300" />
                    <span>WhatsApp</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Financial KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 block">Total Gaji Diterima (THP)</span>
                <span class="text-lg font-black text-emerald-950 mt-0.5 block">Rp {{ number_format($statTotalDibayar, 0, ',', '.') }}</span>
                <span class="text-[10px] text-emerald-600 font-semibold">{{ $statCountDibayar }} Periode Terbayar</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-xs shrink-0">
                <x-lucide-wallet class="w-5 h-5" />
            </div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-stone-500 block">Total Gaji Pokok & Berkala</span>
                <span class="text-lg font-black text-stone-900 mt-0.5 block">Rp {{ number_format($statTotalPokok, 0, ',', '.') }}</span>
                <span class="text-[10px] text-stone-400 font-medium">Honorarium dasar</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-stone-100 text-stone-700 border border-stone-200 flex items-center justify-center shadow-2xs shrink-0">
                <x-lucide-calculator class="w-5 h-5" />
            </div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-cyan-700 block">Total Tunjangan & Insentif</span>
                <span class="text-lg font-black text-cyan-950 mt-0.5 block">Rp {{ number_format($statTotalInsentif, 0, ',', '.') }}</span>
                <span class="text-[10px] text-cyan-600 font-semibold">Kinerja, ekskul, kehadiran</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-cyan-50 text-cyan-700 border border-cyan-200 flex items-center justify-center shadow-2xs shrink-0">
                <x-lucide-award class="w-5 h-5" />
            </div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-700 block">Potongan Kasbon Terbayar</span>
                <span class="text-lg font-black text-rose-950 mt-0.5 block">Rp {{ number_format($statTotalKasbon, 0, ',', '.') }}</span>
                <span class="text-[10px] text-rose-500 font-medium">Cicilan pinjaman lunas</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-700 border border-rose-200 flex items-center justify-center shadow-2xs shrink-0">
                <x-lucide-piggy-bank class="w-5 h-5" />
            </div>
        </div>
    </div>

    <!-- Filters & Salary History Table -->
    <div class="bg-white border border-stone-200 rounded-2xl p-4 sm:p-6 shadow-xs space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-stone-500 mb-1">Status Pembayaran</label>
                <select wire:model.live="filterStatus" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Status</option>
                    <option value="dibayar">Dibayar (Selesai)</option>
                    <option value="draft">Draft (Menunggu)</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-stone-500 mb-1">Bulan</label>
                <select wire:model.live="filterBulan" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Bulan</option>
                    @foreach ($listBulan as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-stone-500 mb-1">Tahun</label>
                <select wire:model.live="filterTahun" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Tahun</option>
                    @for ($y = intval(date('Y')) + 1; $y >= intval(date('Y')) - 4; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-stone-500 mb-1">Cari Keterangan</label>
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari catatan / sumber dana..." />
            </div>
        </div>

        <!-- Bulk Actions Floating / Top Bar (When items are selected) -->
        @if (count($selectedGajiIds) > 0)
            <div class="bg-gradient-to-r from-emerald-900 to-emerald-800 text-white rounded-2xl p-4 shadow-md flex flex-col sm:flex-row items-center justify-between gap-4 border border-emerald-700">
                <div class="flex items-center gap-3.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600/80 text-white flex items-center justify-center font-black text-sm border border-emerald-500 shadow-2xs shrink-0">
                        {{ count($selectedGajiIds) }}
                    </div>
                    <div>
                        <div class="text-xs font-black tracking-wide text-white">{{ count($selectedGajiIds) }} Riwayat Gaji Terpilih</div>
                        <div class="text-[11px] text-emerald-200/80 font-medium">Pilih aksi untuk mengunduh slip PDF atau menghapus data terpilih.</div>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap w-full sm:w-auto justify-end">
                    <x-button 
                        variant="primary" 
                        size="sm" 
                        icon="download" 
                        href="{{ route('finance.gaji-guru.bulk-slip', ['ids' => implode(',', $selectedGajiIds)]) }}"
                        :wireNavigate="false"
                        target="_blank"
                    >
                        Unduh ({{ count($selectedGajiIds) }}) Slip PDF
                    </x-button>

                    <x-button 
                        variant="danger" 
                        size="sm" 
                        icon="trash-2" 
                        wire:click="deleteSelected"
                        data-confirm="Apakah Anda yakin ingin menghapus {{ count($selectedGajiIds) }} riwayat gaji yang dipilih? Data pengeluaran kas terkait juga akan disinkronkan."
                    >
                        Hapus ({{ count($selectedGajiIds) }}) Terpilih
                    </x-button>

                    <button 
                        type="button" 
                        wire:click="$set('selectedGajiIds', []); $set('selectAll', false)" 
                        class="px-3 py-1.5 rounded-xl text-xs font-bold text-emerald-200 hover:text-white bg-white/10 hover:bg-white/20 transition cursor-pointer"
                    >
                        Batal
                    </button>
                </div>
            </div>
        @endif

        <!-- Table of Teacher Salary History -->
        <x-table loadingTarget="search, filterStatus, filterBulan, filterTahun, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider select-none text-[11px]">
                <tr>
                    <th class="p-3.5 text-center w-10 border-b border-r border-emerald-700/60">
                        <input type="checkbox" wire:model.live="selectAll" class="rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer" title="Pilih Semua" />
                    </th>
                    <x-table.th align="center" class="w-32">Periode Gaji</x-table.th>
                    <x-table.th align="right" class="w-36">Gaji Pokok</x-table.th>
                    <x-table.th align="right" class="w-40">Tunjangan / Insentif</x-table.th>
                    <x-table.th align="right" class="w-36">Potongan</x-table.th>
                    <x-table.th align="right" class="w-40">Take Home Pay</x-table.th>
                    <x-table.th align="center" class="w-28">Tgl Bayar</x-table.th>
                    <x-table.th align="center" class="w-24">Status</x-table.th>
                    <x-table.th align="center" class="w-40">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @forelse ($salaries as $sal)
                    @php
                        $totalIns = $sal->insentif + $sal->honor_ekskul + $sal->insentif_bpjs + $sal->insentif_maghrib_mengaji;
                        $totalPot = $sal->potongan_sosial + $sal->potongan_peminjaman + $sal->potongan_bpjstk + $sal->potongan_lainnya;

                        $activeInsentifs = [];
                        if ($sal->insentif > 0) $activeInsentifs[] = 'Insentif: Rp ' . number_format($sal->insentif, 0, ',', '.');
                        if ($sal->honor_ekskul > 0) $activeInsentifs[] = 'Ekskul (' . $sal->jumlah_ekskul . 'x): Rp ' . number_format($sal->honor_ekskul, 0, ',', '.');
                        if ($sal->insentif_bpjs > 0) $activeInsentifs[] = 'BPJSTK: Rp ' . number_format($sal->insentif_bpjs, 0, ',', '.');
                        if ($sal->insentif_maghrib_mengaji > 0) $activeInsentifs[] = 'Maghrib: Rp ' . number_format($sal->insentif_maghrib_mengaji, 0, ',', '.');
                    @endphp
                    <tr class="hover:bg-emerald-50/40 transition group {{ in_array((string)$sal->id, $selectedGajiIds) ? 'bg-emerald-50/70' : '' }}">
                        <td class="p-3.5 text-center border-b border-r border-stone-200">
                            <input type="checkbox" wire:model.live="selectedGajiIds" value="{{ (string)$sal->id }}" class="rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                        </td>
                        <td class="p-3.5 text-center border-b border-r border-stone-200">
                            <span class="px-2.5 py-1 bg-stone-100 border border-stone-200 rounded-lg text-xs font-bold text-stone-700 whitespace-nowrap inline-block">
                                {{ $sal->bulan }} {{ $sal->tahun }}
                            </span>
                            <span class="block text-[10px] text-stone-400 font-mono mt-0.5">{{ $sal->sumber_dana ?: 'Yayasan' }}</span>
                        </td>
                        <td class="p-3.5 text-right border-b border-r border-stone-200">
                            <span class="font-extrabold text-stone-900 text-xs block whitespace-nowrap">Rp {{ number_format($sal->gaji_pokok, 0, ',', '.') }}</span>
                            @if ($sal->gaji_berkala > 0)
                                <span class="text-[10px] text-emerald-700 font-semibold block whitespace-nowrap">+ Berkala: Rp {{ number_format($sal->gaji_berkala, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-right border-b border-r border-stone-200">
                            @if ($totalIns > 0)
                                <span class="font-extrabold text-stone-900 text-xs block whitespace-nowrap">+Rp {{ number_format($totalIns, 0, ',', '.') }}</span>
                                @if (count($activeInsentifs) > 1)
                                    <div class="text-[10px] text-stone-500 font-medium space-y-0.5 mt-0.5">
                                        @foreach ($activeInsentifs as $insLabel)
                                            <span class="block whitespace-nowrap">{{ $insLabel }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <span class="text-stone-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-right border-b border-r border-stone-200">
                            @if ($totalPot > 0)
                                <span class="font-extrabold text-rose-700 text-xs block whitespace-nowrap">-Rp {{ number_format($totalPot, 0, ',', '.') }}</span>
                                @if ($sal->potongan_peminjaman > 0)
                                    <span class="text-[10px] text-rose-600 font-bold block whitespace-nowrap">Kasbon: Rp {{ number_format($sal->potongan_peminjaman, 0, ',', '.') }}</span>
                                @endif
                            @else
                                <span class="text-stone-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-right border-b border-r border-stone-200">
                            <span class="font-black text-xs sm:text-sm text-emerald-950 px-2.5 py-1 bg-emerald-50 border border-emerald-300 rounded-xl inline-block whitespace-nowrap shadow-2xs">
                                Rp {{ number_format($sal->total_diterima, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="p-3.5 text-center border-b border-r border-stone-200">
                            @if ($sal->tanggal_bayar)
                                <span class="text-[11px] font-bold text-stone-700 block whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($sal->tanggal_bayar)->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-stone-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-center border-b border-r border-stone-200">
                            @if ($sal->status === 'dibayar')
                                <x-badge variant="emerald" size="xs" :dot="true">Dibayar</x-badge>
                            @else
                                <x-badge variant="amber" size="xs" :dot="true">Draft</x-badge>
                            @endif
                        </td>
                        <td class="p-3.5 text-center border-b border-stone-200">
                            <div class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                                <button 
                                    type="button" 
                                    wire:click="openDetailModal({{ $sal->id }})" 
                                    class="p-1.5 rounded-xl text-stone-600 hover:text-emerald-700 bg-stone-100 hover:bg-emerald-50 border border-stone-300 hover:border-emerald-300 transition"
                                    title="Lihat Rincian Gaji"
                                >
                                    <x-lucide-receipt class="w-4 h-4" />
                                </button>

                                @if ($sal->status === 'dibayar')
                                    <button 
                                        type="button" 
                                        wire:click="openPreview({{ $sal->id }})" 
                                        class="inline-flex items-center gap-1 px-2 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs transition"
                                        title="Pratinjau Slip PDF"
                                    >
                                        <x-lucide-eye class="w-3.5 h-3.5 text-emerald-700" />
                                        <span>Slip</span>
                                    </button>

                                    <a 
                                        href="{{ route('finance.gaji-guru.slip', ['id' => $sal->id, 'download' => 1]) }}" 
                                        target="_blank" 
                                        class="p-1.5 rounded-xl text-stone-600 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 border border-stone-300 transition"
                                        title="Unduh Slip PDF"
                                    >
                                        <x-lucide-download class="w-4 h-4" />
                                    </a>
                                @endif

                                <button 
                                    type="button" 
                                    wire:click="deleteSalary({{ $sal->id }})" 
                                    data-confirm="Apakah Anda yakin ingin menghapus data gaji periode {{ $sal->bulan }} {{ $sal->tahun }}?"
                                    class="p-1.5 rounded-xl text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 shadow-2xs transition cursor-pointer"
                                    title="Hapus Data Gaji"
                                >
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="9" title="Belum ada riwayat gaji" message="Tidak ditemukan catatan gaji untuk periode yang dipilih." />
                @endforelse
            </tbody>
        </x-table>

        <!-- Pagination Bar -->
        <div class="pt-2">
            {{ $salaries->links() }}
        </div>
    </div>

    <!-- Modal Detail Rincian Gaji Pegawai -->
    @if ($showDetailModal && $selectedSalaryDetail)
        @php
            $sd = $selectedSalaryDetail;
            $penerimaanList = [
                ['label' => 'Gaji Pokok', 'nominal' => $sd->gaji_pokok, 'desc' => 'Honorarium Pokok Bulanan'],
            ];
            if ($sd->gaji_berkala > 0) {
                $penerimaanList[] = ['label' => 'Gaji Berkala', 'nominal' => $sd->gaji_berkala, 'desc' => 'Kenaikan Berkala Pegawai'];
            }
            if ($sd->honor_ekskul > 0) {
                $penerimaanList[] = ['label' => 'Honor Ekstrakurikuler', 'nominal' => $sd->honor_ekskul, 'desc' => ($sd->jumlah_ekskul ? $sd->jumlah_ekskul . 'x Pembinaan Ekskul' : 'Pembina Ekskul')];
            }
            if ($sd->insentif > 0) {
                $penerimaanList[] = ['label' => 'Insentif Kehadiran / Kinerja', 'nominal' => $sd->insentif, 'desc' => 'Tunjangan Disiplin & Mengajar'];
            }
            if ($sd->insentif_bpjs > 0) {
                $penerimaanList[] = ['label' => 'Insentif BPJSTK', 'nominal' => $sd->insentif_bpjs, 'desc' => 'Subsidi Iuran Ketenagakerjaan'];
            }
            if ($sd->insentif_maghrib_mengaji > 0) {
                $penerimaanList[] = ['label' => 'Insentif Maghrib Mengaji', 'nominal' => $sd->insentif_maghrib_mengaji, 'desc' => 'Program Maghrib Mengaji'];
            }
            if ($sd->tunjangan_jabatan > 0) {
                $penerimaanList[] = ['label' => 'Tunjangan Jabatan', 'nominal' => $sd->tunjangan_jabatan, 'desc' => 'Struktural / Amanah Khusus'];
            }
            if ($sd->tunjangan_pendidikan > 0) {
                $penerimaanList[] = ['label' => 'Tunjangan Pendidikan', 'nominal' => $sd->tunjangan_pendidikan, 'desc' => 'Kualifikasi Akademik'];
            }
            if ($sd->bonus > 0) {
                $penerimaanList[] = ['label' => 'Bonus / Tambahan Lain', 'nominal' => $sd->bonus, 'desc' => 'Apresiasi Khusus'];
            }

            $potonganList = [];
            if ($sd->potongan_peminjaman > 0) {
                $potonganList[] = ['label' => 'Potongan Cicilan Kasbon', 'nominal' => $sd->potongan_peminjaman, 'desc' => 'Pelunasan Pinjaman Guru'];
            }
            if ($sd->potongan_sosial > 0) {
                $potonganList[] = ['label' => 'Potongan Sosial', 'nominal' => $sd->potongan_sosial, 'desc' => 'Iuran Sosial Pegawai'];
            }
            if ($sd->potongan_bpjstk > 0) {
                $potonganList[] = ['label' => 'Iuran BPJS Ketenagakerjaan', 'nominal' => $sd->potongan_bpjstk, 'desc' => 'Iuran Kepesertaan'];
            }
            if ($sd->potongan_absensi > 0) {
                $potonganList[] = ['label' => 'Potongan Absensi / Keterlambatan', 'nominal' => $sd->potongan_absensi, 'desc' => 'Ketidakhadiran'];
            }
            if ($sd->potongan_lainnya > 0) {
                $potonganList[] = ['label' => 'Potongan Lain-lain', 'nominal' => $sd->potongan_lainnya, 'desc' => 'Koreksi Lainnya'];
            }
        @endphp

        <x-floating-card 
            :show="true" 
            :title="'Rincian Lengkap Gaji — ' . ($sd->guru->user->nama ?? '-')" 
            :subtitle="'Periode ' . $sd->bulan . ' ' . $sd->tahun . ' • Status: ' . strtoupper($sd->status)" 
            badge="RINCIAN PAYROLL" 
            badgeVariant="emerald" 
            icon="receipt" 
            maxWidth="max-w-5xl" 
            closeAction="closeDetailModal"
            zIndex="z-[99990]"
        >
            <div class="space-y-6 font-sans">
                <!-- Employee Summary Header -->
                <div class="bg-gradient-to-r from-emerald-900 to-emerald-800 text-white rounded-2xl p-5 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-xl bg-white/10 text-white font-black flex items-center justify-center text-base border border-white/20 shrink-0">
                            {{ strtoupper(substr($sd->guru->user->nama ?? 'G', 0, 2)) }}
                        </div>
                        <div>
                            <div class="text-sm font-black text-white">{{ $sd->guru->user->nama ?? '-' }}</div>
                            <div class="text-[11px] text-emerald-100 font-semibold mt-0.5 flex items-center gap-1.5 flex-wrap">
                                <span>{{ $sd->jabatan ?: ($sd->guru->jabatan ?? 'Guru / Pegawai') }}</span>
                                <span>&bull;</span>
                                <span>{{ $sd->sumber_dana ?: 'Yayasan' }}</span>
                                @if ($sd->guru->niy || $sd->guru->nip)
                                    <span>&bull;</span>
                                    <span class="font-mono">NIY: {{ $sd->guru->niy ?? $sd->guru->nip }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if ($sd->status === 'dibayar')
                            <span class="px-3 py-1 bg-emerald-500 text-white rounded-full text-xs font-black shadow-2xs flex items-center gap-1">
                                <x-lucide-check-circle class="w-3.5 h-3.5" />
                                Dibayar ({{ $sd->tanggal_bayar ? \Carbon\Carbon::parse($sd->tanggal_bayar)->format('d M Y') : 'Selesai' }})
                            </span>
                        @else
                            <span class="px-3 py-1 bg-amber-500 text-white rounded-full text-xs font-black shadow-2xs flex items-center gap-1">
                                <x-lucide-clock class="w-3.5 h-3.5" />
                                Draft Penggajian
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Two-Column Breakdown Matrix -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Column A: Penerimaan (Bruto) -->
                    <div class="bg-emerald-50/40 border border-emerald-200/80 rounded-2xl p-5 shadow-2xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between pb-3 mb-3 border-b border-emerald-200/60">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-black text-xs">A</div>
                                    <h4 class="text-xs font-black uppercase tracking-wider text-emerald-950">Penerimaan & Tunjangan</h4>
                                </div>
                                <span class="text-xs font-black text-emerald-900 font-mono">Rp {{ number_format($sd->total_bruto, 0, ',', '.') }}</span>
                            </div>

                            <div class="space-y-2.5">
                                @foreach ($penerimaanList as $item)
                                    <div class="flex items-center justify-between text-xs py-1 border-b border-emerald-100/50">
                                        <div>
                                            <span class="font-bold text-stone-800 block">{{ $item['label'] }}</span>
                                            <span class="text-[10px] text-stone-400">{{ $item['desc'] }}</span>
                                        </div>
                                        <span class="font-mono font-bold text-emerald-900">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="pt-4 mt-4 border-t border-emerald-200/80 flex items-center justify-between">
                            <span class="text-xs font-extrabold text-emerald-900">Total Penghasilan Bruto (A)</span>
                            <span class="text-sm font-black text-emerald-950 font-mono">Rp {{ number_format($sd->total_bruto, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Column B: Potongan -->
                    <div class="bg-rose-50/40 border border-rose-200/80 rounded-2xl p-5 shadow-2xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between pb-3 mb-3 border-b border-rose-200/60">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center font-black text-xs">B</div>
                                    <h4 class="text-xs font-black uppercase tracking-wider text-rose-950">Potongan Gaji</h4>
                                </div>
                                <span class="text-xs font-black text-rose-900 font-mono">-Rp {{ number_format($sd->total_potongan, 0, ',', '.') }}</span>
                            </div>

                            <div class="space-y-2.5">
                                @forelse ($potonganList as $item)
                                    <div class="flex items-center justify-between text-xs py-1 border-b border-rose-100/50">
                                        <div>
                                            <span class="font-bold text-stone-800 block">{{ $item['label'] }}</span>
                                            <span class="text-[10px] text-stone-400">{{ $item['desc'] }}</span>
                                        </div>
                                        <span class="font-mono font-bold text-rose-700">-Rp {{ number_format($item['nominal'], 0, ',', '.') }}</span>
                                    </div>
                                @empty
                                    <div class="py-6 text-center text-xs text-stone-400 italic">
                                        Tidak ada potongan pada periode ini.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="pt-4 mt-4 border-t border-rose-200/80 flex items-center justify-between">
                            <span class="text-xs font-extrabold text-rose-900">Total Potongan (B)</span>
                            <span class="text-sm font-black text-rose-950 font-mono">-Rp {{ number_format($sd->total_potongan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Grand Total Take Home Pay Banner -->
                <div class="bg-emerald-950 text-white rounded-2xl p-5 shadow-sm border border-emerald-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-400 block">Take Home Pay Bersih (A - B)</span>
                        <span class="text-2xl sm:text-3xl font-black text-white font-mono mt-0.5 block">
                            Rp {{ number_format($sd->total_diterima, 0, ',', '.') }}
                        </span>
                        <span class="text-[11px] text-emerald-200/70 italic mt-0.5 block">
                            Terbilang: {{ $sd->terbilang }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <x-button 
                            variant="secondary" 
                            size="md" 
                            icon="eye" 
                            wire:click="openPreview({{ $sd->id }})"
                        >
                            Pratinjau Slip PDF
                        </x-button>

                        <a 
                            href="{{ route('finance.gaji-guru.slip', ['id' => $sd->id, 'download' => 1]) }}" 
                            target="_blank" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition"
                        >
                            <x-lucide-download class="w-4 h-4" />
                            <span>Unduh PDF</span>
                        </a>
                    </div>
                </div>
            </div>
        </x-floating-card>
    @endif

    <!-- Modal Pratinjau Slip Gaji PDF (Rendered on top with highest z-index) -->
    @if ($showPreviewModal && $previewSalaryId)
        <x-floating-card 
            :show="true" 
            title="Pratinjau Slip Gaji Pegawai" 
            subtitle="Dokumen resmi Slip Honorarium Pegawai Yayasan F3 ber-QR Code verifikasi." 
            badge="DOKUMEN RESMI" 
            badgeVariant="emerald" 
            icon="file-text" 
            maxWidth="max-w-4xl" 
            closeAction="closePreview"
            zIndex="z-[99999]"
        >
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-stone-200">
                    <span class="text-xs text-stone-500 font-medium">Dokumen siap dicetak atau disimpan dalam format PDF.</span>
                    <x-button variant="primary" size="sm" icon="download" href="{{ route('finance.gaji-guru.slip', ['id' => $previewSalaryId, 'download' => 1]) }}" :wireNavigate="false" target="_blank">
                        Unduh File PDF
                    </x-button>
                </div>

                <div class="w-full h-[620px] rounded-2xl overflow-hidden border border-stone-200 shadow-inner bg-stone-100">
                    <iframe src="{{ route('finance.gaji-guru.slip', ['id' => $previewSalaryId]) }}" class="w-full h-full border-none"></iframe>
                </div>

                <div class="flex items-center justify-end pt-2">
                    <x-button variant="secondary" size="md" wire:click="closePreview">Tutup</x-button>
                </div>
            </div>
        </x-floating-card>
    @endif
</div>
