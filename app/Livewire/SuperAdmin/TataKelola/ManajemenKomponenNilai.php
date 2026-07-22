<?php

namespace App\Livewire\SuperAdmin\TataKelola;

use Livewire\Component;
use App\Models\KomponenNilai;

class ManajemenKomponenNilai extends Component
{
    public string $filterBerlaku = 'semua'; // 'semua', 'umum', 'tahfidz'

    // Form fields for modal create/edit
    public bool $isModalOpen = false;
    public ?int $editingId = null;
    public string $nama = '';
    public string $kategori = 'pengetahuan';
    public string $berlaku_untuk = 'umum';
    public float $bobot = 10.0;

    public array $komponens = [];

    public function mount()
    {
        $this->loadKomponen();
    }

    public function updatedFilterBerlaku()
    {
        $this->loadKomponen();
    }

    public function loadKomponen()
    {
        $query = KomponenNilai::query();
        if ($this->filterBerlaku !== 'semua') {
            $query->whereIn('berlaku_untuk', [$this->filterBerlaku, 'semua']);
        }

        $this->komponens = $query->orderBy('berlaku_untuk')->orderBy('urutan')->get()->toArray();
    }

    public function openCreate()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function openEdit(int $id)
    {
        $this->resetForm();
        $item = KomponenNilai::findOrFail($id);
        $this->editingId = $item->id;
        $this->nama = $item->nama;
        $this->kategori = $item->kategori;
        $this->berlaku_untuk = $item->berlaku_untuk;
        $this->bobot = floatval($item->bobot);
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function saveForm()
    {
        $this->validate([
            'nama' => 'required|string|max:100',
            'kategori' => 'required|in:pengetahuan,keterampilan,keagamaan,sikap',
            'berlaku_untuk' => 'required|in:umum,tahfidz,semua',
            'bobot' => 'required|numeric|min:0|max:100',
        ]);

        if ($this->editingId) {
            $item = KomponenNilai::findOrFail($this->editingId);
            $item->update([
                'nama' => $this->nama,
                'kategori' => $this->kategori,
                'berlaku_untuk' => $this->berlaku_untuk,
                'bobot' => $this->bobot,
            ]);
            session()->flash('message', "Komponen nilai '{$this->nama}' berhasil diperbarui.");
        } else {
            $maxUrutan = KomponenNilai::max('urutan') ?? 0;
            KomponenNilai::create([
                'nama' => $this->nama,
                'kategori' => $this->kategori,
                'berlaku_untuk' => $this->berlaku_untuk,
                'bobot' => $this->bobot,
                'urutan' => $maxUrutan + 1,
            ]);
            session()->flash('message', "Komponen nilai baru '{$this->nama}' berhasil ditambahkan.");
        }

        $this->closeModal();
        $this->loadKomponen();
    }

    public function delete(int $id)
    {
        $item = KomponenNilai::findOrFail($id);
        
        // Check if used in nilais table
        if ($item->nilais()->exists()) {
            session()->flash('error', "Komponen nilai '{$item->nama}' tidak dapat dihapus karena sudah memiliki data nilai siswa.");
            return;
        }

        $item->delete();
        session()->flash('message', "Komponen nilai '{$item->nama}' berhasil dihapus.");
        $this->loadKomponen();
    }

    public function saveQuickWeights()
    {
        foreach ($this->komponens as $k) {
            KomponenNilai::where('id', $k['id'])->update([
                'nama' => $k['nama'],
                'bobot' => floatval($k['bobot']),
            ]);
        }

        session()->flash('message', 'Perubahan komponen & rekomendasi bobot berhasil disimpan.');
        $this->loadKomponen();
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->nama = '';
        $this->kategori = 'pengetahuan';
        $this->berlaku_untuk = 'umum';
        $this->bobot = 10.0;
    }

    public function render()
    {
        return view('livewire.super-admin.tata-kelola.manajemen-komponen-nilai')
            ->layout('components.layouts.app', ['title' => 'Manajemen Komponen Nilai']);
    }
}
