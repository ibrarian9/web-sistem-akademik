<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class ProfilSaya extends Component
{
    use WithFileUploads;

    public string $nama = '';
    public string $username = '';
    public string $email = '';
    public string $nip = '';
    public string $jabatan = '';
    
    // Signature Upload & Draw
    public $new_ttd;
    public ?string $drawn_ttd = null;
    public ?string $current_ttd = null;

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
        $this->current_ttd = $user->ttd_digital;
    }

    public function saveProfile()
    {
        $user = Auth::user();

        $this->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'nip' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:100',
            'new_ttd' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        $user->nama = $this->nama;
        $user->email = $this->email;
        $user->nip = $this->nip;
        $user->jabatan = $this->jabatan;

        $destinationPath = public_path('uploads/ttd');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        // Process Drawn Signature (Canvas Base64)
        if (!empty($this->drawn_ttd)) {
            if ($user->ttd_digital && File::exists(public_path($user->ttd_digital))) {
                File::delete(public_path($user->ttd_digital));
            }

            $image_parts = explode(";base64,", $this->drawn_ttd);
            if (count($image_parts) === 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $filename = 'ttd_user_' . $user->id . '_' . time() . '.png';
                $filePath = public_path('uploads/ttd/' . $filename);
                file_put_contents($filePath, $image_base64);

                $user->ttd_digital = 'uploads/ttd/' . $filename;
                $this->current_ttd = $user->ttd_digital;
                $this->reset(['drawn_ttd', 'new_ttd']);
            }
        } 
        // Process File Upload
        elseif ($this->new_ttd) {
            if ($user->ttd_digital && File::exists(public_path($user->ttd_digital))) {
                File::delete(public_path($user->ttd_digital));
            }

            $filename = 'ttd_user_' . $user->id . '_' . time() . '.' . $this->new_ttd->getClientOriginalExtension();
            $this->new_ttd->storeAs('uploads/ttd', $filename, 'public');
            
            // Ensure direct file exists in public/uploads/ttd as well
            if (file_exists(storage_path('app/public/uploads/ttd/' . $filename))) {
                @copy(storage_path('app/public/uploads/ttd/' . $filename), public_path('uploads/ttd/' . $filename));
            }

            $user->ttd_digital = 'uploads/ttd/' . $filename;
            $this->current_ttd = $user->ttd_digital;
            $this->reset('new_ttd');
        }

        $user->save();

        session()->flash('message', 'Profil & TTD Digital berhasil diperbarui!');
    }

    public function removeTtd()
    {
        $user = Auth::user();

        if ($user->ttd_digital && File::exists(public_path($user->ttd_digital))) {
            File::delete(public_path($user->ttd_digital));
        }

        $user->ttd_digital = null;
        $user->save();

        $this->current_ttd = null;
        session()->flash('message', 'TTD Digital berhasil dihapus.');
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
