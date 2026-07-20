<?php

namespace App\Http\Controllers;

use App\Models\GajiGuru;
use Barryvdh\DomPDF\Facade\Pdf;

class FinanceReportController extends Controller
{
    public function slipGaji(int $id)
    {
        if (!auth()->check() || !in_array(auth()->user()->role->nama, ['finance', 'super_admin'])) {
            abort(403, 'Akses tidak sah.');
        }

        $gaji = GajiGuru::with('guru.user')->findOrFail($id);

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
