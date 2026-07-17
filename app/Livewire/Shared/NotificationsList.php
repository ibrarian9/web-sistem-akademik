<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Notifikasi;

class NotificationsList extends Component
{
    use WithPagination;

    public string $filter = 'all'; // 'all', 'unread', 'read'

    protected $queryString = ['filter'];

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function markAsRead(int $id)
    {
        $userId = auth()->id();
        Notifikasi::where('id', $id)
            ->where('user_id', $userId)
            ->update(['dibaca_pada' => now()]);

        $this->dispatch('notification-marked-read');
    }

    public function markAllAsRead()
    {
        $userId = auth()->id();
        Notifikasi::where('user_id', $userId)
            ->whereNull('dibaca_pada')
            ->update(['dibaca_pada' => now()]);

        $this->dispatch('notification-marked-read');
    }

    public function getNotifications()
    {
        $userId = auth()->id();
        $query = Notifikasi::where('user_id', $userId)->orderBy('created_at', 'desc');

        if ($this->filter === 'unread') {
            $query->whereNull('dibaca_pada');
        } elseif ($this->filter === 'read') {
            $query->whereNotNull('dibaca_pada');
        }

        return $query->paginate(10);
    }

    public function render()
    {
        return view('livewire.shared.notifications-list', [
            'notifications' => $this->getNotifications(),
        ])->layout('components.layouts.app', ['title' => 'Daftar Notifikasi']);
    }
}
