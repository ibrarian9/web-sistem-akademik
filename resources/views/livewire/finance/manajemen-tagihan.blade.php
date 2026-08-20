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
            <x-button variant="primary" size="md" icon="plus" wire:click="openCreateModal">
                Rilis Tagihan Siswa
            </x-button>
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
                            <x-button variant="secondary" size="xs" icon="eye" wire:click="openDetail({{ $siswa->id }})">
                                Detail
                            </x-button>
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
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold text-stone-800 uppercase tracking-wider">Daftar Tagihan Siswa:</h4>
                    <x-button variant="primary" size="sm" icon="plus" wire:click="openCreateModal({{ $selectedSiswa->id }})">
                        Tambah Tagihan Siswa Ini
                    </x-button>
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

    <!-- FLOATING CARD: FORM RILIS TAGIHAN SISWA -->
    <x-floating-card 
        :show="$showCreateModal" 
        title="Rilis Tagihan Siswa (Perorangan)" 
        subtitle="Terbitkan tagihan baru dengan nominal spesifik sesuai struktur biaya masing-masing anak."
        badge="RILIS TAGIHAN"
        badgeVariant="emerald"
        icon="plus-circle"
        maxWidth="max-w-lg"
        closeAction="closeCreateModal"
    >
        <form wire:submit.prevent="createSingleTagihan" class="space-y-4">
            <!-- Siswa Penerima Tagihan -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Pilih Siswa Penerima Tagihan <span class="text-rose-500">*</span></label>
                <select wire:model="single_siswa_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach ($allStudents as $s)
                        <option value="{{ $s->id }}">{{ $s->user->nama ?? '-' }} (NIS: {{ $s->nis }} - Kelas {{ $s->kelas->nama_kelas ?? '-' }})</option>
                    @endforeach
                </select>
                @error('single_siswa_id') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
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

            <!-- Nominal dengan Pemisah Titik & Logo Rp -->
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
    </x-floating-card>
</div>
