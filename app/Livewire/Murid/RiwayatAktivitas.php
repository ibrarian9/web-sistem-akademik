<?php

namespace App\Livewire\Murid;

use Livewire\Component;
use Livewire\WithPagination;

class RiwayatAktivitas extends Component
{
    use WithPagination;

    public function getActivities()
    {
        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            return collect();
        }

        if (!class_exists(\Spatie\Activitylog\Models\Activity::class)) {
            return collect();
        }

        return \Spatie\Activitylog\Models\Activity::where('siswa_id', $siswa->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function formatLog($log)
    {
        $subjectType = str_replace('App\\Models\\', '', $log->subject_type ?? '');
        $event = $log->event ?? '';

        switch ($subjectType) {
            case 'Nilai':
                return [
                    'icon' => 'edit-3',
                    'color' => 'bg-green-50 text-green-700 border-green-100',
                    'text' => 'Nilai baru Anda untuk mata pelajaran telah diunggah/diperbarui.',
                ];
            case 'AbsensiSiswa':
                return [
                    'icon' => 'calendar',
                    'color' => 'bg-amber-50 text-amber-700 border-amber-100',
                    'text' => 'Status absensi/kehadiran harian Anda telah diinput oleh guru.',
                ];
            case 'Pembayaran':
                return [
                    'icon' => 'credit-card',
                    'color' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                    'text' => 'Pembayaran tagihan Anda telah dicatat oleh bagian Keuangan.',
                ];
            case 'Tagihan':
                return [
                    'icon' => 'file-text',
                    'color' => 'bg-red-50 text-red-700 border-red-100',
                    'text' => 'Tagihan baru telah diterbitkan untuk tahun ajaran aktif.',
                ];
            case 'Rapor':
                return [
                    'icon' => 'award',
                    'color' => 'bg-purple-50 text-purple-700 border-purple-100',
                    'text' => 'Rapor nilai semester Anda telah diterbitkan.',
                ];
            case 'SiswaKelas':
                return [
                    'icon' => 'layers',
                    'color' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                    'text' => 'Terjadi perubahan atau penempatan kelas akademik Anda.',
                ];
            default:
                return [
                    'icon' => 'activity',
                    'color' => 'bg-stone-50 text-stone-700 border-stone-200',
                    'text' => ucfirst($log->description) ?: 'Aktivitas sistem.',
                ];
        }
    }

    public function render()
    {
        return view('livewire.murid.riwayat-aktivitas', [
            'activities' => $this->getActivities(),
        ])->layout('components.layouts.app', ['title' => 'Riwayat Aktivitas']);
    }
}
