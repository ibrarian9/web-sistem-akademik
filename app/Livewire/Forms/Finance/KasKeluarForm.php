<?php

namespace App\Livewire\Forms\Finance;

use App\Models\Pengeluaran;
use Livewire\Form;

class KasKeluarForm extends Form
{
    public ?int $id = null;
    public ?int $kategori_pengeluaran_id = null;
    public bool $is_kategori_kustom = false;
    public string $kategori_keluar_kustom = '';
    public mixed $jumlah = 0.00;
    public string $tanggal = '';
    public string $keterangan = '';
    public mixed $bukti_foto = null;
    public ?string $existing_bukti = null;

    public function rules(): array
    {
        $rules = [
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
            'bukti_foto' => 'nullable|image|max:2048',
        ];

        if ($this->is_kategori_kustom) {
            $rules['kategori_keluar_kustom'] = 'required|string|max:100';
        } else {
            $rules['kategori_pengeluaran_id'] = 'required|exists:kategori_pengeluaran,id';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'kategori_keluar_kustom.required' => 'Nama kategori pengeluaran baru wajib diisi.',
            'kategori_keluar_kustom.max' => 'Nama kategori maksimal 100 karakter.',
            'kategori_pengeluaran_id.required' => 'Kategori pengeluaran wajib dipilih.',
            'kategori_pengeluaran_id.exists' => 'Kategori pengeluaran yang dipilih tidak valid.',
            'jumlah.required' => 'Nominal pengeluaran wajib diisi.',
            'jumlah.min' => 'Nominal pengeluaran tidak boleh bernilai negatif.',
            'tanggal.required' => 'Tanggal pengeluaran wajib diisi.',
            'bukti_foto.image' => 'File bukti harus berupa gambar (JPG, PNG).',
            'bukti_foto.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }

    public function setPengeluaran(Pengeluaran $pengeluaran): void
    {
        $this->id = $pengeluaran->id;
        $this->kategori_pengeluaran_id = $pengeluaran->kategori_pengeluaran_id;
        $this->jumlah = floatval($pengeluaran->jumlah);
        $this->tanggal = $pengeluaran->tanggal ? $pengeluaran->tanggal->format('Y-m-d') : date('Y-m-d');
        $this->keterangan = $pengeluaran->keterangan ?? '';
        $this->existing_bukti = $pengeluaran->bukti;
        $this->bukti_foto = null;
        $this->is_kategori_kustom = false;
        $this->kategori_keluar_kustom = '';
    }

    public function resetForm(?int $defaultKategoriId = null): void
    {
        $this->reset(['id', 'jumlah', 'keterangan', 'is_kategori_kustom', 'kategori_keluar_kustom', 'bukti_foto', 'existing_bukti']);
        $this->tanggal = date('Y-m-d');
        $this->kategori_pengeluaran_id = $defaultKategoriId;
    }
}
