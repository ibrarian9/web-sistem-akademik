<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ strtoupper(str_replace('_', ' ', $jenis_surat)) }} - {{ $nomor_surat }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 20mm 20mm 20mm;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
        }
        /* Kop Surat Header Table */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 4px double #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .kop-logo {
            width: 80px;
            text-align: center;
            vertical-align: middle;
        }
        .kop-logo img {
            width: 75px;
            height: auto;
            max-height: 75px;
        }
        .kop-text {
            text-align: center;
            vertical-align: middle;
            font-family: Arial, Helvetica, sans-serif;
            padding: 0 10px;
        }
        .kop-yfi {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #000;
            margin: 0;
        }
        .kop-title {
            font-size: 16pt;
            font-weight: 900;
            text-transform: uppercase;
            color: #000;
            margin: 2px 0;
            letter-spacing: 0.5px;
        }
        .kop-akreditasi {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #000;
            margin: 0;
        }
        .kop-alamat {
            font-size: 8.5pt;
            margin-top: 4px;
            color: #1f2937;
            line-height: 1.3;
        }

        /* Letter Body Styles */
        .judul-surat {
            text-align: center;
            margin-bottom: 20px;
        }
        .judul-surat h3 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0;
        }
        .judul-surat p {
            font-size: 11pt;
            font-weight: bold;
            margin: 2px 0 0 0;
        }
        p {
            margin: 8px 0;
            text-align: justify;
        }
        .data-table {
            width: 95%;
            margin: 8px 0 12px 20px;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .data-table td.label {
            width: 170px;
            font-weight: normal;
        }
        .data-table td.colon {
            width: 15px;
            text-align: center;
        }
        .data-table td.value {
            font-weight: normal;
        }
        .font-bold-text {
            font-weight: bold;
        }

        /* Signature Block */
        .signature-container {
            width: 100%;
            margin-top: 35px;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature-space {
            height: 75px;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>

    @php
        $logoYayasanPath = public_path('images/logo_yayasan.png');
        if (!file_exists($logoYayasanPath)) {
            $logoYayasanPath = public_path('images/logo_yayasan.jpeg');
        }
        $logoTutWuriPath = public_path('images/logo_tut_wuri.png');
        
        $logoYayasanSrc = file_exists($logoYayasanPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoYayasanPath)) : '';
        $logoTutWuriSrc = file_exists($logoTutWuriPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoTutWuriPath)) : '';
    @endphp

    <!-- KOP SURAT SD TAHFIZH F3 PEKANBARU (LOGO YAYASAN + LOGO TUT WURI HANDAYANI) -->
    <table class="kop-table">
        <tr>
            <!-- Logo Yayasan (Kiri) -->
            <td class="kop-logo">
                @if($logoYayasanSrc)
                    <img src="{{ $logoYayasanSrc }}" alt="Logo Yayasan">
                @else
                    <div style="width:70px;height:70px;background:#065f46;color:#fff;border-radius:50%;line-height:70px;font-weight:bold;">SD F3</div>
                @endif
            </td>

            <!-- Teks Kop Surat Resmi -->
            <td class="kop-text">
                <div class="kop-yfi">YAYASAN FIRYAL INDONESIA (YFI)</div>
                <div class="kop-title">SEKOLAH DASAR TAHFIZH F3</div>
                <div class="kop-akreditasi">AKREDITASI B</div>
                <div class="kop-alamat">
                    Alamat: Jl. Gunung Kidul / Jl. Kepri No. 07 RT.05 / RW.02 Kelurahan Tangkerang Timur<br>
                    Kecamatan Tenayan Raya - Pekanbaru | Email: sdtahfizh.f3@gmail.com – 0823.2499.2447 / 0813.1926.3000
                </div>
            </td>

            <!-- Logo Tut Wuri Handayani (Kanan) -->
            <td class="kop-logo">
                @if($logoTutWuriSrc)
                    <img src="{{ $logoTutWuriSrc }}" alt="Logo Tut Wuri Handayani">
                @else
                    <div style="width:70px;height:70px;background:#1e3a8a;color:#fff;border-radius:50%;line-height:70px;font-weight:bold;">TUT WURI</div>
                @endif
            </td>
        </tr>
    </table>

    @if($jenis_surat === 'aktif_sekolah')
        <!-- 1. SURAT KETERANGAN AKTIF SEKOLAH -->
        <div class="judul-surat">
            <h3>SURAT KETERANGAN AKTIF SEKOLAH</h3>
            <p>Nomor : {{ $nomor_surat }}</p>
        </div>

        <p>Yang bertanda tangan di bawah ini :</p>
        <table class="data-table">
            <tr><td class="label">Nama</td><td class="colon">:</td><td class="value font-bold-text">{{ $penandatangan_nama }}</td></tr>
            <tr><td class="label">Jabatan</td><td class="colon">:</td><td class="value">{{ $penandatangan_jabatan }}</td></tr>
            <tr><td class="label">Alamat</td><td class="colon">:</td><td class="value">Jl. Gunung Kidul Gg. Kepri Kel. Tangkerang Timur Kec. Tenayan Raya - Pekanbaru</td></tr>
        </table>

        <p>Menerangkan dengan sesungguhnya bahwa :</p>
        <table class="data-table">
            <tr><td class="label">Nama</td><td class="colon">:</td><td class="value font-bold-text" style="text-transform: uppercase;">{{ $penerima_nama }}</td></tr>
            <tr><td class="label">Jenis Kelamin</td><td class="colon">:</td><td class="value">{{ $penerima_gender }}</td></tr>
            <tr><td class="label">NISN</td><td class="colon">:</td><td class="value">{{ $penerima_nisn ?: '-' }}</td></tr>
            <tr><td class="label">No. Induk</td><td class="colon">:</td><td class="value">{{ $penerima_nis ?: '-' }}</td></tr>
            <tr><td class="label">Tempat / Tgl Lahir</td><td class="colon">:</td><td class="value">{{ $penerima_ttl }}</td></tr>
            <tr><td class="label">Kelas</td><td class="colon">:</td><td class="value font-bold-text">{{ $penerima_kelas }}</td></tr>
            <tr><td class="label">Alamat</td><td class="colon">:</td><td class="value">{{ $penerima_alamat }}</td></tr>
        </table>

        <p>adalah benar sebagai <strong>Siswa Aktif</strong> di Sekolah Dasar (SD) Tahfizh F3 dan sekarang sedang duduk di kelas <strong>{{ $penerima_kelas }}</strong>.</p>
        <p>Demikian keterangan ini dibuat untuk diketahui dan dipergunakan sebagaimana mestinya.</p>

    @elseif($jenis_surat === 'pengalaman_kerja')
        <!-- 2. SURAT KETERANGAN PENGALAMAN KERJA -->
        <div class="judul-surat">
            <h3>SURAT KETERANGAN PENGALAMAN KERJA</h3>
            <p>Nomor : {{ $nomor_surat }}</p>
        </div>

        <p>Saya yang bertanda tangan di bawah ini :</p>
        <table class="data-table">
            <tr><td class="label">Nama</td><td class="colon">:</td><td class="value font-bold-text">{{ $penandatangan_nama }}</td></tr>
            <tr><td class="label">NIP / NIY</td><td class="colon">:</td><td class="value">{{ $penandatangan_niy ?: '-' }}</td></tr>
            <tr><td class="label">Jabatan</td><td class="colon">:</td><td class="value">{{ $penandatangan_jabatan }}</td></tr>
            <tr><td class="label">Unit Kerja</td><td class="colon">:</td><td class="value">SD TAHFIZH F3</td></tr>
        </table>

        <p>Dengan ini menerangkan bahwa :</p>
        <table class="data-table">
            <tr><td class="label">Nama</td><td class="colon">:</td><td class="value font-bold-text" style="text-transform: uppercase;">{{ $penerima_nama }}</td></tr>
            <tr><td class="label">Tempat/Tanggal Lahir</td><td class="colon">:</td><td class="value">{{ $penerima_ttl }}</td></tr>
            <tr><td class="label">NIK / NIY</td><td class="colon">:</td><td class="value">{{ $penerima_niy ?: ($penerima_nik ?: '-') }}</td></tr>
            <tr><td class="label">Pendidikan</td><td class="colon">:</td><td class="value">{{ $penerima_pendidikan }}</td></tr>
            <tr><td class="label">Unit Kerja/ Instansi</td><td class="colon">:</td><td class="value">SD TAHFIZH F3</td></tr>
        </table>

        <p>Dengan ini menyatakan bahwa nama tersebut di atas benar pernah bekerja di <strong>SD Tahfizh F3</strong> sebagai <strong>{{ $posisi_kerja }}</strong> terhitung mulai <strong>{{ $periode_kerja }}</strong>. Sepanjang bertugas, yang bersangkutan berkelakuan baik dan melaksanakan tugasnya dengan penuh tanggung jawab.</p>
        <p>Demikian surat keterangan ini dibuat dengan sesungguhnya dan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.</p>

    @elseif($jenis_surat === 'menerima_pindah')
        <!-- 3. SURAT KETERANGAN MENERIMA PINDAH -->
        <div class="judul-surat">
            <h3>SURAT KETERANGAN MENERIMA SISWA PINDAHAN</h3>
            <p>Nomor : {{ $nomor_surat }}</p>
        </div>

        <p>Yang bertanda tangan di bawah ini, Kepala SD Tahfizh F3 Kota Pekanbaru Provinsi Riau menerangkan bahwa :</p>
        <table class="data-table">
            <tr><td class="label">Nama</td><td class="colon">:</td><td class="value font-bold-text" style="text-transform: uppercase;">{{ $penerima_nama }}</td></tr>
            <tr><td class="label">Tempat / tanggal lahir</td><td class="colon">:</td><td class="value">{{ $penerima_ttl }}</td></tr>
            <tr><td class="label">Jenis Kelamin</td><td class="colon">:</td><td class="value">{{ $penerima_gender }}</td></tr>
            <tr><td class="label">Kelas</td><td class="colon">:</td><td class="value font-bold-text">{{ $penerima_kelas }}</td></tr>
            <tr><td class="label">Alamat</td><td class="colon">:</td><td class="value">{{ $penerima_alamat }}</td></tr>
        </table>

        <p>Sesuai surat permohonan pindah sekolah oleh orang tua / wali siswa :</p>
        <table class="data-table">
            <tr><td class="label">Nama</td><td class="colon">:</td><td class="value font-bold-text">{{ $ortu_nama }}</td></tr>
            <tr><td class="label">Pekerjaan</td><td class="colon">:</td><td class="value">{{ $ortu_pekerjaan }}</td></tr>
        </table>

        <p>Bahwa yang bersangkutan <strong>DITERIMA</strong> sebagai siswa SD Tahfizh F3 Kota Pekanbaru Provinsi Riau sesuai dengan ketentuan yang ditetapkan.</p>
        <p>Demikian Surat keterangan ini dibuat dan untuk digunakan sebagaimana mestinya.</p>

    @else
        <!-- 4. SURAT KETERANGAN PINDAH SEKOLAH -->
        <div class="judul-surat">
            <h3>SURAT KETERANGAN PINDAH SEKOLAH</h3>
            <p>Nomor : {{ $nomor_surat }}</p>
        </div>

        <p>Yang bertanda tangan di bawah ini kepala SD Tahfizh F3 Kecamatan Tenayan Raya Kota Pekanbaru menerangkan dengan sebenarnya bahwa :</p>
        <table class="data-table">
            <tr><td class="label">Nama Siswa</td><td class="colon">:</td><td class="value font-bold-text" style="text-transform: uppercase;">{{ $penerima_nama }}</td></tr>
            <tr><td class="label">Tempat / Tanggal Lahir</td><td class="colon">:</td><td class="value">{{ $penerima_ttl }}</td></tr>
            <tr><td class="label">NIS/NISN</td><td class="colon">:</td><td class="value">{{ $penerima_nis ?: '-' }} / {{ $penerima_nisn ?: '-' }}</td></tr>
            <tr><td class="label">Jenis Kelamin</td><td class="colon">:</td><td class="value">{{ $penerima_gender }}</td></tr>
            <tr><td class="label">Tingkat / Kelas</td><td class="colon">:</td><td class="value font-bold-text">{{ $penerima_kelas }}</td></tr>
        </table>

        <p>Sesuai dengan permohonan pindah sekolah oleh orangtua / wali :</p>
        <table class="data-table">
            <tr><td class="label">Nama</td><td class="colon">:</td><td class="value font-bold-text">{{ $ortu_nama }}</td></tr>
            <tr><td class="label">Hubungan Dengan Siswa</td><td class="colon">:</td><td class="value">{{ $ortu_hubungan }}</td></tr>
            <tr><td class="label">Pekerjaan</td><td class="colon">:</td><td class="value">{{ $ortu_pekerjaan }}</td></tr>
            <tr><td class="label">Alasan Pindah</td><td class="colon">:</td><td class="value">{{ $alasan_pindah }}</td></tr>
        </table>

        <p>Telah mengajukan untuk pindah sekolah dari SD Tahfizh F3 ke <strong>{{ $sekolah_tujuan ?: '[Nama Sekolah Tujuan]' }}</strong>.</p>
        <p>Demikian surat keterangan pindah sekolah ini dibuat dengan sebenarnya, agar diketahui bersama dan dapat digunakan sebagaimana mestinya.</p>
    @endif

    <!-- SIGNATURE BLOCK -->
    <div class="signature-container">
        <div class="signature-box">
            <p>{{ $kota_surat }}, {{ \Carbon\Carbon::parse($tanggal_surat)->format('d F Y') }}</p>
            <p style="font-weight: bold;">{{ $penandatangan_jabatan }},</p>
            <div class="signature-space"></div>
            <p style="font-weight: bold; text-decoration: underline; text-transform: uppercase;">{{ $penandatangan_nama }}</p>
            <p style="font-size: 10pt;">NIY : {{ $penandatangan_niy }}</p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
