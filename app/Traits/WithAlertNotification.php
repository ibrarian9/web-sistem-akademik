<?php

namespace App\Traits;

trait WithAlertNotification
{
    public function toastSuccess(string $message): void
    {
        session()->flash('message', $message);
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    public function toastError(string $message): void
    {
        session()->flash('error', $message);
        $this->dispatch('toast', [
            'type' => 'error',
            'message' => $message,
        ]);
    }

    public function toastWarning(string $message): void
    {
        session()->flash('warning', $message);
        $this->dispatch('toast', [
            'type' => 'warning',
            'message' => $message,
        ]);
    }

    public function toastInfo(string $message): void
    {
        session()->flash('info', $message);
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => $message,
        ]);
    }
}
