<?php

namespace App\Livewire\Koordinator;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PengajuanKoreksiNilai;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;

class ManajemenKoreksiNilai extends Component
{
    use WithPagination;

    public string $filterStatus = 'pending';

    public function approve($id)
    {
        $pengajuan = PengajuanKoreksiNilai::with(['nilai', 'guru.user'])->find($id);

        if (!$pengajuan || $pengajuan->status !== 'pending') {
            session()->flash('error', 'Pengajuan koreksi tidak valid atau telah diproses.');
            return;
        }

        DB::transaction(function () use ($pengajuan) {
            // 1. Update the original grade in 'nilai' table
            if ($pengajuan->nilai) {
                $pengajuan->nilai->update([
                    'nilai' => $pengajuan->nilai_baru,
                ]);

                // Auto-recalculate RaporDetail if Rapor header exists for this student & semester
                $rapor = \App\Models\Rapor::where('siswa_id', $pengajuan->nilai->siswa_id)
                    ->where('semester_id', $pengajuan->nilai->semester_id)
                    ->first();

                if ($rapor) {
                    $mapelId = $pengajuan->nilai->mapel_id;
                    $allNilais = \App\Models\Nilai::where('siswa_id', $pengajuan->nilai->siswa_id)
                        ->where('semester_id', $pengajuan->nilai->semester_id)
                        ->where('mapel_id', $mapelId)
                        ->get();

                    $components = \App\Models\KomponenNilai::all();
                    $componentsByCategory = $components->groupBy('kategori');

                    $categoryScores = [];
                    foreach (['pengetahuan', 'keterampilan', 'sikap', 'keagamaan'] as $cat) {
                        $catComps = $componentsByCategory->get($cat, collect());
                        $catGrades = [];
                        foreach ($catComps as $comp) {
                            $compNilais = $allNilais->where('komponen_nilai_id', $comp->id);
                            if ($compNilais->count() > 0) {
                                $catGrades[] = $compNilais->avg('nilai');
                            }
                        }
                        $categoryScores[$cat] = count($catGrades) > 0 ? round(array_sum($catGrades) / count($catGrades), 2) : null;
                    }

                    $finalGrade = 0.00;
                    $totalWeight = 0.00;
                    foreach ($components as $comp) {
                        $compNilais = $allNilais->where('komponen_nilai_id', $comp->id);
                        if ($compNilais->count() > 0) {
                            $avg = $compNilais->avg('nilai');
                            $finalGrade += $avg * ($comp->bobot / 100);
                            $totalWeight += $comp->bobot;
                        }
                    }

                    $nilaiAkhir = $totalWeight > 0 ? round($finalGrade / ($totalWeight / 100), 2) : 0.00;

                    $predikat = 'E';
                    if ($nilaiAkhir >= 90) $predikat = 'A';
                    elseif ($nilaiAkhir >= 80) $predikat = 'B';
                    elseif ($nilaiAkhir >= 70) $predikat = 'C';
                    elseif ($nilaiAkhir >= 60) $predikat = 'D';

                    \App\Models\RaporDetail::updateOrCreate([
                        'rapor_id' => $rapor->id,
                        'mapel_id' => $mapelId,
                    ], [
                        'nilai_pengetahuan' => $categoryScores['pengetahuan'],
                        'nilai_keterampilan' => $categoryScores['keterampilan'],
                        'nilai_sikap' => $categoryScores['sikap'],
                        'nilai_keagamaan' => $categoryScores['keagamaan'],
                        'nilai_akhir' => $nilaiAkhir,
                        'predikat' => $predikat,
                    ]);
                }
            }

            // 2. Update status of the request
            $pengajuan->update([
                'status' => 'disetujui',
                'disetujui_oleh_user_id' => auth()->id(),
            ]);

            // 3. Send notification to teacher
            if ($pengajuan->guru && $pengajuan->guru->user_id) {
                Notifikasi::create([
                    'user_id' => $pengajuan->guru->user_id,
                    'judul' => 'Pengajuan Koreksi Nilai Disetujui',
                    'isi_pesan' => "Pengajuan koreksi nilai Anda untuk siswa telah disetujui oleh Koordinator.",
                    'jenis' => 'info',
                    'status_kirim' => 'terkirim',
                ]);
            }
        });

        session()->flash('message', 'Pengajuan koreksi nilai berhasil disetujui dan nilai telah diperbarui.');
    }

    public function reject($id)
    {
        $pengajuan = PengajuanKoreksiNilai::with(['guru.user'])->find($id);

        if (!$pengajuan || $pengajuan->status !== 'pending') {
            session()->flash('error', 'Pengajuan koreksi tidak valid atau telah diproses.');
            return;
        }

        $pengajuan->update([
            'status' => 'ditolak',
            'disetujui_oleh_user_id' => auth()->id(),
        ]);

        if ($pengajuan->guru && $pengajuan->guru->user_id) {
            Notifikasi::create([
                'user_id' => $pengajuan->guru->user_id,
                'judul' => 'Pengajuan Koreksi Nilai Ditolak',
                'isi_pesan' => "Pengajuan koreksi nilai Anda ditolak oleh Koordinator.",
                'jenis' => 'peringatan',
                'status_kirim' => 'terkirim',
            ]);
        }

        session()->flash('message', 'Pengajuan koreksi nilai telah ditolak.');
    }

    public function render()
    {
        $query = PengajuanKoreksiNilai::with([
            'nilai.siswa.user',
            'nilai.mapel',
            'nilai.komponenNilai',
            'guru.user',
            'reviewer'
        ])->orderBy('created_at', 'desc');

        if ($this->filterStatus !== 'semua') {
            $query->where('status', $this->filterStatus);
        }

        $pengajuans = $query->paginate(10);

        return view('livewire.koordinator.manajemen-koreksi-nilai', [
            'pengajuans' => $pengajuans,
        ])->layout('components.layouts.app', ['title' => 'Verifikasi Koreksi Nilai']);
    }
}
