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
    public string $filterPeriode = ''; // '', 'today', 'yesterday', 'this_week', 'this_month'
    public int $perPage = 20;

    public $selectedLog = null;
    public bool $showDetailModal = false;
    public string $detailTab = 'diff'; // 'diff', 'properties', 'raw'

    protected $queryString = [
        'search' => ['except' => ''],
        'filterEvent' => ['except' => ''],
        'filterPeriode' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterEvent()
    {
        $this->resetPage();
    }

    public function updatingFilterPeriode()
    {
        $this->resetPage();
    }

    public function setPeriodPreset(string $preset)
    {
        $this->filterPeriode = ($this->filterPeriode === $preset) ? '' : $preset;
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
            
            // Parse JSON attribute_changes
            $changes = [];
            if (!empty($logArray['attribute_changes'])) {
                if (is_string($logArray['attribute_changes'])) {
                    $decodedChanges = json_decode($logArray['attribute_changes'], true);
                    $changes = json_last_error() === JSON_ERROR_NONE ? $decodedChanges : ['raw' => $logArray['attribute_changes']];
                } elseif (is_array($logArray['attribute_changes'])) {
                    $changes = $logArray['attribute_changes'];
                }
            }
            $logArray['changes_parsed'] = $changes;

            // Parse JSON properties
            $props = [];
            if (!empty($logArray['properties'])) {
                if (is_string($logArray['properties'])) {
                    $decodedProps = json_decode($logArray['properties'], true);
                    $props = json_last_error() === JSON_ERROR_NONE ? $decodedProps : ['raw' => $logArray['properties']];
                } elseif (is_array($logArray['properties'])) {
                    $props = $logArray['properties'];
                }
            }
            $logArray['properties_parsed'] = $props;

            $this->detailTab = !empty($changes) ? 'diff' : 'properties';
            $this->selectedLog = $logArray;
            $this->showDetailModal = true;
        }
    }

    public function closeDetail()
    {
        $this->selectedLog = null;
        $this->showDetailModal = false;
        $this->detailTab = 'diff';
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
            ->when($this->filterPeriode, function ($query) {
                match($this->filterPeriode) {
                    'today' => $query->whereDate('activity_log.created_at', now()->toDateString()),
                    'yesterday' => $query->whereDate('activity_log.created_at', now()->subDay()->toDateString()),
                    'this_week' => $query->whereBetween('activity_log.created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                    'this_month' => $query->whereMonth('activity_log.created_at', now()->month)->whereYear('activity_log.created_at', now()->year),
                    default => null,
                };
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
