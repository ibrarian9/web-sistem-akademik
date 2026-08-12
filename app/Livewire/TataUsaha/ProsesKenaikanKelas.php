<?php

namespace App\Livewire\TataUsaha;

use Livewire\Component;
use App\Models\Kelas;
use App\Models\NilaiTahfidz;
use App\Models\RaporTahfidzDetail;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

class ProsesKenaikanKelas extends Component
{
    public $tipeKenaikan = 'akademik'; // 'akademik' or 'tahfidz'
    public $kelasAsalId;
    public $aksiTujuan = 'naik_kelas'; // 'naik_kelas' or 'lulus_alumni'
    public $kelasTujuanId;
    
    public array $selectedSiswa = [];
    public array $siswaTinggalKelas = [];
    public bool $selectAll = true;

    public function mount()
    {
        $this->initDefaultKelas();
        $this->loadStudents();
    }

    public function updatedTipeKenaikan()
    {
        $this->initDefaultKelas();
        $this->loadStudents();
    }

    protected function initDefaultKelas()
    {
        if ($this->tipeKenaikan === 'tahfidz') {
            $firstKelas = Kelas::where('jenis_kelas', 'tahfidz')
                ->orWhereNotNull('guru_tahfidz_id')
                ->orderBy('nama_kelas', 'asc')
                ->first();
        } else {
            $firstKelas = Kelas::where('jenis_kelas', 'umum')
                ->orWhereNull('jenis_kelas')
                ->orderBy('nama_kelas', 'asc')
                ->first() ?? Kelas::first();
        }

        if ($firstKelas) {
            $this->kelasAsalId = $firstKelas->id;
        } else {
            $this->kelasAsalId = null;
        }
        $this->kelasTujuanId = null;
    }

    public function updatedKelasAsalId()
    {
        $this->loadStudents();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedSiswa = $this->getStudentsQuery()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedSiswa = [];
        }
        $this->siswaTinggalKelas = [];
    }

    public function toggleTinggalKelas($siswaId)
    {
        $idStr = (string)$siswaId;
        if (in_array($idStr, $this->siswaTinggalKelas)) {
            $this->siswaTinggalKelas = array_values(array_diff($this->siswaTinggalKelas, [$idStr]));
        } else {
            $this->siswaTinggalKelas[] = $idStr;
            if (!in_array($idStr, $this->selectedSiswa)) {
                $this->selectedSiswa[] = $idStr;
            }
        }
    }

    public function loadStudents()
    {
        $this->siswaTinggalKelas = [];
        if ($this->kelasAsalId) {
            $this->selectedSiswa = $this->getStudentsQuery()->pluck('id')->map(fn($id) => (string)$id)->toArray();
            $this->selectAll = true;
        } else {
            $this->selectedSiswa = [];
            $this->selectAll = false;
        }
    }

    public function getStudentsQuery()
    {
        if ($this->tipeKenaikan === 'tahfidz') {
            $query = Siswa::where('siswa.kelas_tahfidz_id', $this->kelasAsalId);
        } else {
            $query = Siswa::where('siswa.kelas_id', $this->kelasAsalId);
        }

        return $query->where('siswa.status', 'aktif')
            ->with(['user', 'kelas', 'kelasTahfidz'])
            ->join('users', 'siswa.user_id', '=', 'users.id')
            ->orderBy('users.nama', 'asc')
            ->select('siswa.*');
    }

    public function prosesKenaikan()
    {
        if (empty($this->selectedSiswa)) {
            session()->flash('error', 'Pilih minimal satu siswa/santri untuk diproses.');
            return;
        }

        if ($this->tipeKenaikan === 'tahfidz') {
            if (!$this->kelasTujuanId) {
                session()->flash('error', 'Pilih Halaqah Tahfizh tujuan untuk pemindahan kelompok.');
                return;
            }
            if ((int)$this->kelasTujuanId === (int)$this->kelasAsalId) {
                session()->flash('error', 'Halaqah Tahfizh tujuan harus berbeda dengan Halaqah asal.');
                return;
            }

            $targetHalaqah = Kelas::find($this->kelasTujuanId);
            $countMoved = 0;

            DB::transaction(function () use (&$countMoved) {
                foreach ($this->selectedSiswa as $siswaId) {
                    $siswa = Siswa::find($siswaId);
                    if (!$siswa) continue;

                    $siswa->update([
                        'kelas_tahfidz_id' => $this->kelasTujuanId,
                    ]);
                    $countMoved++;
                }
            });

            session()->flash('message', "Berhasil memindahkan {$countMoved} santri ke Halaqah Tahfizh " . ($targetHalaqah->nama_kelas ?? '') . ".");
            $this->loadStudents();
            return;
        }

        // Mode Akademik
        if ($this->aksiTujuan === 'naik_kelas') {
            if (!$this->kelasTujuanId) {
                session()->flash('error', 'Pilih kelas tujuan untuk kenaikan kelas.');
                return;
            }
            if ((int)$this->kelasTujuanId === (int)$this->kelasAsalId) {
                session()->flash('error', 'Kelas tujuan harus berbeda dengan kelas asal.');
                return;
            }
        }

        $activeSemester = Semester::where('status_aktif', true)->first();
        $targetKelas = $this->aksiTujuan === 'naik_kelas' ? Kelas::find($this->kelasTujuanId) : null;
        
        $countNaik = 0;
        $countTinggal = 0;
        $countLulus = 0;

        DB::transaction(function () use ($activeSemester, &$countNaik, &$countTinggal, &$countLulus) {
            foreach ($this->selectedSiswa as $siswaId) {
                $siswa = Siswa::find($siswaId);
                if (!$siswa) continue;

                $idStr = (string)$siswaId;
                $isTinggal = ($this->aksiTujuan === 'naik_kelas' && in_array($idStr, $this->siswaTinggalKelas));

                if ($this->aksiTujuan === 'naik_kelas') {
                    if ($isTinggal) {
                        // Manual decision by TU to retain student in current class
                        SiswaKelas::updateOrCreate(
                            ['siswa_id' => $siswa->id, 'semester_id' => $activeSemester ? $activeSemester->id : null],
                            ['kelas_id' => $this->kelasAsalId, 'status' => 'tinggal_kelas']
                        );

                        $siswa->update([
                            'kelas_id' => $this->kelasAsalId,
                            'status' => 'aktif',
                        ]);

                        $countTinggal++;
                    } else {
                        // Promote to target class
                        SiswaKelas::updateOrCreate(
                            ['siswa_id' => $siswa->id, 'semester_id' => $activeSemester ? $activeSemester->id : null],
                            ['kelas_id' => $this->kelasAsalId, 'status' => 'naik_kelas']
                        );

                        $siswa->update([
                            'kelas_id' => $this->kelasTujuanId,
                            'status' => 'aktif',
                        ]);

                        $countNaik++;
                    }
                } else {
                    // Graduated to Alumni
                    SiswaKelas::updateOrCreate(
                        ['siswa_id' => $siswa->id, 'semester_id' => $activeSemester ? $activeSemester->id : null],
                        ['kelas_id' => $this->kelasAsalId, 'status' => 'pindah']
                    );

                    $siswa->update([
                        'kelas_id' => null,
                        'status' => 'lulus',
                        'tahun_lulus' => date('Y'),
                        'catatan_alumni' => 'Lulus secara massal pada ' . date('d M Y'),
                    ]);

                    $countLulus++;
                }
            }
        });

        if ($this->aksiTujuan === 'naik_kelas') {
            $msg = "Berhasil menaikkan {$countNaik} siswa ke kelas " . ($targetKelas->nama_kelas ?? '') . ".";
            if ($countTinggal > 0) {
                $msg .= " Serta menetapkan {$countTinggal} siswa untuk Tinggal Kelas.";
            }
            session()->flash('message', $msg);
        } else {
            session()->flash('message', "Berhasil meluluskan {$countLulus} siswa menjadi Alumni.");
        }

        $this->loadStudents();
    }

    public function render()
    {
        if ($this->tipeKenaikan === 'tahfidz') {
            $kelasesAsal = Kelas::where('jenis_kelas', 'tahfidz')
                ->orWhereNotNull('guru_tahfidz_id')
                ->orderBy('nama_kelas', 'asc')
                ->get();
            $kelasesTujuan = $kelasesAsal;
        } else {
            $kelasesAsal = Kelas::where('jenis_kelas', 'umum')
                ->orWhereNull('jenis_kelas')
                ->orderBy('nama_kelas', 'asc')
                ->get();
            if ($kelasesAsal->isEmpty()) {
                $kelasesAsal = Kelas::all();
            }
            $kelasesTujuan = $kelasesAsal;
        }

        $students = $this->kelasAsalId ? $this->getStudentsQuery()->get() : collect();

        // Attach Tahfizh Progress Summaries for validation badges
        $tahfidzSummaries = RaporTahfidzDetail::whereIn('rapor_id', function ($q) use ($students) {
            $q->select('id')->from('rapor')->whereIn('siswa_id', $students->pluck('id'));
        })->get()->keyBy('rapor_id');

        return view('livewire.tata-usaha.proses-kenaikan-kelas', [
            'kelasesAsal' => $kelasesAsal,
            'kelasesTujuan' => $kelasesTujuan,
            'students' => $students,
            'tahfidzSummaries' => $tahfidzSummaries,
        ])->layout('components.layouts.app', ['title' => 'Dual Kenaikan Kelas & Halaqah Tahfizh']);
    }
}
