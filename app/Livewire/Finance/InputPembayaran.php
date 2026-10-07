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
use Livewire\WithFileUploads;
use App\Traits\WithCurrencySanitizer;

class InputPembayaran extends Component
{
    use WithPagination, WithFileUploads, WithCurrencySanitizer;

    // Filters
    public string $search = '';
    public ?int $filterKelas = null;

    // Selection properties
    public ?int $siswa_id = null;
    public ?int $tagihan_id = null;
    public array $selected_tagihan_ids = [];
    public float $siswaDeposit = 0.00;

    // Payment Form properties
    public $nominal_dibayar = 0.00;
    public string $tanggal_bayar = '';
    public string $metode_bayar = 'Tunai';
    public ?string $bukti_bayar = null;
    public $bukti_foto = null;
    public ?int $lastPembayaranId = null;
    public array $createdPembayaranList = [];

    // Selected Invoice details summary
    public ?array $selectedInvoiceInfo = null;
    public array $siswaUnpaidInvoices = [];
    public array $classes = [];

    public function setMetodeBayar(string $method): void
    {
        $this->metode_bayar = $method;
    }

    protected function rules(): array
    {
        return [
            'siswa_id' => 'required|exists:siswa,id',
            'tagihan_id' => 'nullable|exists:tagihan,id',
            'selected_tagihan_ids' => 'required|array|min:1',
            'selected_tagihan_ids.*' => 'exists:tagihan,id',
            'nominal_dibayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'metode_bayar' => 'required|string|in:Tunai,Transfer Bank,E-Wallet,Deposit',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    protected $messages = [
        'selected_tagihan_ids.required' => 'Pilih setidaknya satu tagihan untuk dibayarkan.',
        'selected_tagihan_ids.min' => 'Pilih setidaknya satu tagihan untuk dibayarkan.',
        'nominal_dibayar.min' => 'Nominal pembayaran harus lebih dari 0.',
        'bukti_foto.image' => 'File bukti pembayaran harus berupa foto/gambar.',
        'bukti_foto.mimes' => 'Format foto hanya boleh JPG, JPEG, PNG, atau WEBP.',
        'bukti_foto.max' => 'Ukuran file foto bukti pembayaran maksimal 2MB.',
    ];

    public function mount(?int $siswa_id = null): void
    {
        $this->classes = Kelas::orderBy('nama_kelas')->get()->toArray();
        $this->tanggal_bayar = date('Y-m-d');

        $siswaIdParam = $siswa_id ?? request()->query('siswa_id');
        $tagihanIdParam = request()->query('tagihan_id');

        if ($siswaIdParam) {
            $this->pilihSiswaAndTagihan((int) $siswaIdParam, $tagihanIdParam ? (int) $tagihanIdParam : null);
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterKelas(): void
    {
        $this->resetPage();
    }

    public function updatedTagihanId($value): void
    {
        if ($value) {
            $val = (int) $value;
            if (!in_array($val, $this->selected_tagihan_ids)) {
                $this->selected_tagihan_ids = [$val];
            }
            $this->recalculateSelectedTotals();
        }
    }

    public function pilihSiswaAndTagihan(int $siswaId, ?int $tagihanId = null): void
    {
        $siswa = Siswa::with('user', 'kelas')->find($siswaId);
        if (!$siswa) return;

        $this->siswa_id = $siswa->id;
        $this->siswaDeposit = floatval($siswa->saldo_deposit ?? 0.00);

        $this->loadSiswaUnpaidInvoices($siswaId);

        if ($tagihanId) {
            $this->tagihan_id = $tagihanId;
            $this->selected_tagihan_ids = [$tagihanId];
        } else {
            // Default to first unpaid invoice
            if (!empty($this->siswaUnpaidInvoices)) {
                $firstId = $this->siswaUnpaidInvoices[0]['id'];
                $this->tagihan_id = $firstId;
                $this->selected_tagihan_ids = [$firstId];
            } else {
                $this->tagihan_id = null;
                $this->selected_tagihan_ids = [];
            }
        }

        $this->recalculateSelectedTotals();
    }

    public function switchTagihan(int $tagihanId): void
    {
        $this->tagihan_id = $tagihanId;
        $this->selected_tagihan_ids = [$tagihanId];
        $this->recalculateSelectedTotals();
    }

    public function toggleTagihan(int $tagihanId): void
    {
        if (in_array($tagihanId, $this->selected_tagihan_ids)) {
            $this->selected_tagihan_ids = array_values(array_diff($this->selected_tagihan_ids, [$tagihanId]));
        } else {
            $this->selected_tagihan_ids[] = $tagihanId;
        }

        $this->tagihan_id = $this->selected_tagihan_ids[0] ?? null;
        $this->recalculateSelectedTotals();
    }

    public function selectAllTagihans(): void
    {
        $this->selected_tagihan_ids = array_column($this->siswaUnpaidInvoices, 'id');
        $this->tagihan_id = $this->selected_tagihan_ids[0] ?? null;
        $this->recalculateSelectedTotals();
    }

    public function deselectAllTagihans(): void
    {
        $this->selected_tagihan_ids = [];
        $this->tagihan_id = null;
        $this->recalculateSelectedTotals();
    }

    public function loadSiswaUnpaidInvoices(int $siswaId): void
    {
        $invoices = Tagihan::where('siswa_id', $siswaId)
            ->whereIn('status', ['belum_bayar', 'sebagian'])
            ->with(['jenisTagihan', 'tahunAjaran'])
            ->orderBy('jatuh_tempo', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $this->siswaUnpaidInvoices = $invoices->map(function ($t) {
            $sisa = max(0, floatval($t->nominal) - floatval($t->total_dibayar));
            return [
                'id' => $t->id,
                'jenis' => $t->jenisTagihan->nama ?? 'Tagihan',
                'bulan' => $t->bulan ?: '-',
                'nominal' => floatval($t->nominal),
                'total_dibayar' => floatval($t->total_dibayar),
                'sisa' => $sisa,
                'status' => $t->status,
                'jatuh_tempo' => $t->jatuh_tempo ? date('d/m/Y', strtotime($t->jatuh_tempo)) : '-',
                'is_spp' => str_contains(strtoupper($t->jenisTagihan->nama ?? ''), 'SPP'),
            ];
        })->toArray();
    }

    public function recalculateSelectedTotals(): void
    {
        if (empty($this->selected_tagihan_ids)) {
            $this->selectedInvoiceInfo = null;
            $this->nominal_dibayar = 0.00;
            return;
        }

        $invoices = Tagihan::whereIn('id', $this->selected_tagihan_ids)
            ->with(['jenisTagihan', 'siswa.user', 'siswa.kelas'])
            ->orderBy('jatuh_tempo', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        if ($invoices->isEmpty()) {
            $this->selectedInvoiceInfo = null;
            $this->nominal_dibayar = 0.00;
            return;
        }

        $first = $invoices->first();
        $totalNominal = $invoices->sum(fn($t) => floatval($t->nominal));
        $totalDibayar = $invoices->sum(fn($t) => floatval($t->total_dibayar));
        $totalSisa = $invoices->sum(fn($t) => max(0, floatval($t->nominal) - floatval($t->total_dibayar)));
        $names = $invoices->map(fn($t) => ($t->jenisTagihan->nama ?? 'Tagihan') . ($t->bulan ? ' ' . $t->bulan : ''))->join(', ');

        $this->nominal_dibayar = $totalSisa;
        $this->selectedInvoiceInfo = [
            'id' => $first->id,
            'siswa_nama' => $first->siswa->user->nama ?? '-',
            'siswa_nis' => $first->siswa->nis ?? '-',
            'siswa_kelas' => $first->siswa->kelas->nama_kelas ?? '-',
            'jenis' => count($invoices) > 1 ? (count($invoices) . ' Tagihan Terpilih (' . $names . ')') : ($first->jenisTagihan->nama ?? 'Tagihan'),
            'periode' => count($invoices) > 1 ? (count($invoices) . ' Tagihan') : ($first->bulan ?: '-'),
            'nominal' => $totalNominal,
            'total_dibayar' => $totalDibayar,
            'sisa' => $totalSisa,
            'count' => count($invoices),
            'names' => $names,
        ];
    }

    public function loadSelectedTagihanDetails(int $tagihanId): void
    {
        $this->selected_tagihan_ids = [$tagihanId];
        $this->recalculateSelectedTotals();
    }

    public function resetSelection(): void
    {
        $this->reset([
            'siswa_id', 
            'tagihan_id', 
            'selected_tagihan_ids', 
            'nominal_dibayar', 
            'selectedInvoiceInfo', 
            'siswaUnpaidInvoices', 
            'siswaDeposit', 
            'bukti_foto', 
            'bukti_bayar'
        ]);
        $this->resetValidation();
    }

    public function removeBuktiFoto(): void
    {
        $this->bukti_foto = null;
    }

    public function savePayment(): void
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->sanitizeCurrencies(['nominal_dibayar']);

        // Ensure tagihan_id and selected_tagihan_ids are synced
        if (empty($this->selected_tagihan_ids) && $this->tagihan_id) {
            $this->selected_tagihan_ids = [$this->tagihan_id];
        }
        if (!empty($this->selected_tagihan_ids) && empty($this->tagihan_id)) {
            $this->tagihan_id = $this->selected_tagihan_ids[0];
        }

        $this->validate();

        if (empty($this->selected_tagihan_ids)) {
            session()->flash('error', 'Pilih setidaknya satu tagihan yang ingin dibayar.');
            return;
        }

        if ($this->metode_bayar === 'Deposit') {
            if ($this->siswaDeposit < $this->nominal_dibayar) {
                session()->flash('error', "Saldo deposit siswa (Rp " . number_format($this->siswaDeposit, 0, ',', '.') . ") tidak mencukupi untuk pembayaran sebesar Rp " . number_format($this->nominal_dibayar, 0, ',', '.') . ".");
                return;
            }
        }

        // Simpan file bukti pembayaran jika ada (max 2MB, format gambar)
        $pathBukti = $this->bukti_bayar;
        if ($this->bukti_foto) {
            $pathBukti = $this->bukti_foto->store('bukti_pembayaran', 'public');
        }

        DB::transaction(function () use ($pathBukti) {
            $tagihans = Tagihan::lockForUpdate()
                ->whereIn('id', $this->selected_tagihan_ids)
                ->with(['jenisTagihan', 'siswa'])
                ->orderBy('jatuh_tempo', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            if ($tagihans->isEmpty()) {
                return;
            }

            $remainingAmount = floatval($this->nominal_dibayar);
            $totalExcess = 0.00;
            $createdIds = [];
            $createdList = [];
            $count = $tagihans->count();

            $baseTimestamp = date('Ymd');
            $randomBase = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            foreach ($tagihans as $idx => $tagihan) {
                $isLast = ($idx === $count - 1);
                $sisaTunggakan = max(0, floatval($tagihan->nominal) - floatval($tagihan->total_dibayar));

                if ($isLast) {
                    $allocated = $remainingAmount;
                    if ($allocated > $sisaTunggakan) {
                        $kelebihan = $allocated - $sisaTunggakan;
                        $totalExcess += $kelebihan;
                    } else {
                        $kelebihan = 0.00;
                    }
                } else {
                    $allocated = min($remainingAmount, $sisaTunggakan);
                    $remainingAmount = max(0, $remainingAmount - $allocated);
                    $kelebihan = 0.00;
                }

                // If no remaining amount for this bill, skip creating empty 0 payment if multiple bills
                if ($allocated <= 0 && $count > 1) {
                    continue;
                }

                // Unique receipt number
                $suffix = ($count > 1) ? '-' . ($idx + 1) : '';
                $noResi = 'KW-' . $baseTimestamp . '-' . $randomBase . $suffix;
                while (Pembayaran::where('no_resi', $noResi)->exists()) {
                    $randomBase = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                    $noResi = 'KW-' . $baseTimestamp . '-' . $randomBase . $suffix;
                }

                $pembayaran = Pembayaran::create([
                    'no_resi' => $noResi,
                    'tagihan_id' => $tagihan->id,
                    'tanggal_bayar' => $this->tanggal_bayar,
                    'nominal_dibayar' => $allocated,
                    'kelebihan_bayar' => $kelebihan,
                    'metode_bayar' => $this->metode_bayar,
                    'bukti_bayar' => $pathBukti,
                    'is_void' => false,
                    'petugas_id' => auth()->id(),
                ]);

                $newPaid = floatval($tagihan->total_dibayar) + $allocated;
                $status = ($newPaid >= floatval($tagihan->nominal)) ? 'lunas' : 'sebagian';
                $tagihan->update([
                    'total_dibayar' => $newPaid,
                    'status' => $status,
                ]);

                $createdIds[] = $pembayaran->id;
                $createdList[] = [
                    'id' => $pembayaran->id,
                    'no_resi' => $noResi,
                    'jenis' => ($tagihan->jenisTagihan->nama ?? 'Tagihan') . ($tagihan->bulan ? ' (' . $tagihan->bulan . ')' : ''),
                    'nominal' => $allocated,
                ];
            }

            $siswaObj = Siswa::find($this->siswa_id);
            if ($siswaObj) {
                if ($this->metode_bayar === 'Deposit') {
                    $siswaObj->decrement('saldo_deposit', $this->nominal_dibayar);
                }

                if ($totalExcess > 0) {
                    $siswaObj->increment('saldo_deposit', $totalExcess);
                }

                // In-App Notification
                if ($siswaObj->user_id) {
                    $tagihanNames = $tagihans->map(fn($t) => ($t->jenisTagihan->nama ?? 'Tagihan') . ($t->bulan ? ' ' . $t->bulan : ''))->join(', ');
                    Notifikasi::create([
                        'user_id' => $siswaObj->user_id,
                        'siswa_id' => $siswaObj->id,
                        'judul' => 'Pembayaran Berhasil',
                        'isi_pesan' => "Setoran Pembayaran untuk {$tagihanNames} sebesar Rp " . number_format($this->nominal_dibayar, 0, ',', '.') . " (" . $this->metode_bayar . ") telah diterima.",
                        'jenis' => 'tunggakan',
                        'channel' => 'in_app',
                        'status_kirim' => 'terkirim',
                        'dikirim_pada' => now(),
                    ]);
                }
            }

            $this->lastPembayaranId = $createdIds[0] ?? null;
            $this->createdPembayaranList = $createdList;
        });

        $countPaid = count($this->createdPembayaranList);
        session()->flash('message', "Setoran pembayaran untuk {$countPaid} tagihan sebesar Rp " . number_format($this->nominal_dibayar, 0, ',', '.') . " berhasil disimpan.");
        $this->resetSelection();
        $this->resetPage();
    }

    public function render()
    {
        $queryTunggakan = Tagihan::tunggakan()
            ->whereIn('status', ['belum_bayar', 'sebagian'])
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
