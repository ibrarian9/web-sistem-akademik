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

        $query = Tagihan::with(['siswa.user', 'siswa.kelas', 'tahunAjaran'])
            ->whereIn('status', ['belum_bayar', 'sebagian']);

        if ($kelasId) {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        $records = $query->orderBy('created_at', 'desc')->get();

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
                'Tahun Ajaran',
                'Total Nominal (Rp)',
                'Sudah Dibayar (Rp)',
                'Sisa Tunggakan (Rp)',
                'Status'
            ]);

            foreach ($records as $index => $item) {
                $sisa = $item->nominal - $item->nominal_terbayar;
                fputcsv($file, [
                    $index + 1,
                    $item->siswa->user->nama ?? '-',
                    $item->siswa->nis ?? '-',
                    $item->siswa->kelas->nama_kelas ?? '-',
                    $item->nama_tagihan,
                    $item->tahunAjaran->nama ?? '-',
                    number_format($item->nominal, 0, ',', '.'),
                    number_format($item->nominal_terbayar, 0, ',', '.'),
                    number_format($sisa, 0, ',', '.'),
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

        $query = Pembayaran::with(['siswa.user', 'siswa.kelas', 'tagihan']);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_bayar', [$startDate, $endDate]);
        }

        $records = $query->orderBy('tanggal_bayar', 'desc')->get();

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
                'Catatan'
            ]);

            foreach ($records as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->kode_pembayaran ?? ('TRX-' . $item->id),
                    $item->tanggal_bayar ? $item->tanggal_bayar->format('d/m/Y H:i') : '-',
                    $item->siswa->user->nama ?? '-',
                    $item->siswa->kelas->nama_kelas ?? '-',
                    $item->tagihan->nama_tagihan ?? 'Infaq / Donasi',
                    strtoupper($item->metode_pembayaran ?? 'TUNAI'),
                    number_format($item->nominal, 0, ',', '.'),
                    $item->keterangan ?? '-'
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

        $query = Pengeluaran::with(['user']);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_pengeluaran', [$startDate, $endDate]);
        }

        $records = $query->orderBy('tanggal_pengeluaran', 'desc')->get();

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
                'Judul Pengeluaran / Kebutuhan',
                'Kategori',
                'Nominal (Rp)',
                'Petugas Input',
                'Keterangan'
            ]);

            foreach ($records as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->tanggal_pengeluaran ? $item->tanggal_pengeluaran->format('d/m/Y') : '-',
                    $item->judul_pengeluaran ?? $item->keterangan,
                    strtoupper($item->kategori ?? 'UMUM'),
                    number_format($item->nominal, 0, ',', '.'),
                    $item->user->nama ?? '-',
                    $item->keterangan ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
