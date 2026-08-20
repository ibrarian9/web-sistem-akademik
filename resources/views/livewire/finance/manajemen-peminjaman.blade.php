<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Manajemen Peminjaman / Kasbon Guru" 
        subtitle="Kelola pinjaman kasbon guru beserta riwayat cicilan bulanan yang terintegrasi dengan pemotongan slip gaji."
        badge="FASILITAS KASBON &amp; PINJAMAN"
        badgeVariant="emerald"
        icon="link"
    >
        <x-slot:actions>
            <x-button variant="primary" size="md" icon="plus" wire:click="openCreateModal">
                Catat Pinjaman Baru
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Kasbon & Peminjaman Guru"
        :steps="[
            ['title' => 'Catat Pinjaman Baru', 'desc' => 'Pilih nama guru, tentukan nominal pinjaman, tenor bulan, serta tanggal pencairan kasbon.'],
            ['title' => 'Potong Gaji Otomatis', 'desc' => 'Cicilan per bulan akan terpotong secara otomatis pada perhitungan slip gaji bulanan guru.'],
            ['title' => 'Pelunasan Kasbon', 'desc' => 'Status pinjaman akan otomatis berubah menjadi Lunas setelah seluruh angsuran terpenuhi.']
        ]"
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- Loans List Panel (Full Width) -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="max-w-md w-full">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama guru peminjam..." />
            </div>
            
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-stone-600 uppercase tracking-wider shrink-0">Status Pinjaman:</span>
                <select wire:model.live="filterStatus" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Status</option>
                    <option value="berjalan">Berjalan (Belum Lunas)</option>
                    <option value="lunas">Lunas</option>
                </select>
            </div>
        </div>

        <!-- List Table -->
        <x-table loadingTarget="search, filterStatus, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="min-w-[180px]">Guru Peminjam</x-table.th>
                    <x-table.th align="center" class="w-36">Tgl Pinjam</x-table.th>
                    <x-table.th align="right" class="w-40">Nominal Pinjaman</x-table.th>
                    <x-table.th align="center" class="w-28">Tenor</x-table.th>
                    <x-table.th align="right" class="w-40">Cicilan / Bulan</x-table.th>
                    <x-table.th align="right" class="w-40">Sisa Pinjaman</x-table.th>
                    <x-table.th align="center" class="w-32">Status</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($loans as $loan)
                    <tr class="hover:bg-emerald-50/40 transition">
                        <td class="p-3.5 font-extrabold text-stone-900 text-xs border-r border-stone-200">{{ $loan->guru->user->nama ?? '-' }}</td>
                        <td class="p-3.5 text-xs text-stone-600 text-center font-semibold border-r border-stone-200">{{ $loan->tanggal_pinjam ? $loan->tanggal_pinjam->format('d/m/Y') : '-' }}</td>
                        <td class="p-3.5 text-xs font-bold text-stone-900 text-right border-r border-stone-200">Rp {{ number_format($loan->nominal, 0, ',', '.') }}</td>
                        <td class="p-3.5 text-xs text-stone-600 text-center font-bold border-r border-stone-200">{{ $loan->tenor_bulan }} Bulan</td>
                        <td class="p-3.5 text-xs text-stone-700 text-right font-medium border-r border-stone-200">Rp {{ number_format($loan->cicilan_per_bulan, 0, ',', '.') }}</td>
                        <td class="p-3.5 text-xs font-black text-rose-700 text-right border-r border-stone-200">Rp {{ number_format($loan->sisa_pinjaman, 0, ',', '.') }}</td>
                        <td class="p-3.5 text-center">
                            @if ($loan->status === 'lunas')
                                <x-badge variant="emerald" size="xs" :dot="true">Lunas</x-badge>
                            @else
                                <x-badge variant="amber" size="xs" :dot="true">Berjalan</x-badge>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="7" title="Belum ada data peminjaman kasbon guru" message="Gunakan tombol Catat Pinjaman Baru di atas untuk mencatat kasbon guru." />
                @endforelse
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $loans->links() }}
        </div>
    </div>

    <!-- Floating Card Create Loan Modal Dialog -->
    <x-floating-card 
        :show="$showCreateModal" 
        title="Catat Pinjaman Kasbon Guru" 
        subtitle="Kelola pinjaman kasbon guru beserta tenor cicilan yang terintegrasi slip gaji."
        badge="KASBON GURU"
        badgeVariant="emerald"
        icon="plus-circle"
        maxWidth="max-w-lg"
        closeAction="closeCreateModal"
    >
        <form wire:submit.prevent="savePeminjaman" class="space-y-4">
            <!-- Guru -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Nama Guru Peminjam <span class="text-rose-500">*</span></label>
                <select wire:model="guru_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">-- Pilih Guru Peminjam --</option>
                    @foreach ($gurus as $g)
                        <option value="{{ $g->id }}">{{ $g->user->nama ?? '-' }} (NIP: {{ $g->nip }})</option>
                    @endforeach
                </select>
                @error('guru_id') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Nominal Pinjaman -->
            <x-input-currency 
                label="Nominal Pinjaman (Rp)" 
                name="nominal" 
                wire:model="nominal" 
                placeholder="Contoh: 1.000.000" 
                required 
            />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tenor -->
                <x-input 
                    type="number" 
                    label="Tenor (Bulan)" 
                    name="tenor_bulan" 
                    wire:model="tenor_bulan" 
                    min="1" 
                    max="60" 
                    required 
                />

                <!-- Tanggal Pinjam -->
                <x-input 
                    type="date" 
                    label="Tanggal Pinjam" 
                    name="tanggal_pinjam" 
                    wire:model="tanggal_pinjam" 
                    required 
                />
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button variant="secondary" size="md" wire:click="closeCreateModal">
                    Batal
                </x-button>
                <x-button variant="primary" size="md" type="submit" loadingTarget="savePeminjaman">
                    Simpan Pinjaman Kasbon
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
