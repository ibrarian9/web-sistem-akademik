<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Manajemen Tagihan Siswa" 
        subtitle="Buat, filter, edit, dan pantau status tagihan operasional/SPP siswa sesuai nominal masing-masing anak."
        badge="MANAJEMEN TAGIHAN &amp; SPP"
        badgeVariant="emerald"
        icon="file-text"
    >
        <x-slot:actions>
            <div class="flex items-center gap-2.5">
                <x-button variant="secondary" size="md" icon="credit-card" href="{{ route('finance.input-pembayaran') }}">
                    Kasir Pembayaran Siswa
                </x-button>
                <x-button variant="primary" size="md" icon="plus" wire:click="openCreateModal">
                    Rilis Tagihan Siswa
                </x-button>
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Manajemen Tagihan Siswa" 
        :steps="[
            ['title' => 'Tabel Per-Siswa', 'desc' => 'Daftar diringkas 1 baris per siswa dengan total tagihan, total dibayar, dan sisa tunggakan.'],
            ['title' => 'Tombol Detail & Edit', 'desc' => 'Klik tombol Detail untuk melihat rincian tagihan per bulan, serta tombol Edit untuk mengubah nominal/jatuh tempo.'],
            ['title' => 'Wewenang Akses', 'desc' => 'Finance dapat menerbitkan dan mengedit tagihan. Penghapusan tagihan hanya dapat dilakukan oleh Founder / Super Admin.']
        ]"
    />

    @if (session()->has('warning'))
        <div class="p-4 bg-amber-50 border border-amber-300 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-2xs" role="alert" data-alert-message="{{ session('warning') }}" data-alert-type="warning">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-amber-600 text-white rounded-xl shadow-xs shrink-0">
                    <x-lucide-alert-triangle class="w-5 h-5" />
                </div>
                <div>
                    <span class="text-xs font-black text-amber-950 block">{{ session('warning') }}</span>
                    <span class="text-[11px] text-amber-800 font-medium">Tagihan yang sudah ada sebelumnya tidak diduplikasi untuk menjaga keakuratan data pembukuan.</span>
                </div>
            </div>
            <x-button variant="primary" size="sm" icon="credit-card" href="{{ route('finance.input-pembayaran') }}">
                Buka Kasir Pembayaran
            </x-button>
        </div>
    @endif

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Content Table Card (Full Width) -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Filter & Search Bar -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Student -->
            <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama siswa atau NIS..." />

            <!-- Filter Bulan -->
            <select wire:model.live="filterBulan" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="">Semua Bulan / Periode</option>
                @foreach ($bulanOptions as $bln)
                    <option value="{{ $bln }}">{{ $bln }}</option>
                @endforeach
            </select>

            <!-- Filter Kelas -->
            <select wire:model.live="filterKelas" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="">Semua Kelas</option>
                @foreach ($classes as $c)
                    <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                @endforeach
            </select>

            <!-- Filter Jenis -->
            <select wire:model.live="filterJenis" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="">Semua Kategori</option>
                @foreach ($jenisTagihans as $jt)
                    <option value="{{ $jt['id'] }}">{{ $jt['nama'] }}</option>
                @endforeach
            </select>

            <!-- Filter Status -->
            <select wire:model.live="filterStatus" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                <option value="">Semua Status</option>
                <option value="belum_bayar">Belum Bayar</option>
                <option value="sebagian">Sebagian</option>
                <option value="lunas">Lunas</option>
            </select>
        </div>

        <!-- Date Range Filter Row -->
        <div class="flex items-center justify-between gap-4 border-t border-stone-100 pt-3 flex-wrap">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Periode:</span>
                <x-date-filter model="filterPeriode" startDateModel="startDate" endDateModel="endDate" />
            </div>

            @if (count($selectedIds) > 0)
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
                    {{ count($selectedIds) }} siswa dipilih
                </span>
            @endif
        </div>

        <!-- Students Table (1 Row per Student) -->
        <x-table loadingTarget="search, filterBulan, filterKelas, filterJenis, filterStatus, filterPeriode, startDate, endDate, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900 text-xs">
                <tr>
                    @if ($isFounder)
                        <th class="w-12 p-3.5 text-center border-r border-emerald-700/60">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                        </th>
                    @endif
                    <x-table.th class="min-w-[200px]">Identitas Siswa</x-table.th>
                    <x-table.th class="w-32">Kelas</x-table.th>
                    <x-table.th align="center" class="w-32">Jml Tagihan</x-table.th>
                    <x-table.th align="right" class="w-36">Total Tagihan</x-table.th>
                    <x-table.th align="right" class="w-36">Total Dibayar</x-table.th>
                    <x-table.th align="right" class="w-36">Sisa Piutang</x-table.th>
                    <x-table.th align="center" class="w-32">Status Global</x-table.th>
                    <x-table.th align="center" class="w-28">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($students as $siswa)
                    @php
                        $totalTagihan = $siswa->tagihans->sum('nominal');
                        $totalDibayar = $siswa->tagihans->sum('total_dibayar');
                        $sisaPiutang = max(0, $totalTagihan - $totalDibayar);
                        $countTagihan = $siswa->tagihans->count();
                        
                        $globalStatus = 'lunas';
                        if ($sisaPiutang > 0 && $totalDibayar > 0) {
                            $globalStatus = 'sebagian';
                        } elseif ($sisaPiutang > 0 && $totalDibayar == 0) {
                            $globalStatus = 'belum_bayar';
                        }
                    @endphp
                    <tr class="hover:bg-stone-50 transition duration-150 text-xs">
                        @if ($isFounder)
                            <td class="p-3.5 text-center border-r border-stone-200">
                                <input type="checkbox" wire:model.live="selectedIds" value="{{ $siswa->id }}" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                            </td>
                        @endif

                        <!-- Student Identity -->
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-xs">
                                {{ $siswa->user->nama ?? '-' }}
                            </div>
                            <div class="text-[11px] text-stone-500 font-mono font-bold mt-0.5">
                                NIS: {{ $siswa->nis }}
                            </div>
                        </td>

                        <!-- Class -->
                        <td class="p-3.5 border-r border-stone-200 font-bold text-stone-700">
                            {{ $siswa->kelas->nama_kelas ?? '-' }}
                        </td>

                        <!-- Number of Bills -->
                        <td class="p-3.5 text-center border-r border-stone-200">
                            <span class="px-2.5 py-1 bg-stone-100 border border-stone-200 rounded-lg font-black text-stone-800 text-[11px]">
                                {{ $countTagihan }} Tagihan
                            </span>
                        </td>

                        <!-- Total Tagihan -->
                        <td class="p-3.5 text-right font-black text-stone-900 border-r border-stone-200">
                            Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                        </td>

                        <!-- Total Dibayar -->
                        <td class="p-3.5 text-right font-black text-emerald-700 border-r border-stone-200">
                            Rp {{ number_format($totalDibayar, 0, ',', '.') }}
                        </td>

                        <!-- Sisa Piutang -->
                        <td class="p-3.5 text-right font-black text-rose-700 border-r border-stone-200">
                            Rp {{ number_format($sisaPiutang, 0, ',', '.') }}
                        </td>

                        <!-- Status Global -->
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($globalStatus === 'lunas')
                                <x-badge variant="emerald" size="xs">Semua Lunas</x-badge>
                            @elseif ($globalStatus === 'sebagian')
                                <x-badge variant="amber" size="xs">Ada Sebagian</x-badge>
                            @else
                                <x-badge variant="rose" size="xs">Belum Lunas</x-badge>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <x-button variant="secondary" size="xs" icon="eye" wire:click="openDetail({{ $siswa->id }})" title="Lihat Rincian Tagihan">
                                    Detail
                                </x-button>
                                @if ($sisaPiutang > 0)
                                    <a href="{{ route('finance.input-pembayaran', ['siswa_id' => $siswa->id]) }}" class="inline-flex items-center justify-center font-extrabold transition rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white shadow-2xs px-2.5 py-1 text-[11px] gap-1 cursor-pointer" title="Bayar Sekarang di Kasir">
                                        <x-lucide-credit-card class="w-3.5 h-3.5" />
                                        <span>Bayar</span>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isFounder ? 9 : 8 }}" class="py-12 text-center text-stone-400">
                            <x-table.empty title="Tidak ada data tagihan ditemukan" subtitle="Gunakan filter pencarian atau buat rilis tagihan baru." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $students->links() }}
        </div>
    </div>

    <!-- Floating Bulk Actions Bar (Founder Only) -->
    @if ($isFounder)
        <x-bulk-actions :selectedCount="count($selectedIds)" deleteAction="bulkDelete" cancelAction="resetSelection" confirmText="Apakah Anda yakin ingin menghapus seluruh tagihan yang belum dibayar dari siswa terpilih?" />
    @endif

    <!-- FLOATING CARD: DETAIL RINCIAN TAGIHAN SISWA -->
    <x-floating-card 
        :show="$showDetailModal" 
        :title="$selectedSiswa ? ($selectedSiswa->user->nama ?? 'Siswa') : 'Detail Tagihan Siswa'" 
        :subtitle="$selectedSiswa ? ('NIS: ' . $selectedSiswa->nis . ' | Kelas: ' . ($selectedSiswa->kelas->nama_kelas ?? '-')) : ''"
        badge="RINCIAN TAGIHAN SISWA"
        badgeVariant="emerald"
        icon="file-text"
        maxWidth="max-w-4xl"
        closeAction="closeDetailModal"
    >
        @if ($selectedSiswa)
            <div class="space-y-4">
                <!-- Summary Header within Modal -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-2xl">
                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Total Tagihan</span>
                        <span class="text-base font-black text-stone-900">Rp {{ number_format($selectedSiswa->tagihans->sum('nominal'), 0, ',', '.') }}</span>
                    </div>
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl">
                        <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">Total Terbayar</span>
                        <span class="text-base font-black text-emerald-800">Rp {{ number_format($selectedSiswa->tagihans->sum('total_dibayar'), 0, ',', '.') }}</span>
                    </div>
                    <div class="p-3 bg-rose-50 border border-rose-200 rounded-2xl">
                        <span class="text-[10px] font-bold text-rose-700 uppercase tracking-wider block">Sisa Piutang</span>
                        <span class="text-base font-black text-rose-800">Rp {{ number_format(max(0, $selectedSiswa->tagihans->sum('nominal') - $selectedSiswa->tagihans->sum('total_dibayar')), 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Action Button in Modal -->
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h4 class="text-xs font-bold text-stone-800 uppercase tracking-wider">Daftar Tagihan Siswa:</h4>
                    <div class="flex items-center gap-2">
                        <x-button variant="secondary" size="sm" icon="credit-card" href="{{ route('finance.input-pembayaran', ['siswa_id' => $selectedSiswa->id]) }}">
                            Buka Kasir Siswa Ini
                        </x-button>
                        <x-button variant="primary" size="sm" icon="plus" wire:click="openCreateModal({{ $selectedSiswa->id }})">
                            Tambah Tagihan Siswa Ini
                        </x-button>
                    </div>
                </div>

                <!-- Specific Invoices Table -->
                <div class="border border-stone-200 rounded-2xl overflow-hidden max-h-80 overflow-y-auto">
                    <x-table>
                        <thead class="bg-stone-900 text-white font-extrabold uppercase tracking-wider text-[10px] sticky top-0">
                            <tr>
                                <th class="p-3 text-left">Kategori &amp; Periode</th>
                                <th class="p-3 text-left">Jatuh Tempo</th>
                                <th class="p-3 text-right">Nominal</th>
                                <th class="p-3 text-right">Dibayar</th>
                                <th class="p-3 text-right">Sisa</th>
                                <th class="p-3 text-center">Status</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @forelse ($selectedSiswa->tagihans as $item)
                                @php
                                    $sisaItem = max(0, $item->nominal - $item->total_dibayar);
                                @endphp
                                <tr class="hover:bg-stone-50 text-xs">
                                    <td class="p-3 border-r border-stone-200">
                                        <div class="font-bold text-stone-900">{{ $item->jenisTagihan->nama ?? '-' }}</div>
                                        <div class="text-[11px] text-stone-500 font-medium">Bulan: {{ $item->bulan ?: '-' }}</div>
                                    </td>
                                    <td class="p-3 text-stone-600 border-r border-stone-200 font-medium">
                                        {{ \Carbon\Carbon::parse($item->jatuh_tempo)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="p-3 text-right font-bold text-stone-900 border-r border-stone-200">
                                        Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-right font-bold text-emerald-700 border-r border-stone-200">
                                        Rp {{ number_format($item->total_dibayar, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-right font-black text-rose-700 border-r border-stone-200">
                                        Rp {{ number_format($sisaItem, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-center border-r border-stone-200">
                                        @if ($item->status === 'lunas')
                                            <x-badge variant="emerald" size="xs">Lunas</x-badge>
                                        @elseif ($item->status === 'sebagian')
                                            <x-badge variant="amber" size="xs">Sebagian</x-badge>
                                        @else
                                            <x-badge variant="rose" size="xs">Belum Bayar</x-badge>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Edit Tagihan Button (Founder & Finance) -->
                                            <button 
                                                type="button"
                                                wire:click="openEditModal({{ $item->id }})" 
                                                class="p-1.5 text-stone-500 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg transition cursor-pointer"
                                                title="Edit Tagihan">
                                                <x-lucide-edit-3 class="w-4 h-4" />
                                            </button>

                                            <!-- Delete Tagihan Button (Founder Only) -->
                                            @if ($isFounder && $item->total_dibayar == 0)
                                                <button 
                                                    type="button"
                                                    wire:click="deleteTagihan({{ $item->id }})" 
                                                    data-confirm="Apakah Anda yakin ingin menghapus tagihan ini?"
                                                    class="p-1.5 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer"
                                                    title="Hapus Tagihan">
                                                    <x-lucide-trash-2 class="w-4 h-4" />
                                                </button>
                                            @endif

                                            <!-- Print Receipt Button -->
                                            @if ($item->pembayarans && $item->pembayarans->count() > 0)
                                                <a 
                                                    href="{{ route('finance.pembayaran.resi', $item->pembayarans->first()->id) }}" 
                                                    target="_blank" 
                                                    class="p-1.5 text-stone-500 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition"
                                                    title="Lihat Kuitansi Resi">
                                                    <x-lucide-printer class="w-4 h-4" />
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-stone-400 text-xs font-medium">
                                        Tidak ada tagihan yang tercatat untuk siswa ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </x-table>
                </div>

                <div class="flex items-center justify-end pt-3 border-t border-stone-200">
                    <x-button variant="secondary" size="md" wire:click="closeDetailModal">
                        Tutup
                    </x-button>
                </div>
            </div>
        @endif
    </x-floating-card>

    <!-- FLOATING CARD: FORM EDIT TAGIHAN SISWA (FOUNDER & FINANCE) -->
    <x-floating-card 
        :show="$showEditModal" 
        title="Edit Tagihan Siswa" 
        :subtitle="'Ubah rincian tagihan untuk: ' . $edit_siswa_nama"
        badge="EDIT TAGIHAN"
        badgeVariant="indigo"
        icon="edit-3"
        maxWidth="max-w-lg"
        closeAction="closeEditModal"
    >
        <form wire:submit.prevent="saveEditTagihan" class="space-y-4">
            @if ($edit_total_dibayar > 0)
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-xs font-medium flex items-center gap-2">
                    <x-lucide-alert-triangle class="w-4 h-4 text-amber-600 shrink-0" />
                    <span>Tagihan ini sudah dibayar sebesar <strong>Rp {{ number_format($edit_total_dibayar, 0, ',', '.') }}</strong>. Nominal baru tidak boleh lebih kecil dari jumlah yang sudah dibayar.</span>
                </div>
            @endif

            <!-- Jenis Tagihan -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Kategori Biaya / Tagihan <span class="text-rose-500">*</span></label>
                <select wire:model="edit_jenis_tagihan_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">-- Pilih Kategori Tagihan --</option>
                    @foreach ($jenisTagihans as $jt)
                        <option value="{{ $jt['id'] }}">{{ $jt['nama'] }}</option>
                    @endforeach
                </select>
                @error('edit_jenis_tagihan_id') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Bulan / Periode -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Bulan / Periode <span class="text-rose-500">*</span></label>
                    <select wire:model="edit_bulan" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        @foreach ($bulanOptions as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                    @error('edit_bulan') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Jatuh Tempo -->
                <x-input 
                    type="date" 
                    label="Jatuh Tempo" 
                    name="edit_jatuh_tempo" 
                    wire:model="edit_jatuh_tempo" 
                    required 
                />
            </div>

            <!-- Nominal -->
            <x-input-currency 
                label="Nominal Tagihan Baru (Rp)" 
                name="edit_nominal" 
                wire:model="edit_nominal" 
                placeholder="Contoh: 350.000" 
                required 
            />

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button variant="secondary" size="md" wire:click="closeEditModal">
                    Batal
                </x-button>
                <x-button variant="primary" size="md" type="submit" loadingTarget="saveEditTagihan">
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </x-floating-card>

    <!-- FLOATING CARD: FORM RILIS TAGIHAN SISWA (SINGLE & BULK INPUT) -->
    <x-floating-card 
        :show="$showCreateModal" 
        title="{{ $releaseMode === 'bulk' ? 'Rilis Tagihan Massal' : 'Rilis Tagihan Siswa' }}" 
        subtitle="{{ $releaseMode === 'bulk' ? 'Terbitkan tagihan serentak untuk seluruh siswa per kelas atau seluruh sekolah.' : 'Terbitkan tagihan baru dengan nominal spesifik untuk siswa perorangan.' }}"
        badge="{{ $releaseMode === 'bulk' ? 'RILIS MASSAL' : 'RILIS PERORANGAN' }}"
        badgeVariant="{{ $releaseMode === 'bulk' ? 'indigo' : 'emerald' }}"
        icon="{{ $releaseMode === 'bulk' ? 'layers' : 'plus-circle' }}"
        maxWidth="max-w-xl"
        closeAction="closeCreateModal"
    >
        <!-- Mode Switcher Tabs -->
        <div class="flex items-center p-1 bg-stone-100 rounded-2xl border border-stone-200 mb-5">
            <button 
                type="button" 
                wire:click="setReleaseMode('single')" 
                class="flex-1 py-2 text-xs font-black rounded-xl transition flex items-center justify-center gap-2 cursor-pointer {{ $releaseMode === 'single' ? 'bg-white text-emerald-800 shadow-xs' : 'text-stone-600 hover:text-stone-900' }}">
                <x-lucide-user class="w-4 h-4" />
                <span>Perorangan (1 Siswa)</span>
            </button>
            <button 
                type="button" 
                wire:click="setReleaseMode('bulk')" 
                class="flex-1 py-2 text-xs font-black rounded-xl transition flex items-center justify-center gap-2 cursor-pointer {{ $releaseMode === 'bulk' ? 'bg-white text-indigo-800 shadow-xs' : 'text-stone-600 hover:text-stone-900' }}">
                <x-lucide-layers class="w-4 h-4" />
                <span>Rilis Massal (Bulk)</span>
            </button>
        </div>

        @if ($releaseMode === 'single')
            <!-- FORM SINGLE / PERORANGAN -->
            <form wire:submit.prevent="createSingleTagihan" class="space-y-4">
                <!-- Filter Kelas & Search Autocomplete -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                        Pilih Siswa Penerima Tagihan <span class="text-rose-500">*</span>
                    </label>

                    @if ($single_siswa_id)
                        <!-- Selected Student Pill / Card -->
                        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between gap-3 shadow-2xs">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-700 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-xs">
                                    {{ strtoupper(substr($selectedStudentName, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-extrabold text-stone-900 text-xs">{{ $selectedStudentName }}</div>
                                    <div class="text-[11px] text-emerald-700 font-mono font-bold">
                                        NIS: {{ $selectedStudentNis }} • Kelas {{ $selectedStudentKelas }}
                                    </div>
                                </div>
                            </div>
                            <button 
                                type="button" 
                                wire:click="clearSelectedStudent" 
                                class="px-3 py-1.5 bg-white border border-stone-200 hover:bg-rose-50 hover:text-rose-700 text-stone-600 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
                                Ganti Siswa
                            </button>
                        </div>
                    @else
                        <!-- Filter Kelas & Live Search Input -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 relative">
                            <!-- Filter Kelas -->
                            <div class="sm:col-span-1">
                                <select wire:model.live="release_kelas_id" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                                    <option value="">Semua Kelas</option>
                                    @foreach ($classes as $c)
                                        <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Live Search Input -->
                            <div class="sm:col-span-2 relative">
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        wire:model.live.debounce.250ms="studentSearch" 
                                        placeholder="Ketik nama siswa atau NIS..." 
                                        class="w-full pl-9 pr-4 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-semibold focus:ring-2 focus:ring-emerald-600 shadow-2xs" 
                                    />
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                                        <x-lucide-search class="w-4 h-4" />
                                    </div>
                                </div>

                                <!-- Live Autocomplete Dropdown List -->
                                @if (count($searchedStudents) > 0)
                                    <div class="absolute top-full left-0 right-0 z-50 mt-1.5 bg-white border border-stone-200 rounded-2xl shadow-xl overflow-hidden max-h-56 overflow-y-auto divide-y divide-stone-100">
                                        @foreach ($searchedStudents as $s)
                                            <button 
                                                type="button" 
                                                wire:click="selectStudent({{ $s->id }})" 
                                                class="w-full px-3.5 py-2.5 text-left hover:bg-emerald-50 transition flex items-center justify-between gap-3 cursor-pointer group">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-7 h-7 rounded-lg bg-stone-100 group-hover:bg-emerald-600 group-hover:text-white font-bold text-stone-700 text-[10px] flex items-center justify-center shrink-0 transition">
                                                        {{ strtoupper(substr($s->user->nama ?? 'S', 0, 2)) }}
                                                    </span>
                                                    <div>
                                                        <span class="text-xs font-extrabold text-stone-900 group-hover:text-emerald-900 block leading-tight">
                                                            {{ $s->user->nama ?? '-' }}
                                                        </span>
                                                        <span class="text-[10px] text-stone-500 font-mono">
                                                            NIS: {{ $s->nis }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <span class="px-2 py-0.5 bg-stone-100 text-stone-700 text-[10px] font-extrabold rounded-md border border-stone-200">
                                                    Kelas {{ $s->kelas->nama_kelas ?? '-' }}
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif (strlen(trim($studentSearch)) >= 2)
                                    <div class="absolute top-full left-0 right-0 z-50 mt-1.5 bg-white border border-stone-200 rounded-2xl shadow-xl p-3 text-center text-xs text-stone-500 font-medium">
                                        Tidak ada siswa ditemukan dengan kata kunci "{{ $studentSearch }}".
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @error('single_siswa_id') 
                        <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Jenis Tagihan -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Kategori Biaya / Tagihan <span class="text-rose-500">*</span></label>
                    <select wire:model.live="jenis_tagihan_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="">-- Pilih Kategori Tagihan --</option>
                        @foreach ($jenisTagihans as $jt)
                            <option value="{{ $jt['id'] }}">{{ $jt['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('jenis_tagihan_id') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Bulan / Periode -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Bulan / Periode <span class="text-rose-500">*</span></label>
                        <select wire:model="bulan" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                            @foreach ($bulanOptions as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                        @error('bulan') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jatuh Tempo -->
                    <x-input 
                        type="date" 
                        label="Jatuh Tempo" 
                        name="jatuh_tempo" 
                        wire:model="jatuh_tempo" 
                        required 
                    />
                </div>

                <!-- Nominal Tagihan -->
                <x-input-currency 
                    label="Nominal Tagihan Siswa (Rp)" 
                    name="nominal" 
                    wire:model="nominal" 
                    placeholder="Contoh: 350.000" 
                    hint="Dapat disesuaikan secara fleksibel untuk siswa beasiswa atau potongan khusus."
                    required 
                />

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                    <x-button variant="secondary" size="md" wire:click="closeCreateModal">
                        Batal
                    </x-button>
                    <x-button variant="primary" size="md" type="submit" loadingTarget="createSingleTagihan">
                        Terbitkan Tagihan
                    </x-button>
                </div>
            </form>
        @else
            <!-- FORM BULK / RILIS MASSAL & LINTAS KELAS -->
            <form wire:submit.prevent="createBulkTagihan" class="space-y-4">
                <!-- Pilihan Target Massal -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                        Target Penerima Tagihan <span class="text-rose-500">*</span>
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <label class="p-3 border rounded-xl flex items-start gap-2.5 cursor-pointer transition {{ $bulkTarget === 'custom' ? 'border-indigo-600 bg-indigo-50/60 ring-2 ring-indigo-500/20' : 'border-stone-200 hover:bg-stone-50' }}">
                            <input type="radio" wire:model.live="bulkTarget" value="custom" class="mt-0.5 text-indigo-600 focus:ring-indigo-500" />
                            <div>
                                <span class="text-xs font-extrabold text-stone-900 block">Pilih Siswa (Lintas Kelas)</span>
                                <span class="text-[10px] text-stone-500 leading-tight block mt-0.5">Pilih 2-3 atau lebih siswa dengan SPP sama</span>
                            </div>
                        </label>

                        <label class="p-3 border rounded-xl flex items-start gap-2.5 cursor-pointer transition {{ $bulkTarget === 'class' ? 'border-indigo-600 bg-indigo-50/60 ring-2 ring-indigo-500/20' : 'border-stone-200 hover:bg-stone-50' }}">
                            <input type="radio" wire:model.live="bulkTarget" value="class" class="mt-0.5 text-indigo-600 focus:ring-indigo-500" />
                            <div>
                                <span class="text-xs font-extrabold text-stone-900 block">Per Kelas Tertentu</span>
                                <span class="text-[10px] text-stone-500 leading-tight block mt-0.5">Rilis untuk 1 rombel kelas</span>
                            </div>
                        </label>

                        <label class="p-3 border rounded-xl flex items-start gap-2.5 cursor-pointer transition {{ $bulkTarget === 'all' ? 'border-indigo-600 bg-indigo-50/60 ring-2 ring-indigo-500/20' : 'border-stone-200 hover:bg-stone-50' }}">
                            <input type="radio" wire:model.live="bulkTarget" value="all" class="mt-0.5 text-indigo-600 focus:ring-indigo-500" />
                            <div>
                                <span class="text-xs font-extrabold text-stone-900 block">Seluruh Siswa Aktif</span>
                                <span class="text-[10px] text-stone-500 leading-tight block mt-0.5">Rilis ke seluruh sekolah</span>
                            </div>
                        </label>
                    </div>
                </div>

                @if ($bulkTarget === 'custom')
                    <!-- MULTI-STUDENT PICKER (LINTAS KELAS) -->
                    <div class="space-y-3 bg-stone-50/80 border border-stone-200 p-3.5 rounded-2xl">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-extrabold text-stone-900 flex items-center gap-1.5">
                                <x-lucide-user-plus class="w-4 h-4 text-indigo-600" />
                                <span>Cari &amp; Tambah Siswa Lintas Kelas</span>
                            </span>
                            @if (count($bulkSelectedSiswaIds) > 0)
                                <button 
                                    type="button" 
                                    wire:click="clearBulkSelected" 
                                    class="text-[11px] font-bold text-rose-600 hover:text-rose-800 hover:underline cursor-pointer">
                                    Hapus Semua Pilihan
                                </button>
                            @endif
                        </div>

                        <!-- Filter Kelas & Search Input -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <div class="sm:col-span-1">
                                <select wire:model.live="bulkSearchKelasId" class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-indigo-600 shadow-2xs">
                                    <option value="">Semua Kelas</option>
                                    @foreach ($classes as $c)
                                        <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-2 relative">
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.250ms="bulkSearchStudent" 
                                    placeholder="Ketik nama siswa atau NIS..." 
                                    class="w-full pl-9 pr-4 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-semibold focus:ring-2 focus:ring-indigo-600 shadow-2xs" 
                                />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                                    <x-lucide-search class="w-4 h-4" />
                                </div>
                            </div>
                        </div>

                        <!-- Search Matching Results -->
                        @if (count($bulkSearchedStudents) > 0)
                            <div class="bg-white border border-stone-200 rounded-xl shadow-xs p-2 max-h-44 overflow-y-auto space-y-1 divide-y divide-stone-100">
                                @foreach ($bulkSearchedStudents as $bs)
                                    @php
                                        $isAlreadySelected = in_array($bs->id, $bulkSelectedSiswaIds);
                                    @endphp
                                    <div class="pt-1 first:pt-0 flex items-center justify-between gap-2 p-1.5 hover:bg-indigo-50/50 rounded-lg transition">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-md bg-indigo-100 text-indigo-800 font-extrabold text-[10px] flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($bs->user->nama ?? 'S', 0, 2)) }}
                                            </span>
                                            <div>
                                                <span class="text-xs font-bold text-stone-900 block leading-none">{{ $bs->user->nama ?? '-' }}</span>
                                                <span class="text-[10px] text-stone-500 font-mono">NIS: {{ $bs->nis }} • Kelas {{ $bs->kelas->nama_kelas ?? '-' }}</span>
                                            </div>
                                        </div>
                                        @if ($isAlreadySelected)
                                            <button 
                                                type="button" 
                                                wire:click="removeSiswaFromBulk({{ $bs->id }})" 
                                                class="px-2.5 py-1 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-lg text-[10px] font-bold border border-rose-200 transition cursor-pointer">
                                                Batal Pilih
                                            </button>
                                        @else
                                            <button 
                                                type="button" 
                                                wire:click="addSiswaToBulk({{ $bs->id }})" 
                                                class="px-2.5 py-1 bg-indigo-600 text-white hover:bg-indigo-700 rounded-lg text-[10px] font-bold shadow-2xs transition cursor-pointer">
                                                + Pilih
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @elseif (strlen(trim($bulkSearchStudent)) >= 2)
                            <div class="bg-white border border-stone-200 rounded-xl p-2.5 text-center text-xs text-stone-500">
                                Tidak ada siswa ditemukan dengan kata kunci "{{ $bulkSearchStudent }}".
                            </div>
                        @endif

                        <!-- Selected Students Pill Chips Box -->
                        <div class="space-y-1.5 pt-1">
                            <div class="flex items-center justify-between text-[11px] font-bold text-stone-600">
                                <span>Daftar Siswa Dipilih ({{ count($bulkSelectedSiswaIds) }} Siswa):</span>
                            </div>

                            @if (count($selectedStudentsList) > 0)
                                <div class="flex flex-wrap gap-2 max-h-36 overflow-y-auto p-1 bg-white border border-stone-200 rounded-xl">
                                    @foreach ($selectedStudentsList as $sel)
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 border border-indigo-200 rounded-lg text-indigo-950 text-xs font-bold shadow-2xs">
                                            <span>{{ $sel->user->nama ?? '-' }}</span>
                                            <span class="text-[10px] text-indigo-700 font-mono">({{ $sel->kelas->nama_kelas ?? '-' }})</span>
                                            <button 
                                                type="button" 
                                                wire:click="removeSiswaFromBulk({{ $sel->id }})" 
                                                class="ml-1 text-indigo-400 hover:text-rose-600 transition font-black cursor-pointer" 
                                                title="Hapus dari daftar">
                                                ✕
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-3 bg-white border border-dashed border-stone-300 rounded-xl text-center text-xs text-stone-500 font-medium">
                                    Belum ada siswa yang dipilih. Cari dan klik <strong>+ Pilih</strong> pada siswa yang ingin ditagihkan dengan nominal yang sama.
                                </div>
                            @endif
                        </div>

                        @error('bulkSelectedSiswaIds') 
                            <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> 
                        @enderror
                    </div>
                @elseif ($bulkTarget === 'class')
                    <!-- Filter Kelas Target -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Pilih Kelas Target <span class="text-rose-500">*</span></label>
                        <select wire:model.live="release_kelas_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-indigo-600 shadow-2xs">
                            <option value="">-- Pilih Kelas Target --</option>
                            @foreach ($classes as $c)
                                <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                            @endforeach
                        </select>
                        @error('release_kelas_id') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>
                @endif

                <!-- Bulk Target Summary Badge -->
                <div class="p-3 bg-indigo-50 border border-indigo-200 rounded-xl flex items-center justify-between gap-3 text-indigo-900 text-xs">
                    <div class="flex items-center gap-2 font-bold">
                        <x-lucide-users class="w-4 h-4 text-indigo-600" />
                        <span>Estimasi Penerima: <strong>{{ $bulkStudentCount }} Siswa</strong></span>
                    </div>
                    <span class="text-[10px] text-indigo-700 bg-white px-2 py-0.5 rounded-md border border-indigo-200 font-extrabold">
                        Otomatis Lewati Duplikat
                    </span>
                </div>

                <!-- Jenis Tagihan -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Kategori Biaya / Tagihan <span class="text-rose-500">*</span></label>
                    <select wire:model.live="jenis_tagihan_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-indigo-600 shadow-2xs">
                        <option value="">-- Pilih Kategori Tagihan --</option>
                        @foreach ($jenisTagihans as $jt)
                            <option value="{{ $jt['id'] }}">{{ $jt['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('jenis_tagihan_id') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Bulan / Periode -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Bulan / Periode <span class="text-rose-500">*</span></label>
                        <select wire:model="bulan" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-indigo-600 shadow-2xs">
                            @foreach ($bulanOptions as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                        @error('bulan') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jatuh Tempo -->
                    <x-input 
                        type="date" 
                        label="Jatuh Tempo" 
                        name="jatuh_tempo" 
                        wire:model="jatuh_tempo" 
                        required 
                    />
                </div>

                <!-- Nominal Tagihan -->
                <x-input-currency 
                    label="Nominal Tagihan yang Diterapkan (Rp)" 
                    name="nominal" 
                    wire:model="nominal" 
                    placeholder="Contoh: 350.000" 
                    hint="Nominal ini akan diterapkan serentak ke seluruh siswa yang dipilih di atas."
                    required 
                />

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                    <x-button variant="secondary" size="md" wire:click="closeCreateModal">
                        Batal
                    </x-button>
                    <x-button variant="primary" size="md" type="submit" loadingTarget="createBulkTagihan">
                        Terbitkan Tagihan ({{ $bulkStudentCount }} Siswa)
                    </x-button>
                </div>
            </form>
        @endif
    </x-floating-card>
</div>
