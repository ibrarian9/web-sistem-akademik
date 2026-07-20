<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;

class OverviewPembayaran extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $filterKelas = null;
    public string $filterStatus = ''; // 'lunas', 'tunggakan'
    public ?int $filterTahunAjaran = null;
    public int $perPage = 10;

    // Active dropdowns for details modal
    public ?int $selectedSiswaId = null;
    public $selectedSiswaDetails = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterKelas' => ['except' => null],
        'filterStatus' => ['except' => ''],
        'filterTahunAjaran' => ['except' => null],
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
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterKelas()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterTahunAjaran()
    {
        $this->resetPage();
    }

    public function viewDetails(int $siswaId)
    {
        $this->selectedSiswaId = $siswaId;
        $this->selectedSiswaDetails = Siswa::with(['user', 'kelas', 'tagihans' => function ($query) {
            if ($this->filterTahunAjaran) {
                $query->where('tahun_ajaran_id', $this->filterTahunAjaran);
            }
            $query->with(['jenisTagihan', 'pembayarans'])->latest();
        }])->findOrFail($siswaId);
    }

    public function closeDetails()
    {
        $this->selectedSiswaId = null;
        $this->selectedSiswaDetails = null;
    }

    public function kirimReminder(int $siswaId)
    {
        $siswa = Siswa::with('user')->findOrFail($siswaId);
        
        // Calculate sisa tunggakan
        $tunggakan = Tagihan::where('siswa_id', $siswaId)
            ->where('tahun_ajaran_id', $this->filterTahunAjaran)
            ->where('status', '!=', 'lunas')
            ->get();
        
        $totalSisa = $tunggakan->sum(fn($t) => $t->nominal - $t->total_dibayar);

        if ($totalSisa <= 0) {
            session()->flash('error', "Siswa {$siswa->user->nama} tidak memiliki tunggakan pada tahun ajaran ini.");
            return;
        }

        // Create in-app notification
        Notifikasi::create([
            'user_id' => $siswa->user_id,
            'judul' => 'Reminder Tunggakan SPP/Tagihan',
            'isi_pesan' => 'Halo, mohon segera menyelesaikan tunggakan administrasi sekolah Anda sebesar Rp ' . number_format($totalSisa, 0, ',', '.') . ' untuk tahun ajaran aktif. Terima kasih.',
            'jenis' => 'tunggakan',
        ]);

        session()->flash('message', "Reminder tunggakan berhasil dikirim ke siswa/wali {$siswa->user->nama}.");
    }

    public function voidPayment(int $pembayaranId)
    {
        DB::transaction(function () use ($pembayaranId) {
            $pembayaran = Pembayaran::lockForUpdate()->find($pembayaranId);
            if (!$pembayaran || $pembayaran->is_void) {
                return;
            }

            // Mark void
            $pembayaran->update(['is_void' => true]);

            // Revert tagihan total_dibayar and status
            $tagihan = Tagihan::lockForUpdate()->find($pembayaran->tagihan_id);
            if ($tagihan) {
                $pembayaranEfektif = floatval($pembayaran->nominal_dibayar) - floatval($pembayaran->kelebihan_bayar);
                $newTotalDibayar = max(0, floatval($tagihan->total_dibayar) - $pembayaranEfektif);
                
                $status = 'belum_bayar';
                if ($newTotalDibayar >= floatval($tagihan->nominal)) {
                    $status = 'lunas';
                } elseif ($newTotalDibayar > 0) {
                    $status = 'sebagian';
                }

                $tagihan->update([
                    'total_dibayar' => $newTotalDibayar,
                    'status' => $status,
                ]);

                // Revert student deposit if any overpayment occurred
                if ($pembayaran->kelebihan_bayar > 0) {
                    $siswa = Siswa::lockForUpdate()->find($tagihan->siswa_id);
                    if ($siswa) {
                        $newDeposit = max(0, floatval($siswa->saldo_deposit) - floatval($pembayaran->kelebihan_bayar));
                        $siswa->update(['saldo_deposit' => $newDeposit]);
                    }
                }
            }
        });

        session()->flash('message', 'Transaksi pembayaran berhasil dibatalkan (VOID).');
        if ($this->selectedSiswaId) {
            $this->viewDetails($this->selectedSiswaId);
        }
    }

    public function render()
    {
        $tahunAjarans = TahunAjaran::orderBy('nama', 'desc')->get();
        $kelases = Kelas::all();

        // Calculate statistics based on current school year filter
        $statQuery = Tagihan::query();
        if ($this->filterTahunAjaran) {
            $statQuery->where('tahun_ajaran_id', $this->filterTahunAjaran);
        }

        $totalNominal = (float) $statQuery->sum('nominal');
        $totalDibayar = (float) $statQuery->sum('total_dibayar');
        $nominalTunggakan = $totalNominal - $totalDibayar;
        
        $realisasiPersen = $totalNominal > 0 ? round(($totalDibayar / $totalNominal) * 100, 1) : 0;

        // Count of students in arrears vs fully paid
        // Query to get student aggregates
        $studentSub = Tagihan::select('siswa_id')
            ->selectRaw('SUM(nominal) as total_n')
            ->selectRaw('SUM(total_dibayar) as total_d')
            ->when($this->filterTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $this->filterTahunAjaran))
            ->groupBy('siswa_id');

        $tunggakanCount = DB::table(DB::raw("({$studentSub->toSql()}) as sub"))
            ->mergeBindings($studentSub->getQuery())
            ->whereRaw('total_n > total_d')
            ->count();

        $lunasCount = DB::table(DB::raw("({$studentSub->toSql()}) as sub"))
            ->mergeBindings($studentSub->getQuery())
            ->whereRaw('total_n = total_d')
            ->count();

        // Main Query
        $query = Siswa::with(['user', 'kelas'])
            ->whereHas('user', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('username', 'like', '%' . $this->search . '%');
            });

        if ($this->filterKelas) {
            $query->where('kelas_id', $this->filterKelas);
        }

        // Aggregate per student for display & filtering
        $siswas = $query->get()->map(function ($siswa) {
            $tagihans = Tagihan::where('siswa_id', $siswa->id)
                ->when($this->filterTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $this->filterTahunAjaran))
                ->get();

            $pembayarans = Pembayaran::whereIn('tagihan_id', $tagihans->pluck('id'))->get();

            $totalNominal = $tagihans->sum('nominal');
            $totalPaid = $tagihans->sum('total_dibayar');
            $sisaTunggakan = $totalNominal - $totalPaid;

            $status = 'Lunas Semua';
            if ($tagihans->count() === 0) {
                $status = 'Belum Ada Tagihan';
            } elseif ($sisaTunggakan > 0) {
                $status = 'Ada Tunggakan';
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
                'terakhir_bayar' => $pembayarans->max('tanggal_bayar')?->format('d-m-Y') ?? '-',
                'status' => $status,
            ];
        });

        // Filter status in memory
        if ($this->filterStatus) {
            $siswas = $siswas->filter(function ($item) {
                if ($this->filterStatus === 'lunas') {
                    return $item['status'] === 'Lunas Semua';
                } elseif ($this->filterStatus === 'tunggakan') {
                    return $item['status'] === 'Ada Tunggakan';
                }
                return true;
            });
        }

        // Manual Pagination for the collection
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $paginatedItems = $siswas->slice(($currentPage - 1) * $this->perPage, $this->perPage)->all();
        $siswasPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $siswas->count(),
            $this->perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.finance.overview-pembayaran', [
            'siswas' => $siswasPaginated,
            'kelases' => $kelases,
            'tahunAjarans' => $tahunAjarans,
            'tunggakanCount' => $tunggakanCount,
            'lunasCount' => $lunasCount,
            'nominalTunggakan' => $nominalTunggakan,
            'realisasiPersen' => $realisasiPersen,
        ])->layout('components.layouts.app', ['title' => 'Overview Pembayaran Siswa']);
    }
}
