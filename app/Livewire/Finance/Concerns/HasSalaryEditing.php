<?php

namespace App\Livewire\Finance\Concerns;

use App\Models\GajiGuru;
use App\Models\Peminjaman;
use App\Models\Pengeluaran;
use App\Services\SalaryCalculationService;
use App\Services\FinancialApprovalService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HasSalaryEditing
{
    public bool $showEditModal = false;
    public ?int $editingId = null;
    public ?string $editGuruNama = '';
    public ?string $editJabatan = '';
    public ?string $editJamKerja = '07.00-14.00';
    public ?string $editSumberDana = 'Yayasan';
    public string $editStatus = 'draft';
    public ?string $editTanggalBayar = '';
    public string $editBulan = 'Januari';
    public int $editTahun = 2026;

    // Earnings
    public float $editGajiPokok = 0.00;
    public float $editGajiBerkala = 0.00;
    public int $editJumlahEkskul = 0;
    public float $editHonorEkskul = 0.00;
    public float $editInsentif = 0.00;
    public float $editInsentifBpjs = 0.00;
    public float $editInsentifMaghrib = 0.00;
    public float $editTotalBruto = 0.00;

    // Deductions
    public float $editPotonganSosial = 10000.00;
    public float $editPotonganPinjaman = 0.00;
    public float $editPotonganBpjstk = 0.00;
    public float $editPotonganLainnya = 0.00;
    public float $editTotalPotongan = 0.00;

    // Net THP
    public float $editTotalDiterima = 0.00;
    public string $edit_alasan = '';

    public function openEditModal(int $id): void
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $gaji = GajiGuru::with('guru.user')->findOrFail($id);

        $this->editingId = $id;
        $this->edit_alasan = 'Penyesuaian nominal dan rincian gaji pegawai';
        $this->editGuruNama = $gaji->guru->user->nama ?? '';
        $this->editBulan = $gaji->bulan;
        $this->editTahun = intval($gaji->tahun);
        $this->editJabatan = $gaji->jabatan ?: ($gaji->guru->jabatan ?? 'Guru');
        $this->editJamKerja = $gaji->jam_kerja ?: '07.00-14.00';
        $this->editSumberDana = $gaji->sumber_dana ?: 'Yayasan';
        $this->editStatus = $gaji->status;
        $this->editTanggalBayar = $gaji->tanggal_bayar ? $gaji->tanggal_bayar->format('Y-m-d') : date('Y-m-d');

        $this->editGajiPokok = floatval($gaji->gaji_pokok);
        $this->editGajiBerkala = floatval($gaji->gaji_berkala);
        $this->editJumlahEkskul = intval($gaji->jumlah_ekskul);
        $this->editHonorEkskul = floatval($gaji->honor_ekskul);
        $this->editInsentif = floatval($gaji->insentif);
        $this->editInsentifBpjs = floatval($gaji->insentif_bpjs);
        $this->editInsentifMaghrib = floatval($gaji->insentif_maghrib_mengaji);

        $this->editPotonganSosial = floatval($gaji->potongan_sosial ?: 10000.00);
        $this->editPotonganPinjaman = floatval($gaji->potongan_peminjaman);
        $this->editPotonganBpjstk = floatval($gaji->potongan_bpjstk);
        $this->editPotonganLainnya = floatval($gaji->potongan_lainnya);

        $this->calculateEditTotal();

        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->reset([
            'editingId', 'editGuruNama', 'editJabatan', 'editJamKerja', 'editSumberDana',
            'editBulan', 'editTahun',
            'editGajiPokok', 'editGajiBerkala', 'editJumlahEkskul', 'editHonorEkskul',
            'editInsentif', 'editInsentifBpjs', 'editInsentifMaghrib', 'editTotalBruto',
            'editPotonganSosial', 'editPotonganPinjaman', 'editPotonganBpjstk', 'editPotonganLainnya', 'editTotalPotongan',
            'editTotalDiterima', 'editStatus', 'editTanggalBayar', 'edit_alasan'
        ]);
    }

    public function calculateEditTotal(): void
    {
        $calc = app(SalaryCalculationService::class);
        $this->editTotalBruto = $calc->calculateBruto(
            floatval($this->editGajiPokok),
            floatval($this->editGajiBerkala),
            floatval($this->editHonorEkskul),
            floatval($this->editInsentif),
            floatval($this->editInsentifBpjs),
            floatval($this->editInsentifMaghrib)
        );

        $this->editTotalPotongan = $calc->calculatePotongan(
            floatval($this->editPotonganSosial),
            floatval($this->editPotonganPinjaman),
            floatval($this->editPotonganBpjstk),
            floatval($this->editPotonganLainnya)
        );

        $this->editTotalDiterima = $calc->calculateNetTakeHomePay($this->editTotalBruto, $this->editTotalPotongan);
    }

    public function saveEdit(): void
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->sanitizeCurrencies([
            'editGajiPokok',
            'editGajiBerkala',
            'editHonorEkskul',
            'editInsentif',
            'editInsentifBpjs',
            'editInsentifMaghrib',
            'editPotonganSosial',
            'editPotonganPinjaman',
            'editPotonganBpjstk',
            'editPotonganLainnya',
        ]);

        $rules = [
            'editBulan' => 'required|string|in:' . implode(',', $this->listBulan),
            'editTahun' => 'required|integer|min:2020|max:2035',
            'editGajiPokok' => 'required|numeric|min:0',
            'editGajiBerkala' => 'required|numeric|min:0',
            'editJumlahEkskul' => 'required|integer|min:0',
            'editHonorEkskul' => 'required|numeric|min:0',
            'editInsentif' => 'required|numeric|min:0',
            'editInsentifBpjs' => 'required|numeric|min:0',
            'editInsentifMaghrib' => 'required|numeric|min:0',
            'editPotonganSosial' => 'required|numeric|min:0',
            'editPotonganPinjaman' => 'required|numeric|min:0',
            'editPotonganBpjstk' => 'required|numeric|min:0',
            'editPotonganLainnya' => 'required|numeric|min:0',
            'editSumberDana' => 'required|string|max:100',
        ];

        $gaji = GajiGuru::with('guru.user')->findOrFail($this->editingId);
        $userRole = auth()->user()->role->nama ?? '';

        $duplicate = GajiGuru::withTrashed()
            ->where('guru_id', $gaji->guru_id)
            ->where('bulan', $this->editBulan)
            ->where('tahun', $this->editTahun)
            ->where('id', '!=', $this->editingId)
            ->exists();

        if ($duplicate) {
            $this->addError('editBulan', "Gaji untuk pegawai ini pada periode {$this->editBulan} {$this->editTahun} sudah ada di sistem.");
            return;
        }

        if ($userRole === 'finance' && $gaji->status === 'dibayar') {
            $rules['edit_alasan'] = 'nullable|string|max:500';
        }

        $this->validate($rules);

        $this->calculateEditTotal();

        // Jika user adalah finance dan gaji sudah dibayar, ajukan persetujuan ke Super Admin / Super Admin 2
        if ($userRole === 'finance' && $gaji->status === 'dibayar') {
            $alasan = !empty(trim($this->edit_alasan)) ? trim($this->edit_alasan) : 'Penyesuaian nominal dan komponen rincian gaji pegawai oleh staf keuangan';

            try {
                FinancialApprovalService::createRequest(
                    auth()->user(),
                    'edit',
                    'gaji_guru',
                    $gaji,
                    [
                        'bulan' => $this->editBulan,
                        'tahun' => $this->editTahun,
                        'jabatan' => $this->editJabatan,
                        'jam_kerja' => $this->editJamKerja,
                        'sumber_dana' => $this->editSumberDana,
                        'gaji_pokok' => $this->editGajiPokok,
                        'gaji_berkala' => $this->editGajiBerkala,
                        'jumlah_ekskul' => $this->editJumlahEkskul,
                        'honor_ekskul' => $this->editHonorEkskul,
                        'insentif' => $this->editInsentif,
                        'insentif_bpjs' => $this->editInsentifBpjs,
                        'insentif_maghrib_mengaji' => $this->editInsentifMaghrib,
                        'potongan_sosial' => $this->editPotonganSosial,
                        'potongan_peminjaman' => $this->editPotonganPinjaman,
                        'potongan_bpjstk' => $this->editPotonganBpjstk,
                        'potongan_lainnya' => $this->editPotonganLainnya,
                        'total_bruto' => $this->editTotalBruto,
                        'total_diterima' => $this->editTotalDiterima,
                        'tanggal_bayar' => $this->editTanggalBayar ?: ($gaji->tanggal_bayar ? $gaji->tanggal_bayar->format('Y-m-d') : date('Y-m-d')),
                    ],
                    $alasan,
                    "Edit Gaji Guru: " . ($gaji->guru->user->nama ?? 'Guru') . " - {$this->editBulan} {$this->editTahun} (THP: Rp " . number_format($this->editTotalDiterima, 0, ',', '.') . ")"
                );

                $msg = 'Permohonan perubahan rincian gaji telah berhasil diajukan ke Super Admin / Super Admin 2 untuk disetujui.';
                session()->flash('message', $msg);
                $this->dispatch('notify', ['type' => 'success', 'message' => $msg]);
                $this->dispatch('modal-alert', ['type' => 'create', 'title' => 'Permohonan Diajukan', 'message' => $msg]);
                $this->closeEditModal();
                return;
            } catch (\Throwable $e) {
                Log::error('Gagal mengajukan persetujuan edit gaji: ' . $e->getMessage(), [
                    'exception' => $e,
                    'user_id' => auth()->id(),
                    'gaji_id' => $gaji->id,
                ]);
                $this->addError('edit_alasan', 'Gagal mengajukan persetujuan: ' . $e->getMessage());
                return;
            }
        }

        $oldPotonganPinjaman = floatval($gaji->potongan_peminjaman);

        try {
            DB::transaction(function () use ($gaji, $oldPotonganPinjaman) {
                $gaji->update([
                    'bulan' => $this->editBulan,
                    'tahun' => $this->editTahun,
                    'jabatan' => $this->editJabatan,
                    'jam_kerja' => $this->editJamKerja,
                    'sumber_dana' => $this->editSumberDana,
                    'gaji_pokok' => $this->editGajiPokok,
                    'gaji_berkala' => $this->editGajiBerkala,
                    'jumlah_ekskul' => $this->editJumlahEkskul,
                    'honor_ekskul' => $this->editHonorEkskul,
                    'insentif' => $this->editInsentif,
                    'insentif_bpjs' => $this->editInsentifBpjs,
                    'insentif_maghrib_mengaji' => $this->editInsentifMaghrib,
                    'potongan_sosial' => $this->editPotonganSosial,
                    'potongan_peminjaman' => $this->editPotonganPinjaman,
                    'potongan_bpjstk' => $this->editPotonganBpjstk,
                    'potongan_lainnya' => $this->editPotonganLainnya,
                    'total_bruto' => $this->editTotalBruto,
                    'total_diterima' => $this->editTotalDiterima,
                    'tanggal_bayar' => $this->editTanggalBayar ?: $gaji->tanggal_bayar,
                ]);

                if ($gaji->status === 'dibayar' && $gaji->pengeluaran_id) {
                    $pengeluaran = Pengeluaran::find($gaji->pengeluaran_id);
                    if ($pengeluaran) {
                        $pengeluaran->update([
                            'keterangan' => "Pembayaran Gaji " . ($gaji->guru->user->nama ?? 'Guru') . " ({$this->editBulan} {$this->editTahun})",
                            'jumlah' => $gaji->total_diterima,
                            'tanggal' => $this->editTanggalBayar ?: $pengeluaran->tanggal,
                        ]);
                    }

                    $diffLoan = $this->editPotonganPinjaman - $oldPotonganPinjaman;
                    if ($diffLoan != 0) {
                        $activeLoan = Peminjaman::where('guru_id', $gaji->guru_id)
                            ->where('status', 'berjalan')
                            ->first();

                        if ($activeLoan) {
                            $newSisa = max(0, $activeLoan->sisa_pinjaman - $diffLoan);
                            $status = $newSisa <= 0 ? 'lunas' : 'berjalan';
                            $activeLoan->update([
                                'sisa_pinjaman' => $newSisa,
                                'status' => $status
                            ]);
                        }
                    }
                }
            });
        } catch (\Illuminate\Database\UniqueConstraintViolationException | \Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), '1062 Duplicate entry')) {
                $this->addError('editBulan', "Gaji untuk pegawai ini pada periode {$this->editBulan} {$this->editTahun} sudah ada di sistem.");
                return;
            }
            throw $e;
        }

        session()->flash('message', 'Perubahan rincian gaji berhasil disimpan.');
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Perubahan rincian gaji berhasil disimpan.']);
        $this->dispatch('modal-alert', ['type' => 'create', 'title' => 'Berhasil', 'message' => 'Perubahan rincian gaji berhasil disimpan.']);
        $this->closeEditModal();
    }
}
