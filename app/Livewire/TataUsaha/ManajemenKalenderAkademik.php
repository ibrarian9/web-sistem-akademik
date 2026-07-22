<?php

namespace App\Livewire\TataUsaha;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KalenderAkademik;
use App\Models\TahunAjaran;

class ManajemenKalenderAkademik extends Component
{
    use WithPagination;

    public $search = '';
    public $filterJenis = '';
    public $filterTahunAjaranId = '';

    // Form fields
    public $eventId = null;
    public $tahun_ajaran_id;
    public $nama_kegiatan;
    public $jenis = 'hari_libur';
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $liburkan_presensi = true;
    public $keterangan;

    public $showModal = false;
    public $isEditing = false;

    protected function rules()
    {
        return [
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'nama_kegiatan' => 'required|string|max:255',
            'jenis' => 'required|in:hari_libur,libur_semester,kegiatan_akademik,ujian',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'liburkan_presensi' => 'boolean',
            'keterangan' => 'nullable|string',
        ];
    }

    public function mount()
    {
        $activeTahun = TahunAjaran::where('status_aktif', true)->first();
        if ($activeTahun) {
            $this->tahun_ajaran_id = $activeTahun->id;
            $this->filterTahunAjaranId = $activeTahun->id;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterJenis()
    {
        $this->resetPage();
    }

    public function updatingFilterTahunAjaranId()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $event = KalenderAkademik::findOrFail($id);
        $this->eventId = $event->id;
        $this->tahun_ajaran_id = $event->tahun_ajaran_id;
        $this->nama_kegiatan = $event->nama_kegiatan;
        $this->jenis = $event->jenis;
        $this->tanggal_mulai = $event->tanggal_mulai->format('Y-m-d');
        $this->tanggal_selesai = $event->tanggal_selesai->format('Y-m-d');
        $this->liburkan_presensi = (bool) $event->liburkan_presensi;
        $this->keterangan = $event->keterangan;

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $activeTahun = TahunAjaran::where('status_aktif', true)->first();
        $this->eventId = null;
        $this->tahun_ajaran_id = $activeTahun ? $activeTahun->id : null;
        $this->nama_kegiatan = '';
        $this->jenis = 'hari_libur';
        $this->tanggal_mulai = date('Y-m-d');
        $this->tanggal_selesai = date('Y-m-d');
        $this->liburkan_presensi = true;
        $this->keterangan = '';
        $this->resetValidation();
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->isEditing && $this->eventId) {
            $event = KalenderAkademik::findOrFail($this->eventId);
            $event->update($validated);
            session()->flash('message', 'Agenda kalender akademik berhasil diperbarui.');
        } else {
            KalenderAkademik::create($validated);
            session()->flash('message', 'Agenda kalender akademik baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        KalenderAkademik::findOrFail($id)->delete();
        session()->flash('message', 'Agenda kalender akademik berhasil dihapus.');
    }

    public function render()
    {
        $query = KalenderAkademik::with('tahunAjaran')
            ->when($this->search, function ($q) {
                $q->where('nama_kegiatan', 'like', '%' . $this->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterJenis, function ($q) {
                $q->where('jenis', $this->filterJenis);
            })
            ->when($this->filterTahunAjaranId, function ($q) {
                $q->where('tahun_ajaran_id', $this->filterTahunAjaranId);
            })
            ->orderBy('tanggal_mulai', 'desc');

        $events = $query->paginate(10);
        $tahunAjarans = TahunAjaran::all();

        return view('livewire.tata-usaha.manajemen-kalender-akademik', [
            'events' => $events,
            'tahunAjarans' => $tahunAjarans,
        ])->layout('components.layouts.app', ['title' => 'Kalender Akademik & Hari Libur']);
    }
}
