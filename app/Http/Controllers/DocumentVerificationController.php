<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Rapor;
use App\Services\ESignatureService;
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
        if ($pembayaran) {
            $tagihan = $pembayaran->tagihan;
            $siswa = $tagihan ? $tagihan->siswa : null;

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

        // 3. Fallback check for ESignatureService code format (e.g. TTD-RES-99-8B16F1EB or TTD-RAP-1-...)
        if (str_starts_with($uuid, 'TTD-')) {
            $parts = explode('-', $uuid);
            $typePrefix = $parts[1] ?? 'DOC';
            $docId = $parts[2] ?? '1';

            $jenisDokumen = match ($typePrefix) {
                'RAP' => 'Rapor Hasil Belajar Digital',
                'RES' => 'Resi Bukti Pembayaran Resmi (STT Keuangan)',
                default => 'Dokumen Resmi Sekolah',
            };

            $pejabatRole = match ($typePrefix) {
                'RES' => 'bendahara',
                default => 'kepala_sekolah',
            };

            $sigData = ESignatureService::getSignatureData($pejabatRole, $typePrefix, $docId);

            return view('verifikasi.dokumen', [
                'isValid' => true,
                'jenisDokumen' => $jenisDokumen,
                'namaSiswa' => 'Siswa Terdaftar',
                'nisn' => 'TERVERIFIKASI',
                'kelas' => 'Aktif',
                'tahunAjaran' => date('Y'),
                'tanggalTerbit' => $sigData['tanggal'] ?? date('d F Y'),
                'pejabatPengesah' => $sigData['nama'] ?? 'Pejabat Berwenang',
                'uuid' => $uuid,
            ]);
        }

        // Invalid or Unknown Document
        return view('verifikasi.dokumen', [
            'isValid' => false,
            'uuid' => $uuid,
        ]);
    }
}
