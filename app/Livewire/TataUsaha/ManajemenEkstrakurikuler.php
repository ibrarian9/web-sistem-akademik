<?php

namespace App\Livewire\TataUsaha;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Ekstrakurikuler;
use App\Models\SiswaEkstrakurikuler;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Semester;

class ManajemenEkstrakurikuler extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    // Form Tambah/Edit Ekskul & Assign Pembina
    public bool $isFormOpen = false;
    public ?int $ekskulId = null;
    public string $nama = '';
    public ?int $pembina_guru_id = null;
    public string $deskripsi = '';
    public int $kuota = 30;
    public bool $status_aktif = true;

    // Modal Kelola Roster / Anggota Santri
    public bool $isRosterModalOpen = false;
    public ?int $selectedEkskulForRoster = null;
    public ?int $selectedSiswaIdToAdd = null;
    public string $searchSiswaQuery = '';

    protected $queryString = ['search' => ['except' => '']];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->resetForm();
        $this->isFormOpen = true;
    }

    public function openEdit(int $id)
    {
        $this->resetForm();
        $ekskul = Ekstrakurikuler::findOrFail($id);
        $this->ekskulId = $ekskul->id;
        $this->nama = $ekskul->nama;
        $this->pembina_guru_id = $ekskul->pembina_guru_id;
        $this->deskripsi = $ekskul->deskripsi ?? '';
        $this->kuota = $ekskul->kuota ?? 30;
        $this->status_aktif = (bool) $ekskul->status_aktif;
        $this->isFormOpen = true;
    }

    public function save()
    {
        $this->validate([
            'nama' => 'required|string|max:100',
            'pembina_guru_id' => 'nullable|exists:guru,id',
            'deskripsi' => 'nullable|string',
            'kuota' => 'required|integer|min:1|max:200',
            'status_aktif' => 'required|boolean',
        ]);

        Ekstrakurikuler::updateOrCreate(
            ['id' => $this->ekskulId],
            [
                'nama' => $this->nama,
                'pembina_guru_id' => $this->pembina_guru_id,
                'deskripsi' => $this->deskripsi,
                'kuota' => $this->kuota,
                'status_aktif' => $this->status_aktif,
            ]
        );

        $action = $this->ekskulId ? 'diperbarui' : 'ditambahkan';
        session()->flash('message', "Ekstrakurikuler dan penugasan pembina berhasil {$action}.");

        $this->resetForm();
    }

    public function delete(int $id)
    {
        $ekskul = Ekstrakurikuler::findOrFail($id);
        $ekskul->delete();

        session()->flash('message', 'Data ekstrakurikuler berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->ekskulId = null;
        $this->nama = '';
        $this->pembina_guru_id = null;
        $this->deskripsi = '';
        $this->kuota = 30;
        $this->status_aktif = true;
        $this->isFormOpen = false;
    }

    // ================= ROSTER SISWA MANAGEMENT ================= //

    public function openRosterModal(int $ekskulId)
    {
        $this->selectedEkskulForRoster = $ekskulId;
        $this->selectedSiswaIdToAdd = null;
        $this->searchSiswaQuery = '';
        $this->isRosterModalOpen = true;
    }

    public function closeRosterModal()
    {
        $this->isRosterModalOpen = false;
        $this->selectedEkskulForRoster = null;
    }

    public function addSiswaToEkskul()
    {
        if (!$this->selectedEkskulForRoster || !$this->selectedSiswaIdToAdd) {
            session()->flash('roster_error', 'Silakan pilih siswa yang akan didaftarkan.');
            return;
        }

        $activeSemester = Semester::where('status_aktif', true)->first() ?? Semester::latest()->first();
        if (!$activeSemester) {
            session()->flash('roster_error', 'Semester aktif tidak ditemukan.');
            return;
        }

        $ekskul = Ekstrakurikuler::withCount('siswaEkskul')->find($this->selectedEkskulForRoster);
        if ($ekskul && $ekskul->siswa_ekskul_count >= $ekskul->kuota) {
            session()->flash('roster_error', "Kuota ekstrakurikuler ({$ekskul->kuota} santri) sudah penuh.");
            return;
        }

        $existing = SiswaEkstrakurikuler::where('ekstrakurikuler_id', $this->selectedEkskulForRoster)
            ->where('siswa_id', $this->selectedSiswaIdToAdd)
            ->where('semester_id', $activeSemester->id)
            ->first();

        if ($existing) {
            session()->flash('roster_error', 'Siswa ini sudah terdaftar pada ekstrakurikuler ini.');
            return;
        }

        SiswaEkstrakurikuler::create([
            'ekstrakurikuler_id' => $this->selectedEkskulForRoster,
            'siswa_id' => $this->selectedSiswaIdToAdd,
            'semester_id' => $activeSemester->id,
            'predikat' => 'B',
            'catatan' => 'Terdaftar oleh Staf Tata Usaha',
        ]);

        $this->selectedSiswaIdToAdd = null;
        session()->flash('roster_success', 'Santri berhasil didaftarkan ke dalam ekstrakurikuler.');
    }

    public function removeSiswaFromEkskul(int $siswaEkskulId)
    {
        $enrollment = SiswaEkstrakurikuler::find($siswaEkskulId);
        if ($enrollment) {
            $enrollment->delete();
            session()->flash('roster_success', 'Santri berhasil dikeluarkan dari ekstrakurikuler.');
        }
    }

    public function render()
    {
        $ekskuls = Ekstrakurikuler::with(['pembina.user'])
            ->withCount('siswaEkskul')
            ->when($this->search, function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $this->search . '%')
                  ->orWhereHas('pembina.user', function ($u) {
                      $u->where('nama', 'like', '%' . $this->search . '%');
                  });
            })
            ->latest()
            ->paginate($this->perPage);

        $gurus = Guru::with('user')
            ->where('status_aktif', true)
            ->get()
            ->sortBy('user.nama');

        $activeSemester = Semester::where('status_aktif', true)->first() ?? Semester::latest()->first();

        $rosterList = collect();
        $availableSiswas = collect();
        $currentEkskul = null;

        if ($this->selectedEkskulForRoster) {
            $currentEkskul = Ekstrakurikuler::with('pembina.user')->find($this->selectedEkskulForRoster);
            
            $rosterList = SiswaEkstrakurikuler::with(['siswa.user', 'siswa.kelas'])
                ->where('ekstrakurikuler_id', $this->selectedEkskulForRoster)
                ->when($activeSemester, function ($q) use ($activeSemester) {
                    $q->where('semester_id', $activeSemester->id);
                })
                ->get();

            $enrolledSiswaIds = $rosterList->pluck('siswa_id')->toArray();

            $availableSiswas = Siswa::with(['user', 'kelas'])
                ->where('siswa.status', 'aktif')
                ->whereNotIn('id', $enrolledSiswaIds)
                ->when($this->searchSiswaQuery, function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('nis', 'like', '%' . $this->searchSiswaQuery . '%')
                            ->orWhere('nisn', 'like', '%' . $this->searchSiswaQuery . '%')
                            ->orWhereHas('user', function ($u) {
                                $u->where('nama', 'like', '%' . $this->searchSiswaQuery . '%');
                            });
                    });
                })
                ->orderBy('nis')
                ->limit(30)
                ->get();
        }

        return view('livewire.tata-usaha.manajemen-ekstrakurikuler', [
            'ekskuls' => $ekskuls,
            'gurus' => $gurus,
            'currentEkskul' => $currentEkskul,
            'rosterList' => $rosterList,
            'availableSiswas' => $availableSiswas,
            'activeSemester' => $activeSemester,
        ])->layout('components.layouts.app', ['title' => 'Kelola Ekstrakurikuler - Tata Usaha']);
    }
}
