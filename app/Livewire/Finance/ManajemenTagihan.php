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
    public string $search = '';

    // Mode: 'kolektif' vs 'perorangan' vs 'otomatis'
    public string $modeTagihan = 'kolektif';

    // Create Tagihan Form properties
    public ?int $kelas_id = null;
    public ?int $single_siswa_id = null;
    public ?int $jenis_tagihan_id = null;
    public string $bulan = '';
    public float $nominal = 0.00;
    public string $jatuh_tempo = '';

    // Auto SPP Form properties
    public string $autoBulan = 'Juli';
    public float $autoNominal = 350000.00;
    public string $autoJatuhTempo = '';

    // Option lists
    public array $classes = [];
    public array $jenisTagihans = [];

    public function mount()
    {
        $this->classes = Kelas::orderBy('nama_kelas')->get()->toArray();
        $this->jenisTagihans = JenisTagihan::where('nama', 'not like', '%Infaq%')
            ->where('nama', 'not like', '%Sedekah%')
            ->where('nama', 'not like', '%Donasi%')
            ->get()
            ->toArray();
        $this->jatuh_tempo = date('Y-m-d', strtotime('+30 days'));
        $this->autoJatuhTempo = date('Y-m-10', strtotime('+30 days'));
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterKelas()
    {
        $this->resetPage();
    }

    public function updatingFilterJenis()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedJenisTagihanId($value)
    {
        if ($value) {
            $this->nominal = floatval(JenisTagihan::where('id', $value)->value('default_nominal') ?? 0.00);
        }
    }

    public function createBulkTagihan()
    {
        $this->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'bulan' => 'required|string|max:50',
            'nominal' => 'required|numeric|min:1',
            'jatuh_tempo' => 'required|date',
        ]);

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

        $targetKelas = Kelas::find($this->kelas_id);
        $namaKelasStr = $targetKelas ? " " . $targetKelas->nama_kelas : "";

        session()->flash('message', "Berhasil merilis {$createdCount} tagihan baru untuk siswa Kelas{$namaKelasStr}.");
        $this->reset(['kelas_id', 'jenis_tagihan_id', 'bulan', 'nominal']);
        $this->resetPage();
    }

    public function createSingleTagihan()
    {
        $this->validate([
            'single_siswa_id' => 'required|exists:siswa,id',
            'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'bulan' => 'required|string|max:50',
            'nominal' => 'required|numeric|min:1',
            'jatuh_tempo' => 'required|date',
        ]);

        $activeTA = TahunAjaran::where('status_aktif', true)->first();
        if (!$activeTA) {
            session()->flash('error', 'Tidak ada tahun ajaran aktif.');
            return;
        }

        $siswa = Siswa::with('user')->findOrFail($this->single_siswa_id);

        Tagihan::create([
            'siswa_id' => $siswa->id,
            'jenis_tagihan_id' => $this->jenis_tagihan_id,
            'tahun_ajaran_id' => $activeTA->id,
            'bulan' => $this->bulan,
            'nominal' => $this->nominal,
            'total_dibayar' => 0.00,
            'status' => 'belum_bayar',
            'jatuh_tempo' => $this->jatuh_tempo,
        ]);

        session()->flash('message', "Berhasil merilis tagihan perorangan untuk siswa " . ($siswa->user->nama ?? 'Siswa') . ".");
        $this->reset(['single_siswa_id', 'jenis_tagihan_id', 'bulan', 'nominal']);
        $this->resetPage();
    }

    public function generateAutoSppBulanan()
    {
        $this->validate([
            'autoBulan' => 'required|string|max:50',
            'autoNominal' => 'required|numeric|min:1',
            'autoJatuhTempo' => 'required|date',
        ]);

        $activeTA = TahunAjaran::where('status_aktif', true)->first();
        if (!$activeTA) {
            session()->flash('error', 'Tidak ada tahun ajaran aktif.');
            return;
        }

        $jenisSpp = JenisTagihan::firstOrCreate(
            ['nama' => 'SPP'],
            [
                'kategori' => 'rutin',
                'default_nominal' => 350000,
                'is_blocking' => true,
            ]
        );

        $students = Siswa::where('status', 'aktif')->get();
        if ($students->isEmpty()) {
            session()->flash('error', 'Tidak ada siswa aktif terdaftar.');
            return;
        }

        $createdCount = 0;
        DB::transaction(function () use ($students, $activeTA, $jenisSpp, &$createdCount) {
            foreach ($students as $student) {
                $exists = Tagihan::where([
                    'siswa_id' => $student->id,
                    'jenis_tagihan_id' => $jenisSpp->id,
                    'tahun_ajaran_id' => $activeTA->id,
                    'bulan' => $this->autoBulan,
                ])->exists();

                if (!$exists) {
                    Tagihan::create([
                        'siswa_id' => $student->id,
                        'jenis_tagihan_id' => $jenisSpp->id,
                        'tahun_ajaran_id' => $activeTA->id,
                        'bulan' => $this->autoBulan,
                        'nominal' => $this->autoNominal,
                        'total_dibayar' => 0.00,
                        'status' => 'belum_bayar',
                        'jatuh_tempo' => $this->autoJatuhTempo,
                    ]);
                    $createdCount++;
                }
            }
        });

        session()->flash('message', "Berhasil merilis {$createdCount} tagihan SPP bulan {$this->autoBulan} untuk seluruh siswa aktif.");
        $this->resetPage();
    }

    public function deleteTagihan(int $id)
    {
        $tagihan = Tagihan::findOrFail($id);

        if ($tagihan->total_dibayar > 0) {
            session()->flash('error', 'Tagihan ini sudah pernah dibayar sebagian/lunas, tidak dapat dihapus.');
            return;
        }

        $tagihan->delete();
        session()->flash('message', 'Tagihan berhasil dihapus/dibatalkan.');
    }

    public function render()
    {
        $query = Tagihan::with(['siswa.user', 'siswa.kelas', 'jenisTagihan', 'tahunAjaran'])
            ->latest();

        if ($this->search) {
            $query->whereHas('siswa.user', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            })->orWhereHas('siswa', function ($q) {
                $q->where('nis', 'like', '%' . $this->search . '%');
            });
        }

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
        $allStudents = Siswa::where('status', 'aktif')->with('user', 'kelas')->get();

        return view('livewire.finance.manajemen-tagihan', [
            'tagihans' => $tagihans,
            'allStudents' => $allStudents,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Tagihan']);
    }
}
