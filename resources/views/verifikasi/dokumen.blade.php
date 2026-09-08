<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Keabsahan Dokumen Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-lg sm:max-w-xl w-full bg-slate-800 border border-slate-700 rounded-2xl shadow-2xl p-6 sm:p-8 relative overflow-hidden">
        <!-- Accent Glow -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-emerald-600/20 rounded-full blur-3xl"></div>

        @if($isValid)
            <!-- Verified Header -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-500/10 border border-emerald-500/30 rounded-full text-emerald-400 mb-4 animate-pulse">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-emerald-500/15 border border-emerald-500/30 rounded-full text-emerald-300 text-xs font-bold uppercase tracking-wide mb-2 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                        <span>DOKUMEN RESMI SAH & TERVERIFIKASI</span>
                    </div>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-white mt-2">Portal Pengesahan Digital</h1>
                <p class="text-slate-400 text-xs sm:text-sm mt-1 max-w-sm mx-auto leading-relaxed">Keabsahan dokumen ini telah divalidasi langsung oleh sistem informasi akademik resmi sekolah.</p>
            </div>

            <!-- Details Card -->
            <div class="bg-slate-900/80 border border-slate-700/60 rounded-xl p-4 sm:p-5 space-y-3 text-xs sm:text-sm mb-6">
                @if(!empty($nomorSurat))
                <div class="flex justify-between items-center border-b border-slate-800 pb-2.5 gap-4">
                    <span class="text-slate-400 font-medium shrink-0">Nomor Surat</span>
                    <span class="font-bold text-emerald-400 text-right font-mono">{{ $nomorSurat }}</span>
                </div>
                @endif
                <div class="flex justify-between items-center border-b border-slate-800 pb-2.5 gap-4">
                    <span class="text-slate-400 font-medium shrink-0">Jenis Dokumen</span>
                    <span class="font-semibold text-emerald-400 text-right">{{ $jenisDokumen }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-slate-800 pb-2.5 gap-4">
                    <span class="text-slate-400 font-medium shrink-0">{{ !empty($nomorSurat) ? 'Nama Penerima' : 'Nama Siswa' }}</span>
                    <span class="font-bold text-white text-right uppercase tracking-wide">{{ $namaSiswa }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-slate-800 pb-2.5 gap-4">
                    <span class="text-slate-400 font-medium shrink-0">{{ !empty($nomorSurat) ? 'NISN / NIY / NIK' : 'NISN' }}</span>
                    <span class="font-semibold text-slate-300 text-right">{{ !empty($nisn) && $nisn !== '-' ? $nisn : '-' }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-slate-800 pb-2.5 gap-4">
                    <span class="text-slate-400 font-medium shrink-0">{{ !empty($nomorSurat) ? 'Keterangan / Posisi' : 'Kelas / TA' }}</span>
                    <span class="font-semibold text-slate-300 text-right">{{ $kelas }} @if(!empty($tahunAjaran) && $tahunAjaran !== '-') <span class="text-slate-500 font-normal">({{ $tahunAjaran }})</span> @endif</span>
                </div>
                <div class="flex justify-between items-center border-b border-slate-800 pb-2.5 gap-4">
                    <span class="text-slate-400 font-medium shrink-0">Tanggal Terbit</span>
                    <span class="font-semibold text-slate-300 text-right">{{ $tanggalTerbit }}</span>
                </div>
                <div class="flex justify-between items-start pt-1 gap-4">
                    <span class="text-slate-400 font-medium shrink-0 pt-0.5">Pengesah Resmi</span>
                    <div class="text-right max-w-xs">
                        <span class="font-bold text-emerald-300 block text-xs sm:text-sm">{{ $pejabatNama ?? $pejabatPengesah }}</span>
                        @if(!empty($pejabatJabatan))
                            <span class="text-[11px] sm:text-xs text-slate-400 font-medium block mt-0.5">{{ $pejabatJabatan }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Hash Code -->
            <div class="bg-slate-950 p-3 rounded-lg border border-slate-800 text-center">
                <span class="text-xs text-slate-500 uppercase tracking-wider block mb-1">Kode Hash Digital Dokumen</span>
                <code class="text-xs text-emerald-400 font-mono break-all">{{ $uuid }}</code>
            </div>
        @else
            <!-- Invalid Warning Header -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-rose-500/10 border border-rose-500/30 rounded-full text-rose-400 mb-4">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <span class="px-3 py-1 bg-rose-500/20 text-rose-300 text-xs font-bold uppercase tracking-widest rounded-full border border-rose-500/30">
                    Dokumen Tidak Valid / Palsu
                </span>
                <h1 class="text-xl font-bold text-white mt-3">Verifikasi Gagal</h1>
                <p class="text-slate-400 text-sm mt-2">Kode seri QR Code ini tidak terdaftar dalam database sistem resmi yayasan. Harap waspada terhadap pemalsuan dokumen.</p>
            </div>

            <div class="bg-slate-950 p-3 rounded-lg border border-slate-800 text-center mb-4">
                <span class="text-xs text-slate-500 uppercase tracking-wider block mb-1">Kode Hash Yang Diperiksa</span>
                <code class="text-xs text-rose-400 font-mono break-all">{{ $uuid }}</code>
            </div>
        @endif

        <div class="mt-6 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} Sistem Informasi Akademik & Keuangan Yayasan. All rights reserved.
        </div>
    </div>
</body>
</html>
