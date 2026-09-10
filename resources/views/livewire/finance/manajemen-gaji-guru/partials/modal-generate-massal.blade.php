{{-- 1. Generate Modal (Massal dengan Pratinjau & Edit Pra-Generate dengan Pemisah Titik & Tanpa Panah) --}}
<x-floating-card 
    :show="$showGenerateModal" 
    title="Generate Draf Honorarium Pegawai" 
    subtitle="Tinjau, sesuaikan nominal gaji per guru, lalu generate draf sekaligus dalam satu klik."
    badge="DRAF PAYROLL PRA-GENERATE"
    badgeVariant="emerald"
    icon="calendar-plus"
    maxWidth="max-w-7xl"
    closeAction="closeGenerateModal"
>
    <div class="space-y-4 font-sans">
        <!-- Filter Periode Bar -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-emerald-50/60 p-3.5 rounded-2xl border border-emerald-200/80 items-center">
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Bulan Gaji</label>
                <select wire:model.live="generateBulan" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                    @foreach ($listBulan as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-stone-600 uppercase tracking-wider mb-1">Tahun</label>
                <input type="number" wire:model.live.debounce.400ms="generateTahun" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 text-center" />
            </div>
            <div class="pt-3 sm:pt-0 sm:text-right">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800 block">Status Draf:</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-700 text-white text-xs font-bold shadow-xs">
                    {{ count($generateItems) }} Pegawai Siap Digenerate
                </span>
            </div>
        </div>

        <!-- Pre-Generation Table with Formatted Dot Separators and Clean Look -->
        @if (count($generateItems) > 0)
            <div class="overflow-x-auto max-h-[50vh] rounded-2xl border border-stone-200 shadow-inner custom-scrollbar bg-white">
                <table class="w-full text-left border-separate border-spacing-0 text-xs text-stone-800">
                    <thead class="bg-stone-900 text-white font-extrabold uppercase tracking-wider sticky top-0 z-20 select-none">
                        <tr>
                            <th class="p-2.5 text-center w-10 border-b border-r border-stone-700">
                                <input type="checkbox" wire:model.live="generateSelectAll" class="rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer" title="Pilih Semua" />
                            </th>
                            <th class="p-2.5 border-b border-r border-stone-700 min-w-[150px]">Pegawai & Jabatan</th>
                            <th class="p-2.5 border-b border-r border-stone-700 w-32 text-right">Gaji Pokok (Rp)</th>
                            <th class="p-2.5 border-b border-r border-stone-700 w-28 text-right">Berkala (Rp)</th>
                            <th class="p-2.5 border-b border-r border-stone-700 w-28 text-right">Insentif (Rp)</th>
                            <th class="p-2.5 border-b border-r border-stone-700 w-28 text-right">Ekskul (Rp)</th>
                            <th class="p-2.5 border-b border-r border-stone-700 w-28 text-right">BPJSTK (Rp)</th>
                            <th class="p-2.5 border-b border-r border-stone-700 w-28 text-right">Maghrib (Rp)</th>
                            <th class="p-2.5 border-b border-r border-stone-700 w-28 text-right">Pot. Sosial</th>
                            <th class="p-2.5 border-b border-r border-stone-700 w-28 text-right">Kasbon</th>
                            <th class="p-2.5 border-b border-stone-700 w-36 text-right">Take Home Pay</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        @foreach ($generateItems as $gId => $item)
                            <tr class="hover:bg-emerald-50/50 transition {{ !empty($item['selected']) ? 'bg-white' : 'bg-stone-50/80 opacity-60' }}">
                                <td class="p-2.5 text-center border-b border-r border-stone-200">
                                    <input type="checkbox" wire:model.live="generateItems.{{ $gId }}.selected" class="rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                                </td>
                                <td class="p-2.5 font-extrabold text-stone-900 text-xs border-b border-r border-stone-200">
                                    <div class="leading-tight">{{ $item['nama'] }}</div>
                                    <div class="text-[10px] text-emerald-700 font-bold mt-0.5">{{ $item['jabatan'] }}</div>
                                    <span class="text-[9px] text-stone-400 font-mono">NIY: {{ $item['nip'] }}</span>
                                </td>
                                
                                <!-- Gaji Pokok Formatted Input -->
                                <td class="p-2 border-b border-r border-stone-200 text-right">
                                    <x-input-currency
                                        :prefix="false"
                                        wire:model.live.debounce.300ms="generateItems.{{ $gId }}.gaji_pokok"
                                        placeholder="0"
                                        class="!py-1.5 !px-2.5 !rounded-lg text-xs"
                                    />
                                </td>

                                <!-- Gaji Berkala Formatted Input -->
                                <td class="p-2 border-b border-r border-stone-200 text-right">
                                    <x-input-currency
                                        :prefix="false"
                                        wire:model.live.debounce.300ms="generateItems.{{ $gId }}.gaji_berkala"
                                        placeholder="0"
                                        class="!py-1.5 !px-2.5 !rounded-lg text-xs"
                                    />
                                </td>

                                <!-- Insentif Formatted Input -->
                                <td class="p-2 border-b border-r border-stone-200 text-right">
                                    <x-input-currency
                                        :prefix="false"
                                        wire:model.live.debounce.300ms="generateItems.{{ $gId }}.insentif"
                                        placeholder="0"
                                        class="!py-1.5 !px-2.5 !rounded-lg text-xs"
                                    />
                                </td>

                                <!-- Honor Ekskul Formatted Input -->
                                <td class="p-2 border-b border-r border-stone-200 text-right">
                                    <x-input-currency
                                        :prefix="false"
                                        wire:model.live.debounce.300ms="generateItems.{{ $gId }}.honor_ekskul"
                                        placeholder="0"
                                        class="!py-1.5 !px-2.5 !rounded-lg text-xs"
                                    />
                                </td>

                                <!-- BPJSTK Formatted Input -->
                                <td class="p-2 border-b border-r border-stone-200 text-right">
                                    <x-input-currency
                                        :prefix="false"
                                        wire:model.live.debounce.300ms="generateItems.{{ $gId }}.insentif_bpjs"
                                        placeholder="0"
                                        class="!py-1.5 !px-2.5 !rounded-lg text-xs"
                                    />
                                </td>

                                <!-- Maghrib Mengaji Formatted Input -->
                                <td class="p-2 border-b border-r border-stone-200 text-right">
                                    <x-input-currency
                                        :prefix="false"
                                        wire:model.live.debounce.300ms="generateItems.{{ $gId }}.insentif_maghrib_mengaji"
                                        placeholder="0"
                                        class="!py-1.5 !px-2.5 !rounded-lg text-xs"
                                    />
                                </td>

                                <!-- Potongan Sosial Formatted Input -->
                                <td class="p-2 border-b border-r border-stone-200 text-right">
                                    <x-input-currency
                                        :prefix="false"
                                        wire:model.live.debounce.300ms="generateItems.{{ $gId }}.potongan_sosial"
                                        placeholder="0"
                                        class="!py-1.5 !px-2.5 !rounded-lg text-xs"
                                    />
                                </td>

                                <!-- Potongan Kasbon Formatted Input -->
                                <td class="p-2 border-b border-r border-stone-200 text-right">
                                    <x-input-currency
                                        :prefix="false"
                                        wire:model.live.debounce.300ms="generateItems.{{ $gId }}.potongan_peminjaman"
                                        placeholder="0"
                                        class="!py-1.5 !px-2.5 !rounded-lg text-xs"
                                    />
                                </td>

                                <!-- Calculated THP -->
                                <td class="p-2.5 border-b border-stone-200 text-right font-black text-emerald-800 text-xs">
                                    <span class="px-2 py-1 rounded bg-emerald-50 border border-emerald-200 block text-right font-black text-emerald-900">
                                        Rp {{ number_format($item['total_diterima'], 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Bottom Summary & Actions -->
            @php
                $selectedCount = count(array_filter($generateItems, fn($i) => !empty($i['selected'])));
                $totalEstimatedThp = array_sum(array_map(fn($i) => !empty($i['selected']) ? $i['total_diterima'] : 0, $generateItems));
            @endphp
            <div class="p-4 bg-stone-900 text-white rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-3 shadow-md">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Ringkasan Draf Terpilih</span>
                    <div class="text-xs text-stone-200">
                        <strong>{{ $selectedCount }}</strong> dari {{ count($generateItems) }} pegawai dipilih &bull; Total THP: <strong class="text-emerald-400 font-black">Rp {{ number_format($totalEstimatedThp, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <x-button variant="secondary" size="md" wire:click="closeGenerateModal">Batal</x-button>
                    <x-button 
                        variant="primary" 
                        size="md" 
                        wire:click="generateDrafts" 
                        loadingTarget="generateDrafts"
                        :disabled="$selectedCount === 0"
                    >
                        Generate & Simpan ({{ $selectedCount }} Guru)
                    </x-button>
                </div>
            </div>
        @else
            <div class="p-8 text-center bg-stone-50 rounded-2xl border border-stone-200 space-y-2">
                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto">
                    <x-lucide-check-circle-2 class="w-6 h-6" />
                </div>
                <h4 class="text-sm font-extrabold text-stone-900">Seluruh Pegawai Telah Memiliki Draf</h4>
                <p class="text-xs text-stone-500 max-w-md mx-auto">
                    Tidak ada pegawai aktif yang belum digenerate untuk periode <strong>{{ $generateBulan }} {{ $generateTahun }}</strong>. Silakan pilih bulan/tahun lain atau ubah draf yang sudah ada di tabel utama.
                </p>
                <div class="pt-2">
                    <x-button variant="secondary" size="sm" wire:click="closeGenerateModal">Tutup</x-button>
                </div>
            </div>
        @endif
    </div>
</x-floating-card>
