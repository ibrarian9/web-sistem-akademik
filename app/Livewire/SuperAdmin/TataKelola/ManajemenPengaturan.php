<?php

namespace App\Livewire\SuperAdmin\TataKelola;

use Livewire\Component;
use App\Models\Pengaturan;

class ManajemenPengaturan extends Component
{
    public array $settings = [];

    protected $rules = [
        'settings.*.value' => 'nullable|string',
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $this->settings = Pengaturan::all()->toArray();
    }

    public function save()
    {
        $this->validate();

        foreach ($this->settings as $setting) {
            Pengaturan::findOrFail($setting['id'])->update([
                'value' => $setting['value'] ?? '',
            ]);
        }

        session()->flash('message', 'Pengaturan global sistem berhasil disimpan.');
        $this->loadSettings();
    }

    public function render()
    {
        return view('livewire.super-admin.tata-kelola.manajemen-pengaturan')
            ->layout('components.layouts.app', ['title' => 'Pengaturan Sistem']);
    }
}
