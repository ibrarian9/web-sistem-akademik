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
            font-size: 10pt; 
            color: #0f172a; 
            line-height: 1.35; 
            margin: 0; 
            padding: 0; 
        }
        .page-break { page-break-after: always; }
        
        .header-title { text-align: center; margin-bottom: 12px; }
        .header-title h1 { margin: 0; font-size: 13pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .header-title h2 { margin: 2px 0 0 0; font-size: 11pt; font-weight: bold; text-transform: uppercase; color: #065f46; }

        .meta-table { width: 100%; margin-bottom: 12px; font-size: 9.5pt; border-collapse: collapse; }
        .meta-table td { padding: 2px 4px; vertical-align: top; }

        /* Core Curriculum Tahfizh Banner */
        .tahfidz-core-card {
            border: 1.5px solid #047857;
            background-color: #f0fdf4;
            border-radius: 6px;
            padding: 8px 10px;
            margin-bottom: 14px;
        }
        .tahfidz-core-header {
            font-weight: bold;
            font-size: 10pt;
            color: #064e3b;
            text-transform: uppercase;
            border-bottom: 1px solid #a7f3d0;
            padding-bottom: 4px;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }

        .table-rapor { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9pt; }
        .table-rapor th, .table-rapor td { border: 1px solid #334155; padding: 5px 7px; vertical-align: top; }
        .table-rapor th { background-color: #bbf7d0; font-weight: bold; color: #064e3b; text-align: center; font-size: 9pt; }
        
        .bg-green-header { background-color: #bbf7d0; font-weight: bold; color: #064e3b; padding: 4px 8px; border: 1px solid #334155; font-size: 9pt; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .flex-box { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .flex-box td { vertical-align: top; }

        .box-border { border: 1px solid #334155; padding: 6px 8px; min-height: 55px; font-size: 9pt; }
        
        .strike { text-decoration: line-through; }
    </style>
</head>
<body>

    <!-- HALAMAN 1: RAPOR AKADEMIK & PROGRAM UNGGULAN TAHFIZH -->
    <div class="header-title">
        <h1>LAPORAN HASIL BELAJAR PESERTA DIDIK</h1>
        <h2>SD TAHFIZH F3</h2>
    </div>

    <table class="meta-table">
        <tr>
            <td width="18%">Nama Santri/Siswa</td>
            <td width="32%">: <strong>{{ strtoupper($siswa->user->nama ?? $siswa->nama_panggilan) }}</strong></td>
            <td width="18%">Kelas Akademik</td>
            <td width="32%">: {{ $siswa->kelas->nama_kelas ?? 'V' }}</td>
        </tr>
        <tr>
            <td>NIS / NISN</td>
            <td>: {{ $siswa->nis }} / {{ $siswa->nisn }}</td>
            <td>Semester</td>
            <td>: {{ ucfirst($rapor->semester->semester ?? 'Ganjil') }}</td>
        </tr>
        <tr>
            <td>Sekolah</td>
            <td>: SD TAHFIZH F3</td>
            <td>Tahun Pelajaran</td>
            <td>: {{ $rapor->semester->tahunAjaran->nama ?? '2025/2026' }}</td>
        </tr>
    </table>

    <!-- SECTION UNGGULAN: PROGRAM UTAMA TAHFIZH AL-QUR'AN (CORE CURRICULUM) -->
    <div class="tahfidz-core-card">
        <div class="tahfidz-core-header">
            ★ PROGRAM UNGGULAN (CORE CURRICULUM): TAHFIZH AL-QUR'AN
        </div>
        <table width="100%" style="font-size: 9pt; border-collapse: collapse;">
            <tr>
                <td width="22%"><strong>Total Capaian Hafalan</strong></td>
                <td width="28%">: <span class="font-bold" style="color: #047857; font-size: 10pt;">{{ $tahfidzDetail->total_juz_dihafal ?? 1 }} Juz</span></td>
                <td width="20%"><strong>Predikat Tahfizh</strong></td>
                <td width="30%">: <span class="font-bold" style="color: #065f46;">{{ $tahfidzDetail->predikat_tahfidz ?? 'Sangat Baik (A)' }}</span></td>
            </tr>
            <tr>
                <td><strong>Daftar Surah Lulus</strong></td>
                <td colspan="3">: {{ $tahfidzDetail->daftar_surah_lulus ?? 'Juz 30 (An-Naba, An-Nazi\'at, \'Abasa, At-Takwir, Al-Infitar)' }}</td>
            </tr>
            @if(isset($nilaiTahfidzList) && count($nilaiTahfidzList) > 0)
                <tr>
                    <td><strong>Rata-rata Evaluasi</strong></td>
                    <td colspan="3">: Tajwid: <strong>{{ round($nilaiTahfidzList->avg('nilai_tajwid'), 1) }}</strong> | Kelancaran: <strong>{{ round($nilaiTahfidzList->avg('nilai_kelancaran'), 1) }}</strong> | Predikat Keagamaan: <strong>{{ $nilaiTahfidzList->first()->predikat_keagamaan ?? 'Sangat Baik' }}</strong></td>
                </tr>
            @endif
            <tr>
                <td style="padding-top: 4px;"><strong>Capaian Tahfizh</strong></td>
                <td colspan="3" style="padding-top: 4px; font-style: italic; color: #064e3b;">
                    "Alhamdulillah, Ananda telah menyelesaikan hafalan {{ $tahfidzDetail->total_juz_dihafal ?? 1 }} Juz dengan predikat {{ $tahfidzDetail->predikat_tahfidz ?? 'Sangat Baik' }}. Setoran hafalan mutqin dan bimbingan tajwid sangat fasih."
                </td>
            </tr>
        </table>
    </div>

    <!-- MATA PELAJARAN AKADEMIK (KURIKULUM MERDEKA) -->
    <div style="font-weight: bold; font-size: 9.5pt; margin-bottom: 4px; color: #1e293b;">
        Mata Pelajaran Kurikulum Merdeka (Akademik)
    </div>
    <table class="table-rapor">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Mata Pelajaran</th>
                <th width="12%">Nilai Akhir</th>
                <th width="58%">Capaian Kompetensi & Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($raporDetails as $index => $detail)
                <tr>
                    <td class="text-center font-bold">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $detail['mapel']['nama_mapel'] ?? '-' }}</td>
                    <td class="text-center font-bold" style="font-size: 10pt;">{{ round($detail['nilai_akhir']) }}</td>
                    <td style="font-size: 8.5pt; line-height: 1.25;">
                        @if(!empty($detail['narasi_capaian_full']))
                            <p style="margin: 0;">{{ $detail['narasi_capaian_full'] }}</p>
                        @elseif(!empty($detail['deskripsi_tertinggi']))
                            <p style="margin: 0 0 4px 0;">Ananda {{ strtolower($siswa->nama_panggilan ?? $siswa->user->nama ?? 'siswa') }} {{ $detail['deskripsi_tertinggi'] }}</p>
                            @if(!empty($detail['deskripsi_terendah']))
                                <p style="margin: 0;">Ananda {{ strtolower($siswa->nama_panggilan ?? $siswa->user->nama ?? 'siswa') }} {{ $detail['deskripsi_terendah'] }}</p>
                            @endif
                        @else
                            <p style="margin: 0;">Ananda menunjukkan penguasaan kompetensi mata pelajaran dengan baik.</p>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="color: #64748b; padding: 15px;">Belum ada data nilai mata pelajaran Kurikulum Merdeka yang diterbitkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- HALAMAN 2: KOKURIKULER P5, EKSTRAKURIKULER, ABSENSI, CATATAN WALI, KEPUTUSAN & TTD -->
    
    <!-- SECTION 1: KOKURIKULER P5 -->
    <div style="margin-bottom: 10px;">
        <div class="bg-green-header text-center">Kokurikuler (Projek Penguatan Profil Pelajar Pancasila)</div>
        <div class="box-border" style="line-height: 1.35;">
            <p style="margin: 0;">
                Ananda {{ strtolower($siswa->nama_panggilan ?? $siswa->user->nama ?? 'santri') }} menunjukkan keimanan dan ketakwaan yang sangat baik, berkepribadian mandiri dalam merapikan alat belajar serta rajin bergotong royong dalam kegiatan madrasah.
            </p>
        </div>
    </div>

    <!-- SECTION 2: EKSTRAKURIKULER -->
    <div style="margin-bottom: 10px;">
        <table class="table-rapor" style="margin-bottom: 0;">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="30%">Ekstrakurikuler</th>
                    <th width="65%">Keterangan & Capaian</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td class="font-bold">Pramuka / Hizbul Wathan</td>
                    <td>Sangat aktif dan disiplin dalam setiap latihan kepanduan.</td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td class="font-bold">Tahfidz Club / Murojaah</td>
                    <td>Telah menyelesaikan target murojaah Juz 30 dengan tartil dan lancar.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- SECTION 3: KETIDAKHADIRAN & CATATAN WALI KELAS SIDE-BY-SIDE -->
    <table class="flex-box">
        <tr>
            <td width="35%" style="padding-right: 6px;">
                <table class="table-rapor" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th colspan="2">Ketidakhadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Sakit</td>
                            <td class="text-center font-bold">0 hari</td>
                        </tr>
                        <tr>
                            <td>Izin</td>
                            <td class="text-center font-bold">1 hari</td>
                        </tr>
                        <tr>
                            <td>Tanpa Keterangan</td>
                            <td class="text-center font-bold">0 hari</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="65%" style="padding-left: 6px;">
                <div class="bg-green-header">Catatan Wali Kelas</div>
                <div class="box-border" style="min-height: 52px; line-height: 1.35;">
                    {{ $rapor->catatan_wali_kelas ?: 'Ananda telah menunjukkan perkembangan yang sangat memuaskan baik dalam bidang akademis maupun hafalan Al-Qur\'an. Pertahankan ketekunan ini.' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- SECTION 4: TANGGAPAN ORANG TUA & KEPUTUSAN SIDE-BY-SIDE -->
    <table class="flex-box">
        <tr>
            <td width="35%" style="padding-right: 6px;">
                <div class="bg-green-header">Tanggapan Orang Tua / Wali</div>
                <div class="box-border" style="min-height: 50px;"></div>
            </td>
            <td width="65%" style="padding-left: 6px;">
                <div class="bg-green-header">Keputusan Kenaikan Kelas :</div>
                <div class="box-border" style="font-size: 8.5pt; min-height: 50px;">
                    <p style="margin: 0 0 4px 0;">Berdasarkan pencapaian seluruh kompetensi akademik & target Tahfizh, dinyatakan :</p>
                    <p style="margin: 0; font-size: 9.5pt;" class="font-bold">
                        <span style="display: inline-block; width: 140px;">Naik / <span class="strike">Tinggal</span> *) kelas</span>
                        <span style="font-size: 10pt; float: right; margin-right: 15px;">VI (ENAM)</span>
                    </p>
                    <p style="margin: 4px 0 0 0; font-size: 7.5pt; color: #475569;">*)coret yang tidak perlu</p>
                </div>
            </td>
        </tr>
    </table>

    <!-- SECTION 5: SIGNATURE BLOCK -->
    <div style="margin-top: 15px; font-size: 9pt;">
        <table width="100%">
            <tr>
                <td width="40%" class="text-center">
                    <br>
                    Orang Tua / Wali,<br><br><br><br>
                    <strong>( ..................................... )</strong>
                </td>
                <td width="20%"></td>
                <td width="40%" class="text-center">
                    Pekanbaru, {{ date('d F Y', strtotime($rapor->tanggal_terbit ?? date('Y-m-d'))) }}<br>
                    Wali Kelas<br><br><br><br>
                    <strong>NURUL MINA, S.Pd., Gr</strong><br>
                    <span style="font-size: 8pt;">NIY: 200003152202211000</span>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="text-center" style="padding-top: 10px;">
                    Mengetahui,<br>
                    Kepala Sekolah SD Tahfizh F3<br><br><br><br>
                    <strong>Dr. H. M. Yusuf, M.A.</strong><br>
                    <span style="font-size: 8pt;">NIY: 198010052201907001</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
