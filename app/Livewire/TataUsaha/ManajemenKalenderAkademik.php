<?php

namespace App\Livewire\TataUsaha;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KalenderAkademik;
use App\Models\TahunAjaran;
use App\Models\Semester;

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

    public bool $showModal = false;
    public bool $isEditing = false;

    // Tahun Ajaran modal & properties
    public bool $showTahunAjaranModal = false;
    public string $newTahunAjaranNama = '';

    // Custom Semester Dates
    public string $tglMulaiGanjil = '';
    public string $tglSelesaiGanjil = '';
    public string $tglMulaiGenap = '';
    public string $tglSelesaiGenap = '';

    // Editing existing semester dates
    public ?int $editingSemesterId = null;
    public string $editSemesterMulai = '';
    public string $editSemesterSelesai = '';

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

    public function openTahunAjaranModal()
    {
        if (!$this->canManage()) return;
        $this->newTahunAjaranNama = '';
        $this->setDefaultSemesterDates();
        $this->editingSemesterId = null;
        $this->showTahunAjaranModal = true;
    }

    public function updatedNewTahunAjaranNama($value)
    {
        $this->setDefaultSemesterDates($value);
    }

    private function setDefaultSemesterDates(?string $taNama = null)
    {
        $yearStart = (int) (explode('/', $taNama ?? '')[0] ?? date('Y'));
        if ($yearStart < 2000) $yearStart = (int) date('Y');
        $yearEnd = $yearStart + 1;

        $this->tglMulaiGanjil = "{$yearStart}-07-01";
        $this->tglSelesaiGanjil = "{$yearStart}-12-31";
        $this->tglMulaiGenap = "{$yearEnd}-01-01";
        $this->tglSelesaiGenap = "{$yearEnd}-06-30";
    }

    public function createTahunAjaran()
    {
        if (!$this->canManage()) return;

        if (!$this->tglMulaiGanjil || !$this->tglMulaiGenap) {
            $this->setDefaultSemesterDates($this->newTahunAjaranNama);
        }

        $this->validate([
            'newTahunAjaranNama' => 'required|string|max:50|unique:tahun_ajaran,nama',
            'tglMulaiGanjil' => 'required|date',
            'tglSelesaiGanjil' => 'required|date|after_or_equal:tglMulaiGanjil',
            'tglMulaiGenap' => 'required|date',
            'tglSelesaiGenap' => 'required|date|after_or_equal:tglMulaiGenap',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () {
            $ta = TahunAjaran::create([
                'nama' => $this->newTahunAjaranNama,
                'status_aktif' => false,
            ]);

            Semester::create([
                'tahun_ajaran_id' => $ta->id,
                'semester' => 'Ganjil',
                'tanggal_mulai' => $this->tglMulaiGanjil,
                'tanggal_selesai' => $this->tglSelesaiGanjil,
                'status_aktif' => false,
            ]);

            Semester::create([
                'tahun_ajaran_id' => $ta->id,
                'semester' => 'Genap',
                'tanggal_mulai' => $this->tglMulaiGenap,
                'tanggal_selesai' => $this->tglSelesaiGenap,
                'status_aktif' => false,
            ]);
        });

        session()->flash('message', "Tahun Ajaran {$this->newTahunAjaranNama} beserta Semester Ganjil & Genap berhasil dibuat.");
        $this->newTahunAjaranNama = '';
        $this->setDefaultSemesterDates();
    }

    public function openEditSemester(int $semesterId)
    {
        if (!$this->canManage()) return;
        $sem = Semester::findOrFail($semesterId);
        $this->editingSemesterId = $sem->id;
        $this->editSemesterMulai = $sem->tanggal_mulai ? $sem->tanggal_mulai->format('Y-m-d') : date('Y-m-d');
        $this->editSemesterSelesai = $sem->tanggal_selesai ? $sem->tanggal_selesai->format('Y-m-d') : date('Y-m-d');
    }

    public function saveSemesterDates()
    {
        if (!$this->canManage() || !$this->editingSemesterId) return;

        $this->validate([
            'editSemesterMulai' => 'required|date',
            'editSemesterSelesai' => 'required|date|after_or_equal:editSemesterMulai',
        ]);

        $sem = Semester::findOrFail($this->editingSemesterId);
        $sem->update([
            'tanggal_mulai' => $this->editSemesterMulai,
            'tanggal_selesai' => $this->editSemesterSelesai,
        ]);

        session()->flash('message', "Rentang tanggal Semester {$sem->semester} berhasil diperbarui.");
        $this->editingSemesterId = null;
    }

    public function setTahunAjaranAktif(int $taId, ?int $semesterId = null)
    {
        if (!$this->canManage()) return;

        \Illuminate\Support\Facades\DB::transaction(function () use ($taId, $semesterId) {
            TahunAjaran::query()->update(['status_aktif' => false]);
            \App\Models\Semester::query()->update(['status_aktif' => false]);

            $ta = TahunAjaran::findOrFail($taId);
            $ta->update(['status_aktif' => true]);

            if ($semesterId) {
                $sem = \App\Models\Semester::where('tahun_ajaran_id', $taId)->where('id', $semesterId)->first();
                if ($sem) {
                    $sem->update(['status_aktif' => true]);
                }
            } else {
                $semGanjil = \App\Models\Semester::where('tahun_ajaran_id', $taId)->where('semester', 'Ganjil')->first() 
                    ?? \App\Models\Semester::where('tahun_ajaran_id', $taId)->first();
                if ($semGanjil) {
                    $semGanjil->update(['status_aktif' => true]);
                }
            }
        });

        session()->flash('message', 'Status Tahun Ajaran & Semester aktif berhasil diperbarui.');
    }

    public function deleteTahunAjaran(int $taId)
    {
        if (!$this->canManage()) return;

        $ta = TahunAjaran::withCount(['tagihans', 'danaBos', 'kalenderAkademiks', 'semesters'])->findOrFail($taId);

        $hasGradesOrClasses = false;
        foreach ($ta->semesters as $sem) {
            if ($sem->nilais()->exists() || $sem->kelas()->exists() || $sem->rapors()->exists()) {
                $hasGradesOrClasses = true;
                break;
            }
        }

        if ($ta->tagihans_count > 0 || $ta->dana_bos_count > 0 || $ta->kalender_akademiks_count > 0 || $hasGradesOrClasses) {
            session()->flash('error', "Tahun Ajaran '{$ta->nama}' tidak dapat dihapus karena sudah memiliki data transaksi/akademik yang terhubung.");
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($ta) {
            $ta->semesters()->delete();
            $ta->delete();
        });

        session()->flash('message', "Tahun Ajaran '{$ta->nama}' berhasil dihapus.");
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

    public function canManage(): bool
    {
        $role = auth()->user()->role->nama ?? '';
        return in_array($role, ['tata_usaha', 'super_admin']);
    }

    public function openCreateModal()
    {
        if (!$this->canManage()) {
            session()->flash('error', 'Hanya Tata Usaha dan Super Admin yang dapat mengelola kalender akademik.');
            return;
        }
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        if (!$this->canManage()) {
            session()->flash('error', 'Hanya Tata Usaha dan Super Admin yang dapat mengelola kalender akademik.');
            return;
        }
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
        if (!$this->canManage()) {
            session()->flash('error', 'Hanya Tata Usaha dan Super Admin yang dapat mengelola kalender akademik.');
            return;
        }
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
        if (!$this->canManage()) {
            session()->flash('error', 'Hanya Tata Usaha dan Super Admin yang dapat mengelola kalender akademik.');
            return;
        }
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
        $tahunAjarans = TahunAjaran::with('semesters')->get();

        return view('livewire.tata-usaha.manajemen-kalender-akademik', [
            'events' => $events,
            'tahunAjarans' => $tahunAjarans,
            'canManage' => $this->canManage(),
        ])->layout('components.layouts.app', ['title' => 'Kalender Akademik & Hari Libur']);
    }
}
