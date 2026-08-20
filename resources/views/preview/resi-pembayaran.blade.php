<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pratinjau Kuitansi Pembayaran - {{ $pembayaran->no_resi ?? ('#REC-' . $pembayaran->id) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .print-card {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-stone-950/80 backdrop-blur-md min-h-screen text-stone-900 font-sans antialiased p-4 sm:p-8 flex flex-col items-center justify-center">

    <!-- STICKY TOP ACTION TOOLBAR (Hidden on Print) -->
    <header class="no-print sticky top-4 z-50 w-full max-w-3xl mb-4 bg-stone-900/95 backdrop-blur-md text-white p-3 sm:p-4 rounded-3xl shadow-2xl border border-stone-700 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-emerald-600 text-white rounded-2xl shadow-md">
                <x-lucide-receipt class="w-5 h-5" />
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xs sm:text-sm font-extrabold block">Pratinjau Kuitansi Pembayaran</h1>
                    <span class="px-2 py-0.5 text-[10px] font-extrabold bg-emerald-600/30 text-emerald-400 border border-emerald-500/40 rounded-full">FLOATING CARD</span>
                </div>
                <span class="text-[11px] text-stone-400 font-mono">No. Resi: {{ $pembayaran->no_resi ?? ('REC-' . str_pad($pembayaran->id, 5, '0', STR_PAD_LEFT)) }}</span>
            </div>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto justify-end flex-wrap">
            <button 
                type="button" 
                onclick="window.print()" 
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-emerald-900/40 cursor-pointer">
                <x-lucide-printer class="w-4 h-4" />
                <span>Cetak Kuitansi</span>
            </button>

            <a 
                href="{{ request()->fullUrlWithQuery(['download' => '1']) }}" 
                class="px-3.5 py-2 bg-stone-800 hover:bg-stone-700 text-stone-200 border border-stone-600 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                <x-lucide-download class="w-4 h-4" />
                <span>Unduh PDF</span>
            </a>

            <button 
                type="button" 
                onclick="window.close() || history.back()" 
                class="px-3.5 py-2 bg-stone-800 hover:bg-stone-700 text-stone-300 rounded-xl text-xs font-semibold transition cursor-pointer">
                Tutup
            </button>
        </div>
    </header>

    <!-- OFFICIAL RECEIPT PAPER CARD (FLOATING CARD) -->
    <main class="print-card w-full max-w-3xl bg-white border border-stone-300 rounded-3xl shadow-2xl p-8 sm:p-12 space-y-8 relative overflow-hidden my-auto">
        
        <!-- Watermark LUNAS -->
        <div class="absolute right-12 top-28 opacity-10 pointer-events-none select-none font-black text-7xl text-emerald-800 rotate-[-15deg] uppercase tracking-widest border-8 border-emerald-800 px-6 py-2 rounded-3xl">
            LUNAS
        </div>

        <!-- KOP SURAT YAYASAN -->
        <div class="border-b-2 border-stone-900 pb-5 text-center space-y-1">
            <h2 class="text-xl sm:text-2xl font-black uppercase tracking-wider text-stone-900">YAYASAN PENDIDIKAN ISLAM AL-IKHLAS</h2>
            <p class="text-xs sm:text-sm font-bold text-emerald-800 uppercase tracking-tight">Sistem Informasi Administrasi &amp; Manajemen Keuangan Sekolah</p>
            <p class="text-[11px] text-stone-500 font-medium">Jl. Pesantren No. 45, Kompleks Pendidikan Islam Terpadu | Telp: (021) 789-0123 | Email: keuangan@f3.sch.id</p>
        </div>

        <!-- TITLE & RECEIPT NUMBER -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-stone-200 pb-4">
            <div>
                <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-widest bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200 inline-block mb-1">
                    BUKTI SETORAN SAH
                </span>
                <h3 class="text-lg font-black text-stone-900 uppercase">KUITANSI PEMBAYARAN SISWA</h3>
            </div>

            <div class="text-left sm:text-right font-mono text-xs">
                <div class="text-stone-500">Nomor Resi: <strong class="text-stone-900">{{ $pembayaran->no_resi ?? ('REC-' . str_pad($pembayaran->id, 5, '0', STR_PAD_LEFT)) }}</strong></div>
                <div class="text-stone-500">Tanggal: <strong class="text-stone-900">{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->translatedFormat('d F Y') }}</strong></div>
            </div>
        </div>

        <!-- STUDENT & INVOICE DETAILS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            <!-- Left: Student Info -->
            <div class="bg-stone-50 border border-stone-200 rounded-2xl p-4 space-y-2">
                <span class="text-[10px] font-extrabold text-stone-400 uppercase tracking-wider block border-b border-stone-200 pb-1">Identitas Siswa</span>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-stone-500 font-semibold">Nama Siswa</span>
                    <span class="col-span-2 font-bold text-stone-900">: {{ $pembayaran->tagihan->siswa->user->nama ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-stone-500 font-semibold">NIS / NISN</span>
                    <span class="col-span-2 font-mono font-bold text-stone-800">: {{ $pembayaran->tagihan->siswa->nis ?? '-' }} / {{ $pembayaran->tagihan->siswa->nisn ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-stone-500 font-semibold">Kelas</span>
                    <span class="col-span-2 font-bold text-stone-800">: Kelas {{ $pembayaran->tagihan->siswa->kelas->nama_kelas ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-stone-500 font-semibold">Wali Murid</span>
                    <span class="col-span-2 text-stone-800">: {{ $pembayaran->tagihan->siswa->nama_wali ?? '-' }}</span>
                </div>
            </div>

            <!-- Right: Payment Info -->
            <div class="bg-stone-50 border border-stone-200 rounded-2xl p-4 space-y-2">
                <span class="text-[10px] font-extrabold text-stone-400 uppercase tracking-wider block border-b border-stone-200 pb-1">Rincian Transaksi</span>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-stone-500 font-semibold">Jenis Tagihan</span>
                    <span class="col-span-2 font-bold text-stone-900">: {{ $pembayaran->tagihan->jenisTagihan->nama ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-stone-500 font-semibold">Periode</span>
                    <span class="col-span-2 font-bold text-stone-800">: {{ $pembayaran->tagihan->bulan ?: 'Tahunan' }}</span>
                </div>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-stone-500 font-semibold">Metode Bayar</span>
                    <span class="col-span-2 font-bold text-emerald-800">: {{ $pembayaran->metode_bayar }}</span>
                </div>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-stone-500 font-semibold">Status</span>
                    <span class="col-span-2 font-bold text-emerald-700">: Telah Diterima (Sah)</span>
                </div>
            </div>
        </div>

        <!-- BREAKDOWN TABLE -->
        <div class="border border-stone-200 rounded-2xl overflow-hidden">
            <table class="w-full text-xs text-left">
                <thead class="bg-stone-900 text-white font-extrabold uppercase tracking-wider">
                    <tr>
                        <th class="p-3.5">Deskripsi Pembayaran</th>
                        <th class="p-3.5 text-right w-40">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200">
                    <tr>
                        <td class="p-3.5">
                            <span class="font-bold text-stone-900 block text-xs">Setoran Pembayaran {{ $pembayaran->tagihan->jenisTagihan->nama ?? 'Tagihan' }}</span>
                            <span class="text-[11px] text-stone-500">Periode: {{ $pembayaran->tagihan->bulan ?: '-' }} | Siswa: {{ $pembayaran->tagihan->siswa->user->nama ?? '-' }}</span>
                        </td>
                        <td class="p-3.5 text-right font-black text-stone-900 text-sm">
                            Rp {{ number_format($pembayaran->nominal_dibayar, 0, ',', '.') }}
                        </td>
                    </tr>

                    @if ($pembayaran->kelebihan_bayar > 0)
                        <tr class="bg-emerald-50/50">
                            <td class="p-3.5 text-emerald-800 font-semibold">
                                Dialokasikan ke Saldo Deposit Tabungan Siswa
                            </td>
                            <td class="p-3.5 text-right font-bold text-emerald-800">
                                + Rp {{ number_format($pembayaran->kelebihan_bayar, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
                <tfoot class="bg-stone-50 border-t-2 border-stone-300 font-black text-sm">
                    <tr>
                        <td class="p-3.5 text-right uppercase tracking-wider">Total Disetorkan:</td>
                        <td class="p-3.5 text-right text-emerald-800 text-base">
                            Rp {{ number_format($pembayaran->nominal_dibayar, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- SIGNATURES SECTION -->
        <div class="grid grid-cols-2 gap-8 pt-6 border-t border-stone-200 text-center text-xs">
            <div>
                <span class="text-stone-500 font-semibold block mb-16">Penyetor / Wali Murid,</span>
                <span class="font-bold text-stone-900 block underline uppercase">{{ $pembayaran->tagihan->siswa->nama_wali ?? ($pembayaran->tagihan->siswa->user->nama ?? 'Wali Murid') }}</span>
                <span class="text-[10px] text-stone-400 font-medium">Tanda Tangan Penyetor</span>
            </div>

            <div>
                <span class="text-stone-500 font-semibold block mb-16">Bendahara / Petugas Kasir,</span>
                <span class="font-bold text-stone-900 block underline uppercase">{{ $staffFinance->nama ?? 'Staf Keuangan' }}</span>
                <span class="text-[10px] text-stone-400 font-medium">Tanda Tangan &amp; Cap Stempel Resmi</span>
            </div>
        </div>

        <!-- FOOTER NOTE -->
        <div class="text-center border-t border-stone-100 pt-4">
            <p class="text-[10px] text-stone-400 italic">
                * Kuitansi ini merupakan bukti setoran pembayaran yang sah dan dicatat secara elektronik pada Sistem Informasi Keuangan Yayasan. Simpan kuitansi ini sebagai bukti pelunasan.
            </p>
        </div>
    </main>

</body>
</html>
