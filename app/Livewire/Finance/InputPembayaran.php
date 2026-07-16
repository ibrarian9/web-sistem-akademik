<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;

class InputPembayaran extends Component
{
    // Selection properties
    public ?int $kelas_id = null;
    public ?int $siswa_id = null;
    public ?int $tagihan_id = null;

    // Payment Form properties
    public float $nominal_dibayar = 0.00;
    public string $tanggal_bayar = '';
    public string $metode_bayar = 'Tunai';
    public ?string $bukti_bayar = null; // Stored as a simple filename string for MVP

    // Lists
    public array $classes = [];
    public array $students = [];
    public array $unpaidInvoices = [];

    // Selected Invoice details for summary card
    public ?array $selectedInvoiceInfo = null;

    protected $rules = [
        'siswa_id' => 'required|exists:siswa,id',
        'tagihan_id' => 'required|exists:tagihan,id',
        'nominal_dibayar' => 'required|numeric|min:1',
        'tanggal_bayar' => 'required|date',
        'metode_bayar' => 'required|string|in:Tunai,Transfer Bank,E-Wallet',
    ];

    public function mount()
    {
        $this->classes = Kelas::orderBy('nama_kelas')->get()->toArray();
        $this->tanggal_bayar = date('Y-m-d');
    }

    public function updatedKelasId($value)
    {
        $this->siswa_id = null;
        $this->tagihan_id = null;
        $this->selectedInvoiceInfo = null;
        $this->unpaidInvoices = [];
        
        if ($value) {
            $this->students = Siswa::where('kelas_id', $value)
                ->where('status', 'aktif')
                ->with('user')
                ->get()
                ->toArray();
        } else {
            $this->students = [];
        }
    }

    public function updatedSiswaId($value)
    {
        $this->tagihan_id = null;
        $this->selectedInvoiceInfo = null;
        
        if ($value) {
            $this->unpaidInvoices = Tagihan::where('siswa_id', $value)
                ->whereIn('status', ['belum_bayar', 'sebagian'])
                ->with('jenisTagihan')
                ->get()
                ->toArray();
        } else {
            $this->unpaidInvoices = [];
        }
    }

    public function updatedTagihanId($value)
    {
        if ($value) {
            $invoice = Tagihan::where('id', $value)->with('jenisTagihan')->first();
            if ($invoice) {
                $sisa = floatval($invoice->nominal - $invoice->total_dibayar);
                $this->nominal_dibayar = $sisa;
                $this->selectedInvoiceInfo = [
                    'jenis' => $invoice->jenisTagihan->nama ?? 'Tagihan',
                    'periode' => $invoice->bulan ?: '-',
                    'nominal' => floatval($invoice->nominal),
                    'total_dibayar' => floatval($invoice->total_dibayar),
                    'sisa' => $sisa,
                ];
            }
        } else {
            $this->selectedInvoiceInfo = null;
            $this->nominal_dibayar = 0.00;
        }
    }

    public function savePayment()
    {
        $this->validate();

        $tagihan = Tagihan::where('id', $this->tagihan_id)->first();
        if (!$tagihan) {
            return;
        }

        $sisa = floatval($tagihan->nominal - $tagihan->total_dibayar);
        if ($this->nominal_dibayar > $sisa) {
            session()->flash('error', 'Nominal bayar melebihi sisa tagihan.');
            return;
        }

        DB::transaction(function () use ($tagihan) {
            // Create payment
            Pembayaran::create([
                'tagihan_id' => $this->tagihan_id,
                'tanggal_bayar' => $this->tanggal_bayar,
                'nominal_dibayar' => $this->nominal_dibayar,
                'metode_bayar' => $this->metode_bayar,
                'bukti_bayar' => $this->bukti_bayar,
                'petugas_id' => auth()->id(),
            ]);

            // Update invoice
            $newPaid = floatval($tagihan->total_dibayar) + $this->nominal_dibayar;
            $status = 'sebagian';
            if ($newPaid >= floatval($tagihan->nominal)) {
                $status = 'lunas';
            }

            $tagihan->update([
                'total_dibayar' => $newPaid,
                'status' => $status
            ]);

            // Create notification for the student
            $siswa = Siswa::where('id', $this->siswa_id)->first();
            if ($siswa && $siswa->user_id) {
                Notifikasi::create([
                    'user_id' => $siswa->user_id,
                    'siswa_id' => $siswa->id,
                    'judul' => 'Pembayaran Berhasil',
                    'isi_pesan' => "Setoran Pembayaran untuk " . ($tagihan->jenisTagihan->nama ?? 'Tagihan') . " sebesar Rp " . number_format($this->nominal_dibayar, 0, ',', '.') . " telah diterima.",
                    'jenis' => 'tunggakan',
                    'channel' => 'in_app',
                    'status_kirim' => 'terkirim',
                    'dikirim_pada' => now(),
                ]);
            }
        });

        session()->flash('message', 'Pembayaran berhasil disimpan.');
        
        // Reset properties
        $this->reset(['tagihan_id', 'nominal_dibayar', 'selectedInvoiceInfo']);
        // Refresh unpaid invoices
        $this->updatedSiswaId($this->siswa_id);
    }

    public function render()
    {
        return view('livewire.finance.input-pembayaran')
            ->layout('components.layouts.app', ['title' => 'Input Pembayaran']);
    }
}
