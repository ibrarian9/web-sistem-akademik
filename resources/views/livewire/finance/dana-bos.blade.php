<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Tata Kelola Dana BOS (Bantuan Operasional Sekolah)" 
        :subtitle="auth()->user()->isKepalaSekolah() ? 'Pemantauan alokasi dan realisasi pos anggaran Dana BOS reguler/kinerja dari pemerintah.' : 'Pencatatan dana BOS reguler/kinerja dari pemerintah, terpisah dari kas yayasan sekolah.'"
        :badge="auth()->user()->isKepalaSekolah() ? 'MONITORING KEPALA SEKOLAH' : (auth()->user()->isSuperAdmin2() ? 'LIHAT SAJA (SUPER ADMIN 2)' : 'DANA BOS KEMDIKBUD')"
        badgeVariant="sky"
        icon="landmark"
    >
        <x-slot:actions>
            @if(!auth()->user()->isSuperAdmin2() && !auth()->user()->isKepalaSekolah())
            <x-button variant="primary" size="md" icon="plus" wire:click="openCreateModal('masuk')">
                Catat Penerimaan BOS
            </x-button>
            <x-button variant="danger-solid" size="md" icon="plus" wire:click="openCreateModal('keluar')">
                Catat Belanja BOS
            </x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    <!-- 3-Stat Summary Card Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-stat-card 
            title="Total Penerimaan Dana BOS" 
            :value="'Rp ' . number_format($totalMasuk, 0, ',', '.')" 
            icon="arrow-down-left" 
            variant="emerald" 
        />
        <x-stat-card 
            title="Total Realisasi Belanja BOS" 
            :value="'Rp ' . number_format($totalKeluar, 0, ',', '.')" 
            icon="arrow-up-right" 
            variant="rose" 
        />
        <x-stat-card 
            title="Sisa Saldo Kas Dana BOS" 
            :value="'Rp ' . number_format($saldoBos, 0, ',', '.')" 
            icon="wallet" 
            variant="sky" 
        />
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        :title="auth()->user()->isKepalaSekolah() ? 'Petunjuk Pemantauan Dana BOS' : 'Petunjuk Tata Kelola Dana BOS'"
        :steps="auth()->user()->isKepalaSekolah() ? [
            ['title' => 'Transparansi Anggaran', 'desc' => 'Kepala Sekolah dapat memantau seluruh alokasi penerimaan dan realisasi belanja BOS secara akurat dan transparan.'],
            ['title' => 'Filter Periode & Tab', 'desc' => 'Gunakan tab selector (Semua, Penerimaan, Belanja) dan filter periode (Hari ini, Kemarin, Minggu ini, Bulan ini, Custom) untuk mengevaluasi data.'],
            ['title' => 'Cetak & Ekspor', 'desc' => 'Gunakan tombol Rekap PDF atau Rekap Excel untuk mengunduh laporan realisasi dana BOS kapan pun dibutuhkan.']
        ] : [
            ['title' => 'Pencatatan Terpisah', 'desc' => 'Seluruh pencairan dan realisasi belanja BOS tercatat terpisah dengan kas yayasan demi kepatuhan RKAS.'],
            ['title' => 'Filter Periode & Tab', 'desc' => 'Gunakan tab selector (Semua, Penerimaan, Belanja) dan filter periode (Hari ini, Kemarin, Minggu ini, Bulan ini, Custom).'],
            ['title' => 'Aksi Massal (Bulk Delete)', 'desc' => 'Centang checkbox pada baris transaksi untuk menghapus banyak catatan sekaligus.']
        ]"
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="danger" :message="session('error')" />
    @endif

    <!-- Main Table Panel (Full Width) -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Tab Selector & Search Row -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Tab Buttons -->
            <div class="flex items-center p-1 bg-stone-100 border border-stone-200 rounded-xl w-fit">
                <button type="button" 
                    wire:click="selectTab('semua')" 
                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition {{ $filterJenis === 'semua' ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-500 hover:text-stone-900' }}">
                    Semua Transaksi
                </button>
                <button type="button" 
                    wire:click="selectTab('masuk')" 
                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $filterJenis === 'masuk' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-stone-500 hover:text-stone-900' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                    <span>Penerimaan</span>
                </button>
                <button type="button" 
                    wire:click="selectTab('keluar')" 
                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $filterJenis === 'keluar' ? 'bg-rose-600 text-white shadow-2xs' : 'text-stone-500 hover:text-stone-900' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-300"></span>
                    <span>Belanja BOS</span>
                </button>
            </div>

            <!-- Search Bar -->
            <div class="max-w-md w-full">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari rincian atau kategori belanja BOS..." />
            </div>
        </div>

        <!-- Date Range Filter & Export Row -->
        <div class="flex items-center justify-between gap-4 border-t border-stone-100 pt-3 flex-wrap">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Periode:</span>
                <x-date-filter model="filterPeriode" startDateModel="startDate" endDateModel="endDate" />
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                @if (count($selectedIds) > 0)
                    <span class="text-xs font-bold text-sky-700 bg-sky-50 px-3 py-1.5 rounded-xl border border-sky-200">
                        {{ count($selectedIds) }} transaksi dipilih
                    </span>
                @endif

                @php
                    $isKepsek = auth()->user()?->isKepalaSekolah();
                    $pdfRoute = route($isKepsek ? 'kepala-sekolah.dana-bos.pdf' : 'finance.dana-bos.pdf', ['filter_periode' => $filterPeriode, 'start_date' => $startDate, 'end_date' => $endDate, 'jenis' => $filterJenis, 'search' => $search]);
                    $excelRoute = route($isKepsek ? 'kepala-sekolah.dana-bos.excel' : 'finance.dana-bos.excel', ['filter_periode' => $filterPeriode, 'start_date' => $startDate, 'end_date' => $endDate, 'jenis' => $filterJenis, 'search' => $search]);
                @endphp

                <a href="{{ $pdfRoute }}" 
                   target="_blank" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-50 text-rose-700 hover:bg-rose-100 hover:text-rose-800 border border-rose-200 rounded-xl text-xs font-bold transition shadow-2xs">
                    <x-lucide-file-text class="w-4 h-4 text-rose-600" />
                    <span>Rekap PDF</span>
                </a>

                <a href="{{ $excelRoute }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold transition shadow-2xs">
                    <x-lucide-file-spreadsheet class="w-4 h-4 text-emerald-600" />
                    <span>Rekap Excel</span>
                </a>
            </div>
        </div>

        <!-- Table -->
        <x-table loadingTarget="search, filterJenis, filterPeriode, startDate, endDate, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <th class="w-12 p-3.5 text-center border-r border-emerald-700/60">
                        <input type="checkbox" wire:model.live="selectAll" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                    </th>
                    <x-table.th class="w-32">Tanggal</x-table.th>
                    <x-table.th align="center" class="w-36">Jenis</x-table.th>
                    <x-table.th class="w-48">Kategori / Rekening</x-table.th>
                    <x-table.th align="right" class="w-44">Nominal (Rp)</x-table.th>
                    <x-table.th class="min-w-[200px]">Keterangan / Rincian Belanja</x-table.th>
                    <x-table.th align="center" class="w-28">Bukti Foto</x-table.th>
                    <x-table.th align="center" class="w-32">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($transactions as $t)
                    <tr class="hover:bg-emerald-50/40 transition {{ in_array($t->id, $selectedIds) ? 'bg-sky-50/60 font-semibold' : '' }}">
                        <td class="p-3.5 text-center border-r border-stone-200">
                            <input type="checkbox" wire:model.live="selectedIds" value="{{ $t->id }}" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                        </td>
                        <td class="p-3.5 text-stone-900 text-xs font-bold border-r border-stone-200">
                            {{ \Carbon\Carbon::parse($t->tanggal)->translatedFormat('d M Y') }}
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($t->jenis === 'masuk')
                                <x-badge variant="emerald" size="xs" :dot="true">Penerimaan</x-badge>
                            @else
                                <x-badge variant="rose" size="xs" :dot="true">Belanja</x-badge>
                            @endif
                        </td>
                        <td class="p-3.5 font-bold text-stone-900 text-xs border-r border-stone-200">
                            {{ $t->kategori }}
                        </td>
                        <td class="p-3.5 text-right font-black text-sm border-r border-stone-200 {{ $t->jenis === 'masuk' ? 'text-emerald-800' : 'text-rose-700' }}">
                            {{ $t->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($t->nominal, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-stone-800 text-xs border-r border-stone-200">
                            {{ $t->keterangan }}
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($t->bukti)
                                <button 
                                    type="button" 
                                    wire:click="openPreviewBukti('{{ $t->bukti }}', '{{ $t->kategori }} ({{ strtoupper($t->jenis) }}) - Rp {{ number_format($t->nominal, 0, ',', '.') }}')" 
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-lg text-[11px] font-bold shadow-2xs transition cursor-pointer"
                                    title="Lihat Foto Bukti"
                                >
                                    <x-lucide-camera class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                    <span>Bukti</span>
                                </button>
                            @elseif(!auth()->user()->isSuperAdmin2() && !auth()->user()->isKepalaSekolah())
                                <button 
                                    type="button" 
                                    wire:click="openEditModal({{ $t->id }})" 
                                    class="inline-flex items-center gap-1 px-2 py-1 bg-stone-50 hover:bg-stone-100 text-stone-500 hover:text-stone-800 border border-dashed border-stone-300 rounded-lg text-[10px] font-semibold transition cursor-pointer"
                                    title="Upload Bukti Foto / Struk"
                                >
                                    <x-lucide-plus class="w-3 h-3 text-stone-400 shrink-0" />
                                    <span>+ Bukti</span>
                                </button>
                            @else
                                <span class="text-[10px] text-stone-400 italic font-mono">-</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            @if(!auth()->user()->isSuperAdmin2() && !auth()->user()->isKepalaSekolah())
                                <div class="flex items-center justify-center gap-1.5">
                                    <x-button type="button" variant="outline" size="xs" icon="edit" wire:click="openEditModal({{ $t->id }})" title="Edit Transaksi & Bukti">
                                        Edit
                                    </x-button>
                                    <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="deleteTransaction({{ $t->id }})" data-confirm="{{ auth()->user()->role?->nama === 'finance' ? 'Ajukan permohonan penghapusan catatan transaksi Dana BOS ini ke Super Admin / Super Admin 2?' : 'Yakin ingin menghapus catatan transaksi Dana BOS ini?' }}" title="Hapus Transaksi">
                                        Hapus
                                    </x-button>
                                </div>
                            @else
                                <span class="text-[10px] text-stone-400 font-mono italic">Lihat Saja</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="8" title="Belum ada transaksi Dana BOS" :message="auth()->user()->isSuperAdmin2() || auth()->user()->isKepalaSekolah() ? 'Belum ada catatan transaksi Dana BOS pada periode ini.' : 'Gunakan tombol di atas untuk mencatat penerimaan atau belanja dana BOS.'" />
                @endforelse
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Floating Bulk Actions Bar -->
    @if(!auth()->user()->isSuperAdmin2() && !auth()->user()->isKepalaSekolah() && auth()->user()->role?->nama !== 'finance')
    <x-bulk-actions :selectedCount="count($selectedIds)" deleteAction="bulkDelete" cancelAction="resetSelection" confirmText="Apakah Anda yakin ingin menghapus seluruh catatan transaksi Dana BOS yang dipilih?" />
    @endif

    <!-- Floating Card Form Dana BOS (Masuk / Keluar) -->
    <x-floating-card 
        :show="$showCreateModal" 
        :title="$jenis === 'masuk' ? 'Catat Penerimaan Dana BOS' : 'Catat Belanja / Realisasi BOS'" 
        subtitle="Pencatatan pos anggaran bantuan operasional sekolah sesuai RKAS."
        :badge="$jenis === 'masuk' ? 'PENERIMAAN' : 'BELANJA BOS'"
        :badgeVariant="$jenis === 'masuk' ? 'emerald' : 'rose'"
        icon="landmark"
        maxWidth="max-w-lg"
        closeAction="closeCreateModal"
    >
        <form wire:submit.prevent="saveTransaction" class="space-y-4">
            <!-- Jenis Selector Toggle in Modal -->
            <div class="grid grid-cols-2 gap-2 p-1 bg-stone-100 rounded-xl">
                <button type="button" 
                    wire:click="$set('jenis', 'masuk')" 
                    class="py-2 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 {{ $jenis === 'masuk' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                    <x-lucide-arrow-down-left class="w-4 h-4" />
                    <span>Penerimaan BOS</span>
                </button>
                <button type="button" 
                    wire:click="$set('jenis', 'keluar')" 
                    class="py-2 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 {{ $jenis === 'keluar' ? 'bg-rose-600 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                    <x-lucide-arrow-up-right class="w-4 h-4" />
                    <span>Belanja BOS</span>
                </button>
            </div>

            <!-- Kategori Belanja / Pos Penerimaan -->
            <x-input 
                label="Kategori / Pos Anggaran BOS" 
                name="kategori" 
                wire:model="kategori" 
                placeholder="{{ $jenis === 'masuk' ? 'Contoh: BOS Reguler Tahap 1' : 'Contoh: Belanja Buku Teks Pelajaran Kurikulum Merdeka' }}" 
                required 
            />

            <!-- Nominal Transaksi -->
            <x-input-currency 
                label="Nominal Transaksi (Rp)" 
                name="nominal" 
                wire:model="nominal" 
                placeholder="Contoh: 5.000.000" 
                required 
            />

            <!-- Tanggal -->
            <x-input 
                type="date" 
                label="Tanggal Transaksi" 
                name="tanggal" 
                wire:model="tanggal" 
                required 
            />

            <!-- Keterangan / Rincian -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Keterangan / Rincian Transaksi</label>
                <textarea wire:model="keterangan" rows="3" placeholder="Contoh: Pembelian 120 eksemplar buku paket matematika dari penyedia resmi Siplah..." class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none"></textarea>
                @error('keterangan') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Upload Bukti Foto (Transfer / Nota Belanja) -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                    {{ $jenis === 'masuk' ? 'Foto Bukti Transfer / Penerimaan BOS (Opsional)' : 'Foto Bukti Struk / Nota Belanja BOS (Opsional)' }}
                </label>

                @if ($bukti_foto)
                    <div class="p-2.5 bg-emerald-50 border border-emerald-300 rounded-xl flex items-center justify-between gap-2 shadow-2xs">
                        <div class="flex items-center gap-2 min-w-0">
                            <img src="{{ $bukti_foto->temporaryUrl() }}" alt="Preview Bukti" class="w-10 h-10 object-cover rounded-lg border border-emerald-200 shadow-2xs shrink-0" />
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-emerald-950 block truncate">{{ $bukti_foto->getClientOriginalName() }}</span>
                                <span class="text-[10px] text-emerald-700 font-semibold">{{ number_format($bukti_foto->getSize() / 1024, 1) }} KB</span>
                            </div>
                        </div>
                        <button type="button" wire:click="$set('bukti_foto', null)" class="px-2 py-1 text-[11px] font-bold text-rose-700 bg-white border border-rose-200 rounded-lg shrink-0 hover:bg-rose-50 cursor-pointer">
                            Batal
                        </button>
                    </div>
                @else
                    <div class="relative border-2 border-dashed border-stone-300 hover:border-emerald-500 rounded-xl p-3 bg-stone-50 hover:bg-emerald-50/20 text-center transition group cursor-pointer">
                        <input 
                            type="file" 
                            wire:model="bukti_foto" 
                            accept="image/jpeg,image/png,image/jpg,image/webp" 
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                        />
                        <div class="flex items-center justify-center gap-2 pointer-events-none text-stone-600 group-hover:text-emerald-800">
                            <x-lucide-camera class="w-4 h-4 text-emerald-600" />
                            <span class="text-xs font-bold">Pilih foto {{ $jenis === 'masuk' ? 'bukti transfer' : 'nota/struk' }} (Maks. 2MB, JPG/PNG/WEBP)</span>
                        </div>
                    </div>
                @endif
                <div wire:loading wire:target="bukti_foto" class="text-[11px] text-emerald-700 font-bold mt-1 flex items-center gap-1.5">
                    <span class="animate-spin inline-block w-3 h-3 border-2 border-emerald-600 border-t-transparent rounded-full"></span>
                    <span>Mengunggah foto...</span>
                </div>
                @error('bukti_foto') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button variant="secondary" size="md" wire:click="closeCreateModal">
                    Batal
                </x-button>
                <x-button variant="{{ $jenis === 'masuk' ? 'primary' : 'danger-solid' }}" size="md" type="submit" loadingTarget="saveTransaction">
                    Simpan Transaksi BOS
                </x-button>
            </div>
        </form>
    </x-floating-card>

    <!-- Floating Card Edit Transaksi & Bukti BOS -->
    @if ($showEditModal)
        <x-floating-card 
            :show="true" 
            title="Edit Transaksi Dana BOS" 
            subtitle="Perbarui rincian pos anggaran dan lampiran bukti transfer atau nota belanja."
            :badge="$edit_jenis === 'masuk' ? 'EDIT PENERIMAAN' : 'EDIT BELANJA'"
            :badgeVariant="$edit_jenis === 'masuk' ? 'emerald' : 'rose'"
            icon="edit"
            maxWidth="max-w-lg"
            closeAction="closeEditModal"
        >
            <form wire:submit.prevent="updateTransaction" class="space-y-4">
                <!-- Jenis Selector in Edit Modal -->
                <div class="grid grid-cols-2 gap-2 p-1 bg-stone-100 rounded-xl">
                    <button type="button" 
                        wire:click="$set('edit_jenis', 'masuk')" 
                        class="py-2 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 {{ $edit_jenis === 'masuk' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                        <x-lucide-arrow-down-left class="w-4 h-4" />
                        <span>Penerimaan BOS</span>
                    </button>
                    <button type="button" 
                        wire:click="$set('edit_jenis', 'keluar')" 
                        class="py-2 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 {{ $edit_jenis === 'keluar' ? 'bg-rose-600 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                        <x-lucide-arrow-up-right class="w-4 h-4" />
                        <span>Belanja BOS</span>
                    </button>
                </div>

                <!-- Kategori Belanja / Pos Penerimaan -->
                <x-input 
                    label="Kategori / Pos Anggaran BOS" 
                    name="edit_kategori" 
                    wire:model="edit_kategori" 
                    required 
                />

                <!-- Nominal -->
                <x-input-currency 
                    label="Nominal Transaksi (Rp)" 
                    name="edit_nominal" 
                    wire:model="edit_nominal" 
                    required 
                />

                <!-- Tanggal -->
                <x-input 
                    type="date" 
                    label="Tanggal Transaksi" 
                    name="edit_tanggal" 
                    wire:model="edit_tanggal" 
                    required 
                />

                <!-- Keterangan -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Keterangan / Rincian Transaksi</label>
                    <textarea wire:model="edit_keterangan" rows="3" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none"></textarea>
                    @error('edit_keterangan') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Foto Bukti Existing -->
                @if ($edit_existing_bukti)
                    <div class="space-y-1.5 p-3 bg-stone-50 border border-stone-200 rounded-xl">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-stone-700">Foto Bukti Saat Ini</span>
                            <button type="button" wire:click="deleteEditBukti" wire:confirm="Hapus foto bukti transaksi ini?" class="text-[11px] text-rose-600 hover:text-rose-800 font-bold inline-flex items-center gap-1 cursor-pointer">
                                <x-lucide-trash-2 class="w-3 h-3" />
                                <span>Hapus Foto</span>
                            </button>
                        </div>
                        <div class="rounded-lg overflow-hidden border border-stone-300 bg-stone-900 p-1">
                            <img src="{{ asset('storage/' . $edit_existing_bukti) }}" alt="Bukti BOS" class="w-full max-h-48 object-contain mx-auto rounded" />
                        </div>
                    </div>
                @endif

                <!-- Upload Bukti Baru / Pengganti -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                        {{ $edit_existing_bukti ? 'Ganti Foto Bukti (Transfer/Nota)' : 'Unggah Foto Bukti (Transfer/Nota)' }}
                    </label>

                    @if ($edit_bukti_foto)
                        <div class="p-2.5 bg-emerald-50 border border-emerald-300 rounded-xl flex items-center justify-between gap-2 shadow-2xs">
                            <div class="flex items-center gap-2 min-w-0">
                                <img src="{{ $edit_bukti_foto->temporaryUrl() }}" alt="Preview" class="w-10 h-10 object-cover rounded-lg border border-emerald-200 shadow-2xs shrink-0" />
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-emerald-950 block truncate">{{ $edit_bukti_foto->getClientOriginalName() }}</span>
                                    <span class="text-[10px] text-emerald-700 font-semibold">{{ number_format($edit_bukti_foto->getSize() / 1024, 1) }} KB</span>
                                </div>
                            </div>
                            <button type="button" wire:click="$set('edit_bukti_foto', null)" class="px-2 py-1 text-[11px] font-bold text-rose-700 bg-white border border-rose-200 rounded-lg shrink-0 cursor-pointer">
                                Batal
                            </button>
                        </div>
                    @else
                        <div class="relative border-2 border-dashed border-stone-300 hover:border-emerald-500 rounded-xl p-3 bg-stone-50 hover:bg-emerald-50/20 text-center transition group cursor-pointer">
                            <input 
                                type="file" 
                                wire:model="edit_bukti_foto" 
                                accept="image/jpeg,image/png,image/jpg,image/webp" 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                            />
                            <div class="flex items-center justify-center gap-2 pointer-events-none text-stone-600 group-hover:text-emerald-800">
                                <x-lucide-camera class="w-4 h-4 text-emerald-600" />
                                <span class="text-xs font-bold">Pilih file foto baru (Maks. 2MB, JPG/PNG/WEBP)</span>
                            </div>
                        </div>
                    @endif
                    <div wire:loading wire:target="edit_bukti_foto" class="text-[11px] text-emerald-700 font-bold mt-1 flex items-center gap-1.5">
                        <span class="animate-spin inline-block w-3 h-3 border-2 border-emerald-600 border-t-transparent rounded-full"></span>
                        <span>Mengunggah foto...</span>
                    </div>
                    @error('edit_bukti_foto') <span class="text-[11px] text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
                    <x-button variant="secondary" size="md" wire:click="closeEditModal">
                        Batal
                    </x-button>
                    <x-button variant="primary" size="md" type="submit" loadingTarget="updateTransaction">
                        Simpan Perubahan
                    </x-button>
                </div>
            </form>
        </x-floating-card>
    @endif

    <!-- Modal Lightbox Preview Bukti BOS -->
    @if ($showPreviewBuktiModal && $previewBuktiUrl)
        <x-floating-card 
            :show="true" 
            title="Pratinjau Bukti Transaksi BOS" 
            :subtitle="$previewBuktiTitle ?: 'Lampiran Bukti Transfer / Nota Belanja BOS'"
            badge="FOTO BUKTI"
            badgeVariant="emerald"
            icon="image"
            maxWidth="max-w-md"
            closeAction="closePreviewBukti"
        >
            <div class="space-y-3">
                <div class="rounded-xl overflow-hidden border border-stone-300 bg-stone-900 shadow-2xs p-1">
                    <img src="{{ $previewBuktiUrl }}" alt="Bukti Dana BOS" class="w-full max-h-80 object-contain mx-auto rounded-lg" />
                </div>
                <div class="flex items-center justify-between pt-2">
                    <a href="{{ $previewBuktiUrl }}" target="_blank" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 hover:underline flex items-center gap-1.5">
                        <x-lucide-external-link class="w-3.5 h-3.5" />
                        <span>Buka Resolusi Penuh</span>
                    </a>
                    <x-button type="button" variant="secondary" size="sm" wire:click="closePreviewBukti">
                        Tutup
                    </x-button>
                </div>
            </div>
        </x-floating-card>
    @endif
</div>
