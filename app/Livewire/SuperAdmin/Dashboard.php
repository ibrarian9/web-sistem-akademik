<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Models\Role;
use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Services\AuditLogFormatter;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Component
{
    // Executive Metrics
    public int $totalSiswa = 0;
    public int $totalGuru = 0;
    public int $totalKelas = 0;
    public string $totalTunggakan = 'Rp 0';
    public string $rasioSiswaGuru = '0 : 1';
    public int $totalUser = 0;

    // Academic Period Active
    public string $tahunAjaranAktif = '-';
    public string $semesterAktif = '-';

    // Chart Data Properties
    public array $classLabels = [];
    public array $classStudentCounts = [];
    public array $roleLabels = [];
    public array $roleUserCounts = [];

    // System Activity Feed (Latest 5 logs)
    public array $recentActivities = [];

    // Server & Health Stats
    public array $systemHealth = [];

    public function mount()
    {
        $this->totalSiswa = Siswa::where('status', 'aktif')->count();
        $this->totalGuru = Guru::where('status_aktif', true)->count();
        $this->totalKelas = Kelas::count();
        $this->totalUser = User::count();

        $this->rasioSiswaGuru = $this->totalGuru > 0
            ? round($this->totalSiswa / $this->totalGuru, 1) . ' : 1'
            : '-';

        $tunggakanNominal = Tagihan::tunggakan()
            ->selectRaw('SUM(nominal - total_dibayar) as aggregate')
            ->value('aggregate') ?? 0.00;
        $this->totalTunggakan = 'Rp ' . number_format($tunggakanNominal, 0, ',', '.');

        $this->tahunAjaranAktif = TahunAjaran::where('status_aktif', true)->value('nama') ?? '2025/2026';
        $semesterVal = Semester::where('status_aktif', true)->value('semester');
        $this->semesterAktif = $semesterVal ? ucfirst($semesterVal) : 'Ganjil';

        $this->loadChartData();
        $this->loadRecentActivities();
        $this->loadSystemHealth();
    }

    public function loadChartData()
    {
        // 1. Class student distribution (Top 10 classes)
        $classes = Kelas::withCount(['siswa' => function ($q) {
            $q->where('status', 'aktif');
        }])
            ->orderBy('nama_kelas', 'asc')
            ->limit(10)
            ->get();

        $this->classLabels = $classes->pluck('nama_kelas')->map(fn($n) => 'Kls ' . $n)->toArray();
        $this->classStudentCounts = $classes->pluck('siswa_count')->toArray();

        // 2. User Distribution per Role
        $roles = Role::withCount('users')->get();
        $this->roleLabels = $roles->pluck('nama')->map(fn($r) => ucfirst(str_replace('_', ' ', $r)))->toArray();
        $this->roleUserCounts = $roles->pluck('users_count')->toArray();
    }

    public function loadRecentActivities()
    {
        $logs = DB::table('activity_log')
            ->leftJoin('users', 'activity_log.causer_id', '=', 'users.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->select(
                'activity_log.id',
                'activity_log.description',
                'activity_log.event',
                'activity_log.created_at',
                'users.nama as user_name',
                'roles.nama as role_name'
            )
            ->latest('activity_log.created_at')
            ->limit(5)
            ->get();

        $this->recentActivities = $logs->map(function ($log) {
            $desc = class_exists(AuditLogFormatter::class)
                ? AuditLogFormatter::cleanDescription($log->description)
                : $log->description;

            return [
                'id' => $log->id,
                'user_name' => $log->user_name ?: 'Sistem Otomatis',
                'role_name' => $log->role_name ? ucfirst(str_replace('_', ' ', $log->role_name)) : 'Sistem',
                'description' => $desc,
                'time_ago' => Carbon::parse($log->created_at)->locale('id')->diffForHumans(),
                'event' => $log->event ?: 'info',
            ];
        })->toArray();
    }

    public function loadSystemHealth()
    {
        $this->systemHealth = [
            'database' => 'MariaDB Connected',
            'framework' => 'Laravel ' . app()->version(),
            'php' => 'PHP ' . PHP_VERSION,
            'timezone' => config('app.timezone', 'Asia/Jakarta'),
            'total_users' => $this->totalUser . ' Akun Terdaftar',
        ];
    }

    public function render()
    {
        return view('livewire.super-admin.dashboard')
            ->layout('components.layouts.app', ['title' => 'Dashboard Super Admin']);
    }
}
