<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;
use App\Services\Finance\CsvExportService;
use App\Services\Finance\FinanceReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinanceExportController extends Controller
{
    /**
     * Export Laporan Tunggakan SPP / Tagihan to Excel (.csv).
     */
    public function exportTunggakan(Request $request)
    {
        AuditLogger::log('export', 'Mengekspor Laporan Tunggakan SPP & Tagihan Siswa ke CSV/Excel', null, [
            'log_name' => 'keuangan',
            'properties' => ['format' => 'csv', 'filter' => $request->all()],
        ]);

        $records = FinanceReportService::getTunggakanData($request->all());

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data tunggakan untuk diekspor ke Excel.');
        }

        $filename = 'laporan-tunggakan-' . date('Y-m-d') . '.csv';
        $columns = [
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
        ];

        return CsvExportService::stream($filename, $columns, $records, function ($item, $index) {
            $sisa = $item->nominal - $item->total_dibayar;
            return [
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
            ];
        });
    }

    /**
     * Export Laporan Pemasukan & Infaq to Excel (.csv).
     */
    public function exportPemasukan(Request $request)
    {
        AuditLogger::log('export', 'Mengekspor Laporan Arus Kas Masuk & Infaq ke CSV/Excel', null, [
            'log_name' => 'keuangan',
            'properties' => ['format' => 'csv', 'filter' => $request->all()],
        ]);

        $records = FinanceReportService::getPemasukanData($request->all());

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data pemasukan untuk diekspor ke Excel.');
        }

        $filename = 'laporan-pemasukan-' . date('Y-m-d') . '.csv';
        $columns = [
            'No',
            'Kode Transaksi / Resi',
            'Tanggal Bayar',
            'Nama Siswa',
            'Kelas',
            'Kategori / Tagihan',
            'Metode Pembayaran',
            'Nominal (Rp)',
            'Petugas'
        ];

        return CsvExportService::stream($filename, $columns, $records, function ($item, $index) {
            return [
                $index + 1,
                $item->no_resi ?? ('TRX-' . $item->id),
                $item->tanggal_bayar ? $item->tanggal_bayar->format('d/m/Y') : '-',
                $item->tagihan->siswa->user->nama ?? '-',
                $item->tagihan->siswa->kelas->nama_kelas ?? '-',
                $item->tagihan->jenisTagihan->nama ?? ($item->tagihan->nama_tagihan ?? 'Infaq / Tagihan'),
                strtoupper($item->metode_bayar ?? 'TUNAI'),
                number_format($item->nominal_dibayar, 0, ',', '.'),
                $item->petugas->nama ?? '-'
            ];
        });
    }

    /**
     * Export Laporan Pengeluaran Kas to Excel (.csv).
     */
    public function exportPengeluaran(Request $request)
    {
        AuditLogger::log('export', 'Mengekspor Laporan Arus Kas Keluar & Operasional ke CSV/Excel', null, [
            'log_name' => 'keuangan',
            'properties' => ['format' => 'csv', 'filter' => $request->all()],
        ]);

        $records = FinanceReportService::getPengeluaranData($request->all());

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data pengeluaran untuk diekspor ke Excel.');
        }

        $filename = 'laporan-pengeluaran-' . date('Y-m-d') . '.csv';
        $columns = [
            'No',
            'Tanggal Pengeluaran',
            'Kategori',
            'Keterangan / Kebutuhan',
            'Jumlah Pengeluaran (Rp)',
            'Petugas Input'
        ];

        return CsvExportService::stream($filename, $columns, $records, function ($item, $index) {
            return [
                $index + 1,
                $item->tanggal ? $item->tanggal->format('d/m/Y') : '-',
                $item->kategori->nama ?? 'Umum',
                $item->keterangan ?? '-',
                number_format($item->jumlah, 0, ',', '.'),
                $item->petugas->nama ?? '-'
            ];
        });
    }

    /**
     * Export Rekapitulasi Dana BOS to Excel (.csv).
     */
    public function exportDanaBos(Request $request)
    {
        $userRole = auth()->user()?->role?->nama;
        if (!in_array($userRole, ['finance', 'super_admin', 'super_admin_2', 'founder', 'kepala_sekolah'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengekspor data Dana BOS.');
        }

        AuditLogger::log('export', 'Mengekspor Rekapitulasi Pembukuan Dana BOS ke CSV/Excel', null, [
            'log_name' => 'keuangan',
            'properties' => ['format' => 'csv', 'filter' => $request->all()],
        ]);

        $records = FinanceReportService::getDanaBosData($request->all());

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data Dana BOS untuk diekspor ke Excel.');
        }

        $filename = 'rekap-dana-bos-' . date('Y-m-d') . '.csv';
        $columns = [
            'No',
            'Tanggal',
            'Tahun Ajaran',
            'Jenis Mutasi',
            'Kategori',
            'Uraian / Keterangan',
            'Penerimaan BOS (Rp)',
            'Belanja BOS (Rp)'
        ];

        return CsvExportService::stream($filename, $columns, $records, function ($item, $index) {
            return [
                $index + 1,
                $item->tanggal ? $item->tanggal->format('d/m/Y') : '-',
                $item->tahunAjaran->nama ?? '-',
                $item->jenis === 'masuk' ? 'Penerimaan' : 'Belanja',
                $item->kategori ?? '-',
                $item->keterangan ?? '-',
                $item->jenis === 'masuk' ? number_format($item->nominal, 0, ',', '.') : '0',
                $item->jenis === 'keluar' ? number_format($item->nominal, 0, ',', '.') : '0',
            ];
        });
    }

    /**
     * Export Rekapitulasi Gaji Guru to Excel (.csv).
     */
    public function exportRekapGaji(Request $request)
    {
        AuditLogger::log('export', 'Mengekspor Rekapitulasi Penggajian Guru & Karyawan ke CSV/Excel', null, [
            'log_name' => 'keuangan',
            'properties' => ['format' => 'csv', 'filter' => $request->all()],
        ]);

        $records = FinanceReportService::getRekapGajiData($request->all());

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data penggajian guru untuk diekspor ke Excel.');
        }

        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun');
        $filename = 'rekap-gaji-guru-' . ($bulan ? strtolower($bulan) . '-' : '') . ($tahun ?: date('Y')) . '.csv';

        $columns = [
            'No',
            'Nama Guru',
            'NIP / NUPTK',
            'Jabatan',
            'Jam Kerja',
            'Bulan',
            'Tahun',
            'Gaji Pokok (Rp)',
            'Gaji Berkala (Rp)',
            'Honor Ekskul (Rp)',
            'Insentif (Rp)',
            'Insentif BPJS (Rp)',
            'Insentif Maghrib (Rp)',
            'Total (Rp)',
            'Potongan Sosial (Rp)',
            'Potongan Pinjaman (Rp)',
            'Potongan BPJS TK (Rp)',
            'Potongan Lainnya (Rp)',
            'Total Potongan (Rp)',
            'Total Gaji Bersih Diterima (Rp)',
            'Status',
            'Tanggal Bayar',
            'Sumber Dana'
        ];

        return CsvExportService::stream($filename, $columns, $records, function ($item, $index) {
            return [
                $index + 1,
                $item->guru->user->nama ?? '-',
                $item->guru->nip ?? '-',
                $item->jabatan ?: ($item->guru->jabatan ?? '-'),
                $item->jam_kerja ?? '-',
                $item->bulan ?? '-',
                $item->tahun ?? '-',
                number_format($item->gaji_pokok, 0, ',', '.'),
                number_format($item->gaji_berkala, 0, ',', '.'),
                number_format($item->honor_ekskul, 0, ',', '.'),
                number_format($item->insentif, 0, ',', '.'),
                number_format($item->insentif_bpjs, 0, ',', '.'),
                number_format($item->insentif_maghrib_mengaji, 0, ',', '.'),
                number_format($item->total_bruto, 0, ',', '.'),
                number_format($item->potongan_sosial, 0, ',', '.'),
                number_format($item->potongan_peminjaman, 0, ',', '.'),
                number_format($item->potongan_bpjstk, 0, ',', '.'),
                number_format($item->potongan_lainnya, 0, ',', '.'),
                number_format($item->total_potongan, 0, ',', '.'),
                number_format($item->total_diterima, 0, ',', '.'),
                $item->status === 'dibayar' ? 'LUNAS' : 'DRAFT',
                $item->tanggal_bayar ? date('d/m/Y', strtotime($item->tanggal_bayar)) : '-',
                $item->sumber_dana ?? 'Yayasan'
            ];
        });
    }

    /**
     * Export Rekapitulasi Tabungan Siswa to Excel (.csv).
     */
    public function exportTabungan(Request $request)
    {
        AuditLogger::log('export', 'Mengekspor Laporan Rekapitulasi & Mutasi Tabungan Siswa ke CSV/Excel', null, [
            'log_name' => 'keuangan',
            'properties' => ['format' => 'csv', 'filter' => $request->all()],
        ]);

        $siswaId = $request->query('siswa_id');
        $view = $request->query('view');
        $isHistory = $request->query('history') === '1' || $view === 'history';

        // Mode 0: Jurnal Riwayat Seluruh Mutasi Tabungan Murid
        if ($isHistory) {
            $records = FinanceReportService::getTabunganHistoryData($request->all());

            if ($records->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada riwayat mutasi tabungan siswa untuk diekspor ke Excel.');
            }

            $filename = 'jurnal-mutasi-tabungan-' . date('Y-m-d') . '.csv';
            $columns = [
                'No',
                'Tanggal Transaksi',
                'Kode Mutasi',
                'Nama Siswa',
                'NIS',
                'Kelas',
                'Jenis Transaksi',
                'Nominal (Rp)',
                'Saldo Akhir (Rp)',
                'Petugas Pencatat',
                'Keterangan'
            ];

            return CsvExportService::stream($filename, $columns, $records, function ($item, $index) {
                return [
                    $index + 1,
                    $item->tanggal ? Carbon::parse($item->tanggal)->translatedFormat('d M Y') : '-',
                    $item->kode_transaksi,
                    $item->siswa->user->nama ?? '-',
                    $item->siswa->nis ?? '-',
                    $item->siswa->kelas->nama_kelas ?? '-',
                    strtoupper($item->jenis),
                    ($item->jenis === 'setor' ? '+' : '-') . ' ' . number_format($item->nominal, 0, ',', '.'),
                    number_format($item->saldo_akhir, 0, ',', '.'),
                    $item->petugas->nama ?? 'Sistem',
                    $item->keterangan ?? '-'
                ];
            });
        }

        // Mode 1: Buku Mutasi 1 Siswa
        if ($siswaId) {
            $result = FinanceReportService::getSingleSiswaTabunganMutasi((int) $siswaId);
            $siswa = $result['siswa'];
            $records = $result['mutasi'];

            if (empty($records)) {
                return redirect()->back()->with('error', 'Tidak ada riwayat mutasi tabungan siswa untuk diekspor ke Excel.');
            }

            $filename = 'mutasi-tabungan-' . str_replace(' ', '-', strtolower($siswa->user->nama ?? 'siswa')) . '-' . date('Y-m-d') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function () use ($records, $siswa) {
                $file = fopen('php://output', 'w');
                fputs($file, "\xEF\xBB\xBF");

                // Info Siswa
                fputcsv($file, ['BUKU MUTASI TABUNGAN SANTRI / SISWA']);
                fputcsv($file, ['Nama Siswa', $siswa->user->nama ?? '-']);
                fputcsv($file, ['NIS', $siswa->nis ?? '-']);
                fputcsv($file, ['Kelas', $siswa->kelas->nama_kelas ?? '-']);
                fputcsv($file, []);

                // Header Transaksi
                fputcsv($file, [
                    'No',
                    'Tanggal',
                    'Jenis Transaksi',
                    'Keterangan / Uraian',
                    'Setoran / Masuk (Rp)',
                    'Penarikan / Keluar (Rp)',
                    'Saldo Berjalan (Rp)',
                    'Petugas Input'
                ]);

                foreach ($records as $index => $item) {
                    fputcsv($file, [
                        $index + 1,
                        $item['tanggal'],
                        strtoupper($item['jenis']),
                        $item['keterangan'],
                        $item['jenis'] === 'setor' ? number_format($item['nominal'], 0, ',', '.') : '0',
                        $item['jenis'] === 'tarik' ? number_format($item['nominal'], 0, ',', '.') : '0',
                        number_format($item['saldo_berjalan'], 0, ',', '.'),
                        '-'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Mode 2: Rekap Saldo Seluruh Siswa
        $records = FinanceReportService::getTabunganSummaryData($request->all());

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data tabungan siswa untuk diekspor ke Excel.');
        }

        $filename = 'rekap-tabungan-siswa-' . date('Y-m-d') . '.csv';
        $columns = [
            'No',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Total Setoran (Rp)',
            'Total Penarikan (Rp)',
            'Saldo Tabungan Saat Ini (Rp)'
        ];

        return CsvExportService::stream($filename, $columns, $records, function ($item, $index) {
            return [
                $index + 1,
                $item['nis'],
                $item['nama'],
                $item['kelas'],
                number_format($item['total_setor'], 0, ',', '.'),
                number_format($item['total_tarik'], 0, ',', '.'),
                number_format($item['saldo'], 0, ',', '.')
            ];
        });
    }

    /**
     * Export Arus Kas (Cash Flow) to Excel (.csv).
     */
    public function exportArusKas(Request $request)
    {
        AuditLogger::log('export', 'Mengekspor Ringkasan Arus Kas (Cash Flow) ke CSV/Excel', null, [
            'log_name' => 'keuangan',
            'properties' => ['format' => 'csv', 'filter' => $request->all()],
        ]);

        $component = new \App\Livewire\Finance\ArusKas();
        $component->tab = $request->query('tab', 'semua');
        $component->stream = $request->query('stream', 'semua');
        $component->filterPeriode = $request->query('filter_periode', 'semua');
        $component->startDate = $request->query('start_date');
        $component->endDate = $request->query('end_date');
        $component->search = $request->query('search', '');

        return $component->exportExcel();
    }
}
