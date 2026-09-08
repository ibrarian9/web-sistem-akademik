<?php

namespace App\Livewire\Murid;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\AbsensiSiswa;
use App\Models\Tagihan;
use App\Models\JadwalPelajaran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Component
{
    public float $avgGrade = 0.00;
    public float $attendanceRate = 0.00;
    public int $pendingInvoicesCount = 0;
    public array $todaySchedule = [];
    public bool $hasOutstanding = false;
    public array $activityLogs = [];

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $user = auth()->user();
        $siswa = $user->siswa;
        if (!$siswa) {
            return;
        }

        // Get active semester
        $activeSemester = DB::table('semester')
            ->join('tahun_ajaran', 'semester.tahun_ajaran_id', '=', 'tahun_ajaran.id')
            ->where('tahun_ajaran.status_aktif', true)
            ->where('semester.status_aktif', true)
            ->select('semester.id')
            ->first();

        if (!$activeSemester) {
            return;
        }

        // Calculate average grade
        $grades = Nilai::where('siswa_id', $siswa->id)
            ->where('semester_id', $activeSemester->id)
            ->pluck('nilai');
        
        if ($grades->count() > 0) {
            $this->avgGrade = round($grades->average(), 2);
        }

        // Calculate attendance rate
        $absensi = AbsensiSiswa::where('siswa_id', $siswa->id)
            ->where('kelas_id', $siswa->kelas_id)
            ->get();
        
        if ($absensi->count() > 0) {
            $hadir = $absensi->where('status', 'hadir')->count();
            $this->attendanceRate = round(($hadir / $absensi->count()) * 100, 2);
        } else {
            $this->attendanceRate = 100.00; // Default if no record yet
        }

        // Pending invoices count
        $pendingInvoices = Tagihan::where('siswa_id', $siswa->id)
            ->whereIn('status', ['belum_bayar', 'sebagian'])
            ->with('jenisTagihan')
            ->get();

        $this->pendingInvoicesCount = $pendingInvoices->count();
        $this->hasOutstanding = $pendingInvoices->contains(fn($t) => 
            ($t->jenisTagihan->is_blocking ?? false) && 
            Carbon::parse($t->jatuh_tempo)->startOfDay()->lte(Carbon::today())
        );

        // Today's schedule
        $todayName = strtolower(Carbon::now()->locale('id')->dayName);
        // Fallback for weekend/testing
        if (!in_array($todayName, ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'])) {
            $todayName = 'senin';
        }

        $this->todaySchedule = JadwalPelajaran::where('hari', $todayName)
            ->whereHas('guruMapelKelas', function ($q) use ($siswa, $activeSemester) {
                $q->where('kelas_id', $siswa->kelas_id)
                  ->where('semester_id', $activeSemester->id);
            })
            ->with(['guruMapelKelas.mapel', 'guruMapelKelas.guru.user'])
            ->orderBy('jam_mulai')
            ->get()
            ->map(fn($item) => [
                'jam' => date('H:i', strtotime($item->jam_mulai)) . ' - ' . date('H:i', strtotime($item->jam_selesai)),
                'mapel' => $item->guruMapelKelas->mapel->nama_mapel ?? '-',
                'guru' => $item->guruMapelKelas->guru->user->nama ?? '-',
            ])
            ->toArray();

        // Load activity logs
        if (class_exists(\Spatie\Activitylog\Models\Activity::class)) {
            $this->activityLogs = \Spatie\Activitylog\Models\Activity::where('siswa_id', $siswa->id)
                ->orWhere('causer_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(7)
                ->get()
                ->map(function ($log) {
                    $desc = (string) ($log->description ?? '');
                    
                    $modelNames = [
                        'Nilai' => 'Nilai Akademik',
                        'NilaiSumatifTp' => 'Nilai Sumatif',
                        'NilaiSas' => 'Nilai Akhir Semester (SAS)',
                        'NilaiTahfidz' => 'Nilai & Setoran Tahfizh',
                        'NilaiP5' => 'Penilaian Projek P5',
                        'Rapor' => 'Rapor Akademik',
                        'RaporDetail' => 'Catatan Rapor',
                        'RaporTahfidzDetail' => 'Catatan Rapor Tahfizh',
                        'JadwalRemedial' => 'Jadwal Remedial',
                        'AbsensiSiswa' => 'Presensi Kehadiran',
                        'Pembayaran' => 'Pembayaran SPP / Sekolah',
                        'Tagihan' => 'Tagihan Pendidikan',
                        'Tabungan' => 'Tabungan Santri',
                        'Siswa' => 'Biodata Santri',
                    ];

                    if (preg_match('/^(Membuat|Memperbarui|Menghapus)\s+data\s+(\w+)(?:\s*\((.*?)\))?/i', $desc, $m)) {
                        $action = strtolower($m[1]);
                        $model = $m[2];
                        $identifier = $m[3] ?? '';
                        $friendlyModel = $modelNames[$model] ?? 'data akademik';
                        $cleanIdentifier = preg_replace('/^#?\d+$/', '', trim($identifier));

                        $actionPhrase = match ($action) {
                            'membuat' => "Pencatatan {$friendlyModel} baru",
                            'memperbarui' => "Pembaruan catatan {$friendlyModel}",
                            'menghapus' => "Penyesuaian catatan {$friendlyModel}",
                            default => "Aktivitas pada {$friendlyModel}",
                        };

                        $desc = (!empty($cleanIdentifier) && !preg_match('/^#\d+$/', $cleanIdentifier))
                            ? "{$actionPhrase} ({$cleanIdentifier})"
                            : $actionPhrase;
                    }

                    $desc = preg_replace('/\s*\(\s*#?\d+\s*\)/', '', $desc);
                    $desc = preg_replace('/\s+ID\s+#?\d+/i', '', $desc);
                    $desc = preg_replace('/\s*#\d+/', '', $desc);
                    $desc = trim($desc);

                    return [
                        'description' => $desc ?: 'Pembaruan data akun santri',
                        'time' => $log->created_at ? $log->created_at->diffForHumans() : '-',
                    ];
                })
                ->toArray();
        }
    }

    public function render()
    {
        return view('livewire.murid.dashboard')
            ->layout('components.layouts.app', ['title' => 'Dashboard Murid']);
    }
}
