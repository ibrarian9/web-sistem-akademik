<?php

namespace App\Livewire\SuperAdmin\TataKelola;

use Livewire\Component;
use App\Models\KomponenNilai;

class ManajemenKomponenNilai extends Component
{
    public array $komponens = [];

    protected $rules = [
        'komponens.*.nama' => 'required|string|max:50',
        'komponens.*.bobot' => 'required|numeric|min:0|max:100',
    ];

    public function mount()
    {
        $this->loadKomponen();
    }

    public function loadKomponen()
    {
        $this->komponens = KomponenNilai::orderBy('urutan')->get()->toArray();
    }

    public function save()
    {
        $this->validate();

        $totalBobot = 0;
        foreach ($this->komponens as $k) {
            $totalBobot += $k['bobot'];
        }

        foreach ($this->komponens as $k) {
            KomponenNilai::findOrFail($k['id'])->update([
                'nama' => $k['nama'],
                'bobot' => $k['bobot'],
            ]);
        }

        if ($totalBobot != 100) {
            session()->flash('warning', "Komponen berhasil disimpan, namun total bobot saat ini adalah {$totalBobot}%. Disarankan total bobot berjumlah 100%.");
        } else {
            session()->flash('message', 'Komponen nilai berhasil diperbarui.');
        }

        $this->loadKomponen();
    }

    public function render()
    {
        return view('livewire.super-admin.tata-kelola.manajemen-komponen-nilai')
            ->layout('components.layouts.app', ['title' => 'Manajemen Komponen Nilai']);
    }
}
