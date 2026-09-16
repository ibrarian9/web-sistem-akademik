<!-- FORM BUILDER CARD -->
<div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b border-stone-200 pb-4">
        <div>
            <h3 class="text-xs font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-950 text-xs flex items-center justify-center font-black">
                    <x-lucide-file-text class="w-3.5 h-3.5 text-emerald-900" />
                </span>
                <span>Form Isian {{ str_replace('_', ' ', strtoupper($jenis_surat)) }}</span>
            </h3>
            <p class="text-xs text-stone-500 font-semibold mt-0.5">Pilih nama penerima untuk pengisian data otomatis atau ketik manual.</p>
        </div>

        <!-- Auto-populate Selector Dropdown -->
        <div class="w-full md:w-80">
            @if($jenis_surat === 'pengalaman_kerja')
                <label class="block text-[11px] font-bold text-stone-600 uppercase mb-1">Pilih Guru / Karyawan (Auto-Fill)</label>
                <select wire:model.live="selected_guru_id" class="w-full bg-white border border-stone-300 text-stone-900 rounded-xl px-3.5 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">-- Pilih Guru / Karyawan --</option>
                    @foreach($gurus as $g)
                        <option value="{{ $g->id }}">{{ $g->user->nama ?? '-' }} (NIY: {{ $g->niy ?: '-' }})</option>
                    @endforeach
                </select>
            @else
                <label class="block text-[11px] font-bold text-stone-600 uppercase mb-1">Pilih Siswa (Auto-Fill)</label>
                <select wire:model.live="selected_siswa_id" class="w-full bg-white border border-stone-300 text-stone-900 rounded-xl px-3.5 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">-- Pilih Siswa Aktif --</option>
                    @foreach($siswas as $s)
                        <option value="{{ $s->id }}">{{ $s->user->nama ?? '-' }} (NISN: {{ $s->nisn ?: '-' }})</option>
                    @endforeach
                </select>
            @endif
        </div>
    </div>

    <!-- Form Fields Grid -->
    <form wire:submit.prevent="simpanDanCetak" class="space-y-6 text-xs">
        <!-- Section 1: Nomor & Tanggal Surat -->
        <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl space-y-4">
            <span class="text-xs font-black text-stone-900 uppercase tracking-wider block">1. IDENTITAS SURAT & NOMOR ARSIP</span>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1 sm:col-span-2">
                    <label class="text-xs font-bold text-stone-700 uppercase">Nomor Surat <span class="text-rose-600">*</span></label>
                    <input type="text" wire:model.live="nomor_surat" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-extrabold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                    @error('nomor_surat') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Surat <span class="text-rose-600">*</span></label>
                    <input type="date" wire:model.live="tanggal_surat" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                    @error('tanggal_surat') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Section 2: Data Penerima (Siswa / Guru) -->
        <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl space-y-4">
            <span class="text-xs font-black text-stone-900 uppercase tracking-wider block">2. DATA DIRI PENERIMA SURAT</span>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Nama Lengkap -->
                <div class="space-y-1 lg:col-span-2">
                    <label class="text-xs font-bold text-stone-700 uppercase">Nama Lengkap <span class="text-rose-600">*</span></label>
                    <input type="text" wire:model.live="penerima_nama" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Aisyah Arumi / Rina, S.Pd." />
                    @error('penerima_nama') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Gender -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Jenis Kelamin</label>
                    <select wire:model.live="penerima_gender" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="Laki-Laki">Laki-Laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>

                <!-- Tempat / Tanggal Lahir -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Tempat / Tgl Lahir</label>
                    <input type="text" wire:model.live="penerima_ttl" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Pekanbaru, 15 April 2018" />
                </div>

                @if($jenis_surat !== 'pengalaman_kerja')
                    <!-- NISN / NIS -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">NISN / NIS</label>
                        <input type="text" wire:model.live="penerima_nisn" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="3184651522" />
                    </div>

                    <!-- Kelas -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Kelas / Tingkat</label>
                        <input type="text" wire:model.live="penerima_kelas" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="I (Satu) / II (Dua)" />
                    </div>
                @else
                    <!-- NIY / NIK -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">NIY / NIK</label>
                        <input type="text" wire:model.live="penerima_niy" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="NIY / NIK KTP" />
                    </div>

                    <!-- Pendidikan -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Pendidikan Terakhir</label>
                        <input type="text" wire:model.live="penerima_pendidikan" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="S1 Pendidikan / SMA" />
                    </div>
                @endif

                <!-- Alamat -->
                <div class="space-y-1 sm:col-span-2 lg:col-span-3">
                    <label class="text-xs font-bold text-stone-700 uppercase">Alamat Lengkap Rumah</label>
                    <input type="text" wire:model.live="penerima_alamat" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Jl. Amal, Perum Puri Taman Lestari, Pekanbaru" />
                </div>
            </div>
        </div>

        <!-- Section 3: Field Khusus Template -->
        @if(in_array($jenis_surat, ['menerima_pindah', 'pindah_sekolah']))
            <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-2xl space-y-4">
                <span class="text-xs font-black text-amber-950 uppercase tracking-wider block">3. INFORMASI MUTASI & ORANG TUA / WALI</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nama Orang Tua -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Nama Orang Tua / Wali</label>
                        <input type="text" wire:model.live="ortu_nama" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Budi Chandra" />
                    </div>

                    <!-- Pekerjaan Ortu -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Pekerjaan Orang Tua</label>
                        <input type="text" wire:model.live="ortu_pekerjaan" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Wiraswasta / PNS" />
                    </div>

                    @if($jenis_surat === 'pindah_sekolah')
                        <!-- Sekolah Tujuan -->
                        <div class="space-y-1 sm:col-span-2">
                            <label class="text-xs font-bold text-stone-700 uppercase">Sekolah Tujuan Pindah</label>
                            <input type="text" wire:model.live="sekolah_tujuan" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="SD IT BPMAA Kec. Pekanbaru Kota, Kota Pekanbaru" />
                        </div>

                        <!-- Alasan Pindah -->
                        <div class="space-y-1 sm:col-span-2">
                            <label class="text-xs font-bold text-stone-700 uppercase">Alasan Pindah</label>
                            <input type="text" wire:model.live="alasan_pindah" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Permintaan Orang Tua / Ikut Orang Tua" />
                        </div>
                    @endif
                </div>
            </div>
        @elseif($jenis_surat === 'pengalaman_kerja')
            <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-2xl space-y-4">
                <span class="text-xs font-black text-emerald-950 uppercase tracking-wider block">3. INFORMASI JABATAN & PERIODE KERJA</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Posisi Kerja -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Jabatan / Posisi Kerja</label>
                        <input type="text" wire:model.live="posisi_kerja" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Guru Pendamping / Guru Pengajar" />
                    </div>

                    <!-- Periode Kerja -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Periode Masa Kerja</label>
                        <input type="text" wire:model.live="periode_kerja" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="November 2021 sampai Maret 2023" />
                    </div>
                </div>
            </div>
        @endif

        <!-- Section 4: Penandatangan -->
        <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <span class="text-xs font-black text-stone-900 uppercase tracking-wider block">4. PEJABAT PENANDATANGAN SURAT (PENGESAHAN QR CODE RESMI)</span>
                    <p class="text-[11px] text-stone-500">Tidak perlu tanda tangan manual. Surat resmi disahkan secara elektronik dengan QR Code resmi ke website. Data awal terisi dari Kepala Sekolah.</p>
                </div>
                <button type="button" wire:click="resetPenandatanganToDefault" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded-xl transition-colors cursor-pointer shadow-2xs shrink-0 whitespace-nowrap self-start sm:self-auto">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Kembalikan ke Kepala Sekolah
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Nama Kepala Sekolah / Pejabat</label>
                    <input type="text" wire:model.live="penandatangan_nama" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Jabatan Pejabat</label>
                    <input type="text" wire:model.live="penandatangan_jabatan" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">NIP / NIY Pejabat</label>
                    <input type="text" wire:model.live="penandatangan_niy" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                </div>
            </div>
        </div>

        <!-- Submit Action Button -->
        @if (!auth()->user()?->isSuperAdmin2())
            <div class="flex items-center justify-end border-t border-stone-200 pt-4 gap-2">
                <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="simpanDanCetak">
                    Simpan & Pratinjau PDF Surat (Live Editor)
                </x-button>
            </div>
        @endif
    </form>
</div>
