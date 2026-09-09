<?php

namespace App\Http\Controllers;

use App\Models\CatatanPendampingan;
use App\Models\Pengaturan;
use App\Models\Semester;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Services\AuditLogger;

class PendampinganReportController extends Controller
{
    /**
     * Preview or download official PDF Laporan Pendampingan Siswa Berkebutuhan Khusus.
     */
    public function cetakLaporan(Request $request, int $siswaId)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Silakan masuk ke akun terlebih dahulu.');
        }

        $userRole = $user->role->nama ?? '';

        // Murid only allowed to access their own report
        if ($userRole === 'murid') {
            if (!$user->siswa || (int) $user->siswa->id !== $siswaId) {
                abort(403, 'Akses Ditolak: Anda hanya berhak mengakses dokumen pendampingan Anda sendiri.');
            }
        }

        // Guru Pendamping only allowed to access their assigned student's report
        if ($user->guru && $user->guru->isGuruPendamping()) {
            $isAssigned = Siswa::where('id', $siswaId)
                ->where('shadow_teacher_id', $user->guru->id)
                ->exists();
            if (!$isAssigned) {
                abort(403, 'Akses Ditolak: Anda hanya berhak mengakses dokumen pendampingan untuk siswa bimbingan Anda.');
            }
        }

        $siswa = Siswa::with([
            'user',
            'kelas.guruUmum.user',
            'kelasTahfidz.guruTahfidz.user',
            'shadowTeacher.user'
        ])->findOrFail($siswaId);

        $periode = $request->query('periode', 'tengah_semester');
        if (!in_array($periode, ['tengah_semester', 'akhir_semester', 'semua'])) {
            $periode = 'tengah_semester';
        }

        $activeSemester = Semester::with('tahunAjaran')->where('status_aktif', true)->first()
            ?? Semester::with('tahunAjaran')->latest()->first();

        // Get observation records for this student and period
        $query = CatatanPendampingan::with(['guru.user', 'semester'])
            ->where('siswa_id', $siswaId);

        if ($periode !== 'semua') {
            $query->where('periode', $periode);
        }

        $catatanList = $query->orderBy('tanggal', 'asc')->get();

        // Group observations by aspect
        $aspekList = CatatanPendampingan::ASPEK_LIST;
        $rekapAspek = [];

        foreach ($aspekList as $aspek) {
            $items = $catatanList->where('aspek', $aspek);
            $latest = $items->last();

            $rekapAspek[$aspek] = [
                'nama' => $aspek,
                'capaian_terakhir' => $latest ? $latest->hasil_perkembangan : '-',
                'capaian_label' => $latest ? $latest->hasil_perkembangan_label : 'Belum Diobservasi',
                'badge' => $latest ? $latest->hasil_perkembangan_badge : 'stone',
                'catatan' => $latest ? $latest->catatan : '-',
                'rekomendasi' => $latest ? $latest->rekomendasi : '-',
                'total_observasi' => $items->count(),
                'riwayat' => $items,
            ];
        }

        // Consolidated recommendations
        $rekomendasiUmum = $catatanList->whereNotNull('rekomendasi')->pluck('rekomendasi')->filter()->unique()->values();

        // Determine Guru Pendamping name
        $namaGuruPendamping = $siswa->shadowTeacher?->user?->nama
            ?? ($catatanList->last()?->guru?->user?->nama ?? 'Guru Pendamping Khusus');

        $namaSekolah = Pengaturan::getValue('nama_sekolah', 'PONDOK PESANTREN & SEKOLAH ISLAM TERPADU');
        $alamatSekolah = Pengaturan::getValue('alamat_sekolah', 'Jl. Pendidikan Karakter Islami, Pekanbaru');
        $kepalaSekolah = Pengaturan::getValue('nama_kepala_sekolah', 'Ustadz Pembina, M.Pd');

        $periodeTitle = match ($periode) {
            'tengah_semester' => 'Tengah Semester (PTS)',
            'akhir_semester' => 'Akhir Semester (PAS / PAT)',
            default => 'Laporan Lengkap Seluruh Periode',
        };

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-laporan-pendampingan', [
            'siswa' => $siswa,
            'catatanList' => $catatanList,
            'rekapAspek' => $rekapAspek,
            'rekomendasiUmum' => $rekomendasiUmum,
            'periode' => $periode,
            'periodeTitle' => $periodeTitle,
            'namaGuruPendamping' => $namaGuruPendamping,
            'namaSekolah' => $namaSekolah,
            'alamatSekolah' => $alamatSekolah,
            'kepalaSekolah' => $kepalaSekolah,
            'activeSemester' => $activeSemester,
            'tanggalCetak' => now()->translatedFormat('d F Y'),
        ])->setPaper('a4', 'portrait');

        $cleanSiswaName = str_replace(' ', '_', strtolower($siswa->user->nama ?? 'siswa'));
        $filename = "laporan_pendampingan_{$cleanSiswaName}_{$periode}.pdf";

        AuditLogger::log('download', "Mengunduh/mencetak PDF Laporan Pendampingan Siswa: " . ($siswa->user->nama ?? 'Siswa'), $siswa, [
            'log_name' => 'pendampingan',
            'siswa_id' => $siswa->id,
            'properties' => [
                'periode' => $periode,
                'kelas' => $siswa->kelas->nama_kelas ?? null,
                'guru_pendamping' => $namaGuruPendamping,
            ],
        ]);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
