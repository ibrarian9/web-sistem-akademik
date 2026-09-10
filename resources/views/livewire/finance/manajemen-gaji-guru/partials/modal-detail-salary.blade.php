{{-- 4. Modal Detail Rincian Gaji Pegawai --}}
@if ($showDetailModal && $selectedSalaryDetail)
    <x-floating-card 
        :show="true" 
        title="Rincian Lengkap Honorarium Pegawai" 
        :subtitle="'Periode: ' . ($selectedSalaryDetail->bulan ?? '') . ' ' . ($selectedSalaryDetail->tahun ?? '') . ' — ' . ($selectedSalaryDetail->guru->user->nama ?? '-')" 
        badge="DETAIL RINCIAN GAJI" 
        badgeVariant="emerald" 
        icon="receipt" 
        maxWidth="max-w-5xl" 
        closeAction="closeDetailModal"
        zIndex="z-[99990]"
    >
        <div class="space-y-5 font-sans">
            <!-- Profile & Header Summary Card -->
            <div class="bg-gradient-to-r from-emerald-900 via-emerald-800 to-teal-900 p-5 rounded-2xl text-white shadow-md flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center text-xl font-black text-white shrink-0 shadow-inner">
                        {{ strtoupper(substr($selectedSalaryDetail->guru->user->nama ?? 'G', 0, 2)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base sm:text-lg font-black text-white leading-tight">
                                {{ $selectedSalaryDetail->guru->user->nama ?? '-' }}
                            </h3>
                            @if ($selectedSalaryDetail->status === 'dibayar')
                                <span class="px-2.5 py-0.5 bg-emerald-500/30 border border-emerald-400/50 text-emerald-200 rounded-full text-[10px] font-extrabold uppercase">
                                    ● Dibayar
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 bg-amber-500/30 border border-amber-400/50 text-amber-200 rounded-full text-[10px] font-extrabold uppercase">
                                    ● Draf
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-emerald-100 font-semibold mt-0.5">
                            {{ $selectedSalaryDetail->jabatan ?: ($selectedSalaryDetail->guru->jabatan ?? 'Guru / Pegawai') }} &bull; NIY: {{ $selectedSalaryDetail->guru->niy ?? ($selectedSalaryDetail->guru->nip ?? '-') }}
                        </p>
                        <p class="text-[11px] text-emerald-200/80 font-mono mt-0.5">
                            Periode: <strong class="text-white">{{ $selectedSalaryDetail->bulan }} {{ $selectedSalaryDetail->tahun }}</strong> &bull; Jam Kerja: {{ $selectedSalaryDetail->jam_kerja ?: '07.00-14.00' }} &bull; Sumber: {{ $selectedSalaryDetail->sumber_dana ?: 'Yayasan' }}
                        </p>
                    </div>
                </div>

                <a 
                    href="{{ route('finance.gaji-guru.detail', $selectedSalaryDetail->guru_id) }}" 
                    class="px-3.5 py-2 bg-white/15 hover:bg-white/25 text-white border border-white/30 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 shadow-xs"
                    title="Buka seluruh histori penggajian pegawai ini"
                >
                    <x-lucide-history class="w-4 h-4 text-emerald-300" />
                    <span>Riwayat Gaji Pegawai</span>
                </a>
            </div>

            <!-- 2-Column Earnings & Deductions Breakdown -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <!-- Column Left: Penerimaan (A) -->
                <div class="bg-emerald-50/50 border border-emerald-200/80 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-emerald-200 pb-2">
                        <span class="text-xs font-black text-emerald-950 uppercase tracking-wider flex items-center gap-1.5">
                            <x-lucide-plus-circle class="w-4 h-4 text-emerald-700" />
                            A. Komponen Penerimaan
                        </span>
                        <span class="text-[10px] font-extrabold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-md border border-emerald-300">
                            Total
                        </span>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-emerald-100">
                            <span class="text-stone-700 font-medium">1. Gaji Pokok</span>
                            <span class="font-extrabold text-stone-900">Rp {{ number_format($selectedSalaryDetail->gaji_pokok, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-emerald-100">
                            <span class="text-stone-700 font-medium">2. Gaji Berkala</span>
                            <span class="font-extrabold text-stone-900">Rp {{ number_format($selectedSalaryDetail->gaji_berkala, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-emerald-100">
                            <span class="text-stone-700 font-medium">3. Insentif Kehadiran & Tugas</span>
                            <span class="font-extrabold text-stone-900">Rp {{ number_format($selectedSalaryDetail->insentif, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-emerald-100">
                            <span class="text-stone-700 font-medium">4. Honor Ekskul ({{ $selectedSalaryDetail->jumlah_ekskul }}x pertemuan)</span>
                            <span class="font-extrabold text-cyan-800">Rp {{ number_format($selectedSalaryDetail->honor_ekskul, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-emerald-100">
                            <span class="text-stone-700 font-medium">5. Insentif BPJS Ketenagakerjaan</span>
                            <span class="font-extrabold text-indigo-800">Rp {{ number_format($selectedSalaryDetail->insentif_bpjs, 0, ',', '.') }}</span>
                        </div>
                        @if ($selectedSalaryDetail->insentif_maghrib_mengaji > 0)
                            <div class="flex justify-between py-1.5 border-b border-emerald-100">
                                <span class="text-stone-700 font-medium">6. Insentif Maghrib Mengaji</span>
                                <span class="font-extrabold text-stone-900">Rp {{ number_format($selectedSalaryDetail->insentif_maghrib_mengaji, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="pt-2 border-t-2 border-emerald-300 flex justify-between items-center text-xs font-black text-emerald-950">
                        <span>TOTAL PENERIMAAN (A)</span>
                        <span class="text-sm font-black text-emerald-900">
                            Rp {{ number_format($selectedSalaryDetail->total_bruto ?: ($selectedSalaryDetail->gaji_pokok + $selectedSalaryDetail->gaji_berkala + $selectedSalaryDetail->insentif + $selectedSalaryDetail->honor_ekskul + $selectedSalaryDetail->insentif_bpjs + $selectedSalaryDetail->insentif_maghrib_mengaji), 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Column Right: Potongan (B) -->
                <div class="bg-rose-50/50 border border-rose-200/80 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-rose-200 pb-2">
                        <span class="text-xs font-black text-rose-950 uppercase tracking-wider flex items-center gap-1.5">
                            <x-lucide-minus-circle class="w-4 h-4 text-rose-700" />
                            B. Komponen Potongan
                        </span>
                        <span class="text-[10px] font-extrabold text-rose-800 bg-rose-100 px-2 py-0.5 rounded-md border border-rose-300">
                            Deduksi
                        </span>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-rose-100">
                            <span class="text-stone-700 font-medium">1. Iuran Sosial Yayasan</span>
                            <span class="font-extrabold text-stone-900">Rp {{ number_format($selectedSalaryDetail->potongan_sosial, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-rose-100">
                            <div>
                                <span class="text-stone-700 font-medium block">2. Potongan Kasbon / Pinjaman</span>
                                @if ($selectedSalaryDetail->potongan_peminjaman > 0)
                                    <span class="text-[10px] text-rose-600 font-semibold block">Otomatis memotong cicilan kasbon</span>
                                @endif
                            </div>
                            <span class="font-extrabold text-rose-700">Rp {{ number_format($selectedSalaryDetail->potongan_peminjaman, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-rose-100">
                            <span class="text-stone-700 font-medium">3. Potongan BPJSTK Karyawan</span>
                            <span class="font-extrabold text-amber-800">Rp {{ number_format($selectedSalaryDetail->potongan_bpjstk, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-rose-100">
                            <span class="text-stone-700 font-medium">4. Potongan Lain-lain</span>
                            <span class="font-extrabold text-stone-900">Rp {{ number_format($selectedSalaryDetail->potongan_lainnya, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t-2 border-rose-300 flex justify-between items-center text-xs font-black text-rose-950">
                        <span>TOTAL POTONGAN (B)</span>
                        <span class="text-sm font-black text-rose-800">
                            Rp {{ number_format($selectedSalaryDetail->total_potongan ?: ($selectedSalaryDetail->potongan_sosial + $selectedSalaryDetail->potongan_peminjaman + $selectedSalaryDetail->potongan_bpjstk + $selectedSalaryDetail->potongan_lainnya), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Highlight Box Take Home Pay (A - B) -->
            <div class="p-4 bg-emerald-800 text-white rounded-2xl shadow-md space-y-1">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-300 block">
                            TOTAL DITERIMA / TAKE HOME PAY (THP)
                        </span>
                        <span class="text-xs text-emerald-100 font-medium">
                            Formula: Penerimaan Bersih = Total Komponen (A) - Total Komponen (B)
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                            Rp {{ number_format($selectedSalaryDetail->total_diterima, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                <div class="pt-2 border-t border-emerald-700/60 text-xs text-emerald-100 italic">
                    Terbilang: <strong>{{ \App\Http\Controllers\FinanceReportController::terbilang($selectedSalaryDetail->total_diterima) }} Rupiah</strong>
                </div>
            </div>

            <!-- Info Pembukuan Arus Kas Keuangan -->
            <div class="p-3.5 bg-stone-50 border border-stone-200 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 bg-white border border-stone-300 rounded-xl text-stone-700">
                        <x-lucide-landmark class="w-4 h-4 text-emerald-700" />
                    </div>
                    <div>
                        <div class="font-bold text-stone-900">
                            Status Kas: 
                            @if ($selectedSalaryDetail->status === 'dibayar')
                                <span class="text-emerald-700 font-black">Tercatat di Pengeluaran Kas Yayasan</span>
                                @if ($selectedSalaryDetail->pengeluaran_id)
                                    <span class="text-stone-500 font-mono font-normal">(Ref ID: #{{ $selectedSalaryDetail->pengeluaran_id }})</span>
                                @endif
                            @else
                                <span class="text-amber-700 font-black">Draf (Belum Dicairkan / Belum Mengurangi Kas)</span>
                            @endif
                        </div>
                        <div class="text-stone-500 text-[11px] mt-0.5">
                            Tanggal Pembayaran: {{ $selectedSalaryDetail->tanggal_bayar ? \Carbon\Carbon::parse($selectedSalaryDetail->tanggal_bayar)->translatedFormat('d F Y') : 'Belum dibayarkan' }} &bull; Metode: Kas Utama Yayasan
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    @if ($selectedSalaryDetail->status === 'draft')
                        <x-button variant="primary" size="sm" icon="credit-card" wire:click="openPayModal({{ $selectedSalaryDetail->id }})">
                            Bayar Sekarang
                        </x-button>
                    @else
                        <x-button variant="secondary" size="sm" icon="rotate-ccw" wire:click="revertToDraft({{ $selectedSalaryDetail->id }})" data-confirm="Kembalikan status gaji ini ke Draf?">
                            Batal Bayar
                        </x-button>
                    @endif
                </div>
            </div>

            <!-- Bukti Pembayaran / Struk / TF Section -->
            <div class="p-4 bg-white border border-stone-200 rounded-2xl space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-800">
                            <x-lucide-camera class="w-4 h-4" />
                        </span>
                        <span class="text-xs font-black text-stone-800 uppercase tracking-wider">Foto Bukti Transfer / Struk Pembayaran</span>
                    </div>
                    @if ($selectedSalaryDetail->bukti_bayar)
                        <x-badge variant="emerald" size="xs">Terlampir</x-badge>
                    @elseif ($selectedSalaryDetail->status === 'dibayar')
                        <x-badge variant="stone" size="xs">Belum Diunggah</x-badge>
                    @endif
                </div>

                @if ($selectedSalaryDetail->bukti_bayar)
                    <div class="flex flex-col sm:flex-row items-center gap-4 p-3 bg-stone-50 rounded-xl border border-stone-200">
                        <a href="{{ asset('storage/' . $selectedSalaryDetail->bukti_bayar) }}" target="_blank" class="group relative block shrink-0">
                            <img src="{{ asset('storage/' . $selectedSalaryDetail->bukti_bayar) }}" alt="Bukti Transfer" class="w-20 h-20 object-cover rounded-xl border border-stone-300 group-hover:opacity-90 shadow-2xs" />
                            <div class="absolute inset-0 bg-black/30 rounded-xl opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                                <x-lucide-external-link class="w-5 h-5 text-white" />
                            </div>
                        </a>
                        <div class="flex-1 space-y-1">
                            <div class="text-xs font-bold text-stone-800">Bukti Pembayaran Tersimpan</div>
                            <div class="text-[11px] text-stone-500 font-mono">{{ basename($selectedSalaryDetail->bukti_bayar) }}</div>
                            <div class="pt-1 flex items-center gap-2">
                                <a href="{{ asset('storage/' . $selectedSalaryDetail->bukti_bayar) }}" target="_blank" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 hover:underline flex items-center gap-1">
                                    <x-lucide-eye class="w-3.5 h-3.5" />
                                    <span>Buka Ukuran Penuh</span>
                                </a>
                                @if (!auth()->user()->isSuperAdmin2())
                                    <span class="text-stone-300">&bull;</span>
                                    <button type="button" wire:click="deleteSalaryBuktiFoto({{ $selectedSalaryDetail->id }})" data-confirm="Hapus file foto bukti transfer/struk ini?" class="text-xs font-semibold text-rose-600 hover:text-rose-800 hover:underline flex items-center gap-1 cursor-pointer">
                                        <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                        <span>Hapus Foto</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if ($selectedSalaryDetail->status === 'dibayar' && !auth()->user()->isSuperAdmin2())
                    <div class="pt-2 border-t border-stone-100">
                        <label class="block text-[11px] font-bold text-stone-600 mb-1.5">
                            {{ $selectedSalaryDetail->bukti_bayar ? 'Ganti Foto Bukti Pembayaran:' : 'Unggah Foto Bukti Transfer / Struk:' }}
                        </label>
                        <div class="flex flex-col sm:flex-row items-center gap-3">
                            <input type="file" wire:model="detailBuktiFoto" accept="image/jpeg,image/png,image/jpg,image/webp" 
                                   class="text-xs text-stone-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer border border-stone-300 rounded-xl bg-white p-1" />
                            @if ($detailBuktiFoto)
                                <x-button variant="primary" size="xs" icon="upload" wire:click="updateSalaryBuktiFoto({{ $selectedSalaryDetail->id }})">
                                    Simpan Foto
                                </x-button>
                            @endif
                        </div>
                        <div wire:loading wire:target="detailBuktiFoto" class="text-xs text-emerald-600 font-semibold mt-1">
                            Mengunggah gambar...
                        </div>
                        @error('detailBuktiFoto') <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-stone-200">
                <div class="flex items-center gap-2">
                    @if(!auth()->user()->isSuperAdmin2())
                    <x-button variant="secondary" size="md" icon="edit" wire:click="openEditModal({{ $selectedSalaryDetail->id }})">
                        Ubah Rincian
                    </x-button>
                    @endif
                    
                    <x-button variant="secondary" size="md" icon="eye" wire:click="openPreview({{ $selectedSalaryDetail->id }})">
                        Pratinjau Slip
                    </x-button>

                    <a 
                        href="{{ route('finance.gaji-guru.slip', ['id' => $selectedSalaryDetail->id, 'download' => 1]) }}" 
                        target="_blank" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold bg-stone-100 hover:bg-stone-200 text-stone-800 border border-stone-300 transition shadow-2xs"
                    >
                        <x-lucide-download class="w-4 h-4 text-stone-700" />
                        <span>Unduh PDF</span>
                    </a>
                </div>

                <x-button variant="secondary" size="md" wire:click="closeDetailModal">
                    Tutup
                </x-button>
            </div>
        </div>
    </x-floating-card>
@endif
