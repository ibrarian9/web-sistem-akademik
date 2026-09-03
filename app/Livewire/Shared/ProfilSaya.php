<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengaturan;

class ProfilSaya extends Component
{
    // Personal Profile
    public string $nama = '';
    public string $username = '';
    public string $email = '';
    public string $nip = '';
    public string $jabatan = '';

    // School Profile (Accessible by bendahara/finance, tata_usaha, super_admin, kepala_sekolah)
    public bool $canEditSchool = false;
    public string $nama_sekolah = '';
    public string $alamat_sekolah = '';
    public string $no_telepon = '';

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

        $role = $user->role->nama ?? '';
        $this->canEditSchool = in_array($role, ['finance', 'tata_usaha', 'super_admin', 'kepala_sekolah']);

        if ($this->canEditSchool) {
            $this->nama_sekolah = Pengaturan::getValue('nama_sekolah') ?: Pengaturan::getValue('nama_instansi', 'PONDOK PESANTREN & SEKOLAH ISLAM TERPADU');
            $this->alamat_sekolah = Pengaturan::getValue('alamat_sekolah') ?: Pengaturan::getValue('alamat_instansi', 'Jl. Pendidikan Karakter Islami, Pekanbaru');
            $this->no_telepon = Pengaturan::getValue('no_telepon') ?: Pengaturan::getValue('telepon_instansi', '(0761) 123456');
        }
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

        // Sync with official document signers settings
        $role = $user->role->nama ?? '';
        if ($role === 'finance') {
            Pengaturan::setValue('bendahara_nama', $this->nama, 'Nama Bendahara Keuangan');
            if ($this->nip) Pengaturan::setValue('bendahara_nip', $this->nip, 'NIP / ID Bendahara Keuangan');
            if ($this->jabatan) Pengaturan::setValue('bendahara_jabatan', $this->jabatan, 'Jabatan Bendahara Keuangan');
        } elseif ($role === 'tata_usaha') {
            Pengaturan::setValue('tata_usaha_nama', $this->nama, 'Nama Kepala Tata Usaha');
            if ($this->nip) Pengaturan::setValue('tata_usaha_nip', $this->nip, 'NIP Kepala Tata Usaha');
            if ($this->jabatan) Pengaturan::setValue('tata_usaha_jabatan', $this->jabatan, 'Jabatan Kepala Tata Usaha');
        } elseif ($role === 'kepala_sekolah') {
            Pengaturan::setValue('kepala_sekolah_nama', $this->nama, 'Nama Kepala Sekolah');
            if ($this->nip) Pengaturan::setValue('kepala_sekolah_nip', $this->nip, 'NIP Kepala Sekolah');
            if ($this->jabatan) Pengaturan::setValue('kepala_sekolah_jabatan', $this->jabatan, 'Jabatan Resmi Kepala Sekolah');
        }

        session()->flash('message', 'Informasi profil pribadi berhasil diperbarui!');
    }

    public function saveSchoolProfile()
    {
        if (!$this->canEditSchool) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah informasi sekolah.');
        }

        $this->validate([
            'nama_sekolah' => 'required|string|max:255',
            'alamat_sekolah' => 'required|string|max:500',
            'no_telepon' => 'nullable|string|max:50',
        ]);

        Pengaturan::setValue('nama_sekolah', $this->nama_sekolah, 'Nama Resmi Sekolah');
        Pengaturan::setValue('nama_instansi', $this->nama_sekolah, 'Nama Instansi / Sekolah');
        Pengaturan::setValue('alamat_sekolah', $this->alamat_sekolah, 'Alamat Lengkap Sekolah');
        Pengaturan::setValue('alamat_instansi', $this->alamat_sekolah, 'Alamat Lengkap Instansi');
        Pengaturan::setValue('no_telepon', $this->no_telepon, 'Nomor Telepon Resmi');
        Pengaturan::setValue('telepon_instansi', $this->no_telepon, 'Nomor Telepon Resmi Instansi');

        session()->flash('school_message', 'Informasi identitas sekolah / instansi berhasil diperbarui!');
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
            ->layout('components.layouts.app', ['title' => 'Profil Saya & Pengaturan Lembaga']);
    }
}
