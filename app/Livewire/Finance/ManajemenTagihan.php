<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Tagihan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ManajemenTagihan extends Component
{
    use WithPagination;

    // Filters
    public ?int $filterKelas = null;
    public ?int $filterJenis = null;
    public string $filterStatus = '';

    // Create Tagihan Form properties
    public ?int $kelas_id = null;
    public ?int $jenis_tagihan_id = null;
    public string $bulan = '';
    public float $nominal = 0.00;
    public string $jatuh_tempo = '';

    // Option lists
    public array $classes = [];
    public array $jenisTagihans = [];

    protected $rules = [
        'kelas_id' => 'required|exists:kelas,id',
        'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
        'bulan' => 'required|string|max:50',
        'nominal' => 'required|numeric|min:1',
        'jatuh_tempo' => 'required|date',
    ];

    public function mount()
    {
        $this->classes = Kelas::orderBy('nama_kelas')->get()->toArray();
        $this->jenisTagihans = JenisTagihan::all()->toArray();
        $this->jatuh_tempo = date('Y-m-d', strtotime('+30 days'));
    }

    public function updatedJenisTagihanId($value)
    {
        if ($value) {
            $this->nominal = floatval(JenisTagihan::where('id', $value)->value('default_nominal') ?? 0.00);
        }
    }

    public function createBulkTagihan()
    {
        $this->validate();

        $activeTA = TahunAjaran::where('status_aktif', true)->first();
        if (!$activeTA) {
            session()->flash('error', 'Tidak ada tahun ajaran aktif.');
            return;
        }

        $students = Siswa::where('kelas_id', $this->kelas_id)
            ->where('status', 'aktif')
            ->get();

        if ($students->isEmpty()) {
            session()->flash('error', 'Tidak ada siswa aktif terdaftar di kelas terpilih.');
            return;
        }

        $createdCount = 0;

        DB::transaction(function () use ($students, $activeTA, &$createdCount) {
            foreach ($students as $student) {
                // Avoid duplicating exact billing category & period for student
                $exists = Tagihan::where([
                    'siswa_id' => $student->id,
                    'jenis_tagihan_id' => $this->jenis_tagihan_id,
                    'tahun_ajaran_id' => $activeTA->id,
                    'bulan' => $this->bulan,
                ])->exists();

                if (!$exists) {
                    Tagihan::create([
                        'siswa_id' => $student->id,
                        'jenis_tagihan_id' => $this->jenis_tagihan_id,
                        'tahun_ajaran_id' => $activeTA->id,
                        'bulan' => $this->bulan,
                        'nominal' => $this->nominal,
                        'total_dibayar' => 0.00,
                        'status' => 'belum_bayar',
                        'jatuh_tempo' => $this->jatuh_tempo,
                    ]);
                    $createdCount++;
                }
            }
        });

        session()->flash('message', "Berhasil merilis {$createdCount} tagihan baru untuk siswa Kelas.");
        $this->reset(['kelas_id', 'jenis_tagihan_id', 'bulan', 'nominal']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Tagihan::with(['siswa.user', 'siswa.kelas', 'jenisTagihan', 'tahunAjaran'])
            ->latest();

        if ($this->filterKelas) {
            $query->whereHas('siswa', function ($q) {
                $q->where('kelas_id', $this->filterKelas);
            });
        }

        if ($this->filterJenis) {
            $query->where('jenis_tagihan_id', $this->filterJenis);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $tagihans = $query->paginate(15);

        return view('livewire.finance.manajemen-tagihan', [
            'tagihans' => $tagihans
        ])->layout('components.layouts.app', ['title' => 'Manajemen Tagihan']);
    }
}
