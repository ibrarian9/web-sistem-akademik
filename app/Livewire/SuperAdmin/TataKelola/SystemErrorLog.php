<?php

namespace App\Livewire\SuperAdmin\TataKelola;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;

class SystemErrorLog extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterLevel = '';
    public int $perPage = 20;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterLevel' => ['except' => ''],
    ];

    public function mount()
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role->nama ?? '', ['super_admin', 'kepala_sekolah'])) {
            abort(403, 'Akses Ditolak: Halaman System Error Log khusus untuk Super Admin & Kepala Sekolah.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterLevel()
    {
        $this->resetPage();
    }

    public function clearLog()
    {
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            File::put($logPath, '');
            session()->flash('success', 'Berkas System Error Log berhasil dibersihkan.');
        }
    }

    private function getParsedLogs(): Collection
    {
        $logPath = storage_path('logs/laravel.log');
        if (!File::exists($logPath)) {
            return collect();
        }

        $content = File::get($logPath);
        if (empty(trim($content))) {
            return collect();
        }

        // Regex pattern to split log entries
        $pattern = '/\[(\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}[^\]]*)\]\s+[\w\-]+\.([A-Z]+):\s+([\s\S]*?)(?=\[\d{4}-\d{2}-\d{2}|$)/m';

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $logs = collect();
        if (!empty($matches)) {
            foreach (array_reverse($matches) as $index => $match) {
                $timestamp = $match[1] ?? '';
                $level = strtoupper($match[2] ?? 'ERROR');
                $messageRaw = trim($match[3] ?? '');

                $lines = explode("\n", $messageRaw);
                $mainMessage = $lines[0] ?? $messageRaw;
                $stackTrace = count($lines) > 1 ? implode("\n", array_slice($lines, 1)) : '';

                $logs->push([
                    'id' => $index + 1,
                    'timestamp' => $timestamp,
                    'level' => $level,
                    'message' => $mainMessage,
                    'trace' => $stackTrace,
                    'raw' => $messageRaw,
                ]);
            }
        } else {
            // Fallback for unformatted/corrupted log content
            $lines = array_reverse(array_filter(array_map('trim', explode("\n", $content))));
            foreach ($lines as $idx => $line) {
                $logs->push([
                    'id' => $idx + 1,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'level' => 'UNFORMATTED',
                    'message' => mb_substr($line, 0, 500),
                    'trace' => mb_strlen($line) > 500 ? $line : '',
                    'raw' => $line,
                ]);
            }
        }

        return $logs;
    }

    public function render()
    {
        $allLogs = $this->getParsedLogs();

        $filtered = $allLogs->filter(function ($item) {
            $matchesSearch = true;
            if (!empty($this->search)) {
                $searchLower = strtolower($this->search);
                $matchesSearch = str_contains(strtolower($item['message']), $searchLower) ||
                                str_contains(strtolower($item['trace']), $searchLower) ||
                                str_contains(strtolower($item['timestamp']), $searchLower);
            }

            $matchesLevel = true;
            if (!empty($this->filterLevel)) {
                $matchesLevel = $item['level'] === strtoupper($this->filterLevel);
            }

            return $matchesSearch && $matchesLevel;
        });

        // Manual pagination on collection
        $currentPage = $this->getPage();
        $paginatedLogs = $filtered->slice(($currentPage - 1) * $this->perPage, $this->perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedLogs,
            $filtered->count(),
            $this->perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $stats = [
            'total' => $allLogs->count(),
            'error' => $allLogs->where('level', 'ERROR')->count(),
            'critical' => $allLogs->where('level', 'CRITICAL')->count(),
            'warning' => $allLogs->where('level', 'WARNING')->count(),
        ];

        return view('livewire.super-admin.tata-kelola.system-error-log', [
            'logs' => $paginator,
            'stats' => $stats,
        ])->layout('components.layouts.app', ['title' => 'System Error Log Viewer']);
    }
}
