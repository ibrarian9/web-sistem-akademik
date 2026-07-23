@props([
    'role' => 'kepala_sekolah',
    'docType' => 'DOK',
    'docId' => 1,
    'user' => null,
    'tanggal' => null,
    'location' => 'Sleman',
    'showLocation' => true,
    'title' => null,
])

@php
    $sigData = \App\Services\ESignatureService::getSignatureData(
        $role,
        $docType,
        $docId,
        $user,
        $tanggal ?: date('d-m-Y')
    );
@endphp

<!-- Kotak QR Code Besar Verifikasi Publik Resmi Dokumen -->
<div style="margin: 25px auto 10px auto; text-align: center; width: 100%; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    <div style="display: inline-block; padding: 16px 20px; border: 2.5px solid #059669; background-color: #f0fdf4; border-radius: 14px; width: 360px; max-width: 90%; text-align: center; box-sizing: border-box; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        
        <!-- Tanggal & Lokasi Penetapan -->
        <div style="font-size: 9px; color: #475569; font-weight: bold; margin-bottom: 8px;">
            {{ $location }}, {{ $sigData['tanggal'] }}
        </div>

        <!-- Penandatangan Resmi -->
        <div style="font-size: 10px; font-weight: bold; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px;">
            {{ $sigData['jabatan'] }}
        </div>

        <!-- QR Code Besar Publik (140px x 140px) -->
        <div style="margin: 10px auto; text-align: center;">
            <img src="{{ $sigData['qr_code'] }}" style="width: 140px; height: 140px; display: inline-block; border: 1px solid #cbd5e1; border-radius: 8px; padding: 4px; background-color: #ffffff;" alt="QR Code Verifikasi Dokumen" />
        </div>
        
        <!-- Status Keabsahan Dokumen -->
        <div style="font-size: 11px; font-weight: 800; color: #047857; text-transform: uppercase; letter-spacing: 0.6px; margin-top: 4px;">
            ✓ DOKUMEN RESMI SAH &amp; TERVERIFIKASI
        </div>

        <div style="font-size: 10.5px; font-weight: bold; color: #0f172a; text-decoration: underline; margin-top: 4px;">
            {{ $sigData['nama'] }}
        </div>
        @if ($sigData['nip'] && $sigData['nip'] !== '-')
            <div style="font-size: 8.5px; color: #475569; font-weight: bold; margin-top: 1px;">
                NIP: {{ $sigData['nip'] }}
            </div>
        @endif

        <!-- Kode Verifikasi Unik -->
        <div style="font-size: 9px; color: #0f172a; font-weight: bold; font-family: monospace; background-color: #dcfce7; border: 1px solid #86efac; border-radius: 6px; padding: 3px 8px; margin: 8px auto 6px auto; display: inline-block;">
            KODE VERIFIKASI: {{ $sigData['code'] }}
        </div>

        <!-- Petunjuk Scan QR Code Publik -->
        <div style="font-size: 8px; color: #047857; font-weight: bold; line-height: 1.35; margin-top: 4px;">
            Scan QR Code ini untuk memverifikasi keaslian &amp; keabsahan dokumen secara langsung di website tanpa perlu login.
        </div>
    </div>
</div>
