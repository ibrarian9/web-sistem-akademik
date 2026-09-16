<!-- Modal Detail Rincian Gaji Pegawai -->
@if ($showDetailModal && $selectedSalaryDetail)
    @php
        $sd = $selectedSalaryDetail;
        $penerimaanList = [
            ['label' => 'Gaji Pokok', 'nominal' => $sd->gaji_pokok, 'desc' => 'Honorarium Pokok Bulanan'],
        ];
        if ($sd->gaji_berkala > 0) {
            $penerimaanList[] = ['label' => 'Gaji Berkala', 'nominal' => $sd->gaji_berkala, 'desc' => 'Kenaikan Berkala Pegawai'];
        }
        if ($sd->honor_ekskul > 0) {
            $penerimaanList[] = ['label' => 'Honor Ekstrakurikuler', 'nominal' => $sd->honor_ekskul, 'desc' => ($sd->jumlah_ekskul ? $sd->jumlah_ekskul . 'x Pembinaan Ekskul' : 'Pembina Ekskul')];
        }
        if ($sd->insentif > 0) {
            $penerimaanList[] = ['label' => 'Insentif Kehadiran / Kinerja', 'nominal' => $sd->insentif, 'desc' => 'Tunjangan Disiplin & Mengajar'];
        }
        if ($sd->insentif_bpjs > 0) {
            $penerimaanList[] = ['label' => 'Insentif BPJSTK', 'nominal' => $sd->insentif_bpjs, 'desc' => 'Subsidi Iuran Ketenagakerjaan'];
        }
        if ($sd->insentif_maghrib_mengaji > 0) {
            $penerimaanList[] = ['label' => 'Insentif Maghrib Mengaji', 'nominal' => $sd->insentif_maghrib_mengaji, 'desc' => 'Program Maghrib Mengaji'];
        }
        if ($sd->tunjangan_jabatan > 0) {
            $penerimaanList[] = ['label' => 'Tunjangan Jabatan', 'nominal' => $sd->tunjangan_jabatan, 'desc' => 'Struktural / Amanah Khusus'];
        }
        if ($sd->tunjangan_pendidikan > 0) {
            $penerimaanList[] = ['label' => 'Tunjangan Pendidikan', 'nominal' => $sd->tunjangan_pendidikan, 'desc' => 'Kualifikasi Akademik'];
        }
        if ($sd->bonus > 0) {
            $penerimaanList[] = ['label' => 'Bonus / Tambahan Lain', 'nominal' => $sd->bonus, 'desc' => 'Apresiasi Khusus'];
        }

        $potonganList = [];
        if ($sd->potongan_peminjaman > 0) {
            $potonganList[] = ['label' => 'Potongan Cicilan Kasbon', 'nominal' => $sd->potongan_peminjaman, 'desc' => 'Pelunasan Pinjaman Guru'];
        }
        if ($sd->potongan_sosial > 0) {
            $potonganList[] = ['label' => 'Potongan Sosial', 'nominal' => $sd->potongan_sosial, 'desc' => 'Iuran Sosial Pegawai'];
        }
        if ($sd->potongan_bpjstk > 0) {
            $potonganList[] = ['label' => 'Iuran BPJS Ketenagakerjaan', 'nominal' => $sd->potongan_bpjstk, 'desc' => 'Iuran Kepesertaan'];
        }
        if ($sd->potongan_absensi > 0) {
            $potonganList[] = ['label' => 'Potongan Absensi / Keterlambatan', 'nominal' => $sd->potongan_absensi, 'desc' => 'Ketidakhadiran'];
        }
        if ($sd->potongan_lainnya > 0) {
            $potonganList[] = ['label' => 'Potongan Lain-lain', 'nominal' => $sd->potongan_lainnya, 'desc' => 'Koreksi Lainnya'];
        }
    @endphp

    <x-floating-card 
        :show="true" 
        :title="'Rincian Lengkap Gaji — ' . ($sd->guru->user->nama ?? '-')" 
        :subtitle="'Periode ' . $sd->bulan . ' ' . $sd->tahun . ' • Status: ' . strtoupper($sd->status)" 
        badge="RINCIAN PAYROLL" 
        badgeVariant="emerald" 
        icon="receipt" 
        maxWidth="max-w-5xl" 
        closeAction="closeDetailModal"
        zIndex="z-[99990]"
    >
        <div class="space-y-6 font-sans">
            <!-- Employee Summary Header -->
            <div class="bg-gradient-to-r from-emerald-900 to-emerald-800 text-white rounded-2xl p-5 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-xl bg-white/10 text-white font-black flex items-center justify-center text-base border border-white/20 shrink-0">
                        {{ strtoupper(substr($sd->guru?->user?->nama ?? ($sd->jabatan ?: 'G'), 0, 2)) }}
                    </div>
                    <div>
                        <div class="text-sm font-black text-white">{{ $sd->guru?->user?->nama ?? ($sd->jabatan ? 'Pegawai (' . $sd->jabatan . ')' : '-') }}</div>
                        <div class="text-[11px] text-emerald-100 font-semibold mt-0.5 flex items-center gap-1.5 flex-wrap">
                            <span>{{ $sd->jabatan ?: ($sd->guru?->jabatan ?? 'Guru / Pegawai') }}</span>
                            <span>&bull;</span>
                            <span>{{ $sd->sumber_dana ?: 'Yayasan' }}</span>
                            @if ($sd->guru?->niy || $sd->guru?->nip)
                                <span>&bull;</span>
                                <span class="font-mono">NIY: {{ $sd->guru?->niy ?? $sd->guru?->nip }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @if ($sd->status === 'dibayar')
                        <span class="px-3 py-1 bg-emerald-500 text-white rounded-full text-xs font-black shadow-2xs flex items-center gap-1">
                            <x-lucide-check-circle class="w-3.5 h-3.5" />
                            Dibayar ({{ $sd->tanggal_bayar ? \Carbon\Carbon::parse($sd->tanggal_bayar)->format('d M Y') : 'Selesai' }})
                        </span>
                    @else
                        <span class="px-3 py-1 bg-amber-500 text-white rounded-full text-xs font-black shadow-2xs flex items-center gap-1">
                            <x-lucide-clock class="w-3.5 h-3.5" />
                            Draft Penggajian
                        </span>
                    @endif
                </div>
            </div>

            <!-- Two-Column Breakdown Matrix -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Column A: Penerimaan (Bruto) -->
                <div class="bg-emerald-50/40 border border-emerald-200/80 rounded-2xl p-5 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-emerald-200/60">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-black text-xs">A</div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-emerald-950">Penerimaan & Tunjangan</h4>
                            </div>
                            <span class="text-xs font-black text-emerald-900 font-mono">Rp {{ number_format($sd->total_bruto, 0, ',', '.') }}</span>
                        </div>

                        <div class="space-y-2.5">
                            @foreach ($penerimaanList as $item)
                                <div class="flex items-center justify-between text-xs py-1 border-b border-emerald-100/50">
                                    <div>
                                        <span class="font-bold text-stone-800 block">{{ $item['label'] }}</span>
                                        <span class="text-[10px] text-stone-400">{{ $item['desc'] }}</span>
                                    </div>
                                    <span class="font-mono font-bold text-emerald-900">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-emerald-200/80 flex items-center justify-between">
                        <span class="text-xs font-extrabold text-emerald-900">Total Penghasilan (A)</span>
                        <span class="text-sm font-black text-emerald-950 font-mono">Rp {{ number_format($sd->total_bruto, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Column B: Potongan -->
                <div class="bg-rose-50/40 border border-rose-200/80 rounded-2xl p-5 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-rose-200/60">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center font-black text-xs">B</div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-rose-950">Potongan Gaji</h4>
                            </div>
                            <span class="text-xs font-black text-rose-900 font-mono">-Rp {{ number_format($sd->total_potongan, 0, ',', '.') }}</span>
                        </div>

                        <div class="space-y-2.5">
                            @forelse ($potonganList as $item)
                                <div class="flex items-center justify-between text-xs py-1 border-b border-rose-100/50">
                                    <div>
                                        <span class="font-bold text-stone-800 block">{{ $item['label'] }}</span>
                                        <span class="text-[10px] text-stone-400">{{ $item['desc'] }}</span>
                                    </div>
                                    <span class="font-mono font-bold text-rose-700">-Rp {{ number_format($item['nominal'], 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <div class="py-6 text-center text-xs text-stone-400 italic">
                                    Tidak ada potongan pada periode ini.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-rose-200/80 flex items-center justify-between">
                        <span class="text-xs font-extrabold text-rose-900">Total Potongan (B)</span>
                        <span class="text-sm font-black text-rose-950 font-mono">-Rp {{ number_format($sd->total_potongan, 0, ',', '.') }}</span>
                    </div>
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
                    @if ($sd->bukti_bayar)
                        <x-badge variant="emerald" size="xs">Terlampir</x-badge>
                    @elseif ($sd->status === 'dibayar')
                        <x-badge variant="stone" size="xs">Belum Diunggah</x-badge>
                    @endif
                </div>

                @if ($sd->bukti_bayar)
                    <div class="flex flex-col sm:flex-row items-center gap-4 p-3 bg-stone-50 rounded-xl border border-stone-200">
                        <a href="{{ asset('storage/' . $sd->bukti_bayar) }}" target="_blank" class="group relative block shrink-0">
                            <img src="{{ asset('storage/' . $sd->bukti_bayar) }}" alt="Bukti Transfer" class="w-20 h-20 object-cover rounded-xl border border-stone-300 group-hover:opacity-90 shadow-2xs" />
                            <div class="absolute inset-0 bg-black/30 rounded-xl opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                                <x-lucide-external-link class="w-5 h-5 text-white" />
                            </div>
                        </a>
                        <div class="flex-1 space-y-1">
                            <div class="text-xs font-bold text-stone-800">Bukti Pembayaran Tersimpan</div>
                            <div class="text-[11px] text-stone-500 font-mono">{{ basename($sd->bukti_bayar) }}</div>
                            <div class="pt-1 flex items-center gap-2">
                                <a href="{{ asset('storage/' . $sd->bukti_bayar) }}" target="_blank" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 hover:underline flex items-center gap-1">
                                    <x-lucide-eye class="w-3.5 h-3.5" />
                                    <span>Buka Ukuran Penuh</span>
                                </a>
                                @if (!auth()->user()->isSuperAdmin2())
                                    <span class="text-stone-300">&bull;</span>
                                    <button type="button" wire:click="deleteSalaryBuktiFoto({{ $sd->id }})" data-confirm="Hapus file foto bukti transfer/struk ini?" class="text-xs font-semibold text-rose-600 hover:text-rose-800 hover:underline flex items-center gap-1">
                                        <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                        <span>Hapus Foto</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if ($sd->status === 'dibayar' && !auth()->user()->isSuperAdmin2())
                    <div class="pt-2 border-t border-stone-100">
                        <label class="block text-[11px] font-bold text-stone-600 mb-1.5">
                            {{ $sd->bukti_bayar ? 'Ganti Foto Bukti Pembayaran:' : 'Unggah Foto Bukti Transfer / Struk:' }}
                        </label>
                        <div class="flex flex-col sm:flex-row items-center gap-3">
                            <input type="file" wire:model="detailBuktiFoto" accept="image/jpeg,image/png,image/jpg,image/webp" 
                                   class="text-xs text-stone-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer border border-stone-300 rounded-xl bg-white p-1" />
                            @if ($detailBuktiFoto)
                                <x-button variant="primary" size="xs" icon="upload" wire:click="updateSalaryBuktiFoto({{ $sd->id }})">
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

            <!-- Grand Total Take Home Pay Banner -->
            <div class="bg-emerald-950 text-white rounded-2xl p-5 shadow-sm border border-emerald-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-400 block">Take Home Pay Bersih (A - B)</span>
                    <span class="text-2xl sm:text-3xl font-black text-white font-mono mt-0.5 block">
                        Rp {{ number_format($sd->total_diterima, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-emerald-200/70 italic mt-0.5 block">
                        Terbilang: {{ $sd->terbilang }}
                    </span>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <x-button 
                        variant="secondary" 
                        size="md" 
                        icon="eye" 
                        wire:click="openPreview({{ $sd->id }})"
                    >
                        Pratinjau Slip PDF
                    </x-button>

                    <a 
                        href="{{ route('finance.gaji-guru.slip', ['id' => $sd->id, 'download' => 1]) }}" 
                        target="_blank" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition"
                    >
                        <x-lucide-download class="w-4 h-4" />
                        <span>Unduh PDF</span>
                    </a>
                </div>
            </div>
        </div>
    </x-floating-card>
@endif
