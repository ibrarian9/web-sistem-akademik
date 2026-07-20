<?php

namespace App\Livewire\TataUsaha;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Guru;

class ManajemenKaryawan extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = 'semua';

    public function render()
    {
        $query = User::with(['role', 'guru'])
            ->whereNotIn('role_id', function ($q) {
                $q->select('id')->from('roles')->where('nama', 'murid');
            })
            ->orderBy('nama', 'asc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('username', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterRole !== 'semua') {
            $query->whereHas('role', function ($q) {
                $q->where('nama', $this->filterRole);
            });
        }

        $karyawanList = $query->paginate(12);

        return view('livewire.tata-usaha.manajemen-karyawan', [
            'karyawanList' => $karyawanList,
        ])->layout('components.layouts.app', ['title' => 'Direktori Karyawan & Staff']);
    }
}
