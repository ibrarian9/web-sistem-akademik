<?php

namespace App\Livewire\SuperAdmin\TataKelola;

use App\Models\CapaianGuru;
use App\Models\Guru;
use Livewire\Component;
use Livewire\WithPagination;

class CapaianPengembanganGuru extends Component
{
    use WithPagination;

    public $search = '';
    public $filterGuru = '';
    public $filterKategori = '';
    public $filterStatus = '';

    // Modal Evaluation State
    public $showEvaluateModal = false;
    public $selectedCapaianId = null;
    public $selectedCapaian = null;
    public $skor_nilai = '';
    public $predikat = 'Sangat Baik';
    public $catatan_evaluasi = '';
    public $tanggal_penilaian = '';

    // Modal Detail State
    public $showDetailModal = false;
    public $detailCapaian = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterGuru' => ['except' => ''],
        'filterKategori' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function mount()
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role->nama ?? '', ['super_admin', 'kepala_sekolah'])) {
            abort(403, 'Akses Ditolak: Halaman Evaluasi Capaian Guru khusus untuk Super Admin & Kepala Sekolah.');
        }

        $this->tanggal_penilaian = date('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterGuru()
    {
        $this->resetPage();
    }

    public function openDetailModal($id)
    {
        $capaian = CapaianGuru::with(['guru.user', 'penilai', 'tahunAjaran', 'semester'])->find($id);
        if (!$capaian) {
            return;
        }

        $this->detailCapaian = $capaian;
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailCapaian = null;
    }

    public function openEvaluateFromDetail()
    {
        if ($this->detailCapaian) {
            $id = $this->detailCapaian->id;
            $this->closeDetailModal();
            $this->openEvaluateModal($id);
        }
    }

    public function openEvaluateModal($id)
    {
        $capaian = CapaianGuru::with(['guru.user', 'tahunAjaran', 'semester'])->find($id);
        if (!$capaian) {
            return;
        }

        $this->resetValidation();
        $this->selectedCapaianId = $capaian->id;
        $this->selectedCapaian = $capaian;
        $this->skor_nilai = $capaian->skor_nilai ?: '';
        $this->predikat = $capaian->predikat ?: 'Sangat Baik';
        $this->catatan_evaluasi = $capaian->catatan_evaluasi ?: '';
        $this->tanggal_penilaian = $capaian->tanggal_penilaian ? $capaian->tanggal_penilaian->format('Y-m-d') : date('Y-m-d');
        $this->showEvaluateModal = true;
    }

    public function closeModal()
    {
        $this->showEvaluateModal = false;
        $this->resetValidation();
    }

    public function saveEvaluation()
    {
        $this->validate([
            'selectedCapaianId' => 'required|exists:capaian_gurus,id',
            'skor_nilai' => 'required|numeric|between:0,100',
            'predikat' => 'required|string',
            'catatan_evaluasi' => 'nullable|string',
            'tanggal_penilaian' => 'required|date',
        ]);

        $capaian = CapaianGuru::find($this->selectedCapaianId);
        if ($capaian) {
            $capaian->update([
                'penilai_id' => auth()->id(),
                'skor_nilai' => $this->skor_nilai,
                'predikat' => $this->predikat,
                'catatan_evaluasi' => $this->catatan_evaluasi,
                'tanggal_penilaian' => $this->tanggal_penilaian,
                'status_penilaian' => 'dinilai',
            ]);

            session()->flash('success', 'Penilaian capaian guru berhasil disimpan!');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        CapaianGuru::where('id', $id)->delete();
        session()->flash('success', 'Data pengajuan capaian guru berhasil dihapus.');
    }

    public function render()
    {
        $gurus = Guru::with('user')->get();

        $query = CapaianGuru::with(['guru.user', 'penilai']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('judul', 'like', '%' . $this->search . '%')
                  ->orWhereHas('guru.user', function ($uq) {
                      $uq->where('nama', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->filterGuru) {
            $query->where('guru_id', $this->filterGuru);
        }

        if ($this->filterKategori) {
            $query->where('kategori', $this->filterKategori);
        }

        if ($this->filterStatus) {
            $query->where('status_penilaian', $this->filterStatus);
        }

        $capaianList = $query->orderBy('created_at', 'desc')->paginate(12);

        // Summary metrics
        $totalPengajuan = CapaianGuru::count();
        $belumDinilai = CapaianGuru::where('status_penilaian', 'diajukan')->count();
        $sudahDinilai = CapaianGuru::where('status_penilaian', 'dinilai')->count();
        $avgSkor = CapaianGuru::where('status_penilaian', 'dinilai')->whereNotNull('skor_nilai')->avg('skor_nilai');

        return view('livewire.super-admin.tata-kelola.capaian-pengembangan-guru', [
            'capaianList' => $capaianList,
            'gurus' => $gurus,
            'totalPengajuan' => $totalPengajuan,
            'belumDinilai' => $belumDinilai,
            'sudahDinilai' => $sudahDinilai,
            'avgSkor' => $avgSkor ? round($avgSkor, 1) : 0,
        ])->layout('components.layouts.app', ['title' => 'Evaluasi Capaian & Pengembangan Diri Guru']);
    }
}
