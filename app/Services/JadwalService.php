<?php

namespace App\Services;

use App\Models\JadwalPelajaran;
use App\Models\GuruMapelKelas;

class JadwalService
{
    /**
     * Check if a proposed schedule clashes with existing schedules.
     *
     * @param int $guruMapelKelasId
     * @param string $hari
     * @param string $jamMulai  Format: 'H:i' or 'H:i:s'
     * @param string $jamSelesai Format: 'H:i' or 'H:i:s'
     * @param int|null $excludeJadwalId Exclude a specific schedule ID (for edits)
     * @return array|null Returns array of details if conflict exists, null otherwise.
     */
    public function checkConflict(int $guruMapelKelasId, string $hari, string $jamMulai, string $jamSelesai, ?int $excludeJadwalId = null): ?array
    {
        $gmk = GuruMapelKelas::findOrFail($guruMapelKelasId);
        $semesterId = $gmk->semester_id;
        $guruId = $gmk->guru_id;
        $kelasId = $gmk->kelas_id;

        // Query all schedules in the same semester on the same day
        $schedules = JadwalPelajaran::where('hari', strtolower($hari))
            ->whereHas('guruMapelKelas', function ($query) use ($semesterId) {
                $query->where('semester_id', $semesterId);
            })
            ->when($excludeJadwalId, function ($query) use ($excludeJadwalId) {
                $query->where('id', '!=', $excludeJadwalId);
            })
            ->with(['guruMapelKelas.guru.user', 'guruMapelKelas.kelas', 'guruMapelKelas.mapel'])
            ->get();

        $tMulai = strtotime($jamMulai);
        $tSelesai = strtotime($jamSelesai);

        foreach ($schedules as $sched) {
            $sMulai = strtotime($sched->jam_mulai);
            $sSelesai = strtotime($sched->jam_selesai);

            // Overlap detection: (StartA < EndB) AND (EndA > StartB)
            if ($tMulai < $sSelesai && $tSelesai > $sMulai) {
                // Conflict 1: Class conflict (same class cannot have 2 classes at once)
                if ($sched->guruMapelKelas->kelas_id === $kelasId) {
                    return [
                        'type' => 'kelas',
                        'message' => sprintf(
                            'Bentrok Kelas: Kelas %s sudah memiliki jadwal pelajaran %s dengan Guru %s pada pukul %s - %s.',
                            $sched->guruMapelKelas->kelas->nama_kelas,
                            $sched->guruMapelKelas->mapel->nama_mapel,
                            $sched->guruMapelKelas->guru->user->nama,
                            date('H:i', $sMulai),
                            date('H:i', $sSelesai)
                        )
                    ];
                }

                // Conflict 2: Teacher conflict (same teacher cannot teach 2 classes at once)
                if ($sched->guruMapelKelas->guru_id === $guruId) {
                    return [
                        'type' => 'guru',
                        'message' => sprintf(
                            'Bentrok Guru: Guru %s sudah memiliki jadwal mengajar di Kelas %s (%s) pada pukul %s - %s.',
                            $sched->guruMapelKelas->guru->user->nama,
                            $sched->guruMapelKelas->kelas->nama_kelas,
                            $sched->guruMapelKelas->mapel->nama_mapel,
                            date('H:i', $sMulai),
                            date('H:i', $sSelesai)
                        )
                    ];
                }
            }
        }

        return null;
    }
}
