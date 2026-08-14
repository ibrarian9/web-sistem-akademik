<?php

namespace App\Livewire\SuperAdmin\TataKelola;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class AuditLog extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterEvent = '';
    public int $perPage = 20;

    public $selectedLog = null;
    public bool $showDetailModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterEvent' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterEvent()
    {
        $this->resetPage();
    }

    public function openDetail($id)
    {
        $log = DB::table('activity_log')
            ->leftJoin('users', 'activity_log.causer_id', '=', 'users.id')
            ->select(
                'activity_log.*',
                'users.nama as causer_name',
                'users.username as causer_username',
                'users.email as causer_email'
            )
            ->where('activity_log.id', $id)
            ->first();

        if ($log) {
            $logArray = (array) $log;
            // Parse JSON properties if string
            if (isset($logArray['properties']) && is_string($logArray['properties'])) {
                $decoded = json_decode($logArray['properties'], true);
                $logArray['properties_parsed'] = json_last_error() === JSON_ERROR_NONE ? $decoded : $logArray['properties'];
            } else {
                $logArray['properties_parsed'] = $logArray['properties'] ?? [];
            }
            $this->selectedLog = $logArray;
            $this->showDetailModal = true;
        }
    }

    public function closeDetail()
    {
        $this->selectedLog = null;
        $this->showDetailModal = false;
    }

    public function render()
    {
        // Query directly using DB builder for maximum compatibility and speed
        $logs = DB::table('activity_log')
            ->leftJoin('users', 'activity_log.causer_id', '=', 'users.id')
            ->select(
                'activity_log.*',
                'users.nama as causer_name',
                'users.username as causer_username'
            )
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('activity_log.description', 'like', '%' . $this->search . '%')
                      ->orWhere('activity_log.event', 'like', '%' . $this->search . '%')
                      ->orWhere('users.nama', 'like', '%' . $this->search . '%')
                      ->orWhere('users.username', 'like', '%' . $this->search . '%')
                      ->orWhere('activity_log.ip_address', 'like', '%' . $this->search . '%')
                      ->orWhere('activity_log.user_agent', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterEvent, function ($query) {
                $query->where('activity_log.event', $this->filterEvent);
            })
            ->orderBy('activity_log.created_at', 'desc')
            ->paginate($this->perPage);

        // Fetch distinct events for the filter dropdown
        $events = DB::table('activity_log')
            ->whereNotNull('event')
            ->distinct()
            ->pluck('event');

        return view('livewire.super-admin.tata-kelola.audit-log', [
            'logs' => $logs,
            'events' => $events,
        ])->layout('components.layouts.app', ['title' => 'Audit Log Sistem']);
    }
}
