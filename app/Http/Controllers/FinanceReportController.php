<?php

namespace App\Http\Controllers;

use App\Models\GajiGuru;
use Barryvdh\DomPDF\Facade\Pdf;

class FinanceReportController extends Controller
{
    public function slipGaji(int $id)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $gaji = GajiGuru::with('guru.user')->findOrFail($id);
        $user = auth()->user();
        $userRole = $user->role->nama ?? '';

        // Izinkan Finance, Super Admin, Kepala Sekolah, atau Guru pemilik slip gaji ini
        $isOwnSlip = false;
        if ($userRole === 'guru' && $user->guru) {
            $isOwnSlip = ($user->guru->id === $gaji->guru_id);
        }

        if (!in_array($userRole, ['finance', 'super_admin', 'kepala_sekolah']) && !$isOwnSlip) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh slip gaji ini.');
        }

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-slip-gaji', [
            'gaji' => $gaji,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'slip_gaji_' . str_replace(' ', '_', strtolower($gaji->guru->user->nama ?? 'guru')) . '_' . strtolower($gaji->bulan) . '_' . $gaji->tahun . '.pdf');
    }

    public function cetakResi(int $id)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $pembayaran = \App\Models\Pembayaran::with(['tagihan.siswa.user', 'tagihan.jenisTagihan', 'petugas'])->findOrFail($id);
        $user = auth()->user();
        $userRole = $user->role->nama ?? '';

        // Izinkan Finance, Super Admin, TU, Kepsek, atau Murid/Wali pemilik tagihan ini
        $isOwnReceipt = false;
        if ($userRole === 'murid' && $user->siswa) {
            $isOwnReceipt = ($pembayaran->tagihan && $pembayaran->tagihan->siswa_id === $user->siswa->id);
        }

        if (!in_array($userRole, ['finance', 'super_admin', 'tata_usaha', 'kepala_sekolah']) && !$isOwnReceipt) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh resi ini.');
        }

        $staffFinance = \App\Models\User::whereHas('role', function ($q) {
            $q->where('nama', 'finance');
        })->first();

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-resi-pembayaran', [
            'pembayaran' => $pembayaran,
            'staffFinance' => $pembayaran->petugas ?? $staffFinance,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'resi_pembayaran_' . $pembayaran->id . '.pdf');
    }
}
