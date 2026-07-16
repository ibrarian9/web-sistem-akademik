<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.guest')]
class Login extends Component
{
    public string $username = '';
    public string $password = '';
    public bool $remember = false;

    protected array $rules = [
        'username' => 'required|string',
        'password' => 'required|string',
    ];

    protected array $messages = [
        'username.required' => 'Username wajib diisi.',
        'password.required' => 'Password wajib diisi.',
    ];

    public function login()
    {
        $this->validate();

        if (!Auth::attempt(['username' => $this->username, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        $user = Auth::user();

        if ($user->status !== 'aktif') {
            Auth::logout();
            throw ValidationException::withMessages([
                'username' => 'Akun Anda dinonaktifkan. Silakan hubungi administrator.',
            ]);
        }

        session()->regenerate();

        $role = $user->role->nama ?? '';

        return match ($role) {
            'super_admin' => redirect()->intended(route('super-admin.dashboard')),
            'guru' => redirect()->intended(route('guru.dashboard')),
            'murid' => redirect()->intended(route('murid.dashboard')),
            'finance' => redirect()->intended(route('finance.dashboard')),
            default => redirect()->to('/'),
        };
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
