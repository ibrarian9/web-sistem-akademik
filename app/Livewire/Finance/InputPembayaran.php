<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class InputPembayaran extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public ?int $filterKelas = null;

    // Selection properties
    public ?int $siswa_id = null;
    public ?int $tagihan_id = null;
    public float $siswaDeposit = 0.00;

    // Payment Form properties
    public float $nominal_dibayar = 0.00;
    public string $tanggal_bayar = '';
    public string $metode_bayar = 'Tunai';
    public ?string $bukti_bayar = null;
    public ?int $lastPembayaranId = null;

    // Selected Invoice details summary
    public ?array $selectedInvoiceInfo = null;
    public array $classes = [];

    public function setMetodeBayar(string $method)
    {
        $this->metode_bayar = $method;
    }

    protected function rules()
    {
        return [
            'siswa_id' => 'required|exists:siswa,id',
            'tagihan_id' => 'required|exists:tagihan,id',
            'nominal_dibayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'metode_bayar' => 'required|string|in:Tunai,Transfer Bank,E-Wallet,Deposit',
        ];
    }

    public function mount(?int $siswa_id = null)
    {
        $this->classes = Kelas::orderBy('nama_kelas')->get()->toArray();
        $this->tanggal_bayar = date('Y-m-d');

        $siswaIdParam = $siswa_id ?? request()->query('siswa_id');
        $tagihanIdParam = request()->query('tagihan_id');

        if ($siswaIdParam) {
            $this->pilihSiswaAndTagihan((int) $siswaIdParam, $tagihanIdParam ? (int) $tagihanIdParam : null);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterKelas()
    {
        $this->resetPage();
    }

    public function pilihSiswaAndTagihan(int $siswaId, ?int $tagihanId = null)
    {
        $siswa = Siswa::with('user', 'kelas')->find($siswaId);
        if (!$siswa) return;

        $this->siswa_id = $siswa->id;
        $this->siswaDeposit = floatval($siswa->saldo_deposit ?? 0.00);

        if ($tagihanId) {
            $this->tagihan_id = $tagihanId;
        } else {
            // Find first unpaid invoice for this student
            $firstUnpaid = Tagihan::where('siswa_id', $siswaId)
                ->whereIn('status', ['belum_bayar', 'sebagian'])
                ->first();
            $this->tagihan_id = $firstUnpaid ? $firstUnpaid->id : null;
        }

        if ($this->tagihan_id) {
            $this->loadSelectedTagihanDetails($this->tagihan_id);
        } else {
            $this->selectedInvoiceInfo = null;
            $this->nominal_dibayar = 0.00;
        }
    }

    public function loadSelectedTagihanDetails(int $tagihanId)
    {
        $invoice = Tagihan::where('id', $tagihanId)->with(['jenisTagihan', 'siswa.user', 'siswa.kelas'])->first();
        if ($invoice) {
            $sisa = floatval($invoice->nominal - $invoice->total_dibayar);
            $this->nominal_dibayar = $sisa;
            $this->selectedInvoiceInfo = [
                'id' => $invoice->id,
                'siswa_nama' => $invoice->siswa->user->nama ?? '-',
                'siswa_nis' => $invoice->siswa->nis ?? '-',
                'siswa_kelas' => $invoice->siswa->kelas->nama_kelas ?? '-',
                'jenis' => $invoice->jenisTagihan->nama ?? 'Tagihan',
                'periode' => $invoice->bulan ?: '-',
                'nominal' => floatval($invoice->nominal),
                'total_dibayar' => floatval($invoice->total_dibayar),
                'sisa' => $sisa,
            ];
        }
    }

    public function resetSelection()
    {
        $this->reset(['siswa_id', 'tagihan_id', 'nominal_dibayar', 'selectedInvoiceInfo', 'siswaDeposit']);
    }

    public function savePayment()
    {
        $this->validate();

        $tagihan = Tagihan::where('id', $this->tagihan_id)->first();
        if (!$tagihan) {
            session()->flash('error', 'Tagihan tidak ditemukan.');
            return;
        }

        if ($this->metode_bayar === 'Deposit') {
            if ($this->siswaDeposit < $this->nominal_dibayar) {
                session()->flash('error', "Saldo deposit siswa (Rp " . number_format($this->siswaDeposit, 0, ',', '.') . ") tidak mencukupi untuk pembayaran sebesar Rp " . number_format($this->nominal_dibayar, 0, ',', '.') . ".");
                return;
            }
        }

        DB::transaction(function () {
            // Lock tagihan row for update
            $tagihan = Tagihan::lockForUpdate()->find($this->tagihan_id);
            if (!$tagihan) return;

            $sisaTunggakan = max(0, floatval($tagihan->nominal) - floatval($tagihan->total_dibayar));
            $kelebihan = max(0, floatval($this->nominal_dibayar) - $sisaTunggakan);

            // Unique receipt number
            $noResi = 'KW-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Create payment
            $pembayaran = Pembayaran::create([
                'no_resi' => $noResi,
                'tagihan_id' => $this->tagihan_id,
                'tanggal_bayar' => $this->tanggal_bayar,
                'nominal_dibayar' => $this->nominal_dibayar,
                'kelebihan_bayar' => $kelebihan,
                'metode_bayar' => $this->metode_bayar,
                'bukti_bayar' => $this->bukti_bayar,
                'is_void' => false,
                'petugas_id' => auth()->id(),
            ]);

            $this->lastPembayaranId = $pembayaran->id;

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

            $siswaObj = Siswa::find($this->siswa_id);
            if ($siswaObj) {
                // If paid via deposit, subtract from deposit
                if ($this->metode_bayar === 'Deposit') {
                    $siswaObj->decrement('saldo_deposit', $this->nominal_dibayar);
                }

                // If overpayment, add excess to deposit
                if ($kelebihan > 0) {
                    $siswaObj->increment('saldo_deposit', $kelebihan);
                }
            }

            // Notification
            if ($siswaObj && $siswaObj->user_id) {
                Notifikasi::create([
                    'user_id' => $siswaObj->user_id,
                    'siswa_id' => $siswaObj->id,
                    'judul' => 'Pembayaran Berhasil',
                    'isi_pesan' => "Setoran Pembayaran untuk " . ($tagihan->jenisTagihan->nama ?? 'Tagihan') . " sebesar Rp " . number_format($this->nominal_dibayar, 0, ',', '.') . " (" . $this->metode_bayar . ") telah diterima.",
                    'jenis' => 'tunggakan',
                    'channel' => 'in_app',
                    'status_kirim' => 'terkirim',
                    'dikirim_pada' => now(),
                ]);
            }
        });

        session()->flash('message', 'Setoran pembayaran berhasil disimpan.');
        $this->resetSelection();
        $this->resetPage();
    }

    public function render()
    {
        $queryTunggakan = Tagihan::whereIn('status', ['belum_bayar', 'sebagian'])
            ->with(['siswa.user', 'siswa.kelas', 'jenisTagihan'])
            ->latest();

        if ($this->filterKelas) {
            $queryTunggakan->whereHas('siswa', function ($q) {
                $q->where('kelas_id', $this->filterKelas);
            });
        }

        if (trim($this->search) !== '') {
            $queryTunggakan->where(function ($query) {
                $query->whereHas('siswa.user', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                })->orWhereHas('siswa', function ($q) {
                    $q->where('nis', 'like', '%' . $this->search . '%');
                });
            });
        }

        $activeTunggakan = $queryTunggakan->paginate(12);

        return view('livewire.finance.input-pembayaran', [
            'activeTunggakan' => $activeTunggakan
        ])->layout('components.layouts.app', ['title' => 'Input Pembayaran']);
    }
}
