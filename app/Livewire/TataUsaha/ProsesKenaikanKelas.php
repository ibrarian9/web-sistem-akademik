<?php

namespace App\Livewire\TataUsaha;

use Livewire\Component;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

class ProsesKenaikanKelas extends Component
{
    public $kelasAsalId;
    public $aksiTujuan = 'naik_kelas'; // 'naik_kelas' or 'lulus_alumni'
    public $kelasTujuanId;
    
    public array $selectedSiswa = [];
    public array $siswaTinggalKelas = [];
    public bool $selectAll = true;

    public function mount()
    {
        $firstKelas = Kelas::orderBy('nama_kelas', 'asc')->first();
        if ($firstKelas) {
            $this->kelasAsalId = $firstKelas->id;
        }
        $this->loadStudents();
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
            // Ensure student is selected
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
        return Siswa::where('siswa.kelas_id', $this->kelasAsalId)
            ->where('siswa.status', 'aktif')
            ->join('users', 'siswa.user_id', '=', 'users.id')
            ->orderBy('users.nama', 'asc')
            ->select('siswa.*');
    }

    public function prosesKenaikan()
    {
        if (empty($this->selectedSiswa)) {
            session()->flash('error', 'Pilih minimal satu siswa untuk diproses.');
            return;
        }

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
        $kelases = Kelas::orderBy('nama_kelas', 'asc')->get();
        $students = $this->kelasAsalId ? $this->getStudentsQuery()->get() : collect();

        return view('livewire.tata-usaha.proses-kenaikan-kelas', [
            'kelases' => $kelases,
            'students' => $students,
        ])->layout('components.layouts.app', ['title' => 'Kenaikan Kelas & Kelulusan Massal']);
    }
}
