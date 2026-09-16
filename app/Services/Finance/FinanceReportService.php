<?php

namespace App\Services\Finance;

use App\Models\DanaBos;
use App\Models\GajiGuru;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Pengaturan;
use App\Models\Pengeluaran;
use App\Models\Siswa;
use App\Models\Tabungan;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

class FinanceReportService
{
    public const BULAN_LIST = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    /**
     * Get common school profile headers from settings.
     */
    public static function getSchoolHeader(string $defaultAlamat = 'Jl. Pendidikan Karakter Islami No. 123'): array
    {
        return [
            'namaSekolah' => Pengaturan::getValue('nama_sekolah', 'PONDOK PESANTREN & SEKOLAH ISLAM TERPADU'),
            'alamatSekolah' => Pengaturan::getValue('alamat_sekolah', $defaultAlamat),
            'noTelepon' => Pengaturan::getValue('no_telepon', '(0274) 123456'),
        ];
    }

    /**
     * Format Indonesian period description based on filter parameters.
     */
    public static function formatPeriodeText(?string $filterPeriode, ?string $startDate = null, ?string $endDate = null): string
    {
        return match ($filterPeriode) {
            'hari_ini' => 'Hari Ini (' . date('d/m/Y') . ')',
            'kemarin' => 'Kemarin (' . date('d/m/Y', strtotime('-1 day')) . ')',
            'minggu_ini' => 'Minggu Ini',
            'bulan_ini' => 'Bulan Ini',
            'custom' => ($startDate ? Carbon::parse($startDate)->translatedFormat('d M Y') : '') . ' s/d ' . ($endDate ? Carbon::parse($endDate)->translatedFormat('d M Y') : ''),
            default => 'Semua Periode',
        };
    }

    /**
     * Apply date and period filters to an Eloquent query builder.
     */
    public static function applyDateFilter(
        Builder $query,
        string $column,
        ?string $filterPeriode,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $bulan = null,
        bool $defaultJatuhTempo = false
    ): Builder {
        if (!empty($bulan)) {
            $monthIndex = array_search($bulan, self::BULAN_LIST, true);
            if ($monthIndex !== false) {
                $query->whereMonth($column, $monthIndex + 1);
            }
        }

        if ($filterPeriode === 'hari_ini') {
            $query->whereDate($column, date('Y-m-d'));
        } elseif ($filterPeriode === 'kemarin') {
            $query->whereDate($column, date('Y-m-d', strtotime('-1 day')));
        } elseif ($filterPeriode === 'minggu_ini') {
            $query->whereBetween($column, [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]);
        } elseif ($filterPeriode === 'bulan_ini') {
            $query->whereBetween($column, [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')]);
        } elseif ($filterPeriode === 'custom' || ($startDate && $endDate)) {
            if ($startDate && $endDate) {
                $query->whereBetween($column, [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->whereDate($column, '>=', $startDate);
            } elseif ($endDate) {
                $query->whereDate($column, '<=', $endDate);
            }
        } elseif ($defaultJatuhTempo) {
            $query->jatuhTempo();
        }

        return $query;
    }

    /**
     * Render a DomPDF instance either as stream download or inline browser preview.
     */
    public static function renderPdf($pdf, string $filename, bool $isDownload = false): Response
    {
        if ($isDownload) {
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, ['Content-Type' => 'application/pdf']);
        }

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    /**
     * Fetch filtered pengeluaran query and collection.
     */
    public static function getPengeluaranQuery(array $filters): Builder
    {
        $query = Pengeluaran::with(['kategori', 'petugas']);

        self::applyDateFilter(
            $query,
            'tanggal',
            $filters['filter_periode'] ?? null,
            $filters['start_date'] ?? null,
            $filters['end_date'] ?? null,
            $filters['bulan'] ?? null
        );

        if (!empty($filters['kategori_pengeluaran_id'])) {
            $query->where('kategori_pengeluaran_id', $filters['kategori_pengeluaran_id']);
        }

        if (!empty($filters['search'])) {
            $query->where('keterangan', 'like', '%' . $filters['search'] . '%');
        }

        return $query;
    }

    public static function getPengeluaranData(array $filters): Collection
    {
        return self::getPengeluaranQuery($filters)->orderBy('tanggal', 'asc')->get();
    }

    /**
     * Fetch filtered pemasukan query and collection.
     */
    public static function getPemasukanQuery(array $filters): Builder
    {
        $query = Pembayaran::with(['tagihan.siswa.user', 'tagihan.siswa.kelas', 'tagihan.jenisTagihan', 'petugas']);

        $bulan = $filters['bulan'] ?? null;
        if (!empty($bulan)) {
            $monthIndex = array_search($bulan, self::BULAN_LIST, true);
            if ($monthIndex !== false) {
                $monthNum = $monthIndex + 1;
                $query->where(function ($q) use ($monthNum, $bulan) {
                    $q->whereMonth('tanggal_bayar', $monthNum)
                      ->orWhereHas('tagihan', fn($tq) => $tq->where('bulan', $bulan));
                });
            }
        }

        $filterPeriode = $filters['filter_periode'] ?? null;
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

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

        if (!empty($filters['metode_bayar'])) {
            $query->where('metode_bayar', $filters['metode_bayar']);
        }

        if (!empty($filters['jenis_tagihan_id'])) {
            $query->whereHas('tagihan', function ($q) use ($filters) {
                $q->where('jenis_tagihan_id', $filters['jenis_tagihan_id']);
            });
        }

        if (!empty($filters['search'])) {
            $query->whereHas('tagihan.siswa.user', function ($q) use ($filters) {
                $q->where('nama', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query;
    }

    public static function getPemasukanData(array $filters): Collection
    {
        return self::getPemasukanQuery($filters)->orderBy('tanggal_bayar', 'asc')->get();
    }

    /**
     * Fetch filtered tunggakan query and collection.
     */
    public static function getTunggakanQuery(array $filters): Builder
    {
        $query = Tagihan::with(['siswa.user', 'siswa.kelas', 'jenisTagihan'])
            ->where('status', '!=', 'lunas');

        if (!empty($filters['kelas_id'])) {
            $query->whereHas('siswa', function ($q) use ($filters) {
                $q->where('kelas_id', $filters['kelas_id']);
            });
        }

        if (!empty($filters['tahun_ajaran_id'])) {
            $query->where('tahun_ajaran_id', $filters['tahun_ajaran_id']);
        }

        if (!empty($filters['search'])) {
            $query->whereHas('siswa.user', function ($q) use ($filters) {
                $q->where('nama', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['bulan'])) {
            $query->where('bulan', $filters['bulan']);
        }

        self::applyDateFilter(
            $query,
            'jatuh_tempo',
            $filters['filter_periode'] ?? null,
            $filters['start_date'] ?? null,
            $filters['end_date'] ?? null,
            null,
            true // default jatuh tempo jika tidak ada filter periode
        );

        return $query;
    }

    public static function getTunggakanData(array $filters): Collection
    {
        return self::getTunggakanQuery($filters)->orderBy('created_at', 'desc')->get();
    }

    /**
     * Fetch filtered Dana BOS query and collection.
     */
    public static function getDanaBosQuery(array $filters): Builder
    {
        $query = DanaBos::with('tahunAjaran');

        $jenis = $filters['jenis'] ?? 'semua';
        if ($jenis !== 'semua' && in_array($jenis, ['masuk', 'keluar'], true)) {
            $query->where('jenis', $jenis);
        }

        self::applyDateFilter(
            $query,
            'tanggal',
            $filters['filter_periode'] ?? null,
            $filters['start_date'] ?? null,
            $filters['end_date'] ?? null
        );

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('kategori', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    public static function getDanaBosData(array $filters): Collection
    {
        return self::getDanaBosQuery($filters)->orderBy('tanggal', 'asc')->get();
    }

    /**
     * Fetch filtered rekap gaji query and collection.
     */
    public static function getRekapGajiQuery(array $filters): Builder
    {
        $query = GajiGuru::with(['guru.user']);

        if (!empty($filters['ids'])) {
            $ids = is_array($filters['ids']) ? $filters['ids'] : explode(',', $filters['ids']);
            $query->whereIn('id', array_filter($ids));
        } else {
            if (!empty($filters['bulan'])) {
                $query->where('bulan', $filters['bulan']);
            }
            if (!empty($filters['tahun'])) {
                $query->where('tahun', $filters['tahun']);
            }
            if (!empty($filters['status']) && in_array($filters['status'], ['draft', 'dibayar'], true)) {
                $query->where('status', $filters['status']);
            }
            if (!empty($filters['sumber_dana'])) {
                $query->where('sumber_dana', $filters['sumber_dana']);
            }
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->whereHas('guru.user', function ($sub) use ($search) {
                        $sub->where('nama', 'like', '%' . $search . '%');
                    })->orWhere('jabatan', 'like', '%' . $search . '%')
                      ->orWhereHas('guru', function ($sub) use ($search) {
                          $sub->where('nip', 'like', '%' . $search . '%');
                      });
                });
            }
        }

        return $query;
    }

    public static function getRekapGajiData(array $filters): Collection
    {
        return self::getRekapGajiQuery($filters)->orderBy('guru_id', 'asc')->get();
    }

    /**
     * Fetch filtered tabungan history query.
     */
    public static function getTabunganHistoryQuery(array $filters): Builder
    {
        $query = Tabungan::with(['siswa.user', 'siswa.kelas', 'petugas']);

        if (!empty($filters['kelas_id'])) {
            $query->whereHas('siswa', function ($q) use ($filters) {
                $q->where('kelas_id', $filters['kelas_id']);
            });
        }

        if (!empty($filters['jenis']) && in_array($filters['jenis'], ['setor', 'tarik'], true)) {
            $query->where('jenis', $filters['jenis']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%')
                  ->orWhereHas('siswa.user', function ($uq) use ($search) {
                      $uq->where('nama', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('siswa', function ($sq) use ($search) {
                      $sq->where('nis', 'like', '%' . $search . '%');
                  });
            });
        }

        self::applyDateFilter(
            $query,
            'tanggal',
            $filters['filter_periode'] ?? null,
            $filters['start_date'] ?? null,
            $filters['end_date'] ?? null
        );

        return $query;
    }

    public static function getTabunganHistoryData(array $filters): Collection
    {
        return self::getTabunganHistoryQuery($filters)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Calculate individual student savings ledger and running balances.
     */
    public static function getSingleSiswaTabunganMutasi(int $siswaId): array
    {
        $siswa = Siswa::with(['user', 'kelas'])->findOrFail($siswaId);
        $txRecords = Tabungan::where('siswa_id', $siswaId)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $mutasi = [];
        $runningBalance = 0.0;
        $totalSetor = 0.0;
        $totalTarik = 0.0;

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

        return [
            'siswa' => $siswa,
            'mutasi' => $mutasi,
            'totalSetor' => $totalSetor,
            'totalTarik' => $totalTarik,
            'saldoAkhir' => $runningBalance,
        ];
    }

    /**
     * Get summary balance list for all students.
     */
    public static function getTabunganSummaryData(array $filters): \Illuminate\Support\Collection
    {
        $query = Siswa::with(['user', 'kelas', 'tabungans']);

        if (!empty($filters['kelas_id'])) {
            $query->where('kelas_id', $filters['kelas_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('username', 'like', '%' . $search . '%');
            });
        }

        $siswas = $query->get();

        return $siswas->map(function ($s) {
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
    }

    /**
     * Forward to terbilang helper.
     */
    public static function terbilang(mixed $angka): string
    {
        return \terbilang($angka);
    }
}
