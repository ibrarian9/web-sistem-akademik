<?php

namespace App\Livewire\Forms\Finance;

use App\Models\Tabungan;
use Livewire\Form;

class TabunganTransactionForm extends Form
{
    public ?int $id = null;
    public ?int $siswa_id = null;
    public string $jenis = 'setor'; // 'setor' | 'tarik'
    public mixed $nominal = '';
    public string $tanggal = '';
    public string $keterangan = '';
    public string $alasan = '';

    public function rules(): array
    {
        return [
            'siswa_id' => 'required|exists:siswa,id',
            'jenis' => 'required|in:setor,tarik',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'siswa_id.required' => 'Pilih siswa terlebih dahulu.',
            'jenis.required' => 'Pilih jenis transaksi tabungan.',
            'nominal.required' => 'Nominal transaksi wajib diisi.',
            'nominal.min' => 'Nominal transaksi minimal Rp 1.',
            'tanggal.required' => 'Tanggal transaksi wajib diisi.',
        ];
    }

    public function setTabungan(Tabungan $tabungan): void
    {
        $this->id = $tabungan->id;
        $this->siswa_id = $tabungan->siswa_id;
        $this->jenis = $tabungan->jenis;
        $this->nominal = floatval($tabungan->nominal);
        $this->tanggal = $tabungan->tanggal ? $tabungan->tanggal->format('Y-m-d') : date('Y-m-d');
        $this->keterangan = $tabungan->keterangan ?? '';
        $this->alasan = '';
    }

    public function resetForm(): void
    {
        $this->reset(['id', 'siswa_id', 'nominal', 'keterangan', 'alasan']);
        $this->jenis = 'setor';
        $this->tanggal = date('Y-m-d');
    }
}
