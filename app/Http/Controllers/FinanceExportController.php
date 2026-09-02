<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\DanaBos;
use App\Models\GajiGuru;
use App\Models\Tabungan;
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

    /**
     * Export Rekapitulasi Dana BOS to Excel (.csv).
     */
    public function exportDanaBos(Request $request)
    {
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

        $records = $query->orderBy('tanggal', 'asc')->get();

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data Dana BOS untuk diekspor ke Excel.');
        }

        $filename = 'rekap-dana-bos-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'No',
                'Tanggal',
                'Tahun Ajaran',
                'Jenis Mutasi',
                'Kategori',
                'Uraian / Keterangan',
                'Penerimaan BOS (Rp)',
                'Belanja BOS (Rp)'
            ]);

            foreach ($records as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->tanggal ? $item->tanggal->format('d/m/Y') : '-',
                    $item->tahunAjaran->nama ?? '-',
                    $item->jenis === 'masuk' ? 'Penerimaan' : 'Belanja',
                    $item->kategori ?? '-',
                    $item->keterangan ?? '-',
                    $item->jenis === 'masuk' ? number_format($item->nominal, 0, ',', '.') : '0',
                    $item->jenis === 'keluar' ? number_format($item->nominal, 0, ',', '.') : '0',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Rekapitulasi Gaji Guru to Excel (.csv).
     */
    public function exportRekapGaji(Request $request)
    {
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

        $records = $query->latest('id')->get();

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data penggajian guru untuk diekspor ke Excel.');
        }

        $filename = 'rekap-gaji-guru-' . ($bulan ? strtolower($bulan) . '-' : '') . ($tahun ?: date('Y')) . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
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
            ]);

            foreach ($records as $index => $item) {
                fputcsv($file, [
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
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Rekapitulasi Tabungan Siswa to Excel (.csv).
     */
    public function exportTabungan(Request $request)
    {
        $siswaId = $request->query('siswa_id');
        $kelasId = $request->query('kelas_id');
        $search = $request->query('search');
        $view = $request->query('view');
        $isHistory = $request->query('history') === '1' || $view === 'history';

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

            $records = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();

            if ($records->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada riwayat mutasi tabungan siswa untuk diekspor ke Excel.');
            }

            $filename = 'jurnal-mutasi-tabungan-' . date('Y-m-d') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function () use ($records) {
                $file = fopen('php://output', 'w');
                fputs($file, "\xEF\xBB\xBF");

                fputcsv($file, [
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
                ]);

                foreach ($records as $index => $item) {
                    fputcsv($file, [
                        $index + 1,
                        $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') : '-',
                        $item->kode_transaksi,
                        $item->siswa->user->nama ?? '-',
                        $item->siswa->nis ?? '-',
                        $item->siswa->kelas->nama_kelas ?? '-',
                        strtoupper($item->jenis),
                        ($item->jenis === 'setor' ? '+' : '-') . ' ' . number_format($item->nominal, 0, ',', '.'),
                        number_format($item->saldo_akhir, 0, ',', '.'),
                        $item->petugas->nama ?? 'Sistem',
                        $item->keterangan ?? '-'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Mode 1: Buku Mutasi 1 Siswa
        if ($siswaId) {
            $siswa = Siswa::with(['user', 'kelas'])->findOrFail($siswaId);
            $records = Tabungan::where('siswa_id', $siswaId)
                ->orderBy('tanggal', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            if ($records->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada riwayat mutasi tabungan siswa untuk diekspor ke Excel.');
            }

            $filename = 'mutasi-tabungan-' . str_replace(' ', '-', strtolower($siswa->user->nama ?? 'siswa')) . '-' . date('Y-m-d') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
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

                $runningBalance = 0;
                foreach ($records as $index => $item) {
                    if ($item->jenis === 'setor') {
                        $runningBalance += (float) $item->nominal;
                    } else {
                        $runningBalance -= (float) $item->nominal;
                    }

                    fputcsv($file, [
                        $index + 1,
                        $item->tanggal ? $item->tanggal->format('d/m/Y') : '-',
                        $item->jenis === 'setor' ? 'SETOR' : 'TARIK',
                        $item->keterangan ?: ($item->jenis === 'setor' ? 'Setoran Tabungan' : 'Penarikan Tabungan'),
                        $item->jenis === 'setor' ? number_format($item->nominal, 0, ',', '.') : '0',
                        $item->jenis === 'tarik' ? number_format($item->nominal, 0, ',', '.') : '0',
                        number_format($runningBalance, 0, ',', '.'),
                        $item->petugas->nama ?? '-'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

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

        $records = $query->get();

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data tabungan siswa untuk diekspor ke Excel.');
        }

        $filename = 'rekap-tabungan-siswa-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'No',
                'NIS',
                'Nama Siswa',
                'Kelas',
                'Total Setoran (Rp)',
                'Total Penarikan (Rp)',
                'Saldo Tabungan Saat Ini (Rp)'
            ]);

            foreach ($records as $index => $item) {
                $setor = (float) $item->tabungans->where('jenis', 'setor')->sum('nominal');
                $tarik = (float) $item->tabungans->where('jenis', 'tarik')->sum('nominal');
                $saldo = $setor - $tarik;

                fputcsv($file, [
                    $index + 1,
                    $item->nis ?? '-',
                    $item->user->nama ?? '-',
                    $item->kelas->nama_kelas ?? 'Belum Diatur',
                    number_format($setor, 0, ',', '.'),
                    number_format($tarik, 0, ',', '.'),
                    number_format($saldo, 0, ',', '.')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Arus Kas (Cash Flow) to Excel (.csv).
     */
    public function exportArusKas(Request $request)
    {
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

        return $component->exportExcel();
    }
}
