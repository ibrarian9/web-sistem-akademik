<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Tanda Tangan Elektronik - {{ $code }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            width: 100%;
            max-width: 580px;
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 40px 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        .icon-badge {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .icon-badge.valid {
            background: rgba(16, 185, 129, 0.15);
            border: 2px solid #10b981;
            color: #10b981;
        }
        .icon-badge.invalid {
            background: rgba(239, 68, 68, 0.15);
            border: 2px solid #ef4444;
            color: #ef4444;
        }
        .status-title {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }
        .status-title.valid { color: #34d399; }
        .status-title.invalid { color: #f87171; }
        .subtitle {
            font-size: 14px;
            color: #94a3b8;
            margin-bottom: 28px;
        }
        .info-card {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 20px;
            text-align: left;
            margin-bottom: 28px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 13px;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #94a3b8;
            font-weight: 500;
        }
        .info-val {
            color: #f8fafc;
            font-weight: 700;
            text-align: right;
        }
        .code-badge {
            display: inline-block;
            font-family: monospace;
            background: rgba(99, 102, 241, 0.2);
            color: #818cf8;
            border: 1px solid rgba(99, 102, 241, 0.4);
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
        }
        .cert-seal {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px dashed rgba(255, 255, 255, 0.15);
            font-size: 11px;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .cert-seal svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }
    </style>
</head>
<body>

    <div class="container">
        @if ($isValid)
            <div class="icon-badge valid">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <h1 class="status-title valid">DOKUMEN SAH & TERVERIFIKASI</h1>
            <p class="subtitle">Tanda Tangan Elektronik Asli Terdaftar Pada Sistem Akademik</p>
        @else
            <div class="icon-badge invalid">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
            </div>
            <h1 class="status-title invalid">DOKUMEN TIDAK DITEMUKAN</h1>
            <p class="subtitle">Kode Tanda Tangan Elektronik Tidak Valid / Tidak Terdaftar</p>
        @endif

        <div class="info-card">
            <div class="info-row">
                <span class="info-label">Kode Verifikasi</span>
                <span class="info-val"><span class="code-badge">{{ $code }}</span></span>
            </div>
            <div class="info-row">
                <span class="info-label">Jenis Dokumen</span>
                <span class="info-val">{{ $docType }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">ID / Ref Dokumen</span>
                <span class="info-val">#{{ $docId }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Penerbit / Instansi</span>
                <span class="info-val">{{ $institution }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Penandatangan Resmi</span>
                <span class="info-val">{{ $signerName }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Jabatan</span>
                <span class="info-val">{{ $signerRole }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status Keabsahan</span>
                <span class="info-val" style="color: {{ $isValid ? '#34d399' : '#f87171' }};">
                    {{ $isValid ? 'TERVERIFIKASI & SAH DEMI HUKUM' : 'TIDAK VALID' }}
                </span>
            </div>
        </div>

        <div class="cert-seal">
            <svg viewBox="0 0 24 24">
                <path d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5zm-2 16l-4-4 1.41-1.41L10 15.17l6.59-6.59L18 10l-8 8z"/>
            </svg>
            <span>Verifikasi Keamanan Digital Sistem Informasi Akademik & Keuangan Yayasan</span>
        </div>
    </div>

</body>
</html>
