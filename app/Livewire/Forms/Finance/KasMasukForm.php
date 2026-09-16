<?php

namespace App\Livewire\Forms\Finance;

use Livewire\Form;

class KasMasukForm extends Form
{
    public string $kategori = 'Infaq';
    public bool $is_kategori_kustom = false;
    public string $kategori_kustom = '';
    public mixed $jumlah = 0.00;
    public string $tanggal = '';
    public string $keterangan = '';

    public function rules(): array
    {
        $rules = [
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ];

        if ($this->is_kategori_kustom) {
            $rules['kategori_kustom'] = 'required|string|max:100';
        } else {
            $rules['kategori'] = 'required|string|max:100';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'kategori_kustom.required' => 'Nama kategori penerimaan baru wajib diisi.',
            'kategori_kustom.max' => 'Nama kategori maksimal 100 karakter.',
            'kategori.required' => 'Kategori penerimaan wajib dipilih.',
            'jumlah.required' => 'Nominal penerimaan wajib diisi.',
            'jumlah.min' => 'Nominal penerimaan tidak boleh bernilai negatif.',
            'tanggal.required' => 'Tanggal penerimaan wajib diisi.',
        ];
    }

    public function getEffectiveKategori(): string
    {
        if ($this->is_kategori_kustom) {
            return trim($this->kategori_kustom);
        }

        return $this->kategori ?: 'Infaq';
    }

    public function resetForm(string $defaultKategori = 'Infaq'): void
    {
        $this->reset(['jumlah', 'keterangan', 'is_kategori_kustom', 'kategori_kustom']);
        $this->tanggal = date('Y-m-d');
        $this->kategori = $defaultKategori;
    }
}
