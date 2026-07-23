<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilSaya extends Component
{
    public string $nama = '';
    public string $username = '';
    public string $email = '';
    public string $nip = '';
    public string $jabatan = '';

    // Change Password
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount()
    {
        $user = Auth::user();
        $this->nama = $user->nama ?? '';
        $this->username = $user->username ?? '';
        $this->email = $user->email ?? '';
        $this->nip = $user->nip ?? '';
        $this->jabatan = $user->jabatan ?? ($user->role->nama ?? '');
    }

    public function saveProfile()
    {
        $user = Auth::user();

        $this->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'nip' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:100',
        ]);

        $user->nama = $this->nama;
        $user->email = $this->email;
        $user->nip = $this->nip;
        $user->jabatan = $this->jabatan;

        $user->save();

        session()->flash('message', 'Informasi profil berhasil diperbarui!');
    }

    public function updatePassword()
    {
        $user = Auth::user();

        $this->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password saat ini tidak cocok.');
            return;
        }

        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('password_message', 'Password berhasil diubah!');
    }

    public function render()
    {
        return view('livewire.shared.profil-saya')
            ->layout('components.layouts.app', ['title' => 'Profil Saya']);
    }
}
