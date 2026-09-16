<?php

namespace App\Livewire\Forms\Finance;

use App\Models\DanaBos as BosModel;
use Livewire\Form;

class DanaBosForm extends Form
{
    public ?int $id = null;
    public string $jenis = 'masuk'; // 'masuk' | 'keluar'
    public string $tanggal = '';
    public mixed $nominal = 0.00;
    public string $kategori = '';
    public string $keterangan = '';
    public mixed $bukti_foto = null;
    public ?string $existing_bukti = null;

    public function rules(): array
    {
        return [
            'jenis' => 'required|in:masuk,keluar',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'kategori' => 'required|string|max:255',
            'keterangan' => 'required|string|max:1000',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'jenis.required' => 'Jenis transaksi dana BOS wajib dipilih.',
            'jenis.in' => 'Jenis transaksi harus berupa masuk atau keluar.',
            'tanggal.required' => 'Tanggal transaksi wajib diisi.',
            'nominal.required' => 'Nominal transaksi wajib diisi.',
            'nominal.min' => 'Nominal tidak boleh negatif.',
            'kategori.required' => 'Kategori dana BOS wajib diisi.',
            'keterangan.required' => 'Keterangan transaksi wajib diisi.',
            'bukti_foto.image' => 'File bukti transaksi harus berupa foto/gambar.',
            'bukti_foto.mimes' => 'Format foto hanya boleh JPG, JPEG, PNG, atau WEBP.',
            'bukti_foto.max' => 'Ukuran file foto bukti maksimal 2MB.',
        ];
    }

    public function setTransaction(BosModel $bos): void
    {
        $this->id = $bos->id;
        $this->jenis = $bos->jenis;
        $this->tanggal = $bos->tanggal ? $bos->tanggal->format('Y-m-d') : date('Y-m-d');
        $this->nominal = floatval($bos->nominal);
        $this->kategori = $bos->kategori ?? '';
        $this->keterangan = $bos->keterangan ?? '';
        $this->existing_bukti = $bos->bukti_foto;
        $this->bukti_foto = null;
    }

    public function resetForm(): void
    {
        $this->reset(['id', 'nominal', 'kategori', 'keterangan', 'bukti_foto', 'existing_bukti']);
        $this->jenis = 'masuk';
        $this->tanggal = date('Y-m-d');
    }
}
