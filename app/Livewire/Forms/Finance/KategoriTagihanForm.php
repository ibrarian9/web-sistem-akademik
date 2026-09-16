<?php

namespace App\Livewire\Forms\Finance;

use App\Models\JenisTagihan;
use Livewire\Form;

class KategoriTagihanForm extends Form
{
    public ?int $id = null;
    public string $nama = '';
    public string $tipe = 'rutin'; // 'rutin' | 'one_time' | 'tahunan' | 'semester'
    public float $nominal = 0.00;
    public bool $is_blocking = true;

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:100',
            'tipe' => 'required|in:rutin,one_time,tahunan,semester,per_6_bulan',
            'nominal' => 'required|numeric|min:0',
            'is_blocking' => 'boolean',
        ];
    }

    public function setKategori(JenisTagihan $kategori): void
    {
        $this->id = $kategori->id;
        $this->nama = $kategori->nama;
        $this->tipe = $kategori->tipe ?? 'rutin';
        $this->nominal = floatval($kategori->nominal ?? 0);
        $this->is_blocking = (bool) ($kategori->is_blocking ?? true);
    }

    public function resetForm(): void
    {
        $this->reset(['id', 'nama', 'tipe', 'nominal', 'is_blocking']);
        $this->tipe = 'rutin';
        $this->nominal = 0.00;
        $this->is_blocking = true;
    }
}
