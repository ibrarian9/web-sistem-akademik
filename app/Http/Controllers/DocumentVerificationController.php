<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pengaturan;
use App\Models\Rapor;
use App\Models\RiwayatSurat;
use App\Services\ESignatureService;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class DocumentVerificationController extends Controller
{
    /**
     * Display public document verification page for scanned QR Code UUID/Hash or ESignature Code.
     */
    public function verify(string $uuid)
    {
        // 1. Check if UUID matches a Rapor record in DB
        $rapor = Rapor::where('qr_code_hash', $uuid)->first();
        if ($rapor) {
            $siswa = $rapor->siswa;
            $kelas = $rapor->kelas;
            $semester = $rapor->semester;

            AuditLogger::log('verify', "Verifikasi QR Code Dokumen Berhasil: Rapor Digital (UUID: {$uuid})", $rapor, [
                'log_name' => 'keamanan',
                'siswa_id' => $siswa?->id,
                'properties' => ['uuid' => $uuid, 'is_valid' => true, 'jenis' => 'rapor'],
            ]);

            return view('verifikasi.dokumen', [
                'isValid' => true,
                'jenisDokumen' => 'Rapor Hasil Belajar Digital (Kurikulum Merdeka & Tahfizh)',
                'namaSiswa' => $siswa ? ($siswa->user->nama ?? $siswa->nama_panggilan) : 'Siswa',
                'nisn' => $siswa ? $siswa->nisn : '-',
                'kelas' => $kelas ? $kelas->nama_kelas : '-',
                'tahunAjaran' => $semester ? ($semester->tahunAjaran->nama ?? '-') : '-',
                'tanggalTerbit' => $rapor->tanggal_terbit ? date('d F Y', strtotime($rapor->tanggal_terbit)) : date('d F Y'),
                'pejabatPengesah' => ($kelas && $kelas->guruUmum) ? ($kelas->guruUmum->user->nama ?? 'Wali Kelas') : 'Kepala Sekolah',
                'uuid' => $uuid,
            ]);
        }

        // 2. Check if UUID matches a Pembayaran/Resi record in DB
        $pembayaran = Pembayaran::where('qr_code_hash', $uuid)->first();
        if ($pembayaran && !$pembayaran->is_void) {
            $tagihan = $pembayaran->tagihan;
            $siswa = $tagihan ? $tagihan->siswa : null;

            AuditLogger::log('verify', "Verifikasi QR Code Dokumen Berhasil: Resi Pembayaran #{$pembayaran->no_resi}", $pembayaran, [
                'log_name' => 'keamanan',
                'siswa_id' => $siswa?->id,
                'properties' => ['uuid' => $uuid, 'is_valid' => true, 'no_resi' => $pembayaran->no_resi],
            ]);

            return view('verifikasi.dokumen', [
                'isValid' => true,
                'jenisDokumen' => 'Resi Bukti Pembayaran Resmi (STT Keuangan)',
                'namaSiswa' => $siswa ? ($siswa->user->nama ?? $siswa->nama_panggilan) : 'Siswa',
                'nisn' => $siswa ? $siswa->nisn : '-',
                'kelas' => $siswa && $siswa->kelas ? $siswa->kelas->nama_kelas : '-',
                'tahunAjaran' => $tagihan && $tagihan->tahunAjaran ? $tagihan->tahunAjaran->nama : '-',
                'tanggalTerbit' => date('d F Y', strtotime($pembayaran->tanggal_bayar)),
                'pejabatPengesah' => $pembayaran->petugas ? $pembayaran->petugas->nama : 'Staf Keuangan Yayasan',
                'uuid' => $uuid,
            ]);
        }

        // 3. Fallback check for ESignatureService code format (e.g. TTD-SUR-15-..., TTD-RES-99-..., TTD-RAP-1-...)
        if (str_starts_with($uuid, 'TTD-')) {
            $parts = explode('-', $uuid);
            $typePrefix = $parts[1] ?? 'DOC';
            $docId = $parts[2] ?? '0';

            if ($typePrefix === 'SUR') {
                $surat = RiwayatSurat::find($docId);
                if ($surat) {
                    $payload = $surat->payload_json ?? [];
                    $jenisMap = [
                        'aktif_sekolah' => 'Surat Keterangan Aktif Sekolah',
                        'pengalaman_kerja' => 'Surat Keterangan Pengalaman Kerja',
                        'menerima_pindah' => 'Surat Keterangan Menerima Siswa Pindah',
                        'pindah_sekolah' => 'Surat Keterangan Pindah Sekolah',
                    ];

                    $jenisLabel = $jenisMap[$surat->jenis_surat] ?? 'Surat Keterangan Resmi Sekolah';
                    
                    $identitasPenerima = !empty($payload['penerima_nisn'])
                        ? $payload['penerima_nisn']
                        : (!empty($payload['penerima_niy'])
                            ? $payload['penerima_niy']
                            : (!empty($payload['penerima_nik'])
                                ? $payload['penerima_nik']
                                : (!empty($payload['penerima_nis'])
                                    ? $payload['penerima_nis']
                                    : '-')));

                    if ($surat->jenis_surat === 'pengalaman_kerja') {
                        $posisiKelas = !empty($payload['posisi_kerja']) ? $payload['posisi_kerja'] : 'Guru / Tenaga Pengajar';
                    } else {
                        $posisiKelas = !empty($payload['penerima_kelas']) ? 'Kelas ' . $payload['penerima_kelas'] : 'Siswa Aktif';
                    }

                    $pejabatNama = $payload['penandatangan_nama'] ?? Pengaturan::getValue('kepala_sekolah_nama', 'Kepala Sekolah');
                    $pejabatJabatan = $payload['penandatangan_jabatan'] ?? Pengaturan::getValue('kepala_sekolah_jabatan', 'Kepala Sekolah / Madrasah');
                    $penandatangan = "{$pejabatNama} ({$pejabatJabatan})";

                    return view('verifikasi.dokumen', [
                        'isValid' => true,
                        'nomorSurat' => $surat->nomor_surat,
                        'jenisDokumen' => $jenisLabel,
                        'namaSiswa' => $surat->penerima_nama,
                        'nisn' => $identitasPenerima,
                        'kelas' => $posisiKelas,
                        'tahunAjaran' => date('Y', strtotime($surat->tanggal_surat ?? 'now')),
                        'tanggalTerbit' => $surat->tanggal_surat ? $surat->tanggal_surat->format('d F Y') : date('d F Y'),
                        'pejabatNama' => $pejabatNama,
                        'pejabatJabatan' => $pejabatJabatan,
                        'pejabatPengesah' => $penandatangan,
                        'uuid' => $uuid,
                    ]);
                }
            } elseif ($typePrefix === 'RES') {
                $pembayaranDoc = Pembayaran::with(['tagihan.siswa.user', 'tagihan.siswa.kelas', 'tagihan.tahunAjaran', 'petugas'])->find($docId);
                if ($pembayaranDoc && !$pembayaranDoc->is_void) {
                    $tagihan = $pembayaranDoc->tagihan;
                    $siswa = $tagihan ? $tagihan->siswa : null;

                    return view('verifikasi.dokumen', [
                        'isValid' => true,
                        'jenisDokumen' => 'Resi Bukti Pembayaran Resmi (STT Keuangan)',
                        'namaSiswa' => $siswa ? ($siswa->user->nama ?? $siswa->nama_panggilan) : 'Siswa',
                        'nisn' => $siswa ? $siswa->nisn : '-',
                        'kelas' => $siswa && $siswa->kelas ? $siswa->kelas->nama_kelas : '-',
                        'tahunAjaran' => $tagihan && $tagihan->tahunAjaran ? $tagihan->tahunAjaran->nama : '-',
                        'tanggalTerbit' => date('d F Y', strtotime($pembayaranDoc->tanggal_bayar)),
                        'pejabatPengesah' => $pembayaranDoc->petugas ? $pembayaranDoc->petugas->nama : 'Staf Keuangan Yayasan',
                        'uuid' => $uuid,
                    ]);
                }
            } elseif ($typePrefix === 'RAP') {
                $raporDoc = Rapor::with(['siswa.user', 'kelas', 'semester.tahunAjaran'])->find($docId);
                if ($raporDoc) {
                    $siswa = $raporDoc->siswa;
                    $kelas = $raporDoc->kelas;
                    $semester = $raporDoc->semester;

                    return view('verifikasi.dokumen', [
                        'isValid' => true,
                        'jenisDokumen' => 'Rapor Hasil Belajar Digital (Kurikulum Merdeka & Tahfizh)',
                        'namaSiswa' => $siswa ? ($siswa->user->nama ?? $siswa->nama_panggilan) : 'Siswa',
                        'nisn' => $siswa ? $siswa->nisn : '-',
                        'kelas' => $kelas ? $kelas->nama_kelas : '-',
                        'tahunAjaran' => $semester ? ($semester->tahunAjaran->nama ?? '-') : '-',
                        'tanggalTerbit' => $raporDoc->tanggal_terbit ? date('d F Y', strtotime($raporDoc->tanggal_terbit)) : date('d F Y'),
                        'pejabatPengesah' => ($kelas && $kelas->guruUmum) ? ($kelas->guruUmum->user->nama ?? 'Wali Kelas') : 'Kepala Sekolah',
                        'uuid' => $uuid,
                    ]);
                }
            }
        }

        // Invalid, Voided, or Unknown Document
        AuditLogger::log('verify', "Verifikasi QR Code Dokumen Gagal / Tidak Valid (UUID: {$uuid})", null, [
            'log_name' => 'keamanan',
            'properties' => ['uuid' => $uuid, 'is_valid' => false],
        ]);

        return view('verifikasi.dokumen', [
            'isValid' => false,
            'uuid' => $uuid,
        ]);
    }
}
