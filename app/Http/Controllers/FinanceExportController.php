<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;

class FinanceExportController extends Controller
{
    /**
     * Export Laporan Tunggakan SPP / Tagihan to Excel (.csv).
     */
    public function exportTunggakan(Request $request)
    {
        $kelasId = $request->query('kelas_id');
        $tahunAjaranId = $request->query('tahun_ajaran_id');
        $bulan = $request->query('bulan');
        $filterPeriode = $request->query('filter_periode');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Tagihan::with(['siswa.user', 'siswa.kelas', 'tahunAjaran', 'jenisTagihan'])
            ->whereIn('status', ['belum_bayar', 'sebagian']);

        if ($kelasId) {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
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

        $records = $query->orderBy('created_at', 'desc')->get();

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data tunggakan untuk diekspor ke Excel.');
        }

        $filename = 'laporan-tunggakan-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            // Header row
            fputcsv($file, [
                'No',
                'Nama Siswa',
                'NIS',
                'Kelas',
                'Judul Tagihan',
                'Bulan',
                'Tahun Ajaran',
                'Total Nominal (Rp)',
                'Sudah Dibayar (Rp)',
                'Sisa Tunggakan (Rp)',
                'Jatuh Tempo',
                'Status'
            ]);

            foreach ($records as $index => $item) {
                $sisa = $item->nominal - $item->total_dibayar;
                fputcsv($file, [
                    $index + 1,
                    $item->siswa->user->nama ?? '-',
                    $item->siswa->nis ?? '-',
                    $item->siswa->kelas->nama_kelas ?? '-',
                    $item->jenisTagihan->nama ?? $item->nama_tagihan,
                    $item->bulan ?? '-',
                    $item->tahunAjaran->nama ?? '-',
                    number_format($item->nominal, 0, ',', '.'),
                    number_format($item->total_dibayar, 0, ',', '.'),
                    number_format($sisa, 0, ',', '.'),
                    $item->jatuh_tempo ? $item->jatuh_tempo->format('d/m/Y') : '-',
                    strtoupper(str_replace('_', ' ', $item->status))
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Laporan Pemasukan & Infaq to Excel (.csv).
     */
    public function exportPemasukan(Request $request)
    {
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

        $records = $query->orderBy('tanggal_bayar', 'desc')->get();

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data pemasukan untuk diekspor ke Excel.');
        }

        $filename = 'laporan-pemasukan-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'No',
                'Kode Transaksi / Resi',
                'Tanggal Bayar',
                'Nama Siswa',
                'Kelas',
                'Kategori / Tagihan',
                'Metode Pembayaran',
                'Nominal (Rp)',
                'Petugas'
            ]);

            foreach ($records as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->no_resi ?? ('TRX-' . $item->id),
                    $item->tanggal_bayar ? $item->tanggal_bayar->format('d/m/Y') : '-',
                    $item->tagihan->siswa->user->nama ?? '-',
                    $item->tagihan->siswa->kelas->nama_kelas ?? '-',
                    $item->tagihan->jenisTagihan->nama ?? ($item->tagihan->nama_tagihan ?? 'Infaq / Tagihan'),
                    strtoupper($item->metode_bayar ?? 'TUNAI'),
                    number_format($item->nominal_dibayar, 0, ',', '.'),
                    $item->petugas->nama ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Laporan Pengeluaran Kas to Excel (.csv).
     */
    public function exportPengeluaran(Request $request)
    {
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

        $query = Pengeluaran::with(['kategori', 'petugas']);

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

        $records = $query->orderBy('tanggal', 'desc')->get();

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data pengeluaran untuk diekspor ke Excel.');
        }

        $filename = 'laporan-pengeluaran-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'No',
                'Tanggal Pengeluaran',
                'Kategori',
                'Keterangan / Kebutuhan',
                'Jumlah Pengeluaran (Rp)',
                'Petugas Input'
            ]);

            foreach ($records as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->tanggal ? $item->tanggal->format('d/m/Y') : '-',
                    $item->kategori->nama ?? 'Umum',
                    $item->keterangan ?? '-',
                    number_format($item->jumlah, 0, ',', '.'),
                    $item->petugas->nama ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
