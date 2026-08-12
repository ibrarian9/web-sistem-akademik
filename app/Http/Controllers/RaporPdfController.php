<?php

namespace App\Http\Controllers;

use App\Models\NilaiTahfidz;
use App\Models\Rapor;
use App\Models\RaporDetail;
use App\Models\RaporTahfidzDetail;
use App\Models\Semester;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RaporPdfController extends Controller
{
    /**
     * Preview official PDF Rapor Utama inline in browser tab.
     */
    public function previewPdf($siswaId)
    {
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
            $rapor = Rapor::create([
                'siswa_id' => $siswaId,
                'semester_id' => $semesterId,
                'kelas_id' => $siswa->kelas_id,
                'tanggal_terbit' => date('Y-m-d'),
                'catatan_wali_kelas' => 'Ananda telah menunjukkan perkembangan yang sangat memuaskan baik dalam bidang akademis maupun hafalan Al-Qur\'an.',
                'qr_code_hash' => 'RAP-' . $siswaId . '-' . Str::random(12),
            ]);
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
            $rapor = Rapor::create([
                'siswa_id' => $siswaId,
                'semester_id' => $semesterId,
                'kelas_id' => $siswa->kelas_id,
                'tanggal_terbit' => date('Y-m-d'),
                'catatan_wali_kelas' => 'Alhamdulillah perkembangan hafalan santri sangat baik, makhraj fasih, dan tajwid lancar.',
                'qr_code_hash' => 'RAP-TAH-' . $siswaId . '-' . Str::random(12),
            ]);
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

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
