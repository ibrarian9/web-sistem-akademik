<?php

namespace App\Livewire\Finance\Concerns;

use App\Models\GajiGuru;
use App\Actions\Finance\DisburseGajiAction;
use Illuminate\Support\Facades\Storage;

trait HasSalaryPayment
{
    public bool $showPayModal = false;
    public ?int $paySalaryId = null;
    public ?GajiGuru $paySalaryRecord = null;
    public string $payTanggalBayar = '';
    public ?string $payCatatan = '';
    public $payBuktiFoto = null;
    public ?string $payExistingBukti = null;

    public $detailBuktiFoto = null;

    public function openPayModal(int $id): void
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->paySalaryRecord = GajiGuru::with(['guru.user', 'pengeluaran'])->findOrFail($id);

        if ($this->paySalaryRecord->status === 'dibayar') {
            session()->flash('error', 'Gaji ini sudah dibayarkan.');
            return;
        }

        $this->paySalaryId = $id;
        $this->payTanggalBayar = now()->toDateString();
        $this->payCatatan = '';
        $this->payBuktiFoto = null;
        $this->payExistingBukti = $this->paySalaryRecord->bukti_bayar;
        $this->showPayModal = true;
    }

    public function closePayModal(): void
    {
        $this->showPayModal = false;
        $this->paySalaryId = null;
        $this->paySalaryRecord = null;
        $this->payBuktiFoto = null;
        $this->payCatatan = '';
        $this->payExistingBukti = null;
    }

    public function confirmPaySalary(): void
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->validate([
            'payTanggalBayar' => 'required|date',
            'payBuktiFoto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'payCatatan' => 'nullable|string|max:500',
        ], [
            'payBuktiFoto.image' => 'File bukti transfer/struk harus berupa gambar/foto.',
            'payBuktiFoto.mimes' => 'Format foto bukti hanya boleh JPG, JPEG, PNG, atau WEBP.',
            'payBuktiFoto.max' => 'Ukuran foto bukti transfer/struk maksimal 2MB.',
        ]);

        $gaji = GajiGuru::with('guru.user')->findOrFail($this->paySalaryId);

        if ($gaji->status === 'dibayar') {
            session()->flash('error', 'Gaji ini sudah dibayarkan.');
            $this->closePayModal();
            return;
        }

        $pathBukti = null;
        if ($this->payBuktiFoto) {
            $pathBukti = $this->payBuktiFoto->store('bukti-gaji', 'public');
        }

        $this->executePaySalary($gaji, $pathBukti, $this->payTanggalBayar, $this->payCatatan);

        $this->closePayModal();
        session()->flash('message', 'Pembayaran gaji berhasil diproses' . ($pathBukti ? ' beserta unggahan bukti foto' : '') . ' dan dicatat ke kas pengeluaran.');
    }

    public function paySalary(int $id): void
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $gaji = GajiGuru::with('guru.user')->findOrFail($id);

        if ($gaji->status === 'dibayar') {
            session()->flash('error', 'Gaji ini sudah dibayarkan.');
            return;
        }

        $this->executePaySalary($gaji);
        session()->flash('message', 'Pembayaran gaji berhasil diproses dan dicatat ke kas pengeluaran.');
    }

    protected function executePaySalary(GajiGuru $gaji, ?string $pathBukti = null, ?string $tanggalBayar = null, ?string $catatan = null): void
    {
        app(DisburseGajiAction::class)->execute($gaji, $pathBukti, $tanggalBayar, $catatan, auth()->id());
    }

    public function updateSalaryBuktiFoto(int $salaryId): void
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->validate([
            'detailBuktiFoto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'detailBuktiFoto.required' => 'Silakan pilih file foto bukti terlebih dahulu.',
            'detailBuktiFoto.image' => 'File bukti harus berupa gambar/foto.',
            'detailBuktiFoto.mimes' => 'Format foto hanya boleh JPG, JPEG, PNG, atau WEBP.',
            'detailBuktiFoto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $salary = GajiGuru::with('pengeluaran')->findOrFail($salaryId);

        if ($salary->bukti_bayar && Storage::disk('public')->exists($salary->bukti_bayar)) {
            Storage::disk('public')->delete($salary->bukti_bayar);
        }

        $path = $this->detailBuktiFoto->store('bukti-gaji', 'public');

        $salary->update(['bukti_bayar' => $path]);
        if ($salary->pengeluaran) {
            $salary->pengeluaran->update(['bukti' => $path]);
        }

        $this->detailBuktiFoto = null;
        $this->selectedSalaryDetail = $salary->fresh(['guru.user', 'pengeluaran']);
        session()->flash('message', 'Foto bukti transfer/struk pembayaran berhasil diperbarui.');
    }

    public function deleteSalaryBuktiFoto(int $salaryId): void
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $salary = GajiGuru::with('pengeluaran')->findOrFail($salaryId);

        if ($salary->bukti_bayar && Storage::disk('public')->exists($salary->bukti_bayar)) {
            Storage::disk('public')->delete($salary->bukti_bayar);
        }

        $salary->update(['bukti_bayar' => null]);
        if ($salary->pengeluaran) {
            $salary->pengeluaran->update(['bukti' => null]);
        }

        $this->detailBuktiFoto = null;
        $this->selectedSalaryDetail = $salary->fresh(['guru.user', 'pengeluaran']);
        session()->flash('message', 'Foto bukti transfer/struk berhasil dihapus.');
    }
}
