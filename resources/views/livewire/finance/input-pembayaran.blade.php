<div class="space-y-6 font-sans">
    <!-- Navigation Tabs Menu (Top of Page) -->
    <x-finance.tagihan-nav-tabs active="kasir" />

    <!-- Header Title Bar -->
    <x-page-header 
        title="Input Pembayaran Siswa" 
        subtitle="Pilih dari daftar siswa yang memiliki tagihan aktif di bawah untuk memproses transaksi setoran kasir."
        badge="KASIR PEMBAYARAN SISWA"
        badgeVariant="emerald"
        icon="plus-circle"
    />

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Kasir & Pencatatan Pembayaran Siswa"
        :steps="[
            [
                'title' => '1. Cari & Pilih Siswa', 
                'desc' => 'Gunakan kolom pencarian nama/NIS atau filter kelas pada tabel di bawah, kemudian klik tombol Bayar Sekarang pada baris siswa yang bersangkutan.'
            ],
            [
                'title' => '2. Pilih Tagihan yang Dibayar', 
                'desc' => 'Centang tagihan (SPP bulanan, Uang Gedung, Seragam, dll.) yang ingin dilunasi. Anda dapat mencentang satu atau beberapa tagihan sekaligus.'
            ],
            [
                'title' => '3. Pilih Metode Pembayaran', 
                'desc' => 'Pilih metode bayar: Tunai, Transfer Bank, E-Wallet, Saldo Deposit Tabungan, atau Beasiswa. Masukkan nominal uang yang disetorkan.'
            ],
            [
                'title' => '4. Alokasi Kelebihan Setoran (Deposit)', 
                'desc' => 'Jika Siswa membayar lebih dari jumlah tagihan yang dicentang, selisih uang otomatis disimpan menjadi Saldo Deposit Tabungan untuk tagihan berikutnya.'
            ],
            [
                'title' => '5. Cetak Kuitansi & Resi Sah', 
                'desc' => 'Setelah transaksi berhasil disimpan, tombol Cetak Resi akan muncul untuk mencetak kuitansi resmi lengkap dengan rincian pelunasan, barcode/QR, dan stempel.'
            ],
            [
                'title' => '6. Pembatalan Transaksi Pembayaran', 
                'desc' => 'Jika terjadi salah input, transaksi pembayaran dapat dibatalkan atau dihapus melalui menu Rincian Tagihan Siswa di tabel Riwayat Pembayaran.'
            ]
        ]"
        notes="Pembayaran dengan metode Saldo Deposit akan langsung memotong saldo tabungan siswa. Pastikan saldo deposit siswa mencukupi sebelum memproses transaksi."
    />

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-600 text-white rounded-xl">
                    <x-lucide-check-circle class="w-5 h-5" />
                </div>
                <div>
                    <span class="text-xs font-bold text-emerald-900 block">{{ session('message') }}</span>
                    <span class="text-[11px] text-emerald-700">Setoran pembayaran telah berhasil dicatat ke dalam database keuangan.</span>
                </div>
            </div>

            @if ($lastPembayaranId)
                <x-button variant="primary" size="sm" icon="printer" href="{{ route('finance.cetak-resi', $lastPembayaranId) }}" target="_blank">
                    Cetak Resi Bukti Bayar
                </x-button>
            @endif
        </div>
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- MAIN TABLE: DAFTAR TUNGGAKAN SISWA -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="max-w-md w-full">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama siswa atau NIS..." />
            </div>
            
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-stone-600 uppercase tracking-wider shrink-0">Filter Kelas:</span>
                <select wire:model.live="filterKelas" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Kelas</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c['id'] }}">Kelas {{ $c['nama_kelas'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <x-table loadingTarget="search, filterKelas, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="min-w-[180px]">Siswa & Kelas</x-table.th>
                    <x-table.th class="w-44">Jenis Tagihan</x-table.th>
                    <x-table.th align="center" class="w-32">Periode</x-table.th>
                    <x-table.th align="right" class="w-40">Nominal Tagihan</x-table.th>
                    <x-table.th align="right" class="w-40">Sisa Tunggakan</x-table.th>
                    <x-table.th align="center" class="w-32">Status</x-table.th>
                    <x-table.th align="center" class="w-36">Aksi Kasir</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($activeTunggakan as $t)
                    @php
                        $sisa = floatval($t->nominal - $t->total_dibayar);
                        $isSelected = $selectedInvoiceInfo && $selectedInvoiceInfo['id'] === $t->id;
                    @endphp
                    <tr class="hover:bg-emerald-50/40 transition {{ $isSelected ? 'bg-emerald-50/80 font-bold' : '' }}">
                        <td class="p-3.5 border-r border-stone-200">
                            <span class="font-extrabold text-stone-900 text-xs block">{{ $t->siswa->user->nama ?? '-' }}</span>
                            <span class="text-[10px] text-stone-500 font-mono">NIS: {{ $t->siswa->nis ?? '-' }} | Kelas {{ $t->siswa->kelas->nama_kelas ?? '-' }}</span>
                        </td>
                        <td class="p-3.5 text-xs font-bold text-stone-900 border-r border-stone-200">
                            {{ $t->jenisTagihan->nama ?? '-' }}
                        </td>
                        <td class="p-3.5 text-center text-xs text-stone-600 font-semibold border-r border-stone-200">
                            {{ $t->bulan ?: '-' }}
                        </td>
                        <td class="p-3.5 text-right font-bold text-xs text-stone-800 border-r border-stone-200">
                            Rp {{ number_format($t->nominal, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-right font-black text-rose-700 text-xs border-r border-stone-200">
                            Rp {{ number_format($sisa, 0, ',', '.') }}
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($t->status === 'belum_bayar')
                                <x-badge variant="rose" size="xs" :dot="true">Belum Bayar</x-badge>
                            @else
                                <x-badge variant="amber" size="xs" :dot="true">Sebagian</x-badge>
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            @if(!auth()->user()->isSuperAdmin2())
                            <x-button variant="primary" size="xs" icon="plus" wire:click="pilihSiswaAndTagihan({{ $t->siswa_id }}, {{ $t->id }})">
                                Bayar Sekarang
                            </x-button>
                            @else
                                <span class="text-[10px] text-stone-400 font-mono italic">Lihat Saja</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="7" title="Tidak ada tagihan tertunggak" message="Seluruh siswa telah melunasi tagihan yang dirilis." />
                @endforelse
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $activeTunggakan->links() }}
        </div>
    </div>

    <!-- FLOATING CARD INPUT MODAL: KASIR PEMBAYARAN -->
    @if ($selectedInvoiceInfo)
        <x-floating-card 
            :show="true" 
            title="Form Setoran Kasir Pembayaran" 
            :subtitle="$selectedInvoiceInfo['siswa_nama'] . ' (NIS: ' . $selectedInvoiceInfo['siswa_nis'] . ' - Kelas ' . $selectedInvoiceInfo['siswa_kelas'] . ')'"
            badge="KASIR SETORAN"
            badgeVariant="emerald"
            icon="plus-circle"
            maxWidth="max-w-xl"
            closeAction="resetSelection"
        >
            <!-- Invoice Summary Metrics in Card -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                <div class="p-2.5 bg-stone-50 border border-stone-200 rounded-xl text-center">
                    <span class="text-[9px] font-bold text-stone-400 uppercase tracking-wider block">Jenis Tagihan</span>
                    <span class="text-xs font-bold text-stone-800 block mt-0.5">{{ $selectedInvoiceInfo['jenis'] }}</span>
                </div>
                <div class="p-2.5 bg-stone-50 border border-stone-200 rounded-xl text-center">
                    <span class="text-[9px] font-bold text-stone-400 uppercase tracking-wider block">Total Tagihan</span>
                    <span class="text-xs font-bold text-stone-800 block mt-0.5">Rp {{ number_format($selectedInvoiceInfo['nominal'], 0, ',', '.') }}</span>
                </div>
                <div class="p-2.5 bg-stone-50 border border-stone-200 rounded-xl text-center">
                    <span class="text-[9px] font-bold text-stone-400 uppercase tracking-wider block">Telah Dibayar</span>
                    <span class="text-xs font-bold text-emerald-700 block mt-0.5">Rp {{ number_format($selectedInvoiceInfo['total_dibayar'], 0, ',', '.') }}</span>
                </div>
                <div class="p-2.5 bg-rose-50 border border-rose-200 rounded-xl text-center">
                    <span class="text-[9px] font-bold text-rose-600 uppercase tracking-wider block">Sisa Tunggakan</span>
                    <span class="text-xs font-black text-rose-800 block mt-0.5">Rp {{ number_format($selectedInvoiceInfo['sisa'], 0, ',', '.') }}</span>
                </div>
            </div>

            @if ($siswaDeposit > 0)
                <div class="px-3.5 py-2 bg-emerald-50 border border-emerald-300 rounded-xl flex items-center justify-between text-xs shadow-2xs">
                    <div class="flex items-center gap-2">
                        <x-lucide-wallet class="w-4 h-4 text-emerald-600" />
                        <span class="text-stone-700 font-bold">Saldo Deposit Tersedia:</span>
                    </div>
                    <span class="font-black text-emerald-800">Rp {{ number_format($siswaDeposit, 0, ',', '.') }}</span>
                </div>
            @endif

            <!-- Form Inputs -->
            <form wire:submit.prevent="savePayment" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nominal Bayar -->
                    <x-input-currency 
                        label="Nominal Bayar (Rp)" 
                        name="nominal_dibayar" 
                        wire:model="nominal_dibayar" 
                        placeholder="Contoh: 350.000" 
                        hint="Kelebihan nominal akan otomatis masuk ke Deposit Siswa."
                        required 
                    />

                    <!-- Tanggal Bayar -->
                    <x-input 
                        type="date" 
                        label="Tanggal Bayar" 
                        name="tanggal_bayar" 
                        wire:model="tanggal_bayar" 
                        required 
                    />
                </div>

                <!-- Metode Bayar -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        @foreach (['Tunai', 'Transfer Bank', 'E-Wallet', 'Deposit'] as $method)
                            <button type="button" 
                                wire:click="setMetodeBayar('{{ $method }}')"
                                class="flex items-center justify-center gap-1.5 p-2.5 border rounded-xl text-xs select-none transition duration-150
                                {{ $metode_bayar === $method ? 'border-emerald-500 text-emerald-900 bg-emerald-50 font-black shadow-2xs ring-2 ring-emerald-500/20' : 'border-stone-300 text-stone-600 bg-white hover:bg-stone-50 hover:border-stone-400 font-bold' }}
                            ">
                                @if($method === 'Tunai') <x-lucide-banknote class="w-3.5 h-3.5 text-emerald-600" />
                                @elseif($method === 'Transfer Bank') <x-lucide-building-2 class="w-3.5 h-3.5 text-blue-600" />
                                @elseif($method === 'E-Wallet') <x-lucide-smartphone class="w-3.5 h-3.5 text-purple-600" />
                                @elseif($method === 'Deposit') <x-lucide-wallet class="w-3.5 h-3.5 text-amber-600" />
                                @endif
                                <span>{{ $method }}</span>
                            </button>
                        @endforeach
                    </div>
                    @error('metode_bayar') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Upload Bukti Pembayaran (Foto Struk / Bukti Transfer) -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                            Bukti Pembayaran (Struk / Transfer)
                        </label>
                        <span class="text-[10px] text-stone-400 font-semibold">Opsional • Maks. 2MB • Foto/Gambar</span>
                    </div>

                    @if ($bukti_foto)
                        <div class="p-3 bg-emerald-50/70 border border-emerald-300 rounded-xl flex items-center justify-between gap-3 shadow-2xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="{{ $bukti_foto->temporaryUrl() }}" alt="Preview Bukti" class="w-14 h-14 object-cover rounded-lg border border-emerald-200 shadow-2xs shrink-0" />
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-emerald-950 block truncate">{{ $bukti_foto->getClientOriginalName() }}</span>
                                    <span class="text-[11px] text-emerald-700 font-semibold">{{ number_format($bukti_foto->getSize() / 1024, 1) }} KB • Siap Disimpan</span>
                                </div>
                            </div>
                            <button type="button" wire:click="removeBuktiFoto" class="px-2.5 py-1.5 text-xs font-bold text-rose-700 bg-white hover:bg-rose-50 border border-rose-200 rounded-lg shadow-2xs transition shrink-0 cursor-pointer">
                                Ganti / Hapus
                            </button>
                        </div>
                    @else
                        <div class="relative border-2 border-dashed border-stone-300 hover:border-emerald-500 rounded-xl p-4 bg-stone-50/60 hover:bg-emerald-50/20 text-center transition group cursor-pointer">
                            <input 
                                type="file" 
                                wire:model="bukti_foto" 
                                accept="image/jpeg,image/png,image/jpg,image/webp" 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                            />
                            <div class="flex flex-col items-center justify-center gap-1.5 pointer-events-none">
                                <div class="w-9 h-9 rounded-xl bg-white border border-stone-200 group-hover:border-emerald-300 text-stone-500 group-hover:text-emerald-600 flex items-center justify-center shadow-2xs transition">
                                    <x-lucide-camera class="w-5 h-5" />
                                </div>
                                <div class="text-xs font-bold text-stone-700 group-hover:text-emerald-900">
                                    Klik atau seret foto bukti transfer / struk kasir
                                </div>
                                <div class="text-[10px] text-stone-400 font-medium">
                                    Hanya file gambar (JPG, JPEG, PNG, WEBP) maks 2MB
                                </div>
                            </div>

                            <div wire:loading wire:target="bukti_foto" class="absolute inset-0 bg-white/90 backdrop-blur-xs rounded-xl flex items-center justify-center z-20">
                                <div class="flex items-center gap-2 text-xs font-bold text-emerald-800">
                                    <svg class="animate-spin h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Mengunggah foto...</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    @error('bukti_foto') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-stone-200">
                    <x-button variant="secondary" size="md" wire:click="resetSelection">
                        Batal
                    </x-button>
                    <x-button variant="primary" size="md" type="submit" loadingTarget="savePayment">
                        Simpan Setoran Kasir
                    </x-button>
                </div>
            </form>
        </x-floating-card>
    @endif
</div>
