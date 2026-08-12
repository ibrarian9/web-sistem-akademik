<?php

namespace App\Livewire\Guru;

use App\Models\Guru;
use App\Models\GuruMapelKelas;
use App\Models\JadwalRemedial;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use Livewire\Component;
use Livewire\WithPagination;

class ManajemenRemedial extends Component
{
    use WithPagination;

    public $kelas_id = '';
    public $mapel_id = '';
    public $siswa_id = '';
    public $topik_tp = '';
    public $kategori = 'harian_tp';
    public $tanggal = '';
    public $waktu_mulai = '13:00';
    public $waktu_selesai = '14:30';
    public $ruangan = '';
    public $catatan = '';
    public $status = 'dijadwalkan';

    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $remedialId = null;

    public $filterKelas = '';
    public $filterStatus = '';

    public array $kelasList = [];
    public array $mapelList = [];
    public array $siswaList = [];

    protected function rules()
    {
        return [
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'siswa_id' => 'nullable|exists:siswa,id',
            'topik_tp' => 'required|string|max:255',
            'kategori' => 'required|in:harian_tp,mid_sts,umum',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'ruangan' => 'required|string|max:255',
            'catatan' => 'nullable|string|max:1000',
            'status' => 'required|in:dijadwalkan,selesai,dibatalkan',
        ];
    }

    public function mount()
    {
        $user = auth()->user();
        $guru = $user->guru ?? null;

        // Restriction: Only Guru Umum can manage remedial
        if ($guru && strtolower($guru->jenis_guru ?? '') === 'tahfidz') {
            abort(403, 'Akses Ditolak: Fitur Jadwal Remedial hanya diperuntukkan bagi Guru Mata Pelajaran Umum.');
        }

        $this->loadDropdownData();
    }

    public function loadDropdownData()
    {
        $user = auth()->user();
        $guru = $user->guru ?? null;

        if ($guru) {
            // Get classes taught by this teacher
            $gmkKelasIds = GuruMapelKelas::where('guru_id', $guru->id)->pluck('kelas_id')->toArray();
            $this->kelasList = Kelas::whereIn('id', $gmkKelasIds)->orWhere('guru_umum_id', $guru->id)->get()->toArray();
            
            // If empty fallback to all active classes
            if (empty($this->kelasList)) {
                $this->kelasList = Kelas::all()->toArray();
            }

            // Get mapels taught by this teacher
            $gmkMapelIds = GuruMapelKelas::where('guru_id', $guru->id)->pluck('mapel_id')->toArray();
            $this->mapelList = MataPelajaran::whereIn('id', $gmkMapelIds)->where('jenis', 'umum')->get()->toArray();
            if (empty($this->mapelList)) {
                $this->mapelList = MataPelajaran::where('jenis', 'umum')->get()->toArray();
            }
        } else {
            $this->kelasList = Kelas::all()->toArray();
            $this->mapelList = MataPelajaran::where('jenis', 'umum')->get()->toArray();
        }

        $this->tanggal = date('Y-m-d');
    }

    public function updatedKelasId($value)
    {
        if ($value) {
            $this->siswaList = Siswa::with('user')->where('kelas_id', $value)->get()->toArray();
        } else {
            $this->siswaList = [];
        }
    }

    public function openCreate()
    {
        $this->resetValidation();
        $this->reset(['remedialId', 'kelas_id', 'mapel_id', 'siswa_id', 'topik_tp', 'catatan']);
        $this->kategori = 'harian_tp';
        $this->tanggal = date('Y-m-d');
        $this->waktu_mulai = '13:00';
        $this->waktu_selesai = '14:30';
        $this->ruangan = 'Ruang Kelas';
        $this->status = 'dijadwalkan';
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $this->resetValidation();
        $rem = JadwalRemedial::find($id);
        if (!$rem) {
            session()->flash('error', 'Jadwal Remedial tidak ditemukan.');
            return;
        }

        $this->remedialId = $rem->id;
        $this->kelas_id = $rem->kelas_id;
        $this->mapel_id = $rem->mapel_id;
        $this->siswa_id = $rem->siswa_id;
        $this->topik_tp = $rem->topik_tp;
        $this->kategori = $rem->kategori;
        $this->tanggal = $rem->tanggal ? $rem->tanggal->format('Y-m-d') : date('Y-m-d');
        $this->waktu_mulai = substr($rem->waktu_mulai, 0, 5);
        $this->waktu_selesai = substr($rem->waktu_selesai, 0, 5);
        $this->ruangan = $rem->ruangan;
        $this->catatan = $rem->catatan;
        $this->status = $rem->status;

        $this->updatedKelasId($this->kelas_id);
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $guru = auth()->user()->guru ?? Guru::first();
        if (!$guru) {
            session()->flash('error', 'Data profil Guru tidak ditemukan.');
            return;
        }

        JadwalRemedial::updateOrCreate(
            ['id' => $this->remedialId],
            [
                'guru_id' => $guru->id,
                'kelas_id' => $this->kelas_id,
                'mapel_id' => $this->mapel_id,
                'siswa_id' => $this->siswa_id ?: null,
                'topik_tp' => $this->topik_tp,
                'kategori' => $this->kategori,
                'tanggal' => $this->tanggal,
                'waktu_mulai' => $this->waktu_mulai,
                'waktu_selesai' => $this->waktu_selesai,
                'ruangan' => $this->ruangan,
                'catatan' => $this->catatan,
                'status' => $this->status,
            ]
        );

        $this->showModal = false;
        session()->flash('message', $this->isEdit ? 'Jadwal Remedial berhasil diperbarui.' : 'Jadwal Remedial berhasil ditambahkan.');
    }

    public function updateStatus($id, $newStatus)
    {
        $rem = JadwalRemedial::find($id);
        if ($rem) {
            $rem->update(['status' => $newStatus]);
            session()->flash('message', 'Status Jadwal Remedial berhasil diubah.');
        }
    }

    public function delete($id)
    {
        $rem = JadwalRemedial::find($id);
        if ($rem) {
            $rem->delete();
            session()->flash('message', 'Jadwal Remedial berhasil dihapus.');
        }
    }

    public function render()
    {
        $user = auth()->user();
        $guru = $user->guru ?? null;

        $query = JadwalRemedial::with(['kelas', 'mapel', 'siswa.user', 'guru.user'])
            ->orderBy('tanggal', 'desc');

        if ($guru) {
            $query->where('guru_id', $guru->id);
        }

        if (!empty($this->filterKelas)) {
            $query->where('kelas_id', $this->filterKelas);
        }

        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }

        $remedialList = $query->paginate(10);

        return view('livewire.guru.manajemen-remedial', [
            'remedialList' => $remedialList,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Jadwal Remedial Guru']);
    }
}
