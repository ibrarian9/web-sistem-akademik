<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>RAPOR - {{ $siswa->user->nama ?? $siswa->nama_panggilan }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 10.5pt; 
            color: #0f172a; 
            line-height: 1.35; 
            margin: 0; 
            padding: 0; 
        }
        .page-break { page-break-after: always; }
        
        .header-title { text-align: center; margin-bottom: 15px; }
        .header-title h1 { margin: 0; font-size: 14pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .header-title h2 { margin: 2px 0 0 0; font-size: 12pt; font-weight: bold; text-transform: uppercase; }

        .meta-table { width: 100%; margin-bottom: 15px; font-size: 10pt; border-collapse: collapse; }
        .meta-table td { padding: 2px 4px; vertical-align: top; }

        .table-rapor { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 9.5pt; }
        .table-rapor th, .table-rapor td { border: 1px solid #334155; padding: 6px 8px; vertical-align: top; }
        .table-rapor th { background-color: #bbf7d0; font-weight: bold; color: #064e3b; text-align: center; font-size: 9.5pt; }
        
        .bg-green-header { background-color: #bbf7d0; font-weight: bold; color: #064e3b; padding: 5px 8px; border: 1px solid #334155; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .flex-box { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .flex-box td { vertical-align: top; }

        .box-border { border: 1px solid #334155; padding: 8px; min-height: 80px; }
        
        .strike { text-decoration: line-through; }
    </style>
</head>
<body>

    <!-- HALAMAN 1: RAPOR AKADEMIK KURIKULUM MERDEKA -->
    <div class="header-title">
        <h1>LAPORAN HASIL BELAJAR</h1>
        <h2>(RAPOR)</h2>
    </div>

    <table class="meta-table">
        <tr>
            <td width="18%">Nama Peserta Didik</td>
            <td width="32%">: <strong>{{ strtoupper($siswa->user->nama ?? $siswa->nama_panggilan) }}</strong></td>
            <td width="18%">Kelas</td>
            <td width="32%">: {{ $siswa->kelas->nama_kelas ?? 'V' }}</td>
        </tr>
        <tr>
            <td>NISN</td>
            <td>: {{ $siswa->nisn }}</td>
            <td>Fase</td>
            <td>: C</td>
        </tr>
        <tr>
            <td>Sekolah</td>
            <td>: SD TAHFIZH F3</td>
            <td>Semester</td>
            <td>: {{ $rapor->semester->semester ?? 'II' }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: JL. Gunung kidul/jl.kepri No 7 rt. 05/rw. 02</td>
            <td>Tahun Pelajaran</td>
            <td>: {{ $rapor->semester->tahunAjaran->nama ?? '2025/2026' }}</td>
        </tr>
    </table>

    <table class="table-rapor">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Mata Pelajaran</th>
                <th width="12%">Nilai Akhir</th>
                <th width="58%">Capaian Kompetensi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($raporDetails as $index => $detail)
                <tr>
                    <td class="text-center font-bold">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $detail['mapel']['nama_mapel'] ?? '-' }}</td>
                    <td class="text-center font-bold" style="font-size: 11pt;">{{ round($detail['nilai_akhir']) }}</td>
                    <td style="font-size: 9pt; leading-height: 1.3;">
                        @if(!empty($detail['deskripsi_tertinggi']))
                            <p style="margin: 0 0 6px 0;">Ananda {{ strtolower($siswa->nama_panggilan ?? $siswa->user->nama ?? 'siswa') }} {{ $detail['deskripsi_tertinggi'] }}</p>
                        @endif
                        @if(!empty($detail['deskripsi_terendah']))
                            <p style="margin: 0;">Ananda {{ strtolower($siswa->nama_panggilan ?? $siswa->user->nama ?? 'siswa') }} {{ $detail['deskripsi_terendah'] }}</p>
                        @endif
                        @if(empty($detail['deskripsi_tertinggi']) && empty($detail['deskripsi_terendah']))
                            <p style="margin: 0;">{{ $detail['narasi_capaian_full'] ?? 'Ananda menunjukkan penguasaan kompetensi mata pelajaran dengan sangat baik.' }}</p>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="color: #64748b; padding: 20px;">Belum ada data nilai mata pelajaran Kurikulum Merdeka yang diterbitkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- HALAMAN 2: KOKURIKULER, EKSTRAKURIKULER, KETIDAKHADIRAN, CATATAN, KEPUTUSAN, & TTD -->
    
    <!-- SECTION 1: KOKURIKULER P5 -->
    <div style="margin-bottom: 12px;">
        <div class="bg-green-header text-center">Kokurikuler</div>
        <div class="box-border" style="font-size: 9.5pt; line-height: 1.4;">
            <p style="margin: 0;">
                Ananda {{ strtolower($siswa->nama_panggilan ?? $siswa->user->nama ?? 'fauzan') }} sudah mahir dalam penerapan subdimensi Mengatur diri dan bertanggung jawab (Mandiri), hal tersebut terlihat pada kegiatan "Aku Bisa Sendiri" dan sudah mulai berkembang dalam penerapan subdimensi Mengatur diri dan bertanggung jawab (Mandiri), hal tersebut terlihat pada kegiatan Merapikan Kamar/Tas Sendiri.
            </p>
        </div>
    </div>

    <!-- SECTION 2: EKSTRAKURIKULER -->
    <div style="margin-bottom: 12px;">
        <table class="table-rapor" style="margin-bottom: 0;">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="25%">Ekstrakurikuler</th>
                    <th width="70%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td class="font-bold">Matematika</td>
                    <td>Ananda sudah sangat baik dalam kegiatan ekstra</td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td class="font-bold">Memanah</td>
                    <td>Ananda sudah sangat baik dalam kegiatan ekstra</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- SECTION 3: KETIDAKHADIRAN & CATATAN WALI KELAS SIDE-BY-SIDE -->
    <table class="flex-box">
        <tr>
            <td width="35%" style="padding-right: 8px;">
                <table class="table-rapor" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th colspan="2">Ketidakhadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Sakit</td>
                            <td class="text-center font-bold">- hari</td>
                        </tr>
                        <tr>
                            <td>Izin</td>
                            <td class="text-center font-bold">1 hari</td>
                        </tr>
                        <tr>
                            <td>Tanpa Keterangan</td>
                            <td class="text-center font-bold">- hari</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="65%" style="padding-left: 8px;">
                <div class="bg-green-header">Catatan Wali Kelas</div>
                <div class="box-border" style="font-size: 9.5pt; line-height: 1.4; min-height: 62px;">
                    {{ $rapor->catatan_wali_kelas ?: 'Ananda sudah banyak perkembangan dalam kemampuan belajar terutama pada mata pelajaran matematika, tingkatkan fokus ananda dalam belajar.' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- SECTION 4: TANGGAPAN ORANG TUA & KEPUTUSAN SIDE-BY-SIDE -->
    <table class="flex-box">
        <tr>
            <td width="35%" style="padding-right: 8px;">
                <div class="bg-green-header">Tanggapan Orang Tua/ Wali Murid</div>
                <div class="box-border" style="min-height: 58px;"></div>
            </td>
            <td width="65%" style="padding-left: 8px;">
                <div class="bg-green-header">Keputusan :</div>
                <div class="box-border" style="font-size: 9pt; min-height: 58px;">
                    <p style="margin: 0 0 6px 0;">Berdasarkan pencapaian seluruh kompetensi peserta didik dinyatakan :</p>
                    <p style="margin: 0; font-size: 10pt;" class="font-bold">
                        <span style="display: inline-block; width: 140px;">Naik / <span class="strike">Tinggal</span> *) kelas</span>
                        <span style="font-size: 11pt; float: right; margin-right: 20px;">VI (ENAM)</span>
                    </p>
                    <p style="margin: 6px 0 0 0; font-size: 8pt; color: #475569;">*)coret yang tidak perlu</p>
                </div>
            </td>
        </tr>
    </table>

    <!-- SECTION 5: SIGNATURE BLOCK (TANGGAL, WALI KELAS, KEPALA SEKOLAH, ORANG TUA + QR CODE VERIFIKASI) -->
    <div style="margin-top: 20px; font-size: 9.5pt;">
        <table width="100%">
            <tr>
                <td width="40%" class="text-center">
                    <br>
                    Orang Tua,<br><br><br><br>
                    <strong>RIDWAN</strong>
                </td>
                <td width="20%"></td>
                <td width="40%" class="text-center">
                    Pekanbaru, {{ date('d F Y', strtotime($rapor->tanggal_terbit ?? date('Y-m-d'))) }}<br>
                    Wali Kelas<br><br><br><br>
                    <strong>NURUL MINA, S.Pd., Gr</strong><br>
                    <span style="font-size: 8.5pt;">NIY: 200003152202211000</span>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="text-center" style="padding-top: 15px;">
                    Mengetahui,<br>
                    Kepala Sekolah<br><br><br><br>
                    <strong>RINA, S.Pd., Gr</strong><br>
                    <span style="font-size: 8.5pt;">NIY: 198010052201907001</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

