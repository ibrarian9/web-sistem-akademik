<?php

namespace App\Http\Controllers;

use App\Models\GajiGuru;
use App\Models\Pembayaran;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FinanceReportController extends Controller
{
    public function slipGaji(Request $request, int $id)
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
            abort(403, 'Anda tidak memiliki akses untuk melihat slip gaji ini.');
        }

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-slip-gaji', [
            'gaji' => $gaji,
        ]);

        $filename = 'slip_gaji_' . str_replace(' ', '_', strtolower($gaji->guru->user->nama ?? 'guru')) . '_' . strtolower($gaji->bulan) . '_' . $gaji->tahun . '.pdf';

        if ($request->query('download') === '1' || request('download') === '1') {
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->stream();
            }, $filename, ['Content-Type' => 'application/pdf']);
        }

        // Default: Inline Preview (Browser PDF viewer)
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    public function cetakResi(Request $request, int $id)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $pembayaran = Pembayaran::with(['tagihan.siswa.user', 'tagihan.siswa.kelas', 'tagihan.jenisTagihan', 'petugas'])->findOrFail($id);
        $user = auth()->user();
        $userRole = $user->role->nama ?? '';

        // Izinkan Finance, Super Admin, TU, Kepsek, atau Murid/Wali pemilik tagihan ini
        $isOwnReceipt = false;
        if ($userRole === 'murid' && $user->siswa) {
            $isOwnReceipt = ($pembayaran->tagihan && $pembayaran->tagihan->siswa_id === $user->siswa->id);
        }

        if (!in_array($userRole, ['finance', 'super_admin', 'tata_usaha', 'kepala_sekolah']) && !$isOwnReceipt) {
            abort(403, 'Anda tidak memiliki akses untuk melihat resi ini.');
        }

        $staffFinance = User::whereHas('role', function ($q) {
            $q->where('nama', 'finance');
        })->first();

        // 1. If user explicitly requests direct PDF download
        if ($request->query('download') === '1' || request('download') === '1') {
            $pdf = Pdf::loadView('livewire.shared.laporan.pdf-resi-pembayaran', [
                'pembayaran' => $pembayaran,
                'staffFinance' => $pembayaran->petugas ?? $staffFinance,
            ]);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->stream();
            }, 'resi_pembayaran_' . $pembayaran->id . '.pdf', ['Content-Type' => 'application/pdf']);
        }

        // 2. If user requests inline raw PDF stream
        if ($request->query('format') === 'pdf' || request('format') === 'pdf') {
            $pdf = Pdf::loadView('livewire.shared.laporan.pdf-resi-pembayaran', [
                'pembayaran' => $pembayaran,
                'staffFinance' => $pembayaran->petugas ?? $staffFinance,
            ]);

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="resi_pembayaran_' . $pembayaran->id . '.pdf"'
            ]);
        }

        // 3. Default: Interactive Web Preview Page with Print & Download Toolbar
        return view('preview.resi-pembayaran', [
            'pembayaran' => $pembayaran,
            'staffFinance' => $pembayaran->petugas ?? $staffFinance,
        ]);
    }
}
