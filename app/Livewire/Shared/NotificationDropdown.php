<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use App\Models\Notifikasi;

class NotificationDropdown extends Component
{
    public array $notifications = [];
    public int $unreadCount = 0;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $userId = auth()->id();
        if (!$userId) {
            return;
        }

        $records = Notifikasi::where('user_id', $userId)
            ->whereNull('dibaca_pada')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->unreadCount = $records->count();
        $this->notifications = $records->map(fn($n) => [
            'id' => $n->id,
            'judul' => $n->judul,
            'pesan' => $n->isi_pesan,
            'time' => $n->created_at->diffForHumans(),
        ])->toArray();
    }

    public function markAllAsRead()
    {
        $userId = auth()->id();
        if (!$userId) {
            return;
        }

        Notifikasi::where('user_id', $userId)
            ->whereNull('dibaca_pada')
            ->update(['dibaca_pada' => now()]);

        $this->loadNotifications();
    }

    public function markAsRead(int $id)
    {
        $userId = auth()->id();
        Notifikasi::where('id', $id)
            ->where('user_id', $userId)
            ->update(['dibaca_pada' => now()]);

        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.shared.notification-dropdown');
    }
}
