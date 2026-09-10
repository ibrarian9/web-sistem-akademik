<?php

namespace App\Livewire\Forms\Finance;

use Livewire\Form;
use App\Models\GajiGuru;

class GajiGuruForm extends Form
{
    public ?int $id = null;
    public ?int $guru_id = null;
    public string $bulan = 'Januari';
    public int $tahun = 2026;
    public ?string $jabatan = 'Guru';
    public ?string $jam_kerja = '07.00-14.00';
    public ?string $sumber_dana = 'Yayasan';
    public string $status = 'draft';
    public ?string $tanggal_bayar = null;

    // Penghasilan (Earnings)
    public float $gaji_pokok = 0.00;
    public float $gaji_berkala = 0.00;
    public int $jumlah_ekskul = 0;
    public float $honor_ekskul = 0.00;
    public float $insentif = 0.00;
    public float $insentif_bpjs = 0.00;
    public float $insentif_maghrib = 0.00;
    public float $total_bruto = 0.00;

    // Potongan (Deductions)
    public float $potongan_sosial = 10000.00;
    public float $potongan_pinjaman = 0.00;
    public float $potongan_bpjstk = 0.00;
    public float $potongan_lainnya = 0.00;
    public float $total_potongan = 0.00;

    // Take Home Pay
    public float $total_diterima = 0.00;

    public function setGaji(GajiGuru $gaji): void
    {
        $this->id = $gaji->id;
        $this->guru_id = $gaji->guru_id;
        $this->bulan = $gaji->bulan;
        $this->tahun = $gaji->tahun;
        $this->jabatan = $gaji->jabatan;
        $this->jam_kerja = $gaji->jam_kerja;
        $this->sumber_dana = $gaji->sumber_dana;
        $this->status = $gaji->status;
        $this->tanggal_bayar = $gaji->tanggal_bayar?->toDateString();

        $this->gaji_pokok = floatval($gaji->gaji_pokok);
        $this->gaji_berkala = floatval($gaji->gaji_berkala);
        $this->jumlah_ekskul = intval($gaji->jumlah_ekskul);
        $this->honor_ekskul = floatval($gaji->honor_ekskul);
        $this->insentif = floatval($gaji->insentif);
        $this->insentif_bpjs = floatval($gaji->insentif_bpjs);
        $this->insentif_maghrib = floatval($gaji->insentif_maghrib_mengaji);

        $this->potongan_sosial = floatval($gaji->potongan_sosial);
        $this->potongan_pinjaman = floatval($gaji->potongan_peminjaman);
        $this->potongan_bpjstk = floatval($gaji->potongan_bpjstk);
        $this->potongan_lainnya = floatval($gaji->potongan_lainnya);

        $this->calculateTotals();
    }

    public function calculateTotals(): void
    {
        $this->total_bruto = floatval($this->gaji_pokok)
            + floatval($this->gaji_berkala)
            + floatval($this->honor_ekskul)
            + floatval($this->insentif)
            + floatval($this->insentif_bpjs)
            + floatval($this->insentif_maghrib);

        $this->total_potongan = floatval($this->potongan_sosial)
            + floatval($this->potongan_pinjaman)
            + floatval($this->potongan_bpjstk)
            + floatval($this->potongan_lainnya);

        $this->total_diterima = max(0, $this->total_bruto - $this->total_potongan);
    }
}
