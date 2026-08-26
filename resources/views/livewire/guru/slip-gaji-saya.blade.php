<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight flex items-center gap-2.5">
                <span class="p-2 rounded-2xl bg-emerald-600 text-white shadow-md shadow-emerald-600/20">
                    <x-lucide-banknote class="w-6 h-6" />
                </span>
                <span>Slip Gaji & Riwayat Penggajian</span>
            </h1>
            <p class="text-xs sm:text-sm text-stone-500 font-medium mt-1">
                Pantau rincian penerimaan honorarium, insentif, potongan kasbon, dan unduh slip gaji resmi ber-QR Code.
            </p>
        </div>
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Informasi Transparansi Penggajian Guru & GTK"
        :steps="[
            ['title' => 'Rincian Penghasilan', 'desc' => 'Honor dihitung berdasarkan Gaji Pokok, Insentif BPJS, dan Insentif Tambahan/Maghrib Mengaji.'],
            ['title' => 'Potongan Transparan', 'desc' => 'Jika Anda memiliki fasilitas pinjaman/kasbon, cicilan dipotong otomatis secara terperinci.'],
            ['title' => 'Pratinjau & Cetak Resmi', 'desc' => 'Klik Pratinjau PDF untuk melihat langsung slip gaji resmi dengan verifikasi tanda tangan elektronik.']
        ]"
        notes="Jika terdapat ketidaksesuaian data gaji, silakan konfirmasi ke bagian Keuangan / Tata Usaha."
    />

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-emerald-800 to-emerald-950 text-white p-5 rounded-3xl shadow-xl space-y-2 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-10">
                <x-lucide-wallet class="w-32 h-32" />
            </div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-200">Total Diterima (Tahun {{ $filterTahun ?: date('Y') }})</span>
                <span class="p-1.5 rounded-xl bg-emerald-700/50 text-emerald-200">
                    <x-lucide-trending-up class="w-4 h-4" />
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-black tracking-tight">
                Rp {{ number_format($totalTahunIni, 0, ',', '.') }}
            </div>
            <p class="text-[10px] text-emerald-300 font-medium">Akumulasi gaji bersih yang telah dibayarkan</p>
        </div>

        <div class="bg-white border border-stone-200 p-5 rounded-3xl shadow-xs space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-stone-500">Gaji Terakhir Diterima</span>
                <span class="p-1.5 rounded-xl bg-indigo-50 text-indigo-600">
                    <x-lucide-calendar class="w-4 h-4" />
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight">
                @if ($gajiTerakhir)
                    Rp {{ number_format($gajiTerakhir->total_diterima, 0, ',', '.') }}
                @else
                    Rp 0
                @endif
            </div>
            <p class="text-[10px] text-stone-400 font-medium">
                @if ($gajiTerakhir)
                    Periode {{ $gajiTerakhir->bulan }} {{ $gajiTerakhir->tahun }}
                @else
                    Belum ada riwayat pembayaran
                @endif
            </p>
        </div>

        <div class="bg-white border border-stone-200 p-5 rounded-3xl shadow-xs space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-stone-500">Potongan Kasbon Tahun Ini</span>
                <span class="p-1.5 rounded-xl bg-amber-50 text-amber-600">
                    <x-lucide-receipt class="w-4 h-4" />
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-black text-rose-600 tracking-tight">
                Rp {{ number_format($totalKasbonPotong, 0, ',', '.') }}
            </div>
            <p class="text-[10px] text-stone-400 font-medium">Total angsuran pinjaman terpotong via payroll</p>
        </div>
    </div>

    <!-- Table Section with Filters -->
    <div class="bg-white border border-stone-200 rounded-3xl shadow-xs p-5 sm:p-6 space-y-4">
        <!-- Filter Toolbar -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="p-1.5 rounded-lg bg-stone-100 text-stone-600">
                    <x-lucide-filter class="w-4 h-4" />
                </span>
                <span class="text-xs font-bold text-stone-700 uppercase tracking-wider">Filter Riwayat:</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                <!-- Filter Tahun -->
                <select wire:model.live="filterTahun" class="px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">-- Semua Tahun --</option>
                    @foreach ($availableYears as $yr)
                        <option value="{{ $yr }}">Tahun {{ $yr }}</option>
                    @endforeach
                </select>

                <!-- Filter Bulan -->
                <select wire:model.live="filterBulan" class="px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">-- Semua Bulan --</option>
                    @foreach ($months as $m)
                        <option value="{{ $m }}">{{ $m }}</option>
                    @endforeach
                </select>

                <!-- Filter Status -->
                <select wire:model.live="filterStatus" class="px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">-- Semua Status --</option>
                    <option value="dibayar">Telah Dibayar (Lunas)</option>
                    <option value="draft">Draf Penggajian</option>
                </select>
            </div>
        </div>

        <!-- Table List -->
        <x-table>
            <thead class="bg-stone-900 text-white font-extrabold uppercase tracking-wider text-[11px]">
                <tr>
                    <th class="p-3.5 text-left">Periode Gaji</th>
                    <th class="p-3.5 text-right">Gaji Pokok</th>
                    <th class="p-3.5 text-right">Tunjangan & Insentif</th>
                    <th class="p-3.5 text-right">Potongan</th>
                    <th class="p-3.5 text-right">Total Bersih</th>
                    <th class="p-3.5 text-center">Status</th>
                    <th class="p-3.5 text-center">Aksi Dokumen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200">
                @forelse ($salaries as $sal)
                    <tr class="hover:bg-emerald-50/40 transition">
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-xs text-stone-900">{{ $sal->bulan }} {{ $sal->tahun }}</div>
                            <div class="text-[10px] text-stone-500 font-medium">
                                Dibayar: {{ $sal->tanggal_bayar ? $sal->tanggal_bayar->format('d M Y') : 'Belum Ditentukan' }}
                            </div>
                        </td>
                        <td class="p-3.5 text-xs text-stone-800 text-right font-bold border-r border-stone-200">
                            Rp {{ number_format($sal->gaji_pokok, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-xs text-stone-700 text-right border-r border-stone-200 space-y-0.5">
                            <span class="text-[11px] block text-emerald-800 font-bold">
                                + Rp {{ number_format($sal->insentif_bpjs + $sal->insentif_maghrib_mengaji, 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] block text-stone-500">
                                BPJS: {{ number_format($sal->insentif_bpjs, 0, ',', '.') }} | Ngaji: {{ number_format($sal->insentif_maghrib_mengaji, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="p-3.5 text-xs text-stone-700 text-right border-r border-stone-200 space-y-0.5">
                            <span class="text-[11px] block text-rose-600 font-bold">
                                - Rp {{ number_format($sal->potongan_peminjaman + $sal->potongan_lainnya, 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] block text-stone-500">
                                Kasbon: {{ number_format($sal->potongan_peminjaman, 0, ',', '.') }} | Lain: {{ number_format($sal->potongan_lainnya, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="p-3.5 text-xs font-black text-emerald-800 text-right border-r border-stone-200">
                            Rp {{ number_format($sal->total_diterima, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($sal->status === 'dibayar')
                                <x-badge variant="emerald" size="xs" :dot="true">Dibayar</x-badge>
                            @else
                                <x-badge variant="amber" size="xs" :dot="true">Draf</x-badge>
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Tombol Preview PDF Interactive Modal -->
                                <x-button 
                                    type="button" 
                                    variant="primary" 
                                    size="xs" 
                                    icon="eye" 
                                    wire:click="openPreview({{ $sal->id }})" 
                                    title="Lihat Preview Slip Gaji PDF">
                                    Pratinjau PDF
                                </x-button>

                                <!-- Tombol Direct Download -->
                                <x-button 
                                    variant="secondary" 
                                    size="xs" 
                                    icon="download" 
                                    href="{{ route('gaji-guru.slip', ['id' => $sal->id, 'download' => 1]) }}" 
                                    title="Download File PDF">
                                    Unduh
                                </x-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-table.empty 
                        :colspan="7" 
                        title="Belum ada riwayat slip gaji" 
                        message="Riwayat penggajian Anda untuk periode yang dipilih belum diterbitkan oleh bagian Keuangan." 
                    />
                @endforelse
            </tbody>
        </x-table>

        @if ($salaries->hasPages())
            <div class="pt-2">
                {{ $salaries->links() }}
            </div>
        @endif
    </div>

    <!-- Floating Card Modal PDF Preview (Embedded Viewer) -->
    @if ($showPreviewModal && $previewSalary)
        <x-floating-card 
            :show="true" 
            :title="'Pratinjau Slip Gaji — ' . $previewSalary->bulan . ' ' . $previewSalary->tahun" 
            :subtitle="'Dokumen Resmi Penggajian Guru & GTK: ' . ($previewSalary->guru->user->nama ?? '-')"
            badge="SLIP GAJI RESMI"
            badgeVariant="emerald"
            icon="file-text"
            maxWidth="max-w-4xl"
            closeAction="closePreview"
            zIndex="z-[99995]"
        >
            <!-- Top Action Controls Inside Modal -->
            <div class="flex flex-wrap items-center justify-between gap-3 p-3 bg-stone-50 border border-stone-200 rounded-2xl">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-stone-700">Nominal Diterima:</span>
                    <span class="text-sm font-black text-emerald-800">Rp {{ number_format($previewSalary->total_diterima, 0, ',', '.') }}</span>
                    <span class="text-[10px] text-stone-500 font-mono">({{ $previewSalary->status === 'dibayar' ? 'LUNAS' : 'DRAF' }})</span>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Download Button -->
                    <a 
                        href="{{ route('gaji-guru.slip', ['id' => $previewSalary->id, 'download' => 1]) }}" 
                        class="px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center gap-1.5">
                        <x-lucide-download class="w-3.5 h-3.5" />
                        <span>Unduh PDF</span>
                    </a>

                    <!-- Open in New Tab / Print Window -->
                    <a 
                        href="{{ route('gaji-guru.slip', $previewSalary->id) }}" 
                        target="_blank" 
                        class="px-3 py-1.5 bg-stone-800 hover:bg-stone-900 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center gap-1.5">
                        <x-lucide-printer class="w-3.5 h-3.5" />
                        <span>Buka / Cetak</span>
                    </a>
                </div>
            </div>

            <!-- PDF Viewer Iframe -->
            <div class="w-full h-[62vh] rounded-2xl overflow-hidden border border-stone-200 bg-stone-100 relative shadow-inner">
                <iframe 
                    src="{{ route('gaji-guru.slip', $previewSalary->id) }}" 
                    class="w-full h-full border-0"
                    title="PDF Slip Gaji Preview">
                </iframe>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end pt-2 border-t border-stone-200">
                <x-button variant="secondary" size="md" wire:click="closePreview">
                    Tutup Pratinjau
                </x-button>
            </div>
        </x-floating-card>
    @endif
</div>
