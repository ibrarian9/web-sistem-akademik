<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\Pembayaran;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;

class OverviewPembayaran extends Component
{
    use WithPagination;

    // View Mode Tab: 'spp_matrix' (leger SPP 12 bulan) vs 'lengkap' (matriks per jenis & bulan) vs 'default' (ringkasan siswa)
    public string $activeViewTab = 'spp_matrix';

    // Filters for Matriks SPP 6 Bulan
    public string $sppPeriode = 'ganjil'; // 'ganjil' (semester 1: Juli-Des), 'genap' (semester 2: Jan-Jun), 'terakhir' (6 bulan berjalan)
    public int $perPageSppMatrix = 20;
    public string $filterStatusSpp = ''; // '' for all, 'menunggak', 'lunas'

    // Filters for Default Table
    public string $search = '';
    public ?int $filterKelas = null;
    public string $filterStatus = ''; // 'lunas', 'tunggakan'
    public ?int $filterTahunAjaran = null;
    public int $perPage = 10;

    // Filters for Tabel Lengkap (Grup Per Bulan: X=Jenis Tagihan, Y=Siswa)
    public string $selectedBulan = 'Juli';
    public string $filterStatusBulan = ''; // '' for all, 'belum_bayar', 'lunas'
    public int $perPageLengkap = 20;

    // Per-Murid Matrix View (X=Jenis Tagihan, Y=12 Bulan)
    public ?int $selectedSiswaMatrixId = null;
    public bool $showSiswaMatrixModal = false;

    // Backward-compatible alias properties
    public ?int $filterJenisTagihan = null;
    public string $filterBulanLengkap = '';
    public string $filterStatusLengkap = '';

    public array $standardMonths = [
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'filterKelas' => ['except' => null],
        'filterStatus' => ['except' => ''],
        'filterTahunAjaran' => ['except' => null],
        'activeViewTab' => ['except' => 'spp_matrix'],
        'sppPeriode' => ['except' => 'ganjil'],
        'selectedBulan' => ['except' => 'Juli'],
        'filterStatusBulan' => ['except' => ''],
        'filterStatusSpp' => ['except' => ''],
    ];

    public function mount(): void
    {
        // Default to active school year
        $activeYear = TahunAjaran::where('status_aktif', true)->first();
        if ($activeYear) {
            $this->filterTahunAjaran = $activeYear->id;
        } else {
            $this->filterTahunAjaran = TahunAjaran::latest()->first()?->id;
        }

        // Default to current academic month if matching, otherwise Juli
        $monthIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $currMonth = $monthIndo[(int) date('n')] ?? 'Juli';
        if (in_array($currMonth, $this->standardMonths)) {
            $this->selectedBulan = $currMonth;
        } else {
            $this->selectedBulan = 'Juli';
        }
        $this->filterBulanLengkap = $this->selectedBulan;
    }

    public function setViewTab(string $tab): void
    {
        $this->activeViewTab = $tab;
        $this->resetPage();
        $this->resetPage('pageSpp');
    }

    public function updatingActiveViewTab(): void
    {
        $this->resetPage();
        $this->resetPage('pageSpp');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->resetPage('pageSpp');
    }

    public function updatingFilterKelas(): void
    {
        $this->resetPage();
        $this->resetPage('pageSpp');
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTahunAjaran(): void
    {
        $this->resetPage();
        $this->resetPage('pageSpp');
    }

    public function updatingSelectedBulan(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatusBulan(): void
    {
        $this->resetPage();
    }

    public function updatingPerPageLengkap(): void
    {
        $this->resetPage();
    }

    public function updatingPerPageSppMatrix(): void
    {
        $this->resetPage('pageSpp');
    }

    public function filterByStatusSpp(string $status): void
    {
        $this->filterStatusSpp = ($this->filterStatusSpp === $status) ? '' : $status;
        $this->resetPage('pageSpp');
    }

    public function updatingSppPeriode(): void
    {
        $this->resetPage('pageSpp');
    }

    public function setSppPeriode(string $periode): void
    {
        $this->sppPeriode = $periode;
        $this->resetPage('pageSpp');
    }

    public function getSppMatrixMonths(): array
    {
        if ($this->sppPeriode === 'ganjil') {
            return ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        }

        if ($this->sppPeriode === 'genap') {
            return ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];
        }

        // Default 'terakhir': 6 bulan terakhir s/d bulan berjalan atau terpilih
        $calendarMonths = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $currMonth = $this->selectedBulan;
        $currIdx = array_search($currMonth, $calendarMonths);
        if ($currIdx === false) {
            $currIdx = 8; // September
        }

        $result = [];
        for ($i = 5; $i >= 0; $i--) {
            $idx = ($currIdx - $i + 12) % 12;
            $result[] = $calendarMonths[$idx];
        }

        return $result;
    }

    public function selectBulan(string $bulan): void
    {
        $this->selectedBulan = $bulan;
        $this->filterBulanLengkap = $bulan;
        $this->resetPage();
    }

    public function filterByBulanStatus(string $status): void
    {
        if ($this->filterStatusBulan === $status) {
            $this->filterStatusBulan = '';
            $this->filterStatusLengkap = '';
        } else {
            $this->filterStatusBulan = $status;
            $this->filterStatusLengkap = $status;
        }
        $this->resetPage();
    }

    public function openSiswaMatrix(int $siswaId): void
    {
        $this->selectedSiswaMatrixId = $siswaId;
        $this->showSiswaMatrixModal = true;
    }

    public function closeSiswaMatrix(): void
    {
        $this->selectedSiswaMatrixId = null;
        $this->showSiswaMatrixModal = false;
    }

    public function filterByStatus(string $status): void
    {
        if ($this->filterStatus === $status) {
            $this->filterStatus = '';
        } else {
            $this->filterStatus = $status;
        }
        $this->resetPage();
    }

    public function kirimReminder(int $siswaId)
    {
        $siswa = Siswa::with('user')->findOrFail($siswaId);
        $cutoff = now()->endOfMonth()->toDateString();
        
        // Calculate sisa tunggakan riil (hanya tagihan jatuh tempo s/d bulan berjalan)
        $tunggakan = Tagihan::where('siswa_id', $siswaId)
            ->where('tahun_ajaran_id', $this->filterTahunAjaran)
            ->tunggakan($cutoff)
            ->get();
        
        $totalSisa = $tunggakan->sum(fn($t) => $t->nominal - $t->total_dibayar);

        if ($totalSisa <= 0) {
            session()->flash('error', "Siswa {$siswa->user->nama} tidak memiliki tunggakan jatuh tempo pada tahun ajaran ini.");
            return;
        }

        // Create in-app notification
        Notifikasi::create([
            'user_id' => $siswa->user_id,
            'judul' => 'Reminder Tunggakan SPP/Tagihan',
            'isi_pesan' => 'Halo, mohon segera menyelesaikan tunggakan administrasi sekolah Anda sebesar Rp ' . number_format($totalSisa, 0, ',', '.') . ' untuk periode berjalan. Terima kasih.',
            'jenis' => 'tunggakan',
        ]);

        session()->flash('message', "Reminder tunggakan berhasil dikirim ke siswa/wali {$siswa->user->nama}.");
    }

    public function render()
    {
        $tahunAjarans = TahunAjaran::orderBy('nama', 'desc')->get();
        $kelases = Kelas::all();
        $cutoff = now()->endOfMonth()->toDateString();

        // Calculate statistics based on current school year filter
        $statQuery = Tagihan::query();
        if ($this->filterTahunAjaran) {
            $statQuery->where('tahun_ajaran_id', $this->filterTahunAjaran);
        }

        $totalNominal = (float) $statQuery->sum('nominal');
        $totalDibayar = (float) $statQuery->sum('total_dibayar');

        // Nominal tunggakan jatuh tempo s/d bulan berjalan
        $nominalTunggakan = (float) ((clone $statQuery)->tunggakan($cutoff)
            ->selectRaw('SUM(nominal - total_dibayar) as aggregate')
            ->value('aggregate') ?? 0.0);

        // Nominal tagihan periode mendatang
        $nominalMendatang = (float) ((clone $statQuery)->mendatang($cutoff)
            ->selectRaw('SUM(nominal - total_dibayar) as aggregate')
            ->value('aggregate') ?? 0.0);
        
        $realisasiPersen = $totalNominal > 0 ? round(($totalDibayar / $totalNominal) * 100, 1) : 0;

        // Count of students in arrears vs fully paid (s/d bulan berjalan)
        $studentDueSub = Tagihan::select('siswa_id')
            ->selectRaw('SUM(nominal) as total_n')
            ->selectRaw('SUM(total_dibayar) as total_d')
            ->jatuhTempo($cutoff)
            ->when($this->filterTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $this->filterTahunAjaran))
            ->groupBy('siswa_id');

        $tunggakanCount = DB::table(DB::raw("({$studentDueSub->toSql()}) as sub"))
            ->mergeBindings($studentDueSub->getQuery())
            ->whereRaw('total_n > total_d')
            ->count();

        $lunasCount = DB::table(DB::raw("({$studentDueSub->toSql()}) as sub"))
            ->mergeBindings($studentDueSub->getQuery())
            ->whereRaw('total_n = total_d')
            ->count();

        // 1. Data for TABEL DEFAULT (Ringkasan Siswa)
        $query = Siswa::with([
            'user', 
            'kelas',
            'tagihans' => function ($q) {
                if ($this->filterTahunAjaran) {
                    $q->where('tahun_ajaran_id', $this->filterTahunAjaran);
                }
                $q->with('pembayarans');
            }
        ])
        ->whereHas('user', function ($q) {
            $q->where('nama', 'like', '%' . $this->search . '%')
              ->orWhere('username', 'like', '%' . $this->search . '%');
        });

        if ($this->filterKelas) {
            $query->where('kelas_id', $this->filterKelas);
        }

        $siswas = $query->get()->map(function ($siswa) use ($cutoff) {
            $tagihans = $siswa->tagihans;
            $allPembayarans = $tagihans->flatMap(fn($t) => $t->pembayarans);

            $dueTagihans = $tagihans->filter(fn($t) => $t->is_tunggakan || $t->status === 'lunas');
            $upcomingTagihans = $tagihans->filter(fn($t) => $t->is_mendatang);

            $dueNominal = (float) $dueTagihans->sum('nominal');
            $duePaid = (float) $dueTagihans->sum('total_dibayar');
            $sisaTunggakan = max(0, $dueNominal - $duePaid);

            $futureNominal = (float) $upcomingTagihans->sum('nominal');
            $futurePaid = (float) $upcomingTagihans->sum('total_dibayar');
            $sisaMendatang = max(0, $futureNominal - $futurePaid);

            $totalNominal = (float) $tagihans->sum('nominal');
            $totalPaid = (float) $tagihans->sum('total_dibayar');

            $status = 'Lunas Semua';
            if ($tagihans->count() === 0) {
                $status = 'Belum Ada Tagihan';
            } elseif ($sisaTunggakan > 0) {
                $status = 'Ada Tunggakan';
            } elseif ($sisaMendatang > 0) {
                $status = 'Tertib Berjalan';
            }

            return [
                'id' => $siswa->id,
                'nama' => $siswa->user->nama ?? '-',
                'nis' => $siswa->nis,
                'kelas' => $siswa->kelas->nama_kelas ?? 'Belum Diatur',
                'total_tagihan_count' => $tagihans->count(),
                'lunas_count' => $tagihans->where('status', 'lunas')->count(),
                'belum_lunas_count' => $tagihans->where('status', '!=', 'lunas')->count(),
                'total_nominal' => $totalNominal,
                'total_dibayar' => $totalPaid,
                'sisa_tunggakan' => $sisaTunggakan,
                'sisa_mendatang' => $sisaMendatang,
                'terakhir_bayar' => $allPembayarans->max('tanggal_bayar') ? \Carbon\Carbon::parse($allPembayarans->max('tanggal_bayar'))->format('d-m-Y') : '-',
                'status' => $status,
            ];
        });

        if ($this->filterStatus) {
            $siswas = $siswas->filter(function ($item) {
                if ($this->filterStatus === 'lunas') {
                    return $item['status'] === 'Lunas Semua' || $item['status'] === 'Tertib Berjalan';
                } elseif ($this->filterStatus === 'tunggakan') {
                    return $item['status'] === 'Ada Tunggakan';
                }
                return true;
            });
        }

        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $paginatedItems = $siswas->slice(($currentPage - 1) * $this->perPage, $this->perPage)->all();
        $siswasPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $siswas->count(),
            $this->perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // 2. Data for TABEL LENGKAP
        // Active Jenis Tagihan List for Column Headers (Sumbu X)
        $jenisTagihanList = JenisTagihan::where('nama', 'not like', '%Infaq%')
            ->where('nama', 'not like', '%Sedekah%')
            ->orderBy('id')
            ->get();

        // 12 Months Statistics for Month Filter Strip
        $monthlyStats = [];
        foreach ($this->standardMonths as $m) {
            $monthBills = Tagihan::where('bulan', $m)
                ->when($this->filterTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $this->filterTahunAjaran))
                ->get();

            $totalNom = (float) $monthBills->sum('nominal');
            $totalDib = (float) $monthBills->sum('total_dibayar');
            $totalSis = max(0, $totalNom - $totalDib);
            $lunasCnt = $monthBills->where('status', 'lunas')->count();
            $belumCnt = $monthBills->where('status', '!=', 'lunas')->count();
            $prc = $totalNom > 0 ? round(($totalDib / $totalNom) * 100, 1) : 0;

            $monthlyStats[$m] = [
                'total_tagihan' => $monthBills->count(),
                'lunas_count' => $lunasCnt,
                'belum_count' => $belumCnt,
                'nominal' => $totalNom,
                'dibayar' => $totalDib,
                'sisa' => $totalSis,
                'persen' => $prc,
            ];
        }

        // Active Month for All-Students Grouping
        $targetBulan = $this->selectedBulan ?: 'Juli';

        // Eager load students for Tabel Lengkap
        $queryLengkap = Siswa::with([
            'user',
            'kelas',
            'tagihans' => function ($q) {
                if ($this->filterTahunAjaran) {
                    $q->where('tahun_ajaran_id', $this->filterTahunAjaran);
                }
                $q->with('pembayarans');
            }
        ])
        ->whereHas('user', function ($q) {
            $q->where('nama', 'like', '%' . $this->search . '%')
              ->orWhere('username', 'like', '%' . $this->search . '%');
        });

        if ($this->filterKelas) {
            $queryLengkap->where('kelas_id', $this->filterKelas);
        }

        $standardMonthsList = $this->standardMonths;
        $siswasLengkap = $queryLengkap->get()->map(function ($siswa) use ($targetBulan, $jenisTagihanList, $standardMonthsList) {
            $billsByJenis = [];
            $totalNominalBulan = 0.0;
            $totalDibayarBulan = 0.0;
            $totalTunggakanBulan = 0.0;
            $hasAnyBillThisMonth = false;

            foreach ($jenisTagihanList as $jt) {
                $isNonRutin = ($jt->kategori !== 'rutin') || !str_contains(strtolower($jt->nama), 'spp');

                $tagihan = $siswa->tagihans->where('jenis_tagihan_id', $jt->id)
                    ->first(function ($t) use ($targetBulan) {
                        return $t->bulan === $targetBulan || 
                               ($targetBulan === 'Juli' && in_array($t->bulan, ['Tahunan', null]));
                    });

                if ($tagihan) {
                    $hasAnyBillThisMonth = true;
                    $nom = (float) $tagihan->nominal;
                    $bayar = (float) $tagihan->total_dibayar;
                    $sisa = max(0, $nom - $bayar);

                    $st = 'belum_bayar';
                    if ($tagihan->status === 'lunas' || ($nom > 0 && $bayar >= $nom) || $nom == 0) {
                        $st = 'lunas';
                    } elseif ($bayar > 0 && $bayar < $nom) {
                        $st = 'sebagian';
                    }

                    $totalNominalBulan += $nom;
                    $totalDibayarBulan += $bayar;
                    $totalTunggakanBulan += $sisa;

                    $billsByJenis[$jt->id] = [
                        'has_tagihan' => true,
                        'tagihan_id' => $tagihan->id,
                        'nominal' => $nom,
                        'total_dibayar' => $bayar,
                        'sisa' => $sisa,
                        'status' => $st,
                        'is_one_time_fulfilled' => false,
                        'is_one_time_carried' => false,
                        'original_bulan' => $tagihan->bulan,
                        'original_nominal' => $nom,
                        'terakhir_bayar' => $tagihan->pembayarans->max('tanggal_bayar') ? \Carbon\Carbon::parse($tagihan->pembayarans->max('tanggal_bayar'))->format('d/m/Y') : null,
                    ];
                } elseif ($isNonRutin && ($existingOneTime = $siswa->tagihans->where('jenis_tagihan_id', $jt->id)->first())) {
                    $isLunas = ($existingOneTime->status === 'lunas' || ((float)$existingOneTime->nominal > 0 && (float)$existingOneTime->total_dibayar >= (float)$existingOneTime->nominal) || (float)$existingOneTime->nominal == 0);
                    $origNom = (float) $existingOneTime->nominal;
                    $origBayar = (float) $existingOneTime->total_dibayar;
                    $origSisa = max(0, $origNom - $origBayar);
                    $origBulan = $existingOneTime->bulan ?: 'Tahunan';

                    if ($isLunas) {
                        $hasAnyBillThisMonth = true;
                        $billsByJenis[$jt->id] = [
                            'has_tagihan' => true,
                            'tagihan_id' => $existingOneTime->id,
                            'nominal' => 0.0,
                            'total_dibayar' => 0.0,
                            'sisa' => 0.0,
                            'status' => 'lunas',
                            'is_one_time_fulfilled' => true,
                            'is_one_time_carried' => false,
                            'original_bulan' => $origBulan,
                            'original_nominal' => $origNom,
                            'terakhir_bayar' => $existingOneTime->pembayarans->max('tanggal_bayar') ? \Carbon\Carbon::parse($existingOneTime->pembayarans->max('tanggal_bayar'))->format('d/m/Y') : null,
                        ];
                    } else {
                        $billedIdx = array_search($origBulan, $standardMonthsList);
                        $currIdx = array_search($targetBulan, $standardMonthsList);
                        $isAfterBilled = ($billedIdx === false) || ($currIdx !== false && $currIdx > $billedIdx);

                        if ($isAfterBilled) {
                            $hasAnyBillThisMonth = true;
                            $st = ($origBayar > 0) ? 'sebagian' : 'belum_bayar';
                            $billsByJenis[$jt->id] = [
                                'has_tagihan' => true,
                                'tagihan_id' => $existingOneTime->id,
                                'nominal' => 0.0,
                                'total_dibayar' => 0.0,
                                'sisa' => $origSisa,
                                'status' => $st,
                                'is_one_time_fulfilled' => false,
                                'is_one_time_carried' => true,
                                'original_bulan' => $origBulan,
                                'original_nominal' => $origNom,
                                'terakhir_bayar' => $existingOneTime->pembayarans->max('tanggal_bayar') ? \Carbon\Carbon::parse($existingOneTime->pembayarans->max('tanggal_bayar'))->format('d/m/Y') : null,
                            ];
                        } else {
                            $billsByJenis[$jt->id] = [
                                'has_tagihan' => false,
                                'tagihan_id' => null,
                                'nominal' => 0.0,
                                'total_dibayar' => 0.0,
                                'sisa' => 0.0,
                                'status' => 'tidak_ada',
                                'is_one_time_fulfilled' => false,
                                'is_one_time_carried' => false,
                                'original_bulan' => null,
                                'original_nominal' => 0.0,
                                'terakhir_bayar' => null,
                            ];
                        }
                    }
                } else {
                    $billsByJenis[$jt->id] = [
                        'has_tagihan' => false,
                        'tagihan_id' => null,
                        'nominal' => 0.0,
                        'total_dibayar' => 0.0,
                        'sisa' => 0.0,
                        'status' => 'tidak_ada',
                        'is_one_time_fulfilled' => false,
                        'is_one_time_carried' => false,
                        'original_bulan' => null,
                        'original_nominal' => 0.0,
                        'terakhir_bayar' => null,
                    ];
                }
            }

            $statusGlobalBulan = 'Belum Ada Tagihan';
            if ($hasAnyBillThisMonth) {
                if ($totalTunggakanBulan > 0) {
                    $statusGlobalBulan = 'Ada Tunggakan';
                } else {
                    $statusGlobalBulan = 'Lunas';
                }
            }

            return [
                'id' => $siswa->id,
                'nama' => $siswa->user->nama ?? '-',
                'nis' => $siswa->nis,
                'kelas' => $siswa->kelas->nama_kelas ?? 'Belum Diatur',
                'bills_by_jenis' => $billsByJenis,
                'total_nominal_bulan' => $totalNominalBulan,
                'total_dibayar_bulan' => $totalDibayarBulan,
                'total_tunggakan_bulan' => $totalTunggakanBulan,
                'has_tagihan' => $hasAnyBillThisMonth,
                'status_bulan' => $statusGlobalBulan,
            ];
        });

        // Summary totals per Jenis Tagihan column in active month
        $footerSummaryJenis = [];
        foreach ($jenisTagihanList as $jt) {
            $sumNom = 0.0;
            $sumDib = 0.0;
            $sumSis = 0.0;
            $countLunas = 0;
            $countBelum = 0;

            foreach ($siswasLengkap as $s) {
                $b = $s['bills_by_jenis'][$jt->id];
                if ($b['has_tagihan']) {
                    $sumNom += $b['nominal'];
                    $sumDib += $b['total_dibayar'];
                    $sumSis += $b['sisa'];
                    if ($b['status'] === 'lunas') {
                        $countLunas++;
                    } else {
                        $countBelum++;
                    }
                }
            }

            $footerSummaryJenis[$jt->id] = [
                'nominal' => $sumNom,
                'dibayar' => $sumDib,
                'sisa' => $sumSis,
                'lunas_count' => $countLunas,
                'belum_count' => $countBelum,
            ];
        }

        // Filter by payment status in this month (belum_bayar / lunas)
        $activeStatusFilter = $this->filterStatusBulan ?: $this->filterStatusLengkap;
        if ($activeStatusFilter === 'belum_bayar') {
            $siswasLengkap = $siswasLengkap->filter(fn($item) => $item['total_tunggakan_bulan'] > 0);
        } elseif ($activeStatusFilter === 'lunas') {
            $siswasLengkap = $siswasLengkap->filter(fn($item) => $item['has_tagihan'] && $item['total_tunggakan_bulan'] <= 0);
        }

        $currentPageLengkap = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $paginatedLengkapItems = $siswasLengkap->slice(($currentPageLengkap - 1) * $this->perPageLengkap, $this->perPageLengkap)->all();
        $siswasLengkapPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedLengkapItems,
            $siswasLengkap->count(),
            $this->perPageLengkap,
            $currentPageLengkap,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // 3. Data for PER-MURID MATRIX (Y=12 Bulan, X=Jenis Tagihan)
        $siswaMatrixData = null;
        if ($this->selectedSiswaMatrixId) {
            $targetSiswa = Siswa::with([
                'user',
                'kelas',
                'tagihans' => function ($q) {
                    if ($this->filterTahunAjaran) {
                        $q->where('tahun_ajaran_id', $this->filterTahunAjaran);
                    }
                    $q->with('pembayarans');
                }
            ])->find($this->selectedSiswaMatrixId);

            if ($targetSiswa) {
                $monthsRows = [];
                $grandNominal = 0.0;
                $grandDibayar = 0.0;
                $grandTunggakan = 0.0;

                $hasTahunan = $targetSiswa->tagihans->contains(fn($t) => in_array($t->bulan, ['Tahunan', null]));
                $allRows = $this->standardMonths;
                if ($hasTahunan) {
                    $allRows[] = 'Tahunan';
                }

                foreach ($allRows as $m) {
                    $rowBills = [];
                    $rowNom = 0.0;
                    $rowDib = 0.0;
                    $rowSis = 0.0;

                    foreach ($jenisTagihanList as $jt) {
                        $t = $targetSiswa->tagihans->where('jenis_tagihan_id', $jt->id)
                            ->firstWhere('bulan', $m);

                        if ($t) {
                            $nom = (float) $t->nominal;
                            $bayar = (float) $t->total_dibayar;
                            $sisa = max(0, $nom - $bayar);
                            $st = ($t->status === 'lunas' || ($nom > 0 && $bayar >= $nom) || $nom == 0)
                                ? 'lunas'
                                : ($bayar > 0 ? 'sebagian' : 'belum_bayar');

                            $rowNom += $nom;
                            $rowDib += $bayar;
                            $rowSis += $sisa;

                            $rowBills[$jt->id] = [
                                'has_tagihan' => true,
                                'tagihan_id' => $t->id,
                                'nominal' => $nom,
                                'total_dibayar' => $bayar,
                                'sisa' => $sisa,
                                'status' => $st,
                                'terakhir_bayar' => $t->pembayarans->max('tanggal_bayar') ? \Carbon\Carbon::parse($t->pembayarans->max('tanggal_bayar'))->format('d/m/Y') : null,
                            ];
                        } else {
                            $rowBills[$jt->id] = [
                                'has_tagihan' => false,
                                'tagihan_id' => null,
                                'nominal' => 0.0,
                                'total_dibayar' => 0.0,
                                'sisa' => 0.0,
                                'status' => 'tidak_ada',
                                'terakhir_bayar' => null,
                            ];
                        }
                    }

                    $grandNominal += $rowNom;
                    $grandDibayar += $rowDib;
                    $grandTunggakan += $rowSis;

                    $monthsRows[$m] = [
                        'bulan' => $m,
                        'bills' => $rowBills,
                        'total_nominal' => $rowNom,
                        'total_dibayar' => $rowDib,
                        'sisa_tunggakan' => $rowSis,
                        'status' => ($rowSis > 0) ? 'Ada Tunggakan' : ($rowNom > 0 ? 'Lunas' : '-'),
                    ];
                }

                $studentFooterPerJenis = [];
                foreach ($jenisTagihanList as $jt) {
                    $sumNom = 0.0;
                    $sumDib = 0.0;
                    $sumSis = 0.0;
                    foreach ($allRows as $m) {
                        $b = $monthsRows[$m]['bills'][$jt->id];
                        if ($b['has_tagihan']) {
                            $sumNom += $b['nominal'];
                            $sumDib += $b['total_dibayar'];
                            $sumSis += $b['sisa'];
                        }
                    }
                    $studentFooterPerJenis[$jt->id] = [
                        'nominal' => $sumNom,
                        'dibayar' => $sumDib,
                        'sisa' => $sumSis,
                    ];
                }

                $siswaMatrixData = [
                    'siswa' => $targetSiswa,
                    'months_rows' => $monthsRows,
                    'footer_per_jenis' => $studentFooterPerJenis,
                    'grand_nominal' => $grandNominal,
                    'grand_dibayar' => $grandDibayar,
                    'grand_tunggakan' => $grandTunggakan,
                ];
            }
        }

        // 4. Data for TABEL MATRIKS GABUNGAN (SPP 6 BULAN + KATEGORI NON-SPP)
        $sppMatrixMonths = $this->getSppMatrixMonths();

        // Ambil daftar Jenis Tagihan Non-SPP untuk sumbu X
        $nonSppJenisList = JenisTagihan::where('nama', 'not like', '%SPP%')
            ->where('nama', 'not like', '%Infaq%')
            ->where('nama', 'not like', '%Sedekah%')
            ->where(function ($q) {
                if ($this->filterTahunAjaran) {
                    $q->whereHas('tagihans', fn($tq) => $tq->where('tahun_ajaran_id', $this->filterTahunAjaran))
                      ->orWhere('default_nominal', '>', 0);
                } else {
                    $q->where('default_nominal', '>', 0);
                }
            })
            ->orderBy('id')
            ->get();

        if ($nonSppJenisList->isEmpty()) {
            $nonSppJenisList = JenisTagihan::where('nama', 'not like', '%SPP%')
                ->where('nama', 'not like', '%Infaq%')
                ->where('nama', 'not like', '%Sedekah%')
                ->orderBy('id')
                ->limit(4)
                ->get();
        }

        $querySppMatrix = Siswa::with([
            'user',
            'kelas',
            'tagihans' => function ($q) {
                if ($this->filterTahunAjaran) {
                    $q->where('tahun_ajaran_id', $this->filterTahunAjaran);
                }
                $q->with(['pembayarans', 'jenisTagihan']);
            }
        ])
        ->whereHas('user', function ($q) {
            $q->where('nama', 'like', '%' . $this->search . '%')
              ->orWhere('username', 'like', '%' . $this->search . '%');
        });

        if ($this->filterKelas) {
            $querySppMatrix->where('kelas_id', $this->filterKelas);
        }

        $allStudentsForSpp = $querySppMatrix->get();

        $siswasSppMatrix = $allStudentsForSpp->map(function ($siswa) use ($sppMatrixMonths, $nonSppJenisList) {
            $monthsData = [];
            $totalSppNominal = 0.0;
            $totalSppDibayar = 0.0;
            $totalSppTunggakan = 0.0;

            // 1. Kolom SPP 6 Bulan
            foreach ($sppMatrixMonths as $m) {
                $sppBill = $siswa->tagihans->first(function ($t) use ($m) {
                    return $t->bulan === $m && 
                           (str_contains(strtolower($t->jenisTagihan->nama ?? ''), 'spp') || ($t->jenisTagihan->kategori ?? '') === 'rutin');
                });

                if ($sppBill) {
                    $nom = (float) $sppBill->nominal;
                    $bayar = (float) $sppBill->total_dibayar;
                    $sisa = max(0, $nom - $bayar);
                    $st = ($sppBill->status === 'lunas' || ($nom > 0 && $bayar >= $nom) || $nom == 0)
                        ? 'lunas'
                        : ($bayar > 0 ? 'sebagian' : 'belum_bayar');

                    $totalSppNominal += $nom;
                    $totalSppDibayar += $bayar;
                    $totalSppTunggakan += $sisa;

                    $monthsData[$m] = [
                        'has_tagihan' => true,
                        'tagihan_id' => $sppBill->id,
                        'nominal' => $nom,
                        'total_dibayar' => $bayar,
                        'sisa' => $sisa,
                        'status' => $st,
                        'is_mendatang' => $sppBill->is_mendatang,
                        'terakhir_bayar' => $sppBill->pembayarans->max('tanggal_bayar') ? \Carbon\Carbon::parse($sppBill->pembayarans->max('tanggal_bayar'))->format('d/m/Y') : null,
                    ];
                } else {
                    $monthsData[$m] = [
                        'has_tagihan' => false,
                        'tagihan_id' => null,
                        'nominal' => 0.0,
                        'total_dibayar' => 0.0,
                        'sisa' => 0.0,
                        'status' => 'tidak_ada',
                        'is_mendatang' => false,
                        'terakhir_bayar' => null,
                    ];
                }
            }

            // 2. Kolom Kategori Non-SPP (per Jenis Tagihan pada Sumbu X)
            $nonSppBillsData = [];
            $totalNonSppNominal = 0.0;
            $totalNonSppDibayar = 0.0;
            $totalNonSppTunggakan = 0.0;

            foreach ($nonSppJenisList as $jt) {
                $bills = $siswa->tagihans->where('jenis_tagihan_id', $jt->id);
                if ($bills->isNotEmpty()) {
                    $nom = (float) $bills->sum('nominal');
                    $bayar = (float) $bills->sum('total_dibayar');
                    $sisa = max(0, $nom - $bayar);
                    $unpaidBill = $bills->first(fn($b) => $b->status !== 'lunas' && ($b->nominal - $b->total_dibayar) > 0) ?: $bills->first();

                    $st = 'belum_bayar';
                    if ($nom == 0 || $sisa <= 0 || $bills->every(fn($b) => $b->status === 'lunas')) {
                        $st = 'lunas';
                    } elseif ($bayar > 0) {
                        $st = 'sebagian';
                    }

                    $totalNonSppNominal += $nom;
                    $totalNonSppDibayar += $bayar;
                    $totalNonSppTunggakan += $sisa;

                    $nonSppBillsData[$jt->id] = [
                        'has_tagihan' => true,
                        'tagihan_id' => $unpaidBill?->id,
                        'nominal' => $nom,
                        'total_dibayar' => $bayar,
                        'sisa' => $sisa,
                        'status' => $st,
                        'terakhir_bayar' => $bills->flatMap(fn($b) => $b->pembayarans)->max('tanggal_bayar') ? \Carbon\Carbon::parse($bills->flatMap(fn($b) => $b->pembayarans)->max('tanggal_bayar'))->format('d/m/Y') : null,
                    ];
                } else {
                    $nonSppBillsData[$jt->id] = [
                        'has_tagihan' => false,
                        'tagihan_id' => null,
                        'nominal' => 0.0,
                        'total_dibayar' => 0.0,
                        'sisa' => 0.0,
                        'status' => 'tidak_ada',
                        'terakhir_bayar' => null,
                    ];
                }
            }

            $grandStudentTunggakan = $totalSppTunggakan + $totalNonSppTunggakan;
            $grandStudentNominal = $totalSppNominal + $totalNonSppNominal;

            return [
                'id' => $siswa->id,
                'nama' => $siswa->user->nama ?? '-',
                'nis' => $siswa->nis,
                'kelas' => $siswa->kelas->nama_kelas ?? 'Belum Diatur',
                'spp_months' => $monthsData,
                'total_spp_nominal' => $totalSppNominal,
                'total_spp_dibayar' => $totalSppDibayar,
                'total_spp_tunggakan' => $totalSppTunggakan,
                'non_spp_by_jenis' => $nonSppBillsData,
                'non_spp_nominal' => $totalNonSppNominal,
                'non_spp_dibayar' => $totalNonSppDibayar,
                'non_spp_sisa' => $totalNonSppTunggakan,
                'total_tunggakan' => $grandStudentTunggakan,
                'status_global' => ($grandStudentTunggakan > 0) ? 'Ada Tunggakan' : ($grandStudentNominal > 0 ? 'Lunas' : 'Belum Ada Tagihan'),
            ];
        });

        // Quick Stats for SPP Matrix tab
        $sppStatsSummary = [
            'total_siswa' => $siswasSppMatrix->count(),
            'menunggak_count' => $siswasSppMatrix->where('total_tunggakan', '>', 0)->count(),
            'lunas_count' => $siswasSppMatrix->filter(fn($s) => ($s['total_spp_nominal'] > 0 || $s['non_spp_nominal'] > 0) && $s['total_tunggakan'] <= 0)->count(),
            'total_tunggakan_nominal' => (float) $siswasSppMatrix->sum('total_tunggakan'),
            'total_dibayar_nominal' => (float) ($siswasSppMatrix->sum('total_spp_dibayar') + $siswasSppMatrix->sum('non_spp_dibayar')),
        ];

        // Filter by SPP Status if set
        if ($this->filterStatusSpp === 'menunggak') {
            $siswasSppMatrix = $siswasSppMatrix->filter(fn($item) => $item['total_tunggakan'] > 0);
        } elseif ($this->filterStatusSpp === 'lunas') {
            $siswasSppMatrix = $siswasSppMatrix->filter(fn($item) => ($item['total_spp_nominal'] > 0 || $item['non_spp_nominal'] > 0) && $item['total_tunggakan'] <= 0);
        }

        // Footer Summary per Month for SPP Matrix (6 Bulan)
        $footerSppMatrix = [];
        foreach ($sppMatrixMonths as $m) {
            $sumNom = 0.0;
            $sumDib = 0.0;
            $sumSis = 0.0;
            $countLunas = 0;
            $countBelum = 0;

            foreach ($allStudentsForSpp as $s) {
                $b = $s->tagihans->first(function ($t) use ($m) {
                    return $t->bulan === $m && 
                           (str_contains(strtolower($t->jenisTagihan->nama ?? ''), 'spp') || ($t->jenisTagihan->kategori ?? '') === 'rutin');
                });
                if ($b) {
                    $nom = (float) $b->nominal;
                    $bayar = (float) $b->total_dibayar;
                    $sisa = max(0, $nom - $bayar);
                    $sumNom += $nom;
                    $sumDib += $bayar;
                    $sumSis += $sisa;
                    if ($b->status === 'lunas' || ($nom > 0 && $bayar >= $nom) || $nom == 0) {
                        $countLunas++;
                    } else {
                        $countBelum++;
                    }
                }
            }

            $footerSppMatrix[$m] = [
                'nominal' => $sumNom,
                'dibayar' => $sumDib,
                'sisa' => $sumSis,
                'lunas_count' => $countLunas,
                'belum_count' => $countBelum,
            ];
        }

        // Footer Summary per Kategori Non-SPP
        $footerNonSppMatrix = [];
        foreach ($nonSppJenisList as $jt) {
            $sumNom = 0.0;
            $sumDib = 0.0;
            $sumSis = 0.0;
            $countLunas = 0;
            $countBelum = 0;

            foreach ($allStudentsForSpp as $s) {
                $bills = $s->tagihans->where('jenis_tagihan_id', $jt->id);
                if ($bills->isNotEmpty()) {
                    $nom = (float) $bills->sum('nominal');
                    $bayar = (float) $bills->sum('total_dibayar');
                    $sisa = max(0, $nom - $bayar);
                    $sumNom += $nom;
                    $sumDib += $bayar;
                    $sumSis += $sisa;
                    if ($nom == 0 || $sisa <= 0 || $bills->every(fn($b) => $b->status === 'lunas')) {
                        $countLunas++;
                    } else {
                        $countBelum++;
                    }
                }
            }

            $footerNonSppMatrix[$jt->id] = [
                'nominal' => $sumNom,
                'dibayar' => $sumDib,
                'sisa' => $sumSis,
                'lunas_count' => $countLunas,
                'belum_count' => $countBelum,
            ];
        }

        $currentPageSpp = \Illuminate\Pagination\Paginator::resolveCurrentPage('pageSpp') ?: 1;
        $paginatedSppItems = $siswasSppMatrix->slice(($currentPageSpp - 1) * $this->perPageSppMatrix, $this->perPageSppMatrix)->all();
        $siswasSppMatrixPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedSppItems,
            $siswasSppMatrix->count(),
            $this->perPageSppMatrix,
            $currentPageSpp,
            ['path' => request()->url(), 'query' => request()->query(), 'pageName' => 'pageSpp']
        );

        return view('livewire.finance.overview-pembayaran', [
            'siswas' => $siswasPaginated,
            'siswasLengkap' => $siswasLengkapPaginated,
            'siswasSppMatrix' => $siswasSppMatrixPaginated,
            'footerSppMatrix' => $footerSppMatrix,
            'footerNonSppMatrix' => $footerNonSppMatrix,
            'nonSppJenisList' => $nonSppJenisList,
            'sppStatsSummary' => $sppStatsSummary,
            'sppMatrixMonths' => $sppMatrixMonths,
            'sppPeriode' => $this->sppPeriode,
            'jenisTagihanList' => $jenisTagihanList,
            'monthlyStats' => $monthlyStats,
            'standardMonths' => $this->standardMonths,
            'selectedBulan' => $targetBulan,
            'footerSummaryJenis' => $footerSummaryJenis,
            'siswaMatrixData' => $siswaMatrixData,
            'kelases' => $kelases,
            'tahunAjarans' => $tahunAjarans,
            'tunggakanCount' => $tunggakanCount,
            'lunasCount' => $lunasCount,
            'nominalTunggakan' => $nominalTunggakan,
            'nominalMendatang' => $nominalMendatang,
            'realisasiPersen' => $realisasiPersen,
            'totalNominal' => $totalNominal,
            'totalDibayar' => $totalDibayar,
        ])->layout('components.layouts.app', ['title' => 'Overview Pembayaran Siswa']);
    }
}
