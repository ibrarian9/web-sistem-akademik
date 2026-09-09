<?php

namespace App\Http\Controllers;

use App\Models\NilaiTahfidz;
use App\Models\Rapor;
use App\Models\RaporDetail;
use App\Models\RaporTahfidzDetail;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\AuditLogger;

class RaporPdfController extends Controller
{
    /**
     * Check authorization and SPP lock for viewing a student's rapor.
     */
    protected function authorizeViewRapor(int $siswaId): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Silakan masuk ke akun terlebih dahulu.');
        }

        $userRole = $user->role->nama ?? '';

        // Murid / Wali can only access their own rapor and only when not blocked by SPP
        if ($userRole === 'murid') {
            if (!$user->siswa || (int) $user->siswa->id !== (int) $siswaId) {
                abort(403, 'Akses Ditolak: Anda hanya berhak mengakses dokumen rapor Anda sendiri.');
            }

            // Check for unpaid blocking SPP past due date
            $hasOutstanding = Tagihan::where('siswa_id', $user->siswa->id)
                ->whereIn('status', ['belum_bayar', 'sebagian'])
                ->whereHas('jenisTagihan', function ($q) {
                    $q->where('is_blocking', true);
                })
                ->whereDate('jatuh_tempo', '<=', Carbon::today())
                ->exists();

            if ($hasOutstanding) {
                abort(403, 'Akses Rapor Terkunci: Terdapat kewajiban tagihan SPP yang telah jatuh tempo. Harap selesaikan administrasi keuangan untuk melihat rapor.');
            }
        }
    }

    /**
     * Preview official PDF Rapor Utama inline in browser tab.
     */
    public function previewPdf($siswaId)
    {
        $this->authorizeViewRapor((int) $siswaId);

        $siswa = Siswa::with(['user', 'kelas'])->findOrFail($siswaId);

        $activeSem = DB::table('semester')
            ->join('tahun_ajaran', 'semester.tahun_ajaran_id', '=', 'tahun_ajaran.id')
            ->where('tahun_ajaran.status_aktif', true)
            ->where('semester.status_aktif', true)
            ->select('semester.id')
            ->first();

        $semesterId = $activeSem ? $activeSem->id : (Semester::latest()->first()->id ?? 1);

        $rapor = Rapor::where('siswa_id', $siswaId)
            ->where('semester_id', $semesterId)
            ->first();

        if (!$rapor) {
            abort(404, 'Rapor Hasil Belajar untuk semester ini belum resmi diterbitkan oleh wali kelas.');
        }

        if (empty($rapor->qr_code_hash)) {
            $rapor->qr_code_hash = 'RAP-' . $siswaId . '-' . Str::random(12);
            $rapor->save();
        }

        $raporDetails = RaporDetail::where('rapor_id', $rapor->id)->with('mapel')->get()->toArray();

        // Fetch Tahfizh Core Curriculum Data
        $tahfidzDetail = RaporTahfidzDetail::where('rapor_id', $rapor->id)->first();
        $nilaiTahfidzList = NilaiTahfidz::where('siswa_id', $siswaId)
            ->where('semester_id', $semesterId)
            ->get();

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-rapor-siswa', [
            'rapor' => $rapor,
            'raporDetails' => $raporDetails,
            'tahfidzDetail' => $tahfidzDetail,
            'nilaiTahfidzList' => $nilaiTahfidzList,
            'siswa' => $siswa,
        ]);

        $filename = 'rapor_' . str_replace(' ', '_', strtolower($siswa->user->nama ?? 'siswa')) . '.pdf';

        AuditLogger::log('download', "Mengunduh/melihat PDF Rapor Kurikulum Merdeka siswa: " . ($siswa->user->nama ?? 'Siswa'), $rapor, [
            'log_name' => 'akademik',
            'siswa_id' => $siswa->id,
            'properties' => [
                'rapor_id' => $rapor->id,
                'semester_id' => $semesterId,
                'kelas' => $siswa->kelas->nama_kelas ?? null,
            ],
        ]);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Preview official PDF Rapor Tahfizh inline in browser tab.
     */
    public function previewTahfidzPdf($siswaId)
    {
        $this->authorizeViewRapor((int) $siswaId);

        $siswa = Siswa::with(['user', 'kelas'])->findOrFail($siswaId);

        $activeSem = DB::table('semester')
            ->join('tahun_ajaran', 'semester.tahun_ajaran_id', '=', 'tahun_ajaran.id')
            ->where('tahun_ajaran.status_aktif', true)
            ->where('semester.status_aktif', true)
            ->select('semester.id')
            ->first();

        $semesterId = $activeSem ? $activeSem->id : (Semester::latest()->first()->id ?? 1);

        $rapor = Rapor::where('siswa_id', $siswaId)
            ->where('semester_id', $semesterId)
            ->first();

        if (!$rapor) {
            abort(404, 'Rapor Tahfizh untuk semester ini belum resmi diterbitkan oleh ustadz pengampu.');
        }

        $nilaiTahfidz = NilaiTahfidz::where('siswa_id', $siswaId)
            ->where('semester_id', $semesterId)
            ->first();

        $tahfidzDetail = RaporTahfidzDetail::where('rapor_id', $rapor->id)->first();

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-rapor-tahfidz', [
            'rapor' => $rapor,
            'nilaiTahfidz' => $nilaiTahfidz,
            'tahfidzDetail' => $tahfidzDetail,
            'siswa' => $siswa,
        ]);

        $filename = 'rapor_tahfidz_' . str_replace(' ', '_', strtolower($siswa->user->nama ?? 'santri')) . '.pdf';

        AuditLogger::log('download', "Mengunduh/melihat PDF Lembar Mutaba'ah & Rapor Tahfizh siswa: " . ($siswa->user->nama ?? 'Santri'), $rapor, [
            'log_name' => 'akademik',
            'siswa_id' => $siswa->id,
            'properties' => [
                'rapor_id' => $rapor->id,
                'semester_id' => $semesterId,
                'kelas' => $siswa->kelas->nama_kelas ?? null,
            ],
        ]);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
