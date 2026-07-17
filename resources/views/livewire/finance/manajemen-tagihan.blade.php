<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Manajemen Tagihan Siswa</h2>
        <p class="text-sm text-stone-500">Buat, filter, dan pantau status tagihan operasional/SPP siswa.</p>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Bulk Release Invoice Form -->
        <div class="lg:col-span-1 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6 h-fit">
            <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider border-b border-stone-200 pb-2">Rilis Tagihan Kolektif</h3>
            
            <form wire:submit.prevent="createBulkTagihan" class="space-y-4">
                <!-- Kelas -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Pilih Kelas Target</label>
                    <select wire:model.live="kelas_id" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500">
                        <option value="">Pilih Kelas</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                        @endforeach
                    </select>
                    @error('kelas_id') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Jenis Tagihan -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Kategori Tagihan</label>
                    <select wire:model.live="jenis_tagihan_id" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500">
                        <option value="">Pilih Kategori</option>
                        @foreach ($jenisTagihans as $jt)
                            <option value="{{ $jt['id'] }}">{{ $jt['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('jenis_tagihan_id') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Bulan/Periode -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Bulan / Periode</label>
                    <input wire:model="bulan" type="text" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500" placeholder="Contoh: Juli, Agustus..." />
                    @error('bulan') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Nominal -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Nominal (Rp)</label>
                    <input wire:model="nominal" type="number" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500 text-right font-bold" />
                    @error('nominal') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Jatuh Tempo -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Tanggal Jatuh Tempo</label>
                    <input wire:model="jatuh_tempo" type="date" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500" />
                    @error('jatuh_tempo') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold transition duration-200 shadow-md shadow-green-600/10">
                        Rilis Tagihan Sekarang
                    </button>
                </div>
            </form>
        </div>

        <!-- Invoices List & Filters -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6">
            <!-- Filter Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-stone-50 p-4 border border-stone-200 rounded-2xl">
                <!-- Filter Kelas -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Filter Kelas</label>
                    <select wire:model.live="filterKelas" class="w-full px-2.5 py-2 bg-white border border-stone-300 rounded-lg text-stone-800 text-sm focus:ring-1 focus:ring-green-500">
                        <option value="">Semua Kelas</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Jenis -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Kategori</label>
                    <select wire:model.live="filterJenis" class="w-full px-2.5 py-2 bg-white border border-stone-300 rounded-lg text-stone-800 text-sm focus:ring-1 focus:ring-green-500">
                        <option value="">Semua Kategori</option>
                        @foreach ($jenisTagihans as $jt)
                            <option value="{{ $jt['id'] }}">{{ $jt['nama'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Status</label>
                    <select wire:model.live="filterStatus" class="w-full px-2.5 py-2 bg-white border border-stone-300 rounded-lg text-stone-800 text-sm focus:ring-1 focus:ring-green-500">
                        <option value="">Semua Status</option>
                        <option value="belum_bayar">Belum Bayar</option>
                        <option value="sebagian">Sebagian</option>
                        <option value="lunas">Lunas</option>
                    </select>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-stone-200">
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Siswa</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Kelas</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Kategori / Bulan</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Tagihan / Sisa</th>
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse ($tagihans as $tagihan)
                            <tr class="hover:bg-stone-50">
                                <td class="py-3 text-sm font-semibold text-stone-800">
                                    {{ $tagihan->siswa->user->nama ?? '-' }}
                                    <span class="text-xs text-stone-400 block font-normal">NIS: {{ $tagihan->siswa->nis }}</span>
                                </td>
                                <td class="py-3 text-sm text-stone-600">{{ $tagihan->siswa->kelas->nama_kelas ?? '-' }}</td>
                                <td class="py-3 text-sm text-stone-600">
                                    {{ $tagihan->jenisTagihan->nama ?? '-' }}
                                    <span class="text-xs text-stone-400 block">Bulan: {{ $tagihan->bulan ?: '-' }}</span>
                                </td>
                                <td class="py-3 text-sm text-right">
                                    <span class="font-bold text-stone-800 block">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</span>
                                    <span class="text-xs text-red-600 block">Sisa: Rp {{ number_format($tagihan->nominal - $tagihan->total_dibayar, 0, ',', '.') }}</span>
                                </td>
                                <td class="py-3 text-center">
                                    <x-status-badge :status="$tagihan->status" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-stone-400 font-medium text-sm">
                                    Tidak ada tagihan terdaftar yang cocok dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination links -->
            <div class="pt-4 border-t border-stone-200">
                {{ $tagihans->links() }}
            </div>
        </div>
    </div>
</div>
