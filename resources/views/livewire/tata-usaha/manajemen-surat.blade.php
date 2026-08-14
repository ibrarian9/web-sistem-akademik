<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Layanan Persuratan & Cetak Dokumen Resmi Tata Usaha"
        :steps="[
            ['title' => 'Pilih Template & Penerima', 'desc' => 'Pilih jenis surat dan pilih nama Siswa / Guru untuk pengisian data otomatis.'],
            ['title' => 'Edit Live & Pratinjau PDF', 'desc' => 'Periksa isian surat. Pada modal pratinjau, Anda dapat mengedit teks secara langsung (Live Interactive Editor).'],
            ['title' => 'Unduh PDF / Cetak Resmi', 'desc' => 'Klik tombol Unduh File PDF untuk mengunduh berkas .pdf resmi atau Cetak Dokumen untuk mencetak Kop YFI/SD Tahfizh F3.']
        ]"
        notes="Format Kop & susunan surat 100% disesuaikan dengan dokumen resmi Yayasan Firyal Indonesia / SD Tahfizh F3 Pekanbaru."
    />

    <!-- Hero Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                PERSURATAN &amp; ARSIP TATA USAHA
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Layanan Generator &amp; PDF Surat Resmi</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Pembuatan, pengeditan langsung, pratinjau, dan unduh PDF resmi SD TAHFIZH F3 Pekanbaru.</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-2 bg-stone-100 p-1.5 rounded-2xl border border-stone-200">
            <button type="button" wire:click="$set('activeTab', 'buat')" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $activeTab === 'buat' ? 'bg-emerald-700 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900' }}">
                <x-lucide-file-plus class="w-4 h-4" />
                <span>Buat Surat Baru</span>
            </button>
            <button type="button" wire:click="$set('activeTab', 'riwayat')" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $activeTab === 'riwayat' ? 'bg-emerald-700 text-white shadow-xs' : 'text-stone-600 hover:text-stone-900' }}">
                <x-lucide-history class="w-4 h-4" />
                <span>Riwayat Surat Diterbitkan</span>
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if ($activeTab === 'buat')
        <!-- SELECT TEMPLATE CARDS -->
        <div class="space-y-4">
            <h3 class="text-xs font-extrabold text-stone-700 uppercase tracking-wider">PILIH TEMPLATE SURAT YANG INGIN DIBUAT</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Template 1: Aktif Sekolah -->
                <button type="button" wire:click="$set('jenis_surat', 'aktif_sekolah')" class="p-4 rounded-2xl border text-left transition flex flex-col justify-between space-y-3 {{ $jenis_surat === 'aktif_sekolah' ? 'bg-emerald-50 border-emerald-500 ring-2 ring-emerald-500/20 shadow-md' : 'bg-white border-stone-200 hover:border-stone-300 shadow-xs' }}">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-900 flex items-center justify-center font-black">
                            <x-lucide-graduation-cap class="w-5 h-5 text-emerald-800" />
                        </div>
                        @if($jenis_surat === 'aktif_sekolah') <span class="px-2 py-0.5 bg-emerald-700 text-white text-[10px] font-bold rounded-full">Terpilih</span> @endif
                    </div>
                    <div>
                        <h4 class="font-extrabold text-stone-900 text-xs">Surat Keterangan Aktif Sekolah</h4>
                        <p class="text-[11px] text-stone-500 font-medium mt-0.5">Untuk siswa terdaftar aktif di SD Tahfizh F3.</p>
                    </div>
                </button>

                <!-- Template 2: Pengalaman Kerja -->
                <button type="button" wire:click="$set('jenis_surat', 'pengalaman_kerja')" class="p-4 rounded-2xl border text-left transition flex flex-col justify-between space-y-3 {{ $jenis_surat === 'pengalaman_kerja' ? 'bg-emerald-50 border-emerald-500 ring-2 ring-emerald-500/20 shadow-md' : 'bg-white border-stone-200 hover:border-stone-300 shadow-xs' }}">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-900 flex items-center justify-center font-black">
                            <x-lucide-briefcase class="w-5 h-5 text-emerald-800" />
                        </div>
                        @if($jenis_surat === 'pengalaman_kerja') <span class="px-2 py-0.5 bg-emerald-700 text-white text-[10px] font-bold rounded-full">Terpilih</span> @endif
                    </div>
                    <div>
                        <h4 class="font-extrabold text-stone-900 text-xs">Surat Pengalaman Kerja</h4>
                        <p class="text-[11px] text-stone-500 font-medium mt-0.5">Keterangan masa kerja &amp; tugas guru / karyawan.</p>
                    </div>
                </button>

                <!-- Template 3: Menerima Pindah -->
                <button type="button" wire:click="$set('jenis_surat', 'menerima_pindah')" class="p-4 rounded-2xl border text-left transition flex flex-col justify-between space-y-3 {{ $jenis_surat === 'menerima_pindah' ? 'bg-emerald-50 border-emerald-500 ring-2 ring-emerald-500/20 shadow-md' : 'bg-white border-stone-200 hover:border-stone-300 shadow-xs' }}">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 border border-amber-300 text-amber-900 flex items-center justify-center font-black">
                            <x-lucide-arrow-down-left class="w-5 h-5 text-amber-800" />
                        </div>
                        @if($jenis_surat === 'menerima_pindah') <span class="px-2 py-0.5 bg-emerald-700 text-white text-[10px] font-bold rounded-full">Terpilih</span> @endif
                    </div>
                    <div>
                        <h4 class="font-extrabold text-stone-900 text-xs">Mutasi: Menerima Pindah</h4>
                        <p class="text-[11px] text-stone-500 font-medium mt-0.5">Keterangan menerima siswa pindahan dari luar.</p>
                    </div>
                </button>

                <!-- Template 4: Pindah Sekolah -->
                <button type="button" wire:click="$set('jenis_surat', 'pindah_sekolah')" class="p-4 rounded-2xl border text-left transition flex flex-col justify-between space-y-3 {{ $jenis_surat === 'pindah_sekolah' ? 'bg-emerald-50 border-emerald-500 ring-2 ring-emerald-500/20 shadow-md' : 'bg-white border-stone-200 hover:border-stone-300 shadow-xs' }}">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 border border-rose-300 text-rose-900 flex items-center justify-center font-black">
                            <x-lucide-arrow-up-right class="w-5 h-5 text-rose-800" />
                        </div>
                        @if($jenis_surat === 'pindah_sekolah') <span class="px-2 py-0.5 bg-emerald-700 text-white text-[10px] font-bold rounded-full">Terpilih</span> @endif
                    </div>
                    <div>
                        <h4 class="font-extrabold text-stone-900 text-xs">Mutasi: Pindah Sekolah</h4>
                        <p class="text-[11px] text-stone-500 font-medium mt-0.5">Keterangan mengajukan pindah sekolah keluar.</p>
                    </div>
                </button>
            </div>
        </div>

        <!-- FORM BUILDER CARD -->
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b border-stone-200 pb-4">
                <div>
                    <h3 class="text-sm font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
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
                        <select wire:model.live="selected_guru_id" class="w-full bg-emerald-50 border border-emerald-300 text-emerald-950 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                            <option value="">-- Pilih Guru / Karyawan --</option>
                            @foreach($gurus as $g)
                                <option value="{{ $g->id }}">{{ $g->user->nama ?? '-' }} (NIY: {{ $g->niy ?: '-' }})</option>
                            @endforeach
                        </select>
                    @else
                        <label class="block text-[11px] font-bold text-stone-600 uppercase mb-1">Pilih Siswa (Auto-Fill)</label>
                        <select wire:model.live="selected_siswa_id" class="w-full bg-emerald-50 border border-emerald-300 text-emerald-950 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                            <option value="">-- Pilih Siswa Aktif --</option>
                            @foreach($siswas as $s)
                                <option value="{{ $s->id }}">{{ $s->user->nama ?? '-' }} (NISN: {{ $s->nisn ?: '-' }})</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>

            <!-- Form Fields Grid -->
            <form wire:submit.prevent="simpanDanCetak" class="space-y-6">
                <!-- Section 1: Nomor & Tanggal Surat -->
                <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl space-y-4">
                    <span class="text-xs font-black text-stone-900 uppercase tracking-wider block">1. IDENTITAS SURAT &amp; NOMOR ARSIP</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1 sm:col-span-2">
                            <label class="text-xs font-bold text-stone-700 uppercase">Nomor Surat <span class="text-rose-600">*</span></label>
                            <input type="text" wire:model.live="nomor_surat" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-extrabold focus:ring-2 focus:ring-emerald-600" />
                            @error('nomor_surat') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Surat <span class="text-rose-600">*</span></label>
                            <input type="date" wire:model.live="tanggal_surat" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                            @error('tanggal_surat') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
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
                            <input type="text" wire:model.live="penerima_nama" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="Aisyah Arumi / Rina, S.Pd." />
                            @error('penerima_nama') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Gender -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Jenis Kelamin</label>
                            <select wire:model.live="penerima_gender" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                <option value="Laki-Laki">Laki-Laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>

                        <!-- Tempat / Tanggal Lahir -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Tempat / Tgl Lahir</label>
                            <input type="text" wire:model.live="penerima_ttl" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="Pekanbaru, 15 April 2018" />
                        </div>

                        @if($jenis_surat !== 'pengalaman_kerja')
                            <!-- NISN / NIS -->
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-stone-700 uppercase">NISN / NIS</label>
                                <input type="text" wire:model.live="penerima_nisn" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="3184651522" />
                            </div>

                            <!-- Kelas -->
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-stone-700 uppercase">Kelas / Tingkat</label>
                                <input type="text" wire:model.live="penerima_kelas" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="I (Satu) / II (Dua)" />
                            </div>
                        @else
                            <!-- NIY / NIK -->
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-stone-700 uppercase">NIY / NIK</label>
                                <input type="text" wire:model.live="penerima_niy" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="NIY / NIK KTP" />
                            </div>

                            <!-- Pendidikan -->
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-stone-700 uppercase">Pendidikan Terakhir</label>
                                <input type="text" wire:model.live="penerima_pendidikan" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="S1 Pendidikan / SMA" />
                            </div>
                        @endif

                        <!-- Alamat -->
                        <div class="space-y-1 sm:col-span-2 lg:col-span-3">
                            <label class="text-xs font-bold text-stone-700 uppercase">Alamat Lengkap Rumah</label>
                            <input type="text" wire:model.live="penerima_alamat" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600" placeholder="Jl. Amal, Perum Puri Taman Lestari, Pekanbaru" />
                        </div>
                    </div>
                </div>

                <!-- Section 3: Field Khusus Template -->
                @if(in_array($jenis_surat, ['menerima_pindah', 'pindah_sekolah']))
                    <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-2xl space-y-4">
                        <span class="text-xs font-black text-amber-950 uppercase tracking-wider block">3. INFORMASI MUTASI &amp; ORANG TUA / WALI</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Nama Orang Tua -->
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-stone-700 uppercase">Nama Orang Tua / Wali</label>
                                <input type="text" wire:model.live="ortu_nama" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="Budi Chandra" />
                            </div>

                            <!-- Pekerjaan Ortu -->
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-stone-700 uppercase">Pekerjaan Orang Tua</label>
                                <input type="text" wire:model.live="ortu_pekerjaan" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="Wiraswasta / PNS" />
                            </div>

                            @if($jenis_surat === 'pindah_sekolah')
                                <!-- Sekolah Tujuan -->
                                <div class="space-y-1 sm:col-span-2">
                                    <label class="text-xs font-bold text-stone-700 uppercase">Sekolah Tujuan Pindah</label>
                                    <input type="text" wire:model.live="sekolah_tujuan" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="SD IT BPMAA Kec. Pekanbaru Kota, Kota Pekanbaru" />
                                </div>

                                <!-- Alasan Pindah -->
                                <div class="space-y-1 sm:col-span-2">
                                    <label class="text-xs font-bold text-stone-700 uppercase">Alasan Pindah</label>
                                    <input type="text" wire:model.live="alasan_pindah" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="Permintaan Orang Tua / Ikut Orang Tua" />
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif($jenis_surat === 'pengalaman_kerja')
                    <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-2xl space-y-4">
                        <span class="text-xs font-black text-emerald-950 uppercase tracking-wider block">3. INFORMASI JABATAN &amp; PERIODE KERJA</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Posisi Kerja -->
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-stone-700 uppercase">Jabatan / Posisi Kerja</label>
                                <input type="text" wire:model.live="posisi_kerja" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="Guru Pendamping / Guru Pengajar" />
                            </div>

                            <!-- Periode Kerja -->
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-stone-700 uppercase">Periode Masa Kerja</label>
                                <input type="text" wire:model.live="periode_kerja" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="November 2021 sampai Maret 2023" />
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Section 4: Penandatangan -->
                <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl space-y-4">
                    <span class="text-xs font-black text-stone-900 uppercase tracking-wider block">4. PEJABAT PENANDATANGAN SURAT</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Nama Kepala Sekolah / Pejabat</label>
                            <input type="text" wire:model.live="penandatangan_nama" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Jabatan Pejabat</label>
                            <input type="text" wire:model.live="penandatangan_jabatan" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">NIY Pejabat</label>
                            <input type="text" wire:model.live="penandatangan_niy" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                        </div>
                    </div>
                </div>

                <!-- Submit Action Button -->
                <div class="flex items-center justify-end border-t border-stone-200 pt-4 gap-2">
                    <button type="submit" class="px-8 py-3 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-extrabold shadow-md flex items-center gap-2 transition">
                        <x-lucide-save class="w-4 h-4" />
                        <span>Simpan &amp; Pratinjau PDF Surat (Live Editor)</span>
                    </button>
                </div>
            </form>
        </div>
    @else
        <!-- RIWAYAT SURAT TAB CARD -->
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div class="relative max-w-md w-full">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400">
                        <x-lucide-search class="w-4 h-4" />
                    </span>
                    <input wire:model.live.debounce.300ms="searchRiwayat" type="text" placeholder="Cari nomor surat atau nama penerima..."
                        class="w-full pl-9 pr-4 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 placeholder-stone-400 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-xs" />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs text-stone-800">
                    <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                        <tr>
                            <th class="p-3.5 border-r border-emerald-700 w-44">Nomor Surat</th>
                            <th class="p-3.5 border-r border-emerald-700 min-w-[180px]">Jenis Surat</th>
                            <th class="p-3.5 border-r border-emerald-700 min-w-[180px]">Nama Penerima</th>
                            <th class="p-3.5 border-r border-emerald-700 w-32">Tanggal Surat</th>
                            <th class="p-3.5 border-r border-emerald-700 min-w-[140px]">Dibuat Oleh</th>
                            <th class="p-3.5 text-center min-w-[160px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($riwayats as $r)
                            @php
                                $jenisLabel = match($r->jenis_surat) {
                                    'aktif_sekolah' => 'Surat Keterangan Aktif Sekolah',
                                    'pengalaman_kerja' => 'Surat Pengalaman Kerja',
                                    'menerima_pindah' => 'Mutasi: Menerima Pindah',
                                    'pindah_sekolah' => 'Mutasi: Pindah Sekolah',
                                    default => $r->jenis_surat,
                                };
                            @endphp
                            <tr class="hover:bg-emerald-50/50 transition">
                                <td class="p-3.5 font-bold text-stone-900 border-r border-stone-200">{{ $r->nomor_surat }}</td>
                                <td class="p-3.5 border-r border-stone-200 font-extrabold text-stone-900">{{ $jenisLabel }}</td>
                                <td class="p-3.5 border-r border-stone-200 font-bold text-stone-800">{{ strtoupper($r->penerima_nama) }}</td>
                                <td class="p-3.5 border-r border-stone-200 font-semibold text-stone-600">{{ $r->tanggal_surat ? $r->tanggal_surat->format('d M Y') : '-' }}</td>
                                <td class="p-3.5 border-r border-stone-200 font-medium text-stone-500">{{ $r->creator->nama ?? 'Admin TU' }}</td>
                                <td class="p-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" wire:click="loadRiwayatSurat({{ $r->id }})" class="px-2.5 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-900 rounded-lg font-bold text-xs border border-emerald-300 transition shadow-xs flex items-center gap-1">
                                            <x-lucide-edit class="w-3.5 h-3.5 text-emerald-700" />
                                            <span>Edit / Preview</span>
                                        </button>
                                        <button type="button" wire:click="downloadPdfById({{ $r->id }})" class="px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-900 rounded-lg font-bold text-xs border border-amber-300 transition shadow-xs flex items-center gap-1">
                                            <x-lucide-download class="w-3.5 h-3.5 text-amber-700" />
                                            <span>PDF</span>
                                        </button>
                                        <button type="button" wire:click="deleteRiwayat({{ $r->id }})" data-confirm="Apakah Anda yakin ingin menghapus arsip surat ini?" class="px-2.5 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg font-bold text-xs border border-rose-300 transition shadow-xs flex items-center gap-1">
                                            <x-lucide-trash-2 class="w-3.5 h-3.5 text-rose-600" />
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-stone-500 font-semibold italic">
                                    Belum ada riwayat penerbitan surat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                {{ $riwayats->links() }}
            </div>
        </div>
    @endif

    <!-- MODAL PRATINJAU REAL-TIME & LIVE INTERACTIVE EDITOR (KOP YFI PRESISI DOCX) -->
    @if ($showPrintModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/80 backdrop-blur-xs p-4 overflow-y-auto print:p-0 print:bg-white print:static">
            <div class="bg-white rounded-3xl p-6 shadow-2xl max-w-6xl w-full space-y-4 max-h-[95vh] overflow-y-auto print:max-h-none print:shadow-none print:rounded-none print:p-0">
                <!-- Header Controls Bar -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-stone-200 pb-3 print:hidden">
                    <div>
                        <h3 class="text-sm font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-950 text-xs flex items-center justify-center font-black">
                                <x-lucide-sparkles class="w-3.5 h-3.5 text-emerald-950" />
                            </span>
                            <span>Pratinjau Surat Real-Time &amp; Live Editor</span>
                        </h3>
                        <p class="text-xs text-stone-500 font-medium mt-0.5">Setiap perubahan teks pada form akan langsung mengupdate tampilan pratinjau di bawah secara real-time.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="downloadCurrentPdf" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-extrabold shadow-md flex items-center gap-1.5 transition">
                            <x-lucide-download class="w-4 h-4" />
                            <span>Unduh File PDF</span>
                        </button>
                        <button type="button" onclick="window.print()" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-extrabold shadow-md flex items-center gap-1.5 transition">
                            <x-lucide-printer class="w-4 h-4" />
                            <span>Cetak Dokumen</span>
                        </button>
                        <button type="button" wire:click="$set('showPrintModal', false)" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-bold">
                            Tutup
                        </button>
                    </div>
                </div>

                <!-- LIVE PREVIEW CONTAINER (FORMAT 100% PERSIS DOCX & KOP YFI) -->
                <div id="printable-letter" class="p-10 bg-white font-serif text-black text-sm leading-relaxed max-w-[210mm] mx-auto min-h-[297mm] shadow-xs print:shadow-none print:p-0 border border-stone-200 print:border-none">
                    
                    <!-- KOP SURAT RESMI YAYASAN FIRYAL INDONESIA / SD TAHFIZH F3 (LOGO YAYASAN + LOGO TUT WURI HANDAYANI) -->
                    <div class="border-b-4 border-double border-black pb-2 mb-6 text-center font-sans relative">
                        <div class="flex items-center justify-between">
                            <!-- Logo Yayasan (Kiri) -->
                            <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center">
                                <img src="{{ asset('images/logo_yayasan.png') }}" alt="Logo Yayasan" class="w-16 h-auto max-h-16 object-contain" />
                            </div>

                            <!-- Teks Header Kop -->
                            <div class="flex-1 text-center font-sans px-2">
                                <h3 class="text-sm font-bold uppercase tracking-wider text-black m-0">YAYASAN FIRYAL INDONESIA (YFI)</h3>
                                <h1 class="text-xl font-black uppercase tracking-wider text-black m-0 mt-0.5">SEKOLAH DASAR TAHFIZH F3</h1>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-black m-0">AKREDITASI B</h4>
                                <p class="text-[10px] font-semibold leading-tight text-stone-800 mt-1">
                                    Alamat: Jl. Gunung Kidul / Jl. Kepri No. 07 RT.05 / RW.02 Kelurahan Tangkerang Timur Kecamatan Tenayan Raya - Pekanbaru<br>
                                    Email: sdtahfizh.f3@gmail.com – 0823.2499.2447 / 0813.1926.3000
                                </p>
                            </div>

                            <!-- Logo Tut Wuri Handayani (Kanan) -->
                            <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center">
                                <img src="{{ asset('images/logo_tut_wuri.png') }}" alt="Logo Tut Wuri Handayani" class="w-16 h-auto max-h-16 object-contain" />
                            </div>
                        </div>
                    </div>


                    <!-- ISIAN SURAT PRESISI DENGA BERKAS DOCX TEMPLATE -->
                    @if($jenis_surat === 'aktif_sekolah')
                        <!-- 1. SURAT KETERANGAN AKTIF SEKOLAH -->
                        <div class="text-center mb-6 space-y-1">
                            <h3 class="text-base font-bold uppercase underline tracking-wider">SURAT KETERANGAN AKTIF SEKOLAH</h3>
                            <p class="text-xs font-bold">Nomor : {{ $nomor_surat }}</p>
                        </div>

                        <div class="space-y-4 text-justify">
                            <p>Yang bertanda tangan di bawah ini :</p>
                            <table class="w-full ml-4 border-collapse">
                                <tr><td class="w-44 py-1">Nama</td><td class="w-4">:</td><td class="font-bold py-1">{{ $penandatangan_nama }}</td></tr>
                                <tr><td class="py-1">Jabatan</td><td>:</td><td class="py-1">{{ $penandatangan_jabatan }}</td></tr>
                                <tr><td class="py-1">Alamat</td><td>:</td><td class="py-1">Jl. Gunung Kidul Gg. Kepri Kel. Tangkerang Timur Kec. Tenayan Raya - Pekanbaru</td></tr>
                            </table>

                            <p>Menerangkan dengan sesungguhnya bahwa :</p>
                            <table class="w-full ml-4 border-collapse">
                                <tr><td class="w-44 py-1">Nama</td><td class="w-4">:</td><td class="font-bold uppercase py-1">{{ $penerima_nama }}</td></tr>
                                <tr><td class="py-1">Jenis Kelamin</td><td>:</td><td class="py-1">{{ $penerima_gender }}</td></tr>
                                <tr><td class="py-1">NISN</td><td>:</td><td class="py-1">{{ $penerima_nisn ?: '-' }}</td></tr>
                                <tr><td class="py-1">No. Induk</td><td>:</td><td class="py-1">{{ $penerima_nis ?: '-' }}</td></tr>
                                <tr><td class="py-1">Tempat / Tgl Lahir</td><td>:</td><td class="py-1">{{ $penerima_ttl }}</td></tr>
                                <tr><td class="py-1">Kelas</td><td>:</td><td class="font-bold py-1">{{ $penerima_kelas }}</td></tr>
                                <tr><td class="py-1">Alamat</td><td>:</td><td class="py-1">{{ $penerima_alamat }}</td></tr>
                            </table>

                            <p>adalah benar sebagai <strong>Siswa Aktif</strong> di Sekolah Dasar (SD) Tahfizh F3 dan sekarang sedang duduk di kelas <strong>{{ $penerima_kelas }}</strong>.</p>
                            <p>Demikian keterangan ini dibuat untuk diketahui dan dipergunakan sebagaimana mestinya.</p>
                        </div>

                    @elseif($jenis_surat === 'pengalaman_kerja')
                        <!-- 2. SURAT KETERANGAN PENGALAMAN KERJA -->
                        <div class="text-center mb-6 space-y-1">
                            <h3 class="text-base font-bold uppercase underline tracking-wider">SURAT KETERANGAN PENGALAMAN KERJA</h3>
                            <p class="text-xs font-bold">Nomor : {{ $nomor_surat }}</p>
                        </div>

                        <div class="space-y-4 text-justify">
                            <p>Saya yang bertanda tangan di bawah ini :</p>
                            <table class="w-full ml-4 border-collapse">
                                <tr><td class="w-44 py-1">Nama</td><td class="w-4">:</td><td class="font-bold py-1">{{ $penandatangan_nama }}</td></tr>
                                <tr><td class="py-1">NIP / NIY</td><td>:</td><td class="py-1">{{ $penandatangan_niy ?: '-' }}</td></tr>
                                <tr><td class="py-1">Jabatan</td><td>:</td><td class="py-1">{{ $penandatangan_jabatan }}</td></tr>
                                <tr><td class="py-1">Unit Kerja</td><td>:</td><td class="py-1">SD TAHFIZH F3</td></tr>
                            </table>

                            <p>Dengan ini menerangkan bahwa :</p>
                            <table class="w-full ml-4 border-collapse">
                                <tr><td class="w-44 py-1">Nama</td><td class="w-4">:</td><td class="font-bold uppercase py-1">{{ $penerima_nama }}</td></tr>
                                <tr><td class="py-1">Tempat/Tanggal Lahir</td><td>:</td><td class="py-1">{{ $penerima_ttl }}</td></tr>
                                <tr><td class="py-1">NIK / NIY</td><td>:</td><td class="py-1">{{ $penerima_niy ?: ($penerima_nik ?: '-') }}</td></tr>
                                <tr><td class="py-1">Pendidikan</td><td>:</td><td class="py-1">{{ $penerima_pendidikan }}</td></tr>
                                <tr><td class="py-1">Unit Kerja/ Instansi</td><td>:</td><td class="py-1">SD TAHFIZH F3</td></tr>
                            </table>

                            <p>Dengan ini menyatakan bahwa nama tersebut di atas benar pernah bekerja di <strong>SD Tahfizh F3</strong> sebagai <strong>{{ $posisi_kerja }}</strong> terhitung mulai <strong>{{ $periode_kerja }}</strong>. Sepanjang bertugas, yang bersangkutan berkelakuan baik dan melaksanakan tugasnya dengan penuh tanggung jawab.</p>
                            <p>Demikian surat keterangan ini dibuat dengan sesungguhnya dan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
                        </div>

                    @elseif($jenis_surat === 'menerima_pindah')
                        <!-- 3. SURAT KETERANGAN MENERIMA PINDAH -->
                        <div class="text-center mb-6 space-y-1">
                            <h3 class="text-base font-bold uppercase underline tracking-wider">SURAT KETERANGAN MENERIMA SISWA PINDAHAN</h3>
                            <p class="text-xs font-bold">Nomor : {{ $nomor_surat }}</p>
                        </div>

                        <div class="space-y-4 text-justify">
                            <p>Yang bertanda tangan di bawah ini, Kepala SD Tahfizh F3 Kota Pekanbaru Provinsi Riau menerangkan bahwa :</p>
                            <table class="w-full ml-4 border-collapse">
                                <tr><td class="w-44 py-1">Nama</td><td class="w-4">:</td><td class="font-bold uppercase py-1">{{ $penerima_nama }}</td></tr>
                                <tr><td class="py-1">Tempat / tanggal lahir</td><td>:</td><td class="py-1">{{ $penerima_ttl }}</td></tr>
                                <tr><td class="py-1">Jenis Kelamin</td><td>:</td><td class="py-1">{{ $penerima_gender }}</td></tr>
                                <tr><td class="py-1">Kelas</td><td>:</td><td class="font-bold py-1">{{ $penerima_kelas }}</td></tr>
                                <tr><td class="py-1">Alamat</td><td>:</td><td class="py-1">{{ $penerima_alamat }}</td></tr>
                            </table>

                            <p>Sesuai surat permohonan pindah sekolah oleh orang tua / wali siswa :</p>
                            <table class="w-full ml-4 border-collapse">
                                <tr><td class="w-44 py-1">Nama</td><td class="w-4">:</td><td class="font-bold py-1">{{ $ortu_nama }}</td></tr>
                                <tr><td class="py-1">Pekerjaan</td><td>:</td><td class="py-1">{{ $ortu_pekerjaan }}</td></tr>
                            </table>

                            <p>Bahwa yang bersangkutan <strong>DITERIMA</strong> sebagai siswa SD Tahfizh F3 Kota Pekanbaru Provinsi Riau sesuai dengan ketentuan yang ditetapkan.</p>
                            <p>Demikian Surat keterangan ini dibuat dan untuk digunakan sebagaimana mestinya.</p>
                        </div>

                    @else
                        <!-- 4. SURAT KETERANGAN PINDAH SEKOLAH -->
                        <div class="text-center mb-6 space-y-1">
                            <h3 class="text-base font-bold uppercase underline tracking-wider">SURAT KETERANGAN PINDAH SEKOLAH</h3>
                            <p class="text-xs font-bold">Nomor : {{ $nomor_surat }}</p>
                        </div>

                        <div class="space-y-4 text-justify">
                            <p>Yang bertanda tangan di bawah ini kepala SD Tahfizh F3 Kecamatan Tenayan Raya Kota Pekanbaru menerangkan dengan sebenarnya bahwa :</p>
                            <table class="w-full ml-4 border-collapse">
                                <tr><td class="w-44 py-1">Nama Siswa</td><td class="w-4">:</td><td class="font-bold uppercase py-1">{{ $penerima_nama }}</td></tr>
                                <tr><td class="py-1">Tempat / Tanggal Lahir</td><td>:</td><td class="py-1">{{ $penerima_ttl }}</td></tr>
                                <tr><td class="py-1">NIS/NISN</td><td>:</td><td class="py-1">{{ $penerima_nis ?: '-' }} / {{ $penerima_nisn ?: '-' }}</td></tr>
                                <tr><td class="py-1">Jenis Kelamin</td><td>:</td><td class="py-1">{{ $penerima_gender }}</td></tr>
                                <tr><td class="py-1">Tingkat / Kelas</td><td>:</td><td class="font-bold py-1">{{ $penerima_kelas }}</td></tr>
                            </table>

                            <p>Sesuai dengan permohonan pindah sekolah oleh orangtua / wali :</p>
                            <table class="w-full ml-4 border-collapse">
                                <tr><td class="w-44 py-1">Nama</td><td class="w-4">:</td><td class="font-bold py-1">{{ $ortu_nama }}</td></tr>
                                <tr><td class="py-1">Hubungan Dengan Siswa</td><td>:</td><td class="py-1">{{ $ortu_hubungan }}</td></tr>
                                <tr><td class="py-1">Pekerjaan</td><td>:</td><td class="py-1">{{ $ortu_pekerjaan }}</td></tr>
                                <tr><td class="py-1">Alasan Pindah</td><td>:</td><td class="py-1">{{ $alasan_pindah }}</td></tr>
                            </table>

                            <p>Telah mengajukan untuk pindah sekolah dari SD Tahfizh F3 ke <strong>{{ $sekolah_tujuan ?: '[Nama Sekolah Tujuan]' }}</strong>.</p>
                            <p>Demikian surat keterangan pindah sekolah ini dibuat dengan sebenarnya, agar diketahui bersama dan dapat digunakan sebagaimana mestinya.</p>
                        </div>
                    @endif

                    <!-- SIGNATURE BLOCK -->
                    <div class="mt-12 flex justify-end">
                        <div class="text-center w-72 space-y-1">
                            <p>{{ $kota_surat }}, {{ \Carbon\Carbon::parse($tanggal_surat)->format('d F Y') }}</p>
                            <p class="font-bold">{{ $penandatangan_jabatan }},</p>
                            
                            <!-- Stempel / Signature Space -->
                            <div class="h-20 flex items-center justify-center">
                                <span class="text-[10px] text-stone-300 italic">[Tanda Tangan &amp; Stempel Resmi]</span>
                            </div>

                            <p class="font-extrabold uppercase underline text-stone-900">{{ $penandatangan_nama }}</p>
                            <p class="text-xs font-semibold">NIY : {{ $penandatangan_niy }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
