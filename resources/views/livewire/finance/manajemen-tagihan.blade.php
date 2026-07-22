<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Manajemen Tagihan Siswa</h2>
        <p class="text-sm text-stone-500">Buat, filter, dan pantau status tagihan operasional/SPP siswa (kolektif maupun perorangan).</p>
    </div>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Manajemen Tagihan & Otomatisasi SPP"
        :steps="[
            ['title' => 'Otomatis SPP 1-Klik', 'desc' => 'Gunakan tab Otomatis SPP untuk membuat tagihan SPP bulanan serentak bagi seluruh siswa aktif.'],
            ['title' => 'Tagihan Khusus', 'desc' => 'Rilis tagihan perorangan atau per kelas untuk pembayaran gedung, seragam, atau uang buku.'],
            ['title' => 'Proteksi Duplikasi', 'desc' => 'Sistem secara otomatis mencegah adanya tagihan SPP ganda untuk siswa pada bulan yang sama.']
        ]"
        notes="Jalankan command php artisan tagihan:generate-spp via Cron Job setiap tanggal 1 bulan berjalan."
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Release Invoice Form -->
        <div class="lg:col-span-1 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6 h-fit">
            <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider">Rilis Tagihan</h3>
                <!-- Mode Switcher -->
                <div class="flex bg-stone-100 p-1 rounded-xl text-xs font-bold gap-1">
                    <button type="button" wire:click="$set('modeTagihan', 'perorangan')" class="px-2.5 py-1 rounded-lg transition {{ $modeTagihan === 'perorangan' ? 'bg-white text-stone-800 shadow-sm' : 'text-stone-500 hover:text-stone-800' }}">
                        Perorangan
                    </button>
                    <button type="button" wire:click="$set('modeTagihan', 'otomatis')" class="px-2.5 py-1 rounded-lg transition {{ $modeTagihan === 'otomatis' ? 'bg-emerald-600 text-white shadow-sm' : 'text-stone-500 hover:text-stone-800' }}">
                        Otomatis SPP
                    </button>
                </div>
            </div>

            @if ($modeTagihan === 'perorangan')
                <!-- FORM PERORANGAN -->
                <form wire:submit.prevent="createSingleTagihan" class="space-y-4">
                    <!-- Siswa -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Pilih Siswa Target</label>
                        <select wire:model.live="single_siswa_id" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500">
                            <option value="">Pilih Siswa</option>
                            @foreach ($allStudents as $s)
                                <option value="{{ $s->id }}">{{ $s->user->nama ?? '-' }} (NIS: {{ $s->nis }} - Kelas {{ $s->kelas->nama_kelas ?? '-' }})</option>
                            @endforeach
                        </select>
                        @error('single_siswa_id') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
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
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Bulan / Periode Tagihan</label>
                        <select wire:model="bulan" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500">
                            @foreach ($bulanOptions as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
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
                        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold transition duration-200 shadow-md shadow-blue-600/10">
                            Rilis Tagihan Perorangan
                        </button>
                    </div>
                </form>
            @elseif ($modeTagihan === 'otomatis')
                <!-- FORM OTOMATIS SPP BULANAN -->
                <form wire:submit.prevent="generateAutoSppBulanan" class="space-y-4">
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-1">
                        <span class="text-xs font-bold text-emerald-900 block flex items-center gap-1.5">
                            <x-lucide-zap class="w-4 h-4 text-emerald-600" />
                            1-Klik Tagihan SPP Massal
                        </span>
                        <p class="text-[11px] text-emerald-700 leading-relaxed">
                            Sistem akan membuat tagihan SPP untuk seluruh siswa aktif tanpa risiko duplikasi.
                        </p>
                    </div>

                    <!-- Bulan / Periode SPP -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Pilih Bulan SPP</label>
                        <select wire:model="autoBulan" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500">
                            @foreach (['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'] as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                        @error('autoBulan') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Nominal SPP -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Nominal SPP (Rp)</label>
                        <input wire:model="autoNominal" type="number" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm font-bold text-right focus:ring-2 focus:ring-emerald-500" />
                        @error('autoNominal') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jatuh Tempo -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Jatuh Tempo</label>
                        <input wire:model="autoJatuhTempo" type="date" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500" />
                        @error('autoJatuhTempo') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2">
                            <x-lucide-play class="w-4 h-4 fill-current" />
                            Generate SPP Bulanan
                        </button>
                    </div>

                    <div class="pt-2 border-t border-stone-100 text-[10px] text-stone-500">
                        <strong>Perintah Terminal / Server Cron:</strong><br>
                        <code class="text-emerald-700 bg-stone-100 px-1 py-0.5 rounded">php artisan tagihan:generate-spp</code>
                    </div>
                </form>
            @endif
        </div>

        <!-- Invoices List & Filters -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6">
            <!-- Filter & Search Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 bg-stone-50 p-4 border border-stone-200 rounded-2xl">
                <!-- Search Student -->
                <div class="space-y-1 sm:col-span-1">
                    <label class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Cari Nama/NIS</label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari siswa..." class="w-full pl-8 pr-2.5 py-2 bg-white border border-stone-300 rounded-lg text-stone-800 text-xs focus:ring-1 focus:ring-green-500" />
                        <x-lucide-search class="w-3.5 h-3.5 text-stone-400 absolute left-2.5 top-2.5" />
                    </div>
                </div>

                <!-- Filter Kelas -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Filter Kelas</label>
                    <select wire:model.live="filterKelas" class="w-full px-2.5 py-2 bg-white border border-stone-300 rounded-lg text-stone-800 text-xs focus:ring-1 focus:ring-green-500">
                        <option value="">Semua Kelas</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Jenis -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Kategori</label>
                    <select wire:model.live="filterJenis" class="w-full px-2.5 py-2 bg-white border border-stone-300 rounded-lg text-stone-800 text-xs focus:ring-1 focus:ring-green-500">
                        <option value="">Semua Kategori</option>
                        @foreach ($jenisTagihans as $jt)
                            <option value="{{ $jt['id'] }}">{{ $jt['nama'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Status</label>
                    <select wire:model.live="filterStatus" class="w-full px-2.5 py-2 bg-white border border-stone-300 rounded-lg text-stone-800 text-xs focus:ring-1 focus:ring-green-500">
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
                            <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Aksi</th>
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
                                <td class="py-3 text-center">
                                    @if ($tagihan->total_dibayar == 0)
                                        <button type="button" wire:click="deleteTagihan({{ $tagihan->id }})" wire:confirm="Hapus tagihan ini?"
                                            class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition inline-flex items-center gap-1 text-xs font-semibold" title="Hapus Tagihan">
                                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                            <span>Hapus</span>
                                        </button>
                                    @else
                                        <span class="text-xs text-stone-400 font-medium">Ada Pembayaran</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-stone-400 font-medium text-sm">
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

