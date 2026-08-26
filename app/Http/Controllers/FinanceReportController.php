<?php

namespace App\Http\Controllers;

use App\Models\GajiGuru;
use App\Models\Pembayaran;
use App\Models\User;
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
            'custom' => ($startDate ? date('d/m/Y', strtotime($startDate)) : '') . ' s/d ' . ($endDate ? date('d/m/Y', strtotime($endDate)) : ''),
            default => 'Semua Periode',
        };

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-laporan-pengeluaran', [
            'data' => $data,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'periodeText' => $periodeText,
            'bulan' => $bulan,
            'kategori' => $cat?->nama ?? 'Semua',
            'totalPengeluaran' => $data->sum('jumlah'),
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
