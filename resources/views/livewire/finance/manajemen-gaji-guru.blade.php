<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Manajemen Gaji Guru" 
        subtitle="Kelola draf penggajian bulanan guru, insentif piket &amp; mengaji, potongan kasbon, serta cetak slip gaji ber-QR code."
        badge="HONORARIUM &amp; PENGGAJIAN"
        badgeVariant="emerald"
        icon="wallet"
    >
        <x-slot:actions>
            <x-button variant="primary" size="md" icon="plus" wire:click="openGenerateModal">
                Generate Draf Gaji
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Penggajian Guru & Tenaga Pendidik"
        :steps="[
            ['title' => 'Generate Draf Gaji', 'desc' => 'Klik Generate Draf Gaji untuk menghitung gaji pokok, tunjangan jam mengajar, serta insentif piket bulanan.'],
            ['title' => 'Potongan Kasbon', 'desc' => 'Sistem secara otomatis memperhitungkan potongan cicilan pinjaman/kasbon guru yang masih berjalan.'],
            ['title' => 'Pencairan & Slip Gaji', 'desc' => 'Klik Bayar untuk mengonfirmasi pembayaran dan mencetak Slip Gaji sah ber-QR Code & TTD.']
        ]"
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Search & Filters Bar (Full Width) -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-1">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama guru..." />
            </div>
            
            <div>
                <select wire:model.live="filterStatus" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="dibayar">Dibayar</option>
                </select>
            </div>

            <div>
                <select wire:model.live="filterBulan" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Bulan</option>
                    @foreach ($listBulan as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <input wire:model.live="filterTahun" type="number" placeholder="Tahun" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 text-center shadow-2xs" />
            </div>
        </div>

        <!-- Salary List Table -->
        <x-table loadingTarget="search, filterStatus, filterBulan, filterTahun, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="min-w-[180px]">Guru</x-table.th>
                    <x-table.th align="center" class="w-32">Periode</x-table.th>
                    <x-table.th align="right" class="w-36">Gaji Pokok</x-table.th>
                    <x-table.th align="right" class="w-44">Insentif</x-table.th>
                    <x-table.th align="right" class="w-44">Potongan</x-table.th>
                    <x-table.th align="right" class="w-44">Total Diterima</x-table.th>
                    <x-table.th align="center" class="w-32">Status</x-table.th>
                    <x-table.th align="center" class="w-44">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($salaries as $sal)
                    <tr class="hover:bg-emerald-50/40 transition">
                        <td class="p-3.5 font-extrabold text-stone-900 text-xs border-r border-stone-200">{{ $sal->guru->user->nama ?? '-' }}</td>
                        <td class="p-3.5 text-xs text-stone-600 text-center font-bold border-r border-stone-200">{{ $sal->bulan }} {{ $sal->tahun }}</td>
                        <td class="p-3.5 text-xs text-stone-800 text-right font-bold border-r border-stone-200">Rp {{ number_format($sal->gaji_pokok, 0, ',', '.') }}</td>
                        <td class="p-3.5 text-xs text-stone-700 text-right border-r border-stone-200">
                            <span class="text-[11px] block text-stone-600 font-medium">BPJS: Rp {{ number_format($sal->insentif_bpjs, 0, ',', '.') }}</span>
                            <span class="text-[11px] block text-stone-600 font-medium">Ngaji: Rp {{ number_format($sal->insentif_maghrib_mengaji, 0, ',', '.') }}</span>
                        </td>
                        <td class="p-3.5 text-xs text-stone-700 text-right border-r border-stone-200">
                            <span class="text-[11px] block text-rose-600 font-bold">Kasbon: Rp {{ number_format($sal->potongan_peminjaman, 0, ',', '.') }}</span>
                            <span class="text-[11px] block text-stone-500 font-medium">Lain: Rp {{ number_format($sal->potongan_lainnya, 0, ',', '.') }}</span>
                        </td>
                        <td class="p-3.5 text-xs font-black text-emerald-800 text-right border-r border-stone-200">Rp {{ number_format($sal->total_diterima, 0, ',', '.') }}</td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($sal->status === 'dibayar')
                                <x-badge variant="emerald" size="xs" :dot="true">Dibayar</x-badge>
                            @else
                                <x-badge variant="amber" size="xs" :dot="true">Draft</x-badge>
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                @if ($sal->status === 'draft')
                                    <x-button variant="secondary" size="xs" icon="edit" wire:click="openEditModal({{ $sal->id }})" title="Edit Draf">
                                        Edit
                                    </x-button>
                                    <x-button variant="primary" size="xs" icon="credit-card" wire:click="paySalary({{ $sal->id }})" title="Bayar">
                                        Bayar
                                    </x-button>
                                    <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="deleteDraft({{ $sal->id }})" data-confirm="Apakah Anda yakin ingin menghapus draf gaji ini?" title="Hapus Draf">
                                        Hapus
                                    </x-button>
                                @else
                                    <x-button variant="outline" size="xs" icon="file-text" href="{{ route('finance.gaji-guru.slip', $sal->id) }}" target="_blank">
                                        Slip PDF
                                    </x-button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="8" title="Belum ada draf gaji guru" message="Gunakan tombol Generate Draf Gaji di atas untuk memproses penggajian." />
                @endforelse
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $salaries->links() }}
        </div>
    </div>

    <!-- Floating Card Generate Draft Modal -->
    <x-floating-card 
        :show="$showGenerateModal" 
        title="Generate Draf Gaji Guru" 
        subtitle="Hitung otomatis gaji pokok, tunjangan jam mengajar, dan potongan cicilan kasbon."
        badge="PENGGAJIAN BULANAN"
        badgeVariant="emerald"
        icon="plus-circle"
        maxWidth="max-w-md"
        closeAction="closeGenerateModal"
    >
        <div class="space-y-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Bulan Penggajian</label>
                <select wire:model="generateBulan" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    @foreach ($listBulan as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Tahun</label>
                <input wire:model="generateTahun" type="number" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 text-center shadow-2xs" />
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t border-stone-200">
            <x-button variant="secondary" size="md" wire:click="closeGenerateModal">
                Batal
            </x-button>
            <x-button variant="primary" size="md" wire:click="generateDrafts" loadingTarget="generateDrafts">
                Generate Gaji
            </x-button>
        </div>
    </x-floating-card>

    <!-- Floating Card Edit Draft Modal -->
    <x-floating-card 
        :show="$showEditModal" 
        :title="$editGuruNama" 
        subtitle="Penyesuaian nominal gaji pokok, insentif, atau potongan slip gaji."
        badge="EDIT DRAF GAJI"
        badgeVariant="emerald"
        icon="edit"
        maxWidth="max-w-lg"
        closeAction="closeEditModal"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <x-input-currency 
                    label="Gaji Pokok (Rp)" 
                    name="editGajiPokok" 
                    wire:model="editGajiPokok" 
                    wire:keyup="calculateEditTotal" 
                    required 
                />
            </div>

            <div>
                <x-input-currency 
                    label="Insentif BPJS (Rp)" 
                    name="editInsentifBpjs" 
                    wire:model="editInsentifBpjs" 
                    wire:keyup="calculateEditTotal" 
                />
            </div>

            <div>
                <x-input-currency 
                    label="Insentif Maghrib Mengaji (Rp)" 
                    name="editInsentifMaghrib" 
                    wire:model="editInsentifMaghrib" 
                    wire:keyup="calculateEditTotal" 
                />
            </div>

            <div>
                <x-input-currency 
                    label="Potongan Kasbon / Pinjaman (Rp)" 
                    name="editPotonganPinjaman" 
                    wire:model="editPotonganPinjaman" 
                    wire:keyup="calculateEditTotal" 
                />
            </div>

            <div>
                <x-input-currency 
                    label="Potongan Lainnya (Rp)" 
                    name="editPotonganLainnya" 
                    wire:model="editPotonganLainnya" 
                    wire:keyup="calculateEditTotal" 
                />
            </div>

            <div class="sm:col-span-2 p-4 bg-emerald-50 rounded-2xl border border-emerald-200 flex items-center justify-between shadow-2xs">
                <span class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Total Diterima Guru:</span>
                <span class="text-lg font-black text-emerald-800">Rp {{ number_format($editTotalDiterima, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t border-stone-200">
            <x-button variant="secondary" size="md" wire:click="closeEditModal">
                Batal
            </x-button>
            <x-button variant="primary" size="md" wire:click="saveEdit" loadingTarget="saveEdit">
                Simpan Perubahan
            </x-button>
        </div>
    </x-floating-card>
</div>
