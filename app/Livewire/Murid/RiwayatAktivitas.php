<?php

namespace App\Livewire\Murid;

use Livewire\Component;
use Livewire\WithPagination;

class RiwayatAktivitas extends Component
{
    use WithPagination;

    public function getActivityLogsProperty()
    {
        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            return collect();
        }

        if (!class_exists(\Spatie\Activitylog\Models\Activity::class)) {
            return collect();
        }

        $activities = \Spatie\Activitylog\Models\Activity::where('siswa_id', $siswa->id)
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        return $activities->map(function ($log) {
            $subjectType = str_replace('App\\Models\\', '', $log->subject_type ?? '');
            
            $type = 'sistem';
            $title = 'Aktivitas Sistem';

            switch ($subjectType) {
                case 'Nilai':
                    $type = 'nilai';
                    $title = 'Pembaruan Nilai Siswa';
                    break;
                case 'AbsensiSiswa':
                    $type = 'kehadiran';
                    $title = 'Pencatatan Presensi Kehadiran';
                    break;
                case 'Pembayaran':
                case 'Tagihan':
                case 'Tabungan':
                    $type = 'keuangan';
                    $title = 'Transaksi Keuangan';
                    break;
                default:
                    $type = 'sistem';
                    $title = ucfirst($log->description ?: 'Log Sistem');
                    break;
            }

            return [
                'type' => $type,
                'title' => $title,
                'time' => $log->created_at ? $log->created_at->isoFormat('D MMMM Y, HH:mm') : '-',
                'description' => $log->description ?: 'Pencatatan aktivitas sistem.',
                'actor' => $log->causer ? $log->causer->nama : 'Sistem Otomatis',
            ];
        });
    }

    public function render()
    {
        return view('livewire.murid.riwayat-aktivitas', [
            'activityLogs' => $this->activityLogs,
        ])->layout('components.layouts.app', ['title' => 'Riwayat Aktivitas']);
    }
}

