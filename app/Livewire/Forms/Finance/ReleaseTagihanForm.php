<?php

namespace App\Livewire\Forms\Finance;

use Livewire\Form;

class ReleaseTagihanForm extends Form
{
    public string $periodeTipe = 'single'; // 'single', 'full_year_juli_juni', 'full_year_jan_des', 'custom_range'
    public ?int $jenis_tagihan_id = null;
    public mixed $nominal = 0;
    public string $bulan = 'Juli';
    public string $bulan_mulai = 'Juli';
    public string $bulan_selesai = 'Desember';
    public ?int $single_siswa_id = null;
    public ?int $bulk_kelas_id = null;
    public string $bulk_target_siswa = 'aktif';

    public function rules(): array
    {
        $rules = [
            'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'nominal' => 'required',
        ];

        if ($this->periodeTipe === 'single') {
            $rules['bulan'] = 'required|string|max:50';
        } elseif ($this->periodeTipe === 'custom_range') {
            $rules['bulan_mulai'] = 'required|string';
            $rules['bulan_selesai'] = 'required|string';
        }

        return $rules;
    }
}
