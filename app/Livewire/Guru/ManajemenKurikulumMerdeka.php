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
        if (!$this->mapel_id) {
            $firstMapel = MataPelajaran::orderBy('id', 'asc')->first();
            $this->mapel_id = $firstMapel?->id;
            if ($this->mapel_id) {
                $this->loadMapelData();
            }
        }

        $this->editingLmId = $id;
        if ($id) {
            $lm = LingkupMateri::findOrFail($id);
            $this->nama_lingkup_materi = $lm->nama_lingkup_materi;
            $this->kategori_lm = $lm->kategori;
            $this->urutan_lm = $lm->urutan;
            if (!$this->mapel_id) {
                $this->mapel_id = $lm->mapel_id;
                $this->loadMapelData();
            }
        } else {
            $this->nama_lingkup_materi = '';
            $this->kategori_lm = 'sumatif';
            $this->urutan_lm = $this->mapel_id ? ((LingkupMateri::where('mapel_id', $this->mapel_id)->max('urutan') ?? 0) + 1) : 1;
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
        // Fallback jika mapel_id masih belum terisi
        if ($this->editingLmId && !$this->mapel_id) {
            $existing = LingkupMateri::find($this->editingLmId);
            $this->mapel_id = $existing?->mapel_id;
        }

        if (!$this->mapel_id) {
            $firstMapel = MataPelajaran::orderBy('id', 'asc')->first();
            $this->mapel_id = $firstMapel?->id;
        }

        $this->validate([
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'nama_lingkup_materi' => 'required|string|max:255',
        ], [
            'mapel_id.required' => 'Mata pelajaran wajib dipilih sebelum menyimpan Bab.',
            'mapel_id.exists' => 'Mata pelajaran tidak valid.',
            'nama_lingkup_materi.required' => 'Nama Lingkup Materi / Bab wajib diisi.',
        ]);

        LingkupMateri::updateOrCreate(
            ['id' => $this->editingLmId],
            [
                'mapel_id' => $this->mapel_id,
                'nama_lingkup_materi' => $this->nama_lingkup_materi,
                'kategori' => $this->kategori_lm ?: 'sumatif',
                'urutan' => $this->urutan_lm ?: 1,
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
        if (!$this->mapel_id) {
            $firstMapel = MataPelajaran::orderBy('id', 'asc')->first();
            $this->mapel_id = $firstMapel?->id;
            if ($this->mapel_id) {
                $this->loadMapelData();
            }
        }

        $availableLms = $this->mapel_id ? LingkupMateri::where('mapel_id', $this->mapel_id)->count() : 0;
        if ($availableLms === 0) {
            session()->flash('error', 'Belum ada Bab (Lingkup Materi) pada mata pelajaran ini. Silakan tambahkan Bab terlebih dahulu sebelum membuat TP.');
            $this->openLmModal();
            return;
        }

        if (!$lmId && $this->mapel_id) {
            $lm = LingkupMateri::where('mapel_id', $this->mapel_id)->orderBy('urutan', 'asc')->first();
            $lmId = $lm ? $lm->id : null;
        }

        $this->lingkup_materi_id = $lmId;
        $this->editingTpId = $tpId;

        if ($tpId) {
            $tp = TujuanPembelajaran::findOrFail($tpId);
            $this->deskripsi_tp = $tp->deskripsi_tp;
            $this->urutan_tp = $tp->urutan;
            $this->lingkup_materi_id = $tp->lingkup_materi_id;
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
        $this->lingkup_materi_id = null;
        $this->deskripsi_tp = '';
        $this->resetValidation();
    }

    public function saveTp()
    {
        // 1. Fallback jika sedang edit TP dan lingkup_materi_id kosong
        if ($this->editingTpId && !$this->lingkup_materi_id) {
            $existing = TujuanPembelajaran::find($this->editingTpId);
            $this->lingkup_materi_id = $existing?->lingkup_materi_id;
        }

        // 2. Fallback jika lingkup_materi_id belum terpilih tapi mapel aktif memiliki bab
        if (!$this->lingkup_materi_id && $this->mapel_id) {
            $lm = LingkupMateri::where('mapel_id', $this->mapel_id)->orderBy('urutan', 'asc')->first();
            $this->lingkup_materi_id = $lm?->id;
        }

        // 3. Fallback jika mapel belum diset namun ada Bab di database
        if (!$this->lingkup_materi_id) {
            $lm = LingkupMateri::orderBy('id', 'asc')->first();
            $this->lingkup_materi_id = $lm?->id;
        }

        // 4. Validasi ketat untuk menjamin lingkup_materi_id tidak pernah NULL
        $this->validate([
            'lingkup_materi_id' => 'required|exists:lingkup_materi,id',
            'deskripsi_tp' => 'required|string',
        ], [
            'lingkup_materi_id.required' => 'Bab (Lingkup Materi) target wajib dipilih sebelum menyimpan TP.',
            'lingkup_materi_id.exists' => 'Bab target yang dipilih tidak valid.',
            'deskripsi_tp.required' => 'Deskripsi TP wajib diisi.',
        ]);

        TujuanPembelajaran::updateOrCreate(
            ['id' => $this->editingTpId],
            [
                'lingkup_materi_id' => $this->lingkup_materi_id,
                'deskripsi_tp' => $this->deskripsi_tp,
                'urutan' => $this->urutan_tp ?: 1,
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
