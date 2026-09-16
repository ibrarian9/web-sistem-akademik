<!-- MODAL PRATINJAU REAL-TIME & LIVE INTERACTIVE EDITOR (KOP YFI PRESISI DOCX) -->
@if ($showPrintModal)
    <div class="fixed inset-0 z-[99990] flex items-center justify-center lg:pl-64 bg-stone-950/65 backdrop-blur-xs p-4 lg:p-8 overflow-y-auto print:p-0 print:bg-white print:static print:pl-0 animate-fade-in">
        <div class="bg-white rounded-3xl p-6 shadow-2xl max-w-6xl w-full space-y-4 max-h-[95vh] overflow-y-auto print:max-h-none print:shadow-none print:rounded-none print:p-0">
            <!-- Header Controls Bar -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-stone-200 pb-3 print:hidden">
                <div>
                    <h3 class="text-sm font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-950 text-xs flex items-center justify-center font-black">
                            <x-lucide-sparkles class="w-3.5 h-3.5 text-emerald-950" />
                        </span>
                        <span>Pratinjau Surat Real-Time & Live Editor</span>
                    </h3>
                    <p class="text-xs text-stone-500 font-medium mt-0.5">Setiap perubahan teks pada form akan langsung mengupdate tampilan pratinjau di bawah secara real-time.</p>
                </div>

                <div class="flex items-center gap-2">
                    <x-button type="button" variant="warning" size="md" icon="download" wire:click="downloadCurrentPdf">
                        Unduh File PDF
                    </x-button>
                    <x-button type="button" variant="primary" size="md" icon="printer" onclick="window.print()">
                        Cetak Dokumen
                    </x-button>
                    <x-button type="button" variant="secondary" size="md" wire:click="$set('showPrintModal', false)">
                        Tutup
                    </x-button>
                </div>
            </div>

            <!-- LIVE PREVIEW CONTAINER (FORMAT 100% PERSIS DOCX & KOP YFI) -->
            <div id="printable-letter" class="p-10 bg-white font-serif text-black text-sm leading-relaxed max-w-[210mm] mx-auto min-h-[297mm] shadow-xs print:shadow-none print:p-0 border border-stone-200 print:border-none">
                
                <!-- KOP SURAT RESMI YAYASAN FIRYAL INDONESIA / SD TAHFIZH F3 -->
                <div class="border-b-4 border-double border-black pb-2 mb-6 text-center font-sans relative">
                    <div class="flex items-center justify-between">
                        <!-- Logo Yayasan (Kiri) -->
                        <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center">
                            <img src="{{ asset('images/logo_yayasan.png') }}" alt="Logo Yayasan" class="w-16 h-auto max-h-16 object-contain" />
                        </div>

                        <!-- Teks Header Kop -->
                        <div class="flex-1 text-center font-sans px-2">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-black m-0">YAYASAN F3</h3>
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

                <!-- ISIAN SURAT PRESISI DENGAN BERKAS DOCX TEMPLATE -->
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
                    <!-- 3. SURAT KETERANGAN MENERIMA SISWA PINDAHAN -->
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
                @php
                    $previewPayload = $this->getPayload($suratId);
                @endphp
                <div class="mt-12 flex justify-end">
                    <div class="text-center w-72 space-y-1">
                        <p class="text-stone-800">{{ $kota_surat }}, {{ \Carbon\Carbon::parse($tanggal_surat)->format('d F Y') }}</p>
                        <p class="font-bold text-stone-900">{{ $penandatangan_jabatan }},</p>
                        
                        <!-- Official QR Code Space (Pengesahan Elektronik Resmi) -->
                        <div class="py-2 flex flex-col items-center justify-center">
                            <div class="p-1.5 bg-white border border-stone-200 rounded-xl shadow-xs inline-block">
                                <img src="{{ $previewPayload['qr_code'] }}" alt="QR Code Resmi" class="w-20 h-20" />
                            </div>
                            <span class="mt-1.5 text-[10px] font-black uppercase text-emerald-700 tracking-wide">Ditandatangani Secara Elektronik</span>
                            <a href="{{ $previewPayload['verification_url'] }}" target="_blank" class="text-[9px] text-emerald-600 hover:text-emerald-800 hover:underline font-semibold flex items-center gap-1 mt-0.5">
                                <span>Verifikasi Keaslian Dokumen</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            <span class="text-[8.5px] font-mono text-stone-400">{{ $previewPayload['verification_code'] ?? '' }}</span>
                        </div>

                        <p class="font-extrabold uppercase underline text-stone-900">{{ $penandatangan_nama }}</p>
                        <p class="text-xs font-semibold text-stone-600">{{ !empty($penandatangan_niy) ? 'NIP / NIY : ' . $penandatangan_niy : '' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
