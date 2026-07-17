<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Input Pembayaran Siswa</h2>
        <p class="text-sm text-stone-500">Pilih siswa, tagihan aktif, lalu rekam bukti setoran cicilan atau pelunasan.</p>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Student Selection Panel -->
        <div class="lg:col-span-1 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4 h-fit">
            <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider border-b border-stone-200 pb-2">Pilih Penerima</h3>
            
            <!-- Kelas -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Kelas</label>
                <select wire:model.live="kelas_id" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500">
                    <option value="">Pilih Kelas</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Siswa -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Nama Siswa</label>
                <select wire:model.live="siswa_id" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500" {{ empty($students) ? 'disabled' : '' }}>
                    <option value="">Pilih Siswa</option>
                    @foreach ($students as $s)
                        <option value="{{ $s['id'] }}">{{ $s['user']['nama'] ?? '-' }} (NIS: {{ $s['nis'] }})</option>
                    @endforeach
                </select>
                @error('siswa_id') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Tagihan Aktif -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Tagihan Aktif</label>
                <select wire:model.live="tagihan_id" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500" {{ empty($unpaidInvoices) ? 'disabled' : '' }}>
                    <option value="">Pilih Tagihan</option>
                    @foreach ($unpaidInvoices as $ui)
                        <option value="{{ $ui['id'] }}">{{ $ui['jenis_tagihan']['nama'] }} (Periode: {{ $ui['bulan'] }})</option>
                    @endforeach
                </select>
                @error('tagihan_id') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Payment Details & Form Panel -->
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl p-6 shadow-sm">
            @if ($selectedInvoiceInfo)
                <div class="space-y-6">
                    <!-- Invoice Summary Card -->
                    <div class="p-5 bg-stone-50 border border-stone-200 rounded-2xl space-y-4">
                        <div class="flex justify-between items-center border-b border-stone-200 pb-2">
                            <div>
                                <h4 class="text-sm font-bold text-stone-800">Detail Tagihan Terpilih</h4>
                                <span class="text-xs text-stone-500 font-semibold block uppercase tracking-wider">Kategori: {{ $selectedInvoiceInfo['jenis'] }} | Periode: {{ $selectedInvoiceInfo['periode'] }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="p-3 bg-white border border-stone-200 rounded-xl">
                                <span class="text-xs text-stone-400 font-semibold block uppercase tracking-wider">Total Tagihan</span>
                                <span class="text-sm font-bold text-stone-800 block mt-0.5">Rp {{ number_format($selectedInvoiceInfo['nominal'], 0, ',', '.') }}</span>
                            </div>
                            <div class="p-3 bg-white border border-stone-200 rounded-xl">
                                <span class="text-xs text-stone-400 font-semibold block uppercase tracking-wider">Telah Dibayar</span>
                                <span class="text-sm font-bold text-green-700 block mt-0.5">Rp {{ number_format($selectedInvoiceInfo['total_dibayar'], 0, ',', '.') }}</span>
                            </div>
                            <div class="p-3 bg-white border border-stone-200 rounded-xl">
                                <span class="text-xs text-stone-400 font-semibold block uppercase tracking-wider">Sisa Tunggakan</span>
                                <span class="text-sm font-bold text-red-600 block mt-0.5">Rp {{ number_format($selectedInvoiceInfo['sisa'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Record Form -->
                    <form wire:submit.prevent="savePayment" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Nominal Bayar -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Nominal Bayar (Rp)</label>
                                <input wire:model="nominal_dibayar" type="number" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500 font-bold text-right" />
                                @error('nominal_dibayar') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tanggal Bayar -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Tanggal Bayar</label>
                                <input wire:model="tanggal_bayar" type="date" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500" />
                                @error('tanggal_bayar') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Metode Bayar -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Metode Pembayaran</label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach (['Tunai', 'Transfer Bank', 'E-Wallet'] as $method)
                                    <label class="flex items-center justify-center gap-2 p-3 bg-stone-50 border rounded-xl cursor-pointer text-sm font-semibold select-none transition duration-150
                                        {{ $metode_bayar === $method ? 'border-green-500 text-green-700 bg-green-50' : 'border-stone-300 text-stone-500 hover:border-stone-400' }}
                                    ">
                                        <input type="radio" wire:model="metode_bayar" value="{{ $method }}" class="hidden" />
                                        <span>{{ $method }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('metode_bayar') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end pt-4 border-t border-stone-200">
                            <button type="submit" class="py-3 px-8 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold transition duration-200 shadow-md shadow-green-600/10">
                                Rekam Setoran Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="h-[250px] flex flex-col items-center justify-center text-stone-400 font-medium text-sm">
                    <x-lucide-credit-card class="w-10 h-10 text-stone-300 mb-3" />
                    <span>Pilih siswa dan tagihan aktif di sebelah kiri untuk merekam pembayaran.</span>
                </div>
            @endif
        </div>
    </div>
</div>
