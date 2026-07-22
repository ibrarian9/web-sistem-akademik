<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Manajemen Peminjaman / Kasbon Guru</h2>
        <p class="text-sm text-stone-500">Kelola pinjaman kasbon para guru beserta riwayat cicilan bulanan yang terintegrasi dengan pemotongan gaji.</p>
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Kasbon & Peminjaman Guru"
        :steps="[
            ['title' => 'Catat Pinjaman Baru', 'desc' => 'Pilih nama guru, tentukan nominal pinjaman, tenor bulan, serta alasan pengajuan kasbon.'],
            ['title' => 'Potong Gaji Otomatis', 'desc' => 'Cicilan per bulan akan terpotong secara otomatis pada perhitungan slip gaji bulanan guru.'],
            ['title' => 'Pelunasan Kasbon', 'desc' => 'Status pinjaman akan otomatis berubah menjadi Lunas setelah seluruh angsuran terpenuhi.']
        ]"
    />

    @if (session()->has('message'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm font-semibold flex items-center gap-2">
            <x-lucide-check-circle class="w-5 h-5 text-green-600" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Input Form Panel -->
        <div class="lg:col-span-1 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6 h-fit">
            <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider border-b border-stone-200 pb-2">Catat Pinjaman Baru</h3>
            
            <form wire:submit.prevent="savePeminjaman" class="space-y-4">
                <!-- Guru -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Nama Guru</label>
                    <select wire:model="guru_id" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500">
                        <option value="">-- Pilih Guru --</option>
                        @foreach ($gurus as $g)
                            <option value="{{ $g->id }}">{{ $g->user->nama ?? '-' }} (NIP: {{ $g->nip }})</option>
                        @endforeach
                    </select>
                    @error('guru_id') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Nominal -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Nominal Pinjaman (Rp)</label>
                    <input wire:model="nominal" type="number" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500 text-right font-bold" />
                    @error('nominal') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tenor -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Tenor (Bulan)</label>
                    <input wire:model="tenor_bulan" type="number" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500 text-center font-bold" min="1" max="60" />
                    @error('tenor_bulan') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal Pinjam -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Tanggal Pinjam</label>
                    <input wire:model="tanggal_pinjam" type="date" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500" />
                    @error('tanggal_pinjam') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold transition duration-200 shadow-md shadow-green-600/10">
                        Simpan Pinjaman
                    </button>
                </div>
            </form>
        </div>

        <!-- Loans List Panel -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider">Daftar Peminjaman</h3>
                
                <!-- Search & Filters -->
                <div class="flex flex-wrap items-center gap-3">
                    <input wire:model.live="search" type="text" placeholder="Cari nama guru..." class="px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-green-500/50 focus:border-green-500 w-44" />
                    
                    <select wire:model.live="filterStatus" class="px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-green-500/50 focus:border-green-500">
                        <option value="">Semua Status</option>
                        <option value="berjalan">Berjalan</option>
                        <option value="lunas">Lunas</option>
                    </select>
                </div>
            </div>

            <!-- List Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-stone-200">
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Guru</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Tanggal Pinjam</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Nominal</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Tenor</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Cicilan/Bulan</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Sisa Pinjaman</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse ($loans as $loan)
                            <tr class="hover:bg-stone-50">
                                <td class="py-3.5 text-sm font-semibold text-stone-800">{{ $loan->guru->user->nama ?? '-' }}</td>
                                <td class="py-3.5 text-sm text-stone-600">{{ $loan->tanggal_pinjam ? $loan->tanggal_pinjam->format('d-m-Y') : '-' }}</td>
                                <td class="py-3.5 text-sm font-semibold text-stone-800 text-right">Rp {{ number_format($loan->nominal, 0, ',', '.') }}</td>
                                <td class="py-3.5 text-sm text-stone-600 text-center">{{ $loan->tenor_bulan }} bln</td>
                                <td class="py-3.5 text-sm text-stone-600 text-right">Rp {{ number_format($loan->cicilan_per_bulan, 0, ',', '.') }}</td>
                                <td class="py-3.5 text-sm font-bold text-stone-800 text-right">Rp {{ number_format($loan->sisa_pinjaman, 0, ',', '.') }}</td>
                                <td class="py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                        {{ $loan->status === 'lunas' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}
                                    ">
                                        {{ ucfirst($loan->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-stone-400 font-medium text-sm">
                                    Belum ada data peminjaman guru yang direkam.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pt-4 border-t border-stone-200">
                {{ $loans->links() }}
            </div>
        </div>
    </div>
</div>
