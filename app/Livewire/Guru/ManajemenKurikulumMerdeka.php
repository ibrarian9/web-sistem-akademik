<?php

namespace App\Livewire\Guru;

use App\Models\LingkupMateri;
use App\Models\MataPelajaran;
use App\Models\TemplateDeskripsi;
use App\Models\TujuanPembelajaran;
use Livewire\Component;

class ManajemenKurikulumMerdeka extends Component
{
    public $mapel_id;
    public $selectedMapel;
    
    // Modal visibility flags
    public bool $showLmModal = false;
    public bool $showTpModal = false;

    // Lingkup Materi Form
    public $nama_lingkup_materi;
    public $kategori_lm;
    public $urutan_lm = 1;
    public $editingLmId = null;

    // TP Form
    public $lingkup_materi_id;
    public $deskripsi_tp;
    public $urutan_tp = 1;
    public $editingTpId = null;

    // Template Deskripsi Form
    public $frasa_tertinggi = 'menunjukkan penguasaan dalam';
    public $frasa_terendah = 'membutuhkan penguatan dalam';

    protected $rules = [
        'mapel_id' => 'required|exists:mata_pelajaran,id',
    ];

    public function mount()
    {
        // Access Guard: Block Guru Tahfizh
        $user = auth()->user();
        if ($user && $user->role?->nama === 'guru' && $user->guru) {
            $jenis = strtolower($user->guru->jenis_guru);
            if ($jenis === 'tahfidz' || $jenis === 'tahfizh') {
                session()->flash('error', 'Akses ditolak. Guru Tahfizh tidak mengelola modul Kurikulum Merdeka.');
                return redirect()->route('guru.input-tahfidz');
            }
        }

        $mapel = MataPelajaran::orderBy('id', 'asc')->first();

        if ($mapel) {
            $this->mapel_id = $mapel->id;
            $this->loadMapelData();
        }
    }

    public function updatedMapelId()
    {
        $this->loadMapelData();
    }

    public function loadMapelData()
    {
        $this->selectedMapel = MataPelajaran::find($this->mapel_id);

        $template = TemplateDeskripsi::where('mapel_id', $this->mapel_id)->first();
        if ($template) {
            $this->frasa_tertinggi = $template->frasa_tertinggi;
            $this->frasa_terendah = $template->frasa_terendah;
        } else {
            $this->frasa_tertinggi = 'menunjukkan penguasaan dalam';
            $this->frasa_terendah = 'membutuhkan penguatan dalam';
        }
    }

    public function saveTemplate()
    {
        if (!$this->mapel_id) return;

        TemplateDeskripsi::updateOrCreate(
            ['mapel_id' => $this->mapel_id],
            [
                'frasa_tertinggi' => $this->frasa_tertinggi,
                'frasa_terendah' => $this->frasa_terendah,
            ]
        );

        session()->flash('message', 'Template deskripsi auto-narasi mapel berhasil diperbarui!');
    }

    public function openLmModal($id = null)
    {
        $this->editingLmId = $id;
        if ($id) {
            $lm = LingkupMateri::findOrFail($id);
            $this->nama_lingkup_materi = $lm->nama_lingkup_materi;
            $this->kategori_lm = $lm->kategori;
            $this->urutan_lm = $lm->urutan;
        } else {
            $this->nama_lingkup_materi = '';
            $this->kategori_lm = 'sumatif';
            $this->urutan_lm = (LingkupMateri::where('mapel_id', $this->mapel_id)->max('urutan') ?? 0) + 1;
        }
        $this->showLmModal = true;
    }

    public function editLingkupMateri($id)
    {
        $this->openLmModal($id);
    }

    public function closeLmModal()
    {
        $this->showLmModal = false;
        $this->editingLmId = null;
        $this->nama_lingkup_materi = '';
    }

    public function saveLm()
    {
        $this->validate(['nama_lingkup_materi' => 'required|string|max:255']);

        LingkupMateri::updateOrCreate(
            ['id' => $this->editingLmId],
            [
                'mapel_id' => $this->mapel_id,
                'nama_lingkup_materi' => $this->nama_lingkup_materi,
                'kategori' => $this->kategori_lm ?: 'sumatif',
                'urutan' => $this->urutan_lm,
            ]
        );

        $this->closeLmModal();
        $this->loadMapelData();
        session()->flash('message', 'Lingkup Materi berhasil disimpan.');
    }

    public function saveLingkupMateri()
    {
        $this->saveLm();
    }

    public function deleteLm($id)
    {
        LingkupMateri::findOrFail($id)->delete();
        $this->loadMapelData();
        session()->flash('message', 'Lingkup Materi berhasil dihapus.');
    }

    public function deleteLingkupMateri($id)
    {
        $this->deleteLm($id);
    }

    public function openTpModal($lmId = null, $tpId = null)
    {
        if (!$lmId) {
            $lm = LingkupMateri::where('mapel_id', $this->mapel_id)->first();
            $lmId = $lm ? $lm->id : null;
        }

        $this->lingkup_materi_id = $lmId;
        $this->editingTpId = $tpId;

        if ($tpId) {
            $tp = TujuanPembelajaran::findOrFail($tpId);
            $this->deskripsi_tp = $tp->deskripsi_tp;
            $this->urutan_tp = $tp->urutan;
        } else {
            $this->deskripsi_tp = '';
            $this->urutan_tp = $lmId ? ((TujuanPembelajaran::where('lingkup_materi_id', $lmId)->max('urutan') ?? 0) + 1) : 1;
        }

        $this->showTpModal = true;
    }

    public function editTp($id)
    {
        $tp = TujuanPembelajaran::findOrFail($id);
        $this->openTpModal($tp->lingkup_materi_id, $id);
    }

    public function closeTpModal()
    {
        $this->showTpModal = false;
        $this->editingTpId = null;
        $this->deskripsi_tp = '';
    }

    public function saveTp()
    {
        $this->validate(['deskripsi_tp' => 'required|string']);

        TujuanPembelajaran::updateOrCreate(
            ['id' => $this->editingTpId],
            [
                'lingkup_materi_id' => $this->lingkup_materi_id,
                'deskripsi_tp' => $this->deskripsi_tp,
                'urutan' => $this->urutan_tp,
            ]
        );

        $this->closeTpModal();
        $this->loadMapelData();
        session()->flash('message', 'Tujuan Pembelajaran berhasil disimpan.');
    }

    public function deleteTp($id)
    {
        TujuanPembelajaran::findOrFail($id)->delete();
        $this->loadMapelData();
        session()->flash('message', 'Tujuan Pembelajaran berhasil dihapus.');
    }

    public function render()
    {
        $mapels = MataPelajaran::orderBy('nama_mapel', 'asc')->get();
        $lingkupMateris = [];

        if ($this->mapel_id) {
            $lingkupMateris = LingkupMateri::where('mapel_id', $this->mapel_id)
                ->with('tujuanPembelajaran')
                ->orderBy('urutan', 'asc')
                ->get();
        }

        return view('livewire.guru.manajemen-kurikulum-merdeka', [
            'mapels' => $mapels,
            'lingkupMateris' => $lingkupMateris,
        ])->layout('components.layouts.app', ['title' => 'Setup Kurikulum Merdeka']);
    }
}
