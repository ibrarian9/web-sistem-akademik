<?php

namespace App\Livewire\Finance\Concerns;

use App\Models\GajiGuru;
use App\Models\Peminjaman;
use App\Models\Pengeluaran;
use App\Services\FinancialApprovalService;
use Illuminate\Support\Facades\DB;

trait HasSalaryDeletion
{
    public function revertToDraft(int $id): void
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        if (auth()->user()->role?->nama === 'finance') {
            session()->flash('error', 'Akses Ditolak: Pembatalan status gaji yang telah dibayarkan hanya dapat dilakukan oleh Super Admin.');
            return;
        }

        $gaji = GajiGuru::findOrFail($id);

        if ($gaji->status !== 'dibayar') {
            session()->flash('error', 'Hanya gaji yang sudah dibayar yang dapat dibatalkan.');
            return;
        }

        DB::transaction(function () use ($gaji) {
            if ($gaji->pengeluaran_id) {
                $pengeluaran = Pengeluaran::find($gaji->pengeluaran_id);
                if ($pengeluaran) {
                    $pengeluaran->delete();
                }
            }

            if ($gaji->potongan_peminjaman > 0) {
                $loan = Peminjaman::where('guru_id', $gaji->guru_id)->first();
                if ($loan) {
                    $loan->update([
                        'sisa_pinjaman' => $loan->sisa_pinjaman + $gaji->potongan_peminjaman,
                        'status' => 'berjalan'
                    ]);
                }
            }

            $gaji->update([
                'status' => 'draft',
                'pengeluaran_id' => null,
            ]);
        });

        session()->flash('message', 'Status gaji berhasil dikembalikan ke Draf dan catatan kas pengeluaran telah disesuaikan.');
    }

    public function deleteSalary(int $id, ?string $alasan = null): void
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $gaji = GajiGuru::with('guru.user')->findOrFail($id);
        $userRole = auth()->user()->role->nama ?? '';

        if ($userRole === 'finance' && $gaji->status === 'dibayar') {
            $reason = $alasan ?: 'Penghapusan data gaji diajukan oleh staf keuangan';
            FinancialApprovalService::createRequest(
                auth()->user(),
                'hapus',
                'gaji_guru',
                $gaji,
                null,
                $reason,
                "Hapus Gaji Guru: " . ($gaji->guru->user->nama ?? 'Guru') . " - {$gaji->bulan} {$gaji->tahun} (THP: Rp " . number_format($gaji->total_diterima, 0, ',', '.') . ")"
            );

            $msg = 'Permohonan penghapusan data gaji telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.';
            session()->flash('message', $msg);
            $this->dispatch('notify', ['type' => 'success', 'message' => $msg]);
            $this->dispatch('modal-alert', ['type' => 'create', 'title' => 'Permohonan Diajukan', 'message' => $msg]);
            return;
        }

        DB::transaction(function () use ($gaji) {
            if ($gaji->status === 'dibayar') {
                if ($gaji->pengeluaran_id) {
                    $pengeluaran = Pengeluaran::find($gaji->pengeluaran_id);
                    if ($pengeluaran) {
                        $pengeluaran->delete();
                    }
                }

                if ($gaji->potongan_peminjaman > 0) {
                    $loan = Peminjaman::where('guru_id', $gaji->guru_id)->first();
                    if ($loan) {
                        $loan->update([
                            'sisa_pinjaman' => $loan->sisa_pinjaman + $gaji->potongan_peminjaman,
                            'status' => 'berjalan'
                        ]);
                    }
                }
            }

            if ($gaji->status === 'draft') {
                $gaji->forceDelete();
            } else {
                $gaji->delete();
            }
        });

        $msg = 'Data gaji berhasil dihapus.';
        session()->flash('message', $msg);
        $this->dispatch('notify', ['type' => 'success', 'message' => $msg]);
        $this->dispatch('modal-alert', ['type' => 'delete', 'title' => 'Data Dihapus', 'message' => $msg]);
    }

    public function deleteDraft(int $id): void
    {
        $this->deleteSalary($id);
    }

    public function deleteSelected(): void
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.']);
            return;
        }

        if (empty($this->selectedGajiIds)) {
            session()->flash('error', 'Silakan pilih data gaji yang ingin dihapus terlebih dahulu.');
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Silakan pilih data gaji yang ingin dihapus terlebih dahulu.']);
            return;
        }

        $salaries = GajiGuru::with('guru.user')->whereIn('id', $this->selectedGajiIds)->get();
        $count = $salaries->count();
        $userRole = auth()->user()->role->nama ?? '';

        if ($userRole === 'finance') {
            $submittedCount = 0;
            $deletedDraftCount = 0;

            DB::transaction(function () use ($salaries, &$submittedCount, &$deletedDraftCount) {
                foreach ($salaries as $gaji) {
                    if ($gaji->status === 'dibayar') {
                        FinancialApprovalService::createRequest(
                            auth()->user(),
                            'hapus',
                            'gaji_guru',
                            $gaji,
                            null,
                            'Penghapusan batch diajukan oleh staf keuangan',
                            "Hapus Gaji Guru: " . ($gaji->guru->user->nama ?? 'Guru') . " - {$gaji->bulan} {$gaji->tahun} (THP: Rp " . number_format($gaji->total_diterima, 0, ',', '.') . ")"
                        );
                        $submittedCount++;
                    } else {
                        if ($gaji->status === 'draft') {
                            $gaji->forceDelete();
                        } else {
                            $gaji->delete();
                        }
                        $deletedDraftCount++;
                    }
                }
            });

            $this->selectedGajiIds = [];
            $this->selectAll = false;

            $msg = [];
            if ($deletedDraftCount > 0) {
                $msg[] = "{$deletedDraftCount} data draf gaji berhasil dihapus.";
            }
            if ($submittedCount > 0) {
                $msg[] = "{$submittedCount} permohonan hapus gaji berstatus dibayar diajukan ke Super Admin untuk disetujui.";
            }

            $messageText = implode(' ', $msg) ?: 'Aksi batch selesai.';
            session()->flash('message', $messageText);
            $this->dispatch('notify', ['type' => 'success', 'message' => $messageText]);
            $this->dispatch('modal-alert', ['type' => 'create', 'title' => 'Permohonan Berhasil Diajukan', 'message' => $messageText]);
            return;
        }

        DB::transaction(function () use ($salaries) {
            foreach ($salaries as $gaji) {
                if ($gaji->status === 'dibayar') {
                    if ($gaji->pengeluaran_id) {
                        $pengeluaran = Pengeluaran::find($gaji->pengeluaran_id);
                        if ($pengeluaran) {
                            $pengeluaran->delete();
                        }
                    }

                    if ($gaji->potongan_peminjaman > 0) {
                        $loan = Peminjaman::where('guru_id', $gaji->guru_id)->first();
                        if ($loan) {
                            $loan->update([
                                'sisa_pinjaman' => $loan->sisa_pinjaman + $gaji->potongan_peminjaman,
                                'status' => 'berjalan'
                            ]);
                        }
                    }
                }

                if ($gaji->status === 'draft') {
                    $gaji->forceDelete();
                } else {
                    $gaji->delete();
                }
            }
        });

        $this->selectedGajiIds = [];
        $this->selectAll = false;
        $messageText = "Berhasil menghapus {$count} data gaji terpilih.";
        session()->flash('message', $messageText);
        $this->dispatch('notify', ['type' => 'success', 'message' => $messageText]);
        $this->dispatch('modal-alert', ['type' => 'delete', 'title' => 'Data Berhasil Dihapus', 'message' => $messageText]);
    }
}
