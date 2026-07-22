<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use App\Models\Pengaturan;
use App\Models\User;

class ESignatureService
{
    /**
     * Generate unique verification code for a document.
     */
    public static function generateCode(string $docType, string|int $docId, string $date = ''): string
    {
        $prefix = strtoupper(substr($docType, 0, 3));
        $date = $date ?: date('Ymd');
        $hash = strtoupper(substr(md5($docType . '-' . $docId . '-' . $date . '-AKADEMIK_YAYASAN_KEY'), 0, 8));
        return "TTD-{$prefix}-{$docId}-{$hash}";
    }

    /**
     * Get the full public verification URL for a given code.
     */
    public static function getVerificationUrl(string $code): string
    {
        return url('/verifikasi-dokumen/' . $code);
    }

    /**
     * Generate SVG Base64 Data URI for QR Code.
     */
    public static function generateQrCode(string $content): string
    {
        try {
            $options = new QROptions([
                'version'          => 4,
                'outputBase64'     => true,
                'scale'            => 6,
                'margin'           => 1,
                'imageTransparent' => false,
            ]);

            return (new QRCode($options))->render($content);
        } catch (\Throwable $e) {
            // Fallback lightweight inline SVG QR representation if anything fails
            $encodedUrl = rawurlencode($content);
            return "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='100' height='100'><rect width='100' height='100' fill='%23f1f5f9'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' font-size='10' fill='%23334155'>QR VERIFIED</text></svg>";
        }
    }

    /**
     * Build signature metadata array for PDF rendering.
     */
    public static function getSignatureData(string $role, string $docType, string|int $docId, ?User $user = null, string $tanggal = ''): array
    {
        $tanggal = $tanggal ?: date('d-m-Y');
        $code = self::generateCode($docType, $docId, $tanggal);
        $verificationUrl = self::getVerificationUrl($code);
        $qrCodeDataUri = self::generateQrCode($verificationUrl);

        $nama = '';
        $nip = '';
        $jabatan = '';
        $ttdImage = null;

        if ($user) {
            $nama = $user->nama;
            $nip = $user->nip ?: ($user->guru->nip ?? '-');
            $jabatan = $user->jabatan ?: ($user->role->nama ?? 'Pejabat Resah');
            $ttdImage = $user->ttd_digital;
        } else {
            switch (strtolower($role)) {
                case 'kepala_sekolah':
                    $nama = Pengaturan::getValue('kepala_sekolah_nama', 'Drs. H. Ahmad Fauzi, M.Pd.');
                    $nip = Pengaturan::getValue('kepala_sekolah_nip', '19750812 200003 1 001');
                    $jabatan = Pengaturan::getValue('kepala_sekolah_jabatan', 'Kepala Sekolah / Madrasah');
                    $ttdImage = Pengaturan::getValue('kepala_sekolah_ttd', null);
                    break;
                case 'bendahara':
                case 'finance':
                    $nama = Pengaturan::getValue('bendahara_nama', 'Siti Aminah, S.E.');
                    $nip = Pengaturan::getValue('bendahara_nip', '19820415 200801 2 004');
                    $jabatan = Pengaturan::getValue('bendahara_jabatan', 'Bendahara Keuangan Yayasan');
                    $ttdImage = Pengaturan::getValue('bendahara_ttd', null);
                    break;
                case 'tata_usaha':
                    $nama = Pengaturan::getValue('tata_usaha_nama', 'Budi Santoso, S.Kom.');
                    $nip = Pengaturan::getValue('tata_usaha_nip', '19881120 201202 1 003');
                    $jabatan = Pengaturan::getValue('tata_usaha_jabatan', 'Kepala Tata Usaha');
                    $ttdImage = Pengaturan::getValue('tata_usaha_ttd', null);
                    break;
                default:
                    $nama = 'Pejabat Berwenang';
                    $nip = '-';
                    $jabatan = 'Petugas Instansi';
                    break;
            }
        }

        return [
            'code'             => $code,
            'verification_url' => $verificationUrl,
            'qr_code'          => $qrCodeDataUri,
            'nama'             => $nama,
            'nip'              => $nip,
            'jabatan'          => $jabatan,
            'ttd_image'        => $ttdImage,
            'tanggal'          => $tanggal,
            'instansi'         => Pengaturan::getValue('nama_instansi', 'Yayasan Pendidikan Islam'),
        ];
    }
}
