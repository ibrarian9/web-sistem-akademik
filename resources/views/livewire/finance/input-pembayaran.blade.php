<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Input Pembayaran Siswa</h2>
            <p class="text-sm text-stone-500">Pilih dari daftar siswa yang menunggak di bawah untuk langsung mengisi setoran pembayaran.</p>
        </div>
    </div>

    <!-- Guidance Card -->
    <div x-data="{ openGuide: true }" class="bg-emerald-50/80 border border-emerald-200/80 rounded-2xl p-4 transition-all shadow-sm">
        <div class="flex items-center justify-between cursor-pointer" @click="openGuide = !openGuide">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-sm">
                    <x-lucide-info class="w-5 h-5" />
                </div>
                <div>
                    <h4 class="text-xs font-bold text-emerald-950 uppercase tracking-wider">Petunjuk Kasir &amp; Input Pembayaran</h4>
                    <p class="text-xs text-emerald-800">Cari siswa menunggak, pilih tagihan, tentukan metode pembayaran, dan cetak resi.</p>
                </div>
            </div>
            <button class="text-emerald-700 hover:text-emerald-900 text-xs font-semibold flex items-center gap-1">
                <span x-text="openGuide ? 'Sembunyikan' : 'Tampilkan'"></span>
                <x-lucide-chevron-down class="w-4 h-4 transition-transform" ::class="openGuide ? 'rotate-180' : ''" />
            </button>
        </div>
        <div x-show="openGuide" class="mt-3 pt-3 border-t border-emerald-200/60 grid grid-cols-1 md:grid-cols-3 gap-3 text-xs text-emerald-900">
            <div class="flex items-start gap-2 bg-white/70 p-2.5 rounded-xl border border-emerald-100">
                <x-lucide-user-check class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span><strong>Langkah 1:</strong> Pilih siswa dari tabel daftar penunggak di sebelah kanan untuk memicu pengisian otomatis.</span>
            </div>
            <div class="flex items-start gap-2 bg-white/70 p-2.5 rounded-xl border border-emerald-100">
                <x-lucide-credit-card class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span><strong>Langkah 2:</strong> Pilih metode pembayaran (Tunai, Bank, E-Wallet) dan masukkan nominal setoran.</span>
            </div>
            <div class="flex items-start gap-2 bg-white/70 p-2.5 rounded-xl border border-emerald-100">
                <x-lucide-printer class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span><strong>Langkah 3:</strong> Setelah simpan, klik tombol "Cetak Resi Bukti Bayar" yang disertai tanda tangan Staf Keuangan.</span>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-600 text-white rounded-xl">
                    <x-lucide-check-circle class="w-5 h-5" />
                </div>
                <div>
                    <span class="text-sm font-bold text-emerald-900 block">{{ session('message') }}</span>
                    <span class="text-xs text-emerald-700">Setoran pembayaran telah berhasil dicatat ke dalam database keuangan.</span>
                </div>
            </div>

            @if ($lastPembayaranId)
                <a href="{{ route('finance.cetak-resi', $lastPembayaranId) }}" target="_blank" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
                    <x-lucide-printer class="w-4 h-4" />
                    <span>Cetak Resi Bukti Bayar</span>
                </a>
            @endif
        </div>
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- ACTIVE FORM CARD (Top Section) -->
    @if ($selectedInvoiceInfo)
        <div class="bg-white border-2 border-emerald-500/40 rounded-2xl p-6 shadow-md space-y-6 animate-fade-in">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-stone-200 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center font-bold text-emerald-700">
                        <x-lucide-user class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-stone-900">{{ $selectedInvoiceInfo['siswa_nama'] }}</h3>
                        <span class="text-xs text-stone-500 font-medium">NIS: {{ $selectedInvoiceInfo['siswa_nis'] }} | Kelas: {{ $selectedInvoiceInfo['siswa_kelas'] }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    @if ($siswaDeposit > 0)
                        <div class="px-3 py-1.5 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs">
                            <x-lucide-wallet class="w-4 h-4 text-emerald-600" />
                            <span class="text-stone-600 font-semibold">Deposit:</span>
                            <span class="font-black text-emerald-800">Rp {{ number_format($siswaDeposit, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <button type="button" wire:click="resetSelection" class="px-3 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-600 rounded-xl text-xs font-semibold transition flex items-center gap-1">
                        <x-lucide-x class="w-3.5 h-3.5" />
                        <span>Batal / Ganti Siswa</span>
                    </button>
                </div>
            </div>

            <!-- Invoice Summary Metrics -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl text-center">
                    <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider block">Jenis Tagihan</span>
                    <span class="text-xs font-bold text-stone-800 block mt-0.5">{{ $selectedInvoiceInfo['jenis'] }} ({{ $selectedInvoiceInfo['periode'] }})</span>
                </div>
                <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl text-center">
                    <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider block">Total Tagihan</span>
                    <span class="text-xs font-bold text-stone-800 block mt-0.5">Rp {{ number_format($selectedInvoiceInfo['nominal'], 0, ',', '.') }}</span>
                </div>
                <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl text-center">
                    <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider block">Telah Dibayar</span>
                    <span class="text-xs font-bold text-green-700 block mt-0.5">Rp {{ number_format($selectedInvoiceInfo['total_dibayar'], 0, ',', '.') }}</span>
                </div>
                <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-center">
                    <span class="text-[11px] font-bold text-red-600 uppercase tracking-wider block">Sisa Tunggakan</span>
                    <span class="text-sm font-black text-red-700 block mt-0.5">Rp {{ number_format($selectedInvoiceInfo['sisa'], 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Form Inputs -->
            <form wire:submit.prevent="savePayment" class="space-y-4 pt-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nominal Bayar -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Nominal Bayar (Rp)</label>
                        <input wire:model="nominal_dibayar" type="number" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 font-bold text-right text-emerald-700" />
                        <span class="text-[11px] text-stone-400 block">Jika pembayaran melebihi sisa tunggakan, kelebihan akan masuk otomatis ke Saldo Deposit Siswa.</span>
                        @error('nominal_dibayar') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tanggal Bayar -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Tanggal Bayar</label>
                        <input wire:model="tanggal_bayar" type="date" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500" />
                        @error('tanggal_bayar') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Metode Bayar -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach (['Tunai', 'Transfer Bank', 'E-Wallet', 'Deposit'] as $method)
                            <button type="button" 
                                wire:click="setMetodeBayar('{{ $method }}')"
                                class="flex items-center justify-center gap-2 p-3 border rounded-xl text-xs font-semibold select-none transition duration-150
                                {{ $metode_bayar === $method ? 'border-emerald-500 text-emerald-700 bg-emerald-50 font-bold shadow-sm ring-2 ring-emerald-500/20' : 'border-stone-300 text-stone-600 bg-stone-50 hover:bg-stone-100 hover:border-stone-400' }}
                            ">
                                @if($method === 'Tunai') <x-lucide-banknote class="w-4 h-4 text-emerald-600" />
                                @elseif($method === 'Transfer Bank') <x-lucide-building-2 class="w-4 h-4 text-blue-600" />
                                @elseif($method === 'E-Wallet') <x-lucide-smartphone class="w-4 h-4 text-purple-600" />
                                @elseif($method === 'Deposit') <x-lucide-wallet class="w-4 h-4 text-amber-600" />
                                @endif
                                <span>{{ $method }}</span>
                            </button>
                        @endforeach
                    </div>
                    @error('metode_bayar') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-stone-200">
                    <button type="button" wire:click="resetSelection" class="py-2.5 px-5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-sm font-bold transition">
                        Batal
                    </button>
                    <button type="submit" class="py-2.5 px-8 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition duration-200 shadow-md shadow-emerald-600/10 flex items-center gap-2">
                        <x-lucide-check-circle class="w-4 h-4" />
                        <span>Simpan Setoran Pembayaran</span>
                    </button>
                </div>
            </form>
        </div>
    @else
        <!-- GUIDANCE BANNER -->
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-2xl p-5 flex items-center gap-4">
            <div class="p-3 bg-emerald-600 text-white rounded-xl shadow-sm">
                <x-lucide-info class="w-6 h-6" />
            </div>
            <div>
                <h3 class="text-sm font-bold text-emerald-950">Cara Pengisian Pembayaran:</h3>
                <p class="text-xs text-emerald-800">
                    Pilih siswa pada tabel <strong>Daftar Siswa Menunggak</strong> di bawah dengan menekan tombol <span class="font-bold text-emerald-900 bg-emerald-200/60 px-1.5 py-0.5 rounded">Bayar Sekarang</span> untuk mengisi setoran pembayaran.
                </p>
            </div>
        </div>
    @endif

    <!-- MAIN TABLE: DAFTAR TUNGGAKAN SISWA -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-stone-200 pb-4">
            <div>
                <h3 class="text-lg font-bold text-stone-800">Daftar Siswa Menunggak / Memiliki Tagihan Aktif</h3>
                <p class="text-xs text-stone-500">Klik "Bayar Sekarang" pada siswa di bawah untuk memproses pembayaran.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <!-- Filter Kelas -->
                <select wire:model.live="filterKelas" class="w-full sm:w-44 px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Kelas</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                    @endforeach
                </select>

                <!-- Search NIS/Nama -->
                <div class="w-full sm:w-64 relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama siswa / NIS..." 
                        class="w-full pl-9 pr-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-xs focus:ring-2 focus:ring-emerald-500" />
                    <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" />
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-stone-200 text-xs font-semibold text-stone-500 uppercase tracking-wider">
                        <th class="pb-3">Siswa &amp; Kelas</th>
                        <th class="pb-3">Jenis Tagihan</th>
                        <th class="pb-3 text-center">Periode</th>
                        <th class="pb-3 text-right">Nominal Tagihan</th>
                        <th class="pb-3 text-right">Sisa Tunggakan</th>
                        <th class="pb-3 text-center">Status</th>
                        <th class="pb-3 text-center">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-sm">
                    @forelse ($activeTunggakan as $t)
                        @php
                            $sisa = floatval($t->nominal - $t->total_dibayar);
                            $isSelected = $selectedInvoiceInfo && $selectedInvoiceInfo['id'] === $t->id;
                        @endphp
                        <tr class="hover:bg-stone-50 transition {{ $isSelected ? 'bg-emerald-50/60 font-semibold' : '' }}">
                            <td class="py-3.5">
                                <span class="font-bold text-stone-800 block">{{ $t->siswa->user->nama ?? '-' }}</span>
                                <span class="text-xs text-stone-500">NIS: {{ $t->siswa->nis ?? '-' }} | Kelas {{ $t->siswa->kelas->nama_kelas ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 font-medium text-stone-700">
                                {{ $t->jenisTagihan->nama ?? '-' }}
                            </td>
                            <td class="py-3.5 text-center text-xs text-stone-500 font-semibold">
                                {{ $t->bulan ?: '-' }}
                            </td>
                            <td class="py-3.5 text-right font-semibold text-stone-700">
                                Rp {{ number_format($t->nominal, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 text-right font-black text-red-600">
                                Rp {{ number_format($sisa, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 text-center">
                                @if ($t->status === 'belum_bayar')
                                    <span class="px-2.5 py-1 bg-red-50 text-red-700 border border-red-200 rounded-lg text-xs font-bold">Belum Bayar</span>
                                @else
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg text-xs font-bold">Sebagian</span>
                                @endif
                            </td>
                            <td class="py-3.5 text-center">
                                <button type="button" wire:click="pilihSiswaAndTagihan({{ $t->siswa_id }}, {{ $t->id }})" 
                                    class="py-1.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition duration-150 flex items-center gap-1 mx-auto shadow-sm">
                                    <x-lucide-arrow-up-right class="w-3.5 h-3.5" />
                                    <span>Bayar Sekarang</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-stone-400 font-medium text-sm">
                                Tidak ada siswa yang menunggak saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-2 border-t border-stone-200">
            {{ $activeTunggakan->links() }}
        </div>
    </div>
</div>
