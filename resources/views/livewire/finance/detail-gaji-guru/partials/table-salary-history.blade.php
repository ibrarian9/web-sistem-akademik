<!-- Table of Teacher Salary History -->
<x-table loadingTarget="search, filterStatus, filterBulan, filterTahun, page">
    <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider select-none text-[11px]">
        <tr>
            <th class="p-3.5 text-center w-10 border-b border-r border-emerald-700/60">
                <input type="checkbox" wire:model.live="selectAll" class="rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer" title="Pilih Semua" />
            </th>
            <x-table.th align="center" class="w-32">Periode Gaji</x-table.th>
            <x-table.th align="right" class="w-36">Gaji Pokok</x-table.th>
            <x-table.th align="right" class="w-40">Tunjangan & Insentif</x-table.th>
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
                        <div class="inline-flex flex-col items-center gap-1">
                            <x-badge variant="emerald" size="xs" :dot="true">Dibayar</x-badge>
                            @if ($sal->bukti_bayar)
                                <a href="{{ asset('storage/' . $sal->bukti_bayar) }}" target="_blank" 
                                   class="inline-flex items-center gap-1 text-[10px] text-emerald-700 hover:text-emerald-900 font-bold hover:underline"
                                   title="Lihat Foto Bukti Struk/TF">
                                    <x-lucide-camera class="w-3 h-3" />
                                    <span>Struk/TF</span>
                                </a>
                            @endif
                        </div>
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

                        @if(!auth()->user()->isSuperAdmin2())
                        <button 
                            type="button" 
                            wire:click="deleteSalary({{ $sal->id }})" 
                            data-confirm="{{ auth()->user()->role?->nama === 'finance' ? 'Ajukan permohonan penghapusan data gaji periode ' . $sal->bulan . ' ' . $sal->tahun . ' ke Super Admin / Super Admin 2?' : 'Apakah Anda yakin ingin menghapus data gaji periode ' . $sal->bulan . ' ' . $sal->tahun . '?' }}"
                            class="p-1.5 rounded-xl text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 shadow-2xs transition cursor-pointer"
                            title="Hapus Data Gaji"
                        >
                            <x-lucide-trash-2 class="w-4 h-4" />
                        </button>
                        @endif
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
