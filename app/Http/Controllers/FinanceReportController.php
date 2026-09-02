<?php

namespace App\Http\Controllers;

use App\Models\GajiGuru;
use App\Models\Pembayaran;
use App\Models\User;
use App\Models\DanaBos;
use App\Models\Tabungan;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Pengaturan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FinanceReportController extends Controller
{
    public function slipGaji(Request $request, int $id)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $gaji = GajiGuru::with('guru.user')->findOrFail($id);
        $user = auth()->user();
        $userRole = $user->role->nama ?? '';

        // Izinkan Finance, Super Admin, Kepala Sekolah, atau Guru pemilik slip gaji ini
        $isOwnSlip = false;
        if ($userRole === 'guru' && $user->guru) {
            $isOwnSlip = ($user->guru->id === $gaji->guru_id);
        }

        if (!in_array($userRole, ['finance', 'super_admin', 'kepala_sekolah']) && !$isOwnSlip) {
            abort(403, 'Anda tidak memiliki akses untuk melihat slip gaji ini.');
        }

        $namaSekolah = \App\Models\Pengaturan::getValue('nama_sekolah', 'PONDOK PESANTREN & SEKOLAH ISLAM TERPADU');
        $alamatSekolah = \App\Models\Pengaturan::getValue('alamat_sekolah', 'Jl. Pendidikan Karakter Islami No. 123');
        $noTelepon = \App\Models\Pengaturan::getValue('no_telepon', '(0274) 123456');
        $terbilangText = self::terbilang($gaji->total_diterima) . ' Rupiah';

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-slip-gaji', [
            'gaji' => $gaji,
            'namaSekolah' => $namaSekolah,
            'alamatSekolah' => $alamatSekolah,
            'noTelepon' => $noTelepon,
            'terbilang' => $terbilangText,
        ]);

        $filename = 'slip_gaji_' . str_replace(' ', '_', strtolower($gaji->guru->user->nama ?? 'guru')) . '_' . strtolower($gaji->bulan) . '_' . $gaji->tahun . '.pdf';

        if ($request->query('download') === '1' || request('download') === '1') {
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, ['Content-Type' => 'application/pdf']);
        }

        // Default: Inline Preview (Browser PDF viewer)
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    public function bulkSlipGaji(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $user = auth()->user();
        $userRole = $user->role->nama ?? '';

        if (!in_array($userRole, ['finance', 'super_admin', 'kepala_sekolah'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunduh slip gaji massal.');
        }

        $query = GajiGuru::with(['guru.user']);

        if ($request->filled('ids')) {
            $ids = is_array($request->ids) ? $request->ids : explode(',', $request->ids);
            $query->whereIn('id', array_filter($ids));
        } else {
            if ($request->filled('bulan')) {
                $query->where('bulan', $request->bulan);
            }
            if ($request->filled('tahun')) {
                $query->where('tahun', $request->tahun);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
        }

        $salaries = $query->orderBy('guru_id', 'asc')->get();

        if ($salaries->isEmpty()) {
            abort(404, 'Tidak ada data slip gaji yang ditemukan untuk kriteria ini.');
        }

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-bulk-slip-gaji', [
            'salaries' => $salaries,
            'bulan' => $request->bulan ?? '',
            'tahun' => $request->tahun ?? '',
        ]);

        $periodLabel = $request->bulan ? str_replace(' ', '_', strtolower($request->bulan)) . '_' : '';
        $yearLabel = $request->tahun ?? date('Y');
        $filename = 'bulk_slip_gaji_' . $periodLabel . $yearLabel . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    public function cetakResi(Request $request, int $id)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $pembayaran = Pembayaran::with(['tagihan.siswa.user', 'tagihan.siswa.kelas', 'tagihan.jenisTagihan', 'petugas'])->findOrFail($id);
        $user = auth()->user();
        $userRole = $user->role->nama ?? '';

        // Izinkan Finance, Super Admin, TU, Kepsek, atau Murid/Wali pemilik tagihan ini
        $isOwnReceipt = false;
        if ($userRole === 'murid' && $user->siswa) {
            $isOwnReceipt = ($pembayaran->tagihan && $pembayaran->tagihan->siswa_id === $user->siswa->id);
        }

        if (!in_array($userRole, ['finance', 'super_admin', 'tata_usaha', 'kepala_sekolah']) && !$isOwnReceipt) {
            abort(403, 'Anda tidak memiliki akses untuk melihat resi ini.');
        }

        $staffFinance = User::whereHas('role', function ($q) {
            $q->where('nama', 'finance');
        })->first();

        // 1. If user explicitly requests direct PDF download
        if ($request->query('download') === '1' || request('download') === '1') {
            $pdf = Pdf::loadView('livewire.shared.laporan.pdf-resi-pembayaran', [
                'pembayaran' => $pembayaran,
                'staffFinance' => $pembayaran->petugas ?? $staffFinance,
            ]);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'resi_pembayaran_' . $pembayaran->id . '.pdf', ['Content-Type' => 'application/pdf']);
        }

        // 2. If user requests inline raw PDF stream
        if ($request->query('format') === 'pdf' || request('format') === 'pdf') {
            $pdf = Pdf::loadView('livewire.shared.laporan.pdf-resi-pembayaran', [
                'pembayaran' => $pembayaran,
                'staffFinance' => $pembayaran->petugas ?? $staffFinance,
            ]);

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="resi_pembayaran_' . $pembayaran->id . '.pdf"'
            ]);
        }

        // 3. Default: Interactive Web Preview Page with Print & Download Toolbar
        return view('preview.resi-pembayaran', [
            'pembayaran' => $pembayaran,
            'staffFinance' => $pembayaran->petugas ?? $staffFinance,
        ]);
    }

    public function laporanPengeluaranPdf(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $user = auth()->user();
        $userRole = $user->role->nama ?? '';

        if (!in_array($userRole, ['finance', 'super_admin', 'kepala_sekolah'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat laporan pengeluaran.');
        }

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $filterPeriode = $request->query('filter_periode');
        $bulan = $request->query('bulan');
        $kategoriId = $request->query('kategori_pengeluaran_id');
        $search = $request->query('search');

        $listBulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $query = \App\Models\Pengeluaran::with(['kategori', 'petugas']);

        if (!empty($bulan)) {
            $monthIndex = array_search($bulan, $listBulan);
            if ($monthIndex !== false) {
                $query->whereMonth('tanggal', $monthIndex + 1);
            }
        }

        if ($filterPeriode === 'hari_ini') {
            $query->whereDate('tanggal', date('Y-m-d'));
        } elseif ($filterPeriode === 'kemarin') {
            $query->whereDate('tanggal', date('Y-m-d', strtotime('-1 day')));
        } elseif ($filterPeriode === 'minggu_ini') {
            $query->whereBetween('tanggal', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]);
        } elseif ($filterPeriode === 'bulan_ini') {
            $query->whereBetween('tanggal', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')]);
        } elseif ($filterPeriode === 'custom' || ($startDate && $endDate)) {
            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->whereDate('tanggal', '>=', $startDate);
            } elseif ($endDate) {
                $query->whereDate('tanggal', '<=', $endDate);
            }
        }

        if ($kategoriId) {
            $query->where('kategori_pengeluaran_id', $kategoriId);
        }

        if ($search) {
            $query->where('keterangan', 'like', '%' . $search . '%');
        }

        $data = $query->orderBy('tanggal', 'asc')->get();

        if ($data->isEmpty()) {
            abort(404, 'Tidak ada data pengeluaran untuk kriteria ini.');
        }

        $cat = \App\Models\KategoriPengeluaran::find($kategoriId);

        $periodeText = match ($filterPeriode) {
            'hari_ini' => 'Hari Ini (' . date('d/m/Y') . ')',
            'kemarin' => 'Kemarin (' . date('d/m/Y', strtotime('-1 day')) . ')',
            'minggu_ini' => 'Minggu Ini',
            'bulan_ini' => 'Bulan Ini',
            'custom' => ($startDate ? \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') : '') . ' s/d ' . ($endDate ? \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') : ''),
            default => 'Semua Periode',
        };

        $namaSekolah = Pengaturan::getValue('nama_sekolah', 'PONDOK PESANTREN & SEKOLAH ISLAM TERPADU');
        $alamatSekolah = Pengaturan::getValue('alamat_sekolah', 'Jl. Pendidikan Karakter Islami No. 123');
        $noTelepon = Pengaturan::getValue('no_telepon', '(0274) 123456');

        $judul = $request->query('judul') ?: 'LAPORAN PENGELUARAN KEUANGAN YAYASAN';
        $catatan = $request->query('catatan');
        $penandatangan = $request->query('penandatangan');
        $jabatanPenandatangan = $request->query('jabatan_penandatangan') ?: 'Bendahara Yayasan';

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-laporan-pengeluaran', [
            'data' => $data,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'periodeText' => $periodeText,
            'bulan' => $bulan,
            'kategori' => $cat?->nama ?? 'Semua',
            'totalPengeluaran' => $data->sum('jumlah'),
            'namaSekolah' => $namaSekolah,
            'alamatSekolah' => $alamatSekolah,
            'noTelepon' => $noTelepon,
            'judul' => $judul,
            'catatan' => $catatan,
            'penandatangan' => $penandatangan,
            'jabatanPenandatangan' => $jabatanPenandatangan,
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan_pengeluaran_' . date('Ymd_His') . '.pdf';

        if ($request->query('download') === '1' || request('download') === '1') {
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, ['Content-Type' => 'application/pdf']);
        }

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    public function laporanPemasukanPdf(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $user = auth()->user();
        $userRole = $user->role->nama ?? '';

        if (!in_array($userRole, ['finance', 'super_admin', 'kepala_sekolah'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat laporan pemasukan.');
        }

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $filterPeriode = $request->query('filter_periode');
        $bulan = $request->query('bulan');
        $metodeBayar = $request->query('metode_bayar');
        $jenisTagihanId = $request->query('jenis_tagihan_id');
        $search = $request->query('search');

        $listBulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $query = Pembayaran::with(['tagihan.siswa.user', 'tagihan.siswa.kelas', 'tagihan.jenisTagihan', 'petugas']);

        if (!empty($bulan)) {
            $monthIndex = array_search($bulan, $listBulan);
            if ($monthIndex !== false) {
                $monthNum = $monthIndex + 1;
                $query->where(function ($q) use ($monthNum, $bulan) {
                    $q->whereMonth('tanggal_bayar', $monthNum)
                      ->orWhereHas('tagihan', fn($tq) => $tq->where('bulan', $bulan));
                });
            }
        }

        if ($filterPeriode === 'hari_ini') {
            $query->whereDate('tanggal_bayar', date('Y-m-d'));
        } elseif ($filterPeriode === 'kemarin') {
            $query->whereDate('tanggal_bayar', date('Y-m-d', strtotime('-1 day')));
        } elseif ($filterPeriode === 'minggu_ini') {
            $query->whereBetween('tanggal_bayar', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]);
        } elseif ($filterPeriode === 'bulan_ini') {
            $query->whereBetween('tanggal_bayar', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')]);
        } elseif ($filterPeriode === 'custom' || ($startDate && $endDate)) {
            if ($startDate && $endDate) {
                $query->whereBetween('tanggal_bayar', [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->whereDate('tanggal_bayar', '>=', $startDate);
            } elseif ($endDate) {
                $query->whereDate('tanggal_bayar', '<=', $endDate);
            }
        }

        if ($metodeBayar) {
            $query->where('metode_bayar', $metodeBayar);
        }

        if ($jenisTagihanId) {
            $query->whereHas('tagihan', function ($q) use ($jenisTagihanId) {
                $q->where('jenis_tagihan_id', $jenisTagihanId);
            });
        }

        if ($search) {
            $query->whereHas('tagihan.siswa.user', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }

        $data = $query->orderBy('tanggal_bayar', 'asc')->get();

        if ($data->isEmpty()) {
            abort(404, 'Tidak ada data pemasukan untuk kriteria ini.');
        }

        $jt = \App\Models\JenisTagihan::find($jenisTagihanId);

        $periodeText = match ($filterPeriode) {
            'hari_ini' => 'Hari Ini (' . date('d/m/Y') . ')',
            'kemarin' => 'Kemarin (' . date('d/m/Y', strtotime('-1 day')) . ')',
            'minggu_ini' => 'Minggu Ini',
            'bulan_ini' => 'Bulan Ini',
            'custom' => ($startDate ? date('d/m/Y', strtotime($startDate)) : '') . ' s/d ' . ($endDate ? date('d/m/Y', strtotime($endDate)) : ''),
            default => 'Semua Periode',
        };

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-laporan-pemasukan', [
            'data' => $data,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'periodeText' => $periodeText,
            'bulan' => $bulan,
            'metodeBayar' => $metodeBayar ?: 'Semua',
            'jenisTagihan' => $jt?->nama ?? 'Semua',
            'totalPemasukan' => $data->sum('nominal_dibayar'),
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan_pemasukan_' . date('Ymd_His') . '.pdf';

        if ($request->query('download') === '1' || request('download') === '1') {
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, ['Content-Type' => 'application/pdf']);
        }

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    public function laporanTunggakanPdf(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $user = auth()->user();
        $userRole = $user->role->nama ?? '';

        if (!in_array($userRole, ['finance', 'super_admin', 'kepala_sekolah'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat laporan tunggakan.');
        }

        $kelasId = $request->query('kelas_id');
        $tahunAjaranId = $request->query('tahun_ajaran_id');
        $bulan = $request->query('bulan');
        $filterPeriode = $request->query('filter_periode');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $search = $request->query('search');

        $query = \App\Models\Tagihan::with(['siswa.user', 'siswa.kelas', 'jenisTagihan'])
            ->where('status', '!=', 'lunas');

        if ($kelasId) {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        if ($search) {
            $query->whereHas('siswa.user', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }

        if ($bulan) {
            $query->where('bulan', $bulan);
        }

        if ($filterPeriode === 'hari_ini') {
            $query->whereDate('jatuh_tempo', date('Y-m-d'));
        } elseif ($filterPeriode === 'kemarin') {
            $query->whereDate('jatuh_tempo', date('Y-m-d', strtotime('-1 day')));
        } elseif ($filterPeriode === 'minggu_ini') {
            $query->whereBetween('jatuh_tempo', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]);
        } elseif ($filterPeriode === 'bulan_ini') {
            $query->whereBetween('jatuh_tempo', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')]);
        } elseif ($filterPeriode === 'custom' || ($startDate && $endDate)) {
            if ($startDate && $endDate) {
                $query->whereBetween('jatuh_tempo', [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->whereDate('jatuh_tempo', '>=', $startDate);
            } elseif ($endDate) {
                $query->whereDate('jatuh_tempo', '<=', $endDate);
            }
        }

        $data = $query->get();

        if ($data->isEmpty()) {
            abort(404, 'Tidak ada data tunggakan untuk kriteria ini.');
        }

        $kelas = \App\Models\Kelas::find($kelasId);
        $ta = \App\Models\TahunAjaran::find($tahunAjaranId);

        $periodeText = match ($filterPeriode) {
            'hari_ini' => 'Hari Ini (' . date('d/m/Y') . ')',
            'kemarin' => 'Kemarin (' . date('d/m/Y', strtotime('-1 day')) . ')',
            'minggu_ini' => 'Minggu Ini',
            'bulan_ini' => 'Bulan Ini',
            'custom' => ($startDate ? date('d/m/Y', strtotime($startDate)) : '') . ' s/d ' . ($endDate ? date('d/m/Y', strtotime($endDate)) : ''),
            default => 'Semua Periode',
        };

        $namaSekolah = \App\Models\Pengaturan::getValue('nama_sekolah', 'PONDOK PESANTREN & SEKOLAH ISLAM TERPADU');
        $alamatSekolah = \App\Models\Pengaturan::getValue('alamat_sekolah', 'Jl. Pendidikan Karakter Islami No. 123');
        $noTelepon = \App\Models\Pengaturan::getValue('no_telepon', '(0274) 123456');

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-laporan-tunggakan', [
            'data' => $data,
            'kelas' => $kelas?->nama_kelas ?? 'Semua Kelas',
            'tahunAjaran' => $ta?->nama ?? 'Semua',
            'bulan' => $bulan ?: 'Semua Bulan',
            'periodeText' => $periodeText,
            'namaSekolah' => $namaSekolah,
            'alamatSekolah' => $alamatSekolah,
            'noTelepon' => $noTelepon,
            'totalTunggakan' => $data->sum(fn($t) => $t->nominal - $t->total_dibayar),
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan_tunggakan_' . date('Ymd_His') . '.pdf';

        if ($request->query('download') === '1' || request('download') === '1') {
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, ['Content-Type' => 'application/pdf']);
        }

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    public function danaBosPdf(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $user = auth()->user();
        $userRole = $user->role->nama ?? '';

        if (!in_array($userRole, ['finance', 'super_admin', 'kepala_sekolah', 'founder'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk mencetak laporan Dana BOS.');
        }

        $filterPeriode = $request->query('filter_periode');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $jenis = $request->query('jenis', 'semua');
        $search = $request->query('search');

        $query = DanaBos::with('tahunAjaran');

        if ($jenis !== 'semua' && in_array($jenis, ['masuk', 'keluar'])) {
            $query->where('jenis', $jenis);
        }

        if ($filterPeriode === 'hari_ini') {
            $query->whereDate('tanggal', date('Y-m-d'));
        } elseif ($filterPeriode === 'kemarin') {
            $query->whereDate('tanggal', date('Y-m-d', strtotime('-1 day')));
        } elseif ($filterPeriode === 'minggu_ini') {
            $query->whereBetween('tanggal', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]);
        } elseif ($filterPeriode === 'bulan_ini') {
            $query->whereBetween('tanggal', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')]);
        } elseif ($filterPeriode === 'custom' || ($startDate && $endDate)) {
            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->whereDate('tanggal', '>=', $startDate);
            } elseif ($endDate) {
                $query->whereDate('tanggal', '<=', $endDate);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kategori', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        $data = $query->orderBy('tanggal', 'asc')->get();

        if ($data->isEmpty()) {
            abort(404, 'Tidak ada catatan transaksi dana BOS yang ditemukan.');
        }

        $totalMasuk = (float) DanaBos::where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = (float) DanaBos::where('jenis', 'keluar')->sum('nominal');
        $saldoBos = $totalMasuk - $totalKeluar;

        $activeTA = TahunAjaran::where('status_aktif', true)->first();

        $namaSekolah = Pengaturan::getValue('nama_sekolah', 'PONDOK PESANTREN & SEKOLAH ISLAM TERPADU');
        $alamatSekolah = Pengaturan::getValue('alamat_sekolah', 'Jl. Pendidikan Karakter Islami No. 123');
        $noTelepon = Pengaturan::getValue('no_telepon', '(0274) 123456');

        $periodeText = match ($filterPeriode) {
            'hari_ini' => 'Hari Ini (' . date('d/m/Y') . ')',
            'kemarin' => 'Kemarin (' . date('d/m/Y', strtotime('-1 day')) . ')',
            'minggu_ini' => 'Minggu Ini',
            'bulan_ini' => 'Bulan Ini',
            'custom' => ($startDate ? date('d/m/Y', strtotime($startDate)) : '') . ' s/d ' . ($endDate ? date('d/m/Y', strtotime($endDate)) : ''),
            default => 'Semua Periode',
        };

        $jenisText = match ($jenis) {
            'masuk' => 'Penerimaan Sahaja',
            'keluar' => 'Belanja / Realisasi Sahaja',
            default => 'Semua Mutasi (Penerimaan & Belanja)'
        };

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-dana-bos', [
            'data' => $data,
            'namaSekolah' => $namaSekolah,
            'alamatSekolah' => $alamatSekolah,
            'noTelepon' => $noTelepon,
            'tahunAjaran' => $activeTA?->nama ?? 'Semua',
            'periodeText' => $periodeText,
            'jenisText' => $jenisText,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'saldoBos' => $saldoBos,
        ])->setPaper('a4', 'portrait');

        $filename = 'rekap_dana_bos_' . date('Ymd_His') . '.pdf';

        if ($request->query('download') === '1' || request('download') === '1') {
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, ['Content-Type' => 'application/pdf']);
        }

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    public function rekapGajiPdf(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $user = auth()->user();
        $userRole = $user->role->nama ?? '';

        if (!in_array($userRole, ['finance', 'super_admin', 'kepala_sekolah', 'founder'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat rekapitulasi gaji guru.');
        }

        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun');
        $status = $request->query('status');
        $search = $request->query('search');

        $query = GajiGuru::with(['guru.user']);

        if ($request->filled('ids')) {
            $ids = is_array($request->ids) ? $request->ids : explode(',', $request->ids);
            $query->whereIn('id', array_filter($ids));
        } else {
            if ($bulan) {
                $query->where('bulan', $bulan);
            }
            if ($tahun) {
                $query->where('tahun', $tahun);
            }
            if ($status && in_array($status, ['draft', 'dibayar'])) {
                $query->where('status', $status);
            }
            if ($sumberDana = $request->query('sumber_dana')) {
                $query->where('sumber_dana', $sumberDana);
            }
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('guru.user', function ($sub) use ($search) {
                        $sub->where('nama', 'like', '%' . $search . '%');
                    })->orWhere('jabatan', 'like', '%' . $search . '%')
                      ->orWhereHas('guru', function ($sub) use ($search) {
                          $sub->where('nip', 'like', '%' . $search . '%')
                              ->orWhere('niy', 'like', '%' . $search . '%');
                      });
                });
            }
        }

        $data = $query->latest('id')->get();

        if ($data->isEmpty()) {
            abort(404, 'Tidak ada data penggajian guru yang ditemukan.');
        }

        $namaSekolah = Pengaturan::getValue('nama_sekolah', 'PONDOK PESANTREN & SEKOLAH ISLAM TERPADU');
        $alamatSekolah = Pengaturan::getValue('alamat_sekolah', 'Jl. Pendidikan Karakter Islami No. 123');
        $noTelepon = Pengaturan::getValue('no_telepon', '(0274) 123456');

        $statusText = match ($status) {
            'dibayar' => 'Sudah Dibayar (Lunas)',
            'draft' => 'Draft / Belum Ditransfer',
            default => 'Semua Status'
        };

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-rekap-gaji-guru', [
            'data' => $data,
            'bulan' => $bulan ?: 'Semua Bulan',
            'tahun' => $tahun ?: date('Y'),
            'statusText' => $statusText,
            'namaSekolah' => $namaSekolah,
            'alamatSekolah' => $alamatSekolah,
            'noTelepon' => $noTelepon,
        ])->setPaper('a4', 'landscape');

        $filename = 'rekap_gaji_guru_' . ($bulan ? strtolower($bulan) . '_' : '') . ($tahun ?: date('Y')) . '.pdf';

        if ($request->query('download') === '1' || request('download') === '1') {
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, ['Content-Type' => 'application/pdf']);
        }

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    public function tabunganSiswaPdf(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $user = auth()->user();
        $userRole = $user->role->nama ?? '';

        if (!in_array($userRole, ['finance', 'super_admin', 'kepala_sekolah', 'founder'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat laporan tabungan siswa.');
        }

        $siswaId = $request->query('siswa_id');
        $kelasId = $request->query('kelas_id');
        $search = $request->query('search');
        $view = $request->query('view');
        $isHistory = $request->query('history') === '1' || $view === 'history';

        $namaSekolah = Pengaturan::getValue('nama_sekolah', 'PONDOK PESANTREN & SEKOLAH ISLAM TERPADU');
        $alamatSekolah = Pengaturan::getValue('alamat_sekolah', 'Jl. Pendidikan Karakter Islami, Pekanbaru');
        $noTelepon = Pengaturan::getValue('no_telepon', '(0761) 123456');

        // Mode 0: Jurnal Riwayat Seluruh Mutasi Tabungan Murid
        if ($isHistory) {
            $filterPeriode = $request->query('filter_periode');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $jenis = $request->query('jenis');

            $query = Tabungan::with(['siswa.user', 'siswa.kelas', 'petugas']);

            if ($kelasId) {
                $query->whereHas('siswa', function ($q) use ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                });
            }

            if ($jenis && in_array($jenis, ['setor', 'tarik'])) {
                $query->where('jenis', $jenis);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('kode_transaksi', 'like', '%' . $search . '%')
                      ->orWhere('keterangan', 'like', '%' . $search . '%')
                      ->orWhereHas('siswa.user', function ($uq) use ($search) {
                          $uq->where('nama', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('siswa', function ($sq) use ($search) {
                          $sq->where('nis', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('petugas', function ($pq) use ($search) {
                          $pq->where('nama', 'like', '%' . $search . '%');
                      });
                });
            }

            if ($filterPeriode === 'hari_ini') {
                $query->whereDate('tanggal', date('Y-m-d'));
            } elseif ($filterPeriode === 'kemarin') {
                $query->whereDate('tanggal', date('Y-m-d', strtotime('-1 day')));
            } elseif ($filterPeriode === 'minggu_ini') {
                $query->whereBetween('tanggal', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]);
            } elseif ($filterPeriode === 'bulan_ini') {
                $query->whereBetween('tanggal', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')]);
            } elseif ($filterPeriode === 'custom' || ($startDate && $endDate)) {
                if ($startDate && $endDate) {
                    $query->whereBetween('tanggal', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $query->whereDate('tanggal', '>=', $startDate);
                } elseif ($endDate) {
                    $query->whereDate('tanggal', '<=', $endDate);
                }
            }

            $data = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();

            if ($data->isEmpty()) {
                abort(404, 'Tidak ada riwayat mutasi tabungan yang ditemukan untuk kriteria filter ini.');
            }

            $totalSetor = (float) $data->where('jenis', 'setor')->sum('nominal');
            $totalTarik = (float) $data->where('jenis', 'tarik')->sum('nominal');

            $kelas = Kelas::find($kelasId);
            $namaKelas = $kelas?->nama_kelas ? ('Kelas ' . $kelas->nama_kelas) : 'Semua Kelas';

            $periodeText = match ($filterPeriode) {
                'hari_ini' => 'Hari Ini (' . date('d/m/Y') . ')',
                'kemarin' => 'Kemarin (' . date('d/m/Y', strtotime('-1 day')) . ')',
                'minggu_ini' => 'Minggu Ini',
                'bulan_ini' => 'Bulan Ini',
                'custom' => ($startDate ? \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') : '') . ' s/d ' . ($endDate ? \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') : ''),
                default => 'Semua Periode',
            };

            $jenisText = match ($jenis) {
                'setor' => 'Mutasi Setor Sahaja',
                'tarik' => 'Mutasi Tarik Sahaja',
                default => 'Semua Mutasi (Setor & Tarik)'
            };

            $pdf = Pdf::loadView('livewire.shared.laporan.pdf-jurnal-tabungan-siswa', [
                'data' => $data,
                'totalSetor' => $totalSetor,
                'totalTarik' => $totalTarik,
                'namaKelas' => $namaKelas,
                'periodeText' => $periodeText,
                'jenisText' => $jenisText,
                'namaSekolah' => $namaSekolah,
                'alamatSekolah' => $alamatSekolah,
                'noTelepon' => $noTelepon,
            ])->setPaper('a4', 'landscape');

            $filename = 'jurnal_mutasi_tabungan_' . date('Ymd_His') . '.pdf';
        }
        // Mode 1: Buku Mutasi 1 Siswa
        elseif ($siswaId) {
            $siswa = Siswa::with(['user', 'kelas'])->findOrFail($siswaId);
            $txRecords = Tabungan::where('siswa_id', $siswaId)
                ->orderBy('tanggal', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $mutasi = [];
            $runningBalance = 0;
            $totalSetor = 0;
            $totalTarik = 0;

            foreach ($txRecords as $tx) {
                if ($tx->jenis === 'setor') {
                    $runningBalance += (float) $tx->nominal;
                    $totalSetor += (float) $tx->nominal;
                } else {
                    $runningBalance -= (float) $tx->nominal;
                    $totalTarik += (float) $tx->nominal;
                }

                $mutasi[] = [
                    'tanggal' => $tx->tanggal ? $tx->tanggal->format('d/m/Y') : '-',
                    'jenis' => $tx->jenis,
                    'keterangan' => $tx->keterangan ?: ($tx->jenis === 'setor' ? 'Setoran Tabungan' : 'Penarikan Tabungan'),
                    'nominal' => (float) $tx->nominal,
                    'saldo_berjalan' => $runningBalance,
                ];
            }

            $pdf = Pdf::loadView('livewire.shared.laporan.pdf-buku-tabungan-siswa', [
                'siswa' => $siswa,
                'mutasi' => $mutasi,
                'totalSetor' => $totalSetor,
                'totalTarik' => $totalTarik,
                'saldoAkhir' => $runningBalance,
                'namaSekolah' => $namaSekolah,
                'alamatSekolah' => $alamatSekolah,
                'noTelepon' => $noTelepon,
            ])->setPaper('a4', 'portrait');

            $filename = 'buku_tabungan_' . str_replace(' ', '_', strtolower($siswa->user->nama ?? 'siswa')) . '.pdf';
        } else {
            // Mode 2: Rekap Saldo Seluruh Siswa
            $query = Siswa::with(['user', 'kelas', 'tabungans']);

            if ($kelasId) {
                $query->where('kelas_id', $kelasId);
            }

            if ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%')
                      ->orWhere('username', 'like', '%' . $search . '%');
                });
            }

            $siswas = $query->get();

            if ($siswas->isEmpty()) {
                abort(404, 'Tidak ada data tabungan siswa yang ditemukan.');
            }

            $data = $siswas->map(function ($s) {
                $setor = (float) $s->tabungans->where('jenis', 'setor')->sum('nominal');
                $tarik = (float) $s->tabungans->where('jenis', 'tarik')->sum('nominal');
                $saldo = $setor - $tarik;

                return [
                    'nis' => $s->nis ?? '-',
                    'nama' => $s->user->nama ?? '-',
                    'kelas' => $s->kelas->nama_kelas ?? 'Belum Diatur',
                    'total_setor' => $setor,
                    'total_tarik' => $tarik,
                    'saldo' => $saldo,
                ];
            });

            $totalSetorAll = (float) Tabungan::where('jenis', 'setor')->sum('nominal');
            $totalTarikAll = (float) Tabungan::where('jenis', 'tarik')->sum('nominal');
            $totalSaldoAll = $totalSetorAll - $totalTarikAll;

            $kelas = Kelas::find($kelasId);

            $pdf = Pdf::loadView('livewire.shared.laporan.pdf-rekap-tabungan-siswa', [
                'data' => $data,
                'namaKelas' => $kelas?->nama_kelas ?? 'Semua Kelas',
                'totalSetorAll' => $totalSetorAll,
                'totalTarikAll' => $totalTarikAll,
                'totalSaldoAll' => $totalSaldoAll,
                'namaSekolah' => $namaSekolah,
                'alamatSekolah' => $alamatSekolah,
                'noTelepon' => $noTelepon,
            ])->setPaper('a4', 'portrait');

            $filename = 'rekap_tabungan_siswa_' . date('Ymd_His') . '.pdf';
        }

        if ($request->query('download') === '1' || request('download') === '1') {
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, ['Content-Type' => 'application/pdf']);
        }

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    public function arusKasPdf(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $user = auth()->user();
        $userRole = $user->role->nama ?? '';

        if (!in_array($userRole, ['finance', 'super_admin', 'kepala_sekolah', 'founder'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat laporan arus kas.');
        }

        $tab = $request->query('tab', 'semua');
        $stream = $request->query('stream', 'semua');
        $filterPeriode = $request->query('filter_periode', 'semua');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $search = $request->query('search', '');

        $component = new \App\Livewire\Finance\ArusKas();
        $component->tab = $tab;
        $component->stream = $stream;
        $component->filterPeriode = $filterPeriode;
        $component->startDate = $startDate;
        $component->endDate = $endDate;
        $component->search = $search;

        return $component->exportPdf();
    }

    public static function terbilang($angka)
    {
        $angka = abs((float)$angka);
        $huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $temp = '';

        if ($angka < 12) {
            $temp = ' ' . $huruf[(int)$angka];
        } elseif ($angka < 20) {
            $temp = self::terbilang($angka - 10) . ' Belas';
        } elseif ($angka < 100) {
            $temp = self::terbilang((int)($angka / 10)) . ' Puluh' . self::terbilang((int)$angka % 10);
        } elseif ($angka < 200) {
            $temp = ' Seratus' . self::terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $temp = self::terbilang((int)($angka / 100)) . ' Ratus' . self::terbilang((int)$angka % 100);
        } elseif ($angka < 2000) {
            $temp = ' Seribu' . self::terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $temp = self::terbilang((int)($angka / 1000)) . ' Ribu' . self::terbilang((int)$angka % 1000);
        } elseif ($angka < 1000000000) {
            $temp = self::terbilang((int)($angka / 1000000)) . ' Juta' . self::terbilang((int)$angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $temp = self::terbilang((int)($angka / 1000000000)) . ' Miliar' . self::terbilang(fmod($angka, 1000000000));
        }

        return trim($temp);
    }
}
