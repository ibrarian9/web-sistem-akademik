<?php

namespace App\Livewire\Guru;

use App\Models\CapaianGuru;
use App\Models\Guru;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Livewire\Component;

class CapaianPengembanganDiri extends Component
{
    public $showModal = false;
    public $capaianId = null;
    public $judul = '';
    public $kategori = 'pengembangan_diri';
    public $link_gdrive = '';
    public $deskripsi = '';

    public function mount()
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role->nama ?? '', ['guru', 'super_admin'])) {
            abort(403, 'Akses Ditolak: Halaman ini khusus untuk Pengajuan Pengembangan Diri Guru.');
        }
    }

    public function openCreate()
    {
        $this->resetValidation();
        $this->reset(['capaianId', 'judul', 'deskripsi', 'link_gdrive']);
        $this->kategori = 'pengembangan_diri';
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $user = auth()->user();
        $guru = $user->guru;

        if (!$guru) {
            return;
        }

        $capaian = CapaianGuru::where('guru_id', $guru->id)->where('id', $id)->first();
        if (!$capaian) {
            return;
        }

        $this->resetValidation();
        $this->capaianId = $capaian->id;
        $this->judul = $capaian->judul;
        $this->kategori = $capaian->kategori;
        $this->link_gdrive = $capaian->link_gdrive;
        $this->deskripsi = $capaian->deskripsi;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function save()
    {
        $user = auth()->user();
        $guru = $user->guru ?? Guru::first();

        if (!$guru) {
            session()->flash('error', 'Data profil Guru tidak ditemukan.');
            return;
        }

        $this->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string',
            'link_gdrive' => 'nullable|url|max:1000',
            'deskripsi' => 'nullable|string',
        ], [
            'link_gdrive.url' => 'Format URL Google Drive tidak valid. Sertakan http:// atau https://',
        ]);

        $activeTahun = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::first();
        $activeSemester = Semester::where('status_aktif', true)->first() ?? Semester::first();

        CapaianGuru::updateOrCreate(
            ['id' => $this->capaianId],
            [
                'guru_id' => $guru->id,
                'judul' => $this->judul,
                'kategori' => $this->kategori,
                'link_gdrive' => $this->link_gdrive,
                'deskripsi' => $this->deskripsi,
                'tahun_ajaran_id' => $activeTahun->id ?? null,
                'semester_id' => $activeSemester->id ?? null,
                'status_penilaian' => $this->capaianId ? undefined : 'diajukan',
            ]
        );

        session()->flash('success', 'Data capaian & link Google Drive berhasil disimpan!');
        $this->closeModal();
    }

    public function delete($id)
    {
        $user = auth()->user();
        $guru = $user->guru;

        if ($guru) {
            CapaianGuru::where('guru_id', $guru->id)->where('id', $id)->delete();
            session()->flash('success', 'Data capaian berhasil dihapus.');
        }
    }

    public function render()
    {
        $user = auth()->user();
        $guru = $user->guru ?? Guru::first();

        $capaianList = collect();
        $summary = [
            'total' => 0,
            'dinilai' => 0,
            'rata_skor' => 0,
        ];

        if ($guru) {
            $capaianList = CapaianGuru::with(['penilai', 'tahunAjaran', 'semester'])
                ->where('guru_id', $guru->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $summary['total'] = $capaianList->count();
            $summary['dinilai'] = $capaianList->where('status_penilaian', 'dinilai')->count();
            
            $ratedList = $capaianList->where('status_penilaian', 'dinilai')->whereNotNull('skor_nilai');
            $summary['rata_skor'] = $ratedList->isNotEmpty() ? round($ratedList->avg('skor_nilai'), 1) : 0;
        }

        return view('livewire.guru.capaian-pengembangan-diri', [
            'guru' => $guru,
            'capaianList' => $capaianList,
            'summary' => $summary,
        ])->layout('components.layouts.app', ['title' => 'Pengembangan Diri & Capaian Guru']);
    }
}
