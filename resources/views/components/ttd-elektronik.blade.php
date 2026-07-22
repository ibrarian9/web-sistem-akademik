@props([
    'role' => 'kepala_sekolah',
    'docType' => 'DOK',
    'docId' => 1,
    'user' => null,
    'tanggal' => null,
    'location' => 'Sleman',
    'showLocation' => true,
    'title' => null
])

@php
    $sigData = \App\Services\ESignatureService::getSignatureData(
        $role,
        $docType,
        $docId,
        $user,
        $tanggal ?: date('d-m-Y')
    );
    $locationStr = $showLocation ? ($location . ', ' . $sigData['tanggal']) : '';
@endphp

<div class="ttd-box" style="text-align: center; font-size: 10px; color: #1e293b; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; display: inline-block; width: 100%;">
    <!-- Baris Tanggal/Lokasi dengan Tinggi Tetap untuk Penjajaran Horisontal 100% Presisi -->
    <div style="height: 15px; line-height: 15px; margin-bottom: 3px; font-size: 9.5px; color: #334155; text-align: center;">
        {!! !empty($locationStr) ? e($locationStr) : '&nbsp;' !!}
    </div>

    <!-- Jabatan Pejabat Penandatangan -->
    <div style="font-weight: bold; height: 15px; line-height: 15px; margin-bottom: 5px; color: #0f172a; font-size: 10px;">
        {{ $title ?: $sigData['jabatan'] }}
    </div>
    
    <!-- Kotak TTD Elektronik & QR Code (Besar & Jelas) -->
    <div style="margin: 3px auto 6px auto; padding: 6px; border: 1.5px dashed #047857; background-color: #f0fdf4; border-radius: 8px; width: 220px; text-align: center; box-sizing: border-box;">
        <table style="width: 100%; border-collapse: collapse; margin: 0 auto;">
            <tr>
                <!-- QR Code (Diperbesar 64px x 64px) -->
                <td style="width: 68px; text-align: center; vertical-align: middle; padding-right: 4px;">
                    <img src="{{ $sigData['qr_code'] }}" style="width: 64px; height: 64px; display: block; margin: 0 auto;" />
                </td>

                <!-- Gambar TTD Manual (Jika Ada) + Lencana Verifikasi Elektronik -->
                <td style="text-align: left; vertical-align: middle; padding-left: 4px;">
                    @if(!empty($sigData['ttd_image']) && file_exists(public_path($sigData['ttd_image'])))
                        <div style="margin-bottom: 3px;">
                            <img src="{{ public_path($sigData['ttd_image']) }}" style="height: 36px; max-width: 120px; object-fit: contain; display: block;" />
                        </div>
                    @endif
                    <div style="font-size: 7.5px; font-weight: bold; color: #047857; text-transform: uppercase; letter-spacing: 0.5px;">TTD ELEKTRONIK</div>
                    <div style="font-size: 7px; color: #0f172a; font-weight: bold; font-family: monospace; margin: 1px 0;">{{ $sigData['code'] }}</div>
                    <div style="font-size: 6.5px; color: #059669; font-weight: bold;">Dokumen Sah Terverifikasi</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Nama Penandatangan -->
    <div style="font-weight: bold; text-decoration: underline; color: #0f172a; font-size: 10.5px; height: 15px; line-height: 15px;">
        {{ $sigData['nama'] }}
    </div>

    <!-- NIP Penandatangan (Tinggi Tetap Presisi) -->
    <div style="font-size: 9px; color: #475569; height: 14px; line-height: 14px; margin-top: 1px;">
        @if(!empty($sigData['nip']) && $sigData['nip'] !== '-')
            NIP: {{ $sigData['nip'] }}
        @else
            &nbsp;
        @endif
    </div>
</div>
