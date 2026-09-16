<?php

namespace App\Livewire\Forms\SuperAdmin;

use App\Models\Guru;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Form;

class GuruForm extends Form
{
    public ?int $guruId = null;
    public string $nama = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $niy = '';
    public string $nik = '';
    public string $tempat_lahir = '';
    public ?string $tanggal_lahir = null;
    public string $status_kepegawaian = 'honorer';
    public string $jenis_guru = 'umum'; // umum, tahfidz, keduanya, pendamping
    public string $pendidikan = '';
    public string $grade_guru = '';
    public string $status_pernikahan = 'belum_menikah';
    public ?string $tanggal_masuk = null;
    public bool $status_aktif = true;
    public string $no_hp = '';
    public string $alamat = '';

    public function rules(): array
    {
        $guruUserId = $this->guruId ? Guru::find($this->guruId)?->user_id : null;

        $rules = [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . ($guruUserId ?? 'NULL'),
            'nik' => 'nullable|string|max:20',
            'status_kepegawaian' => 'required|in:pns,gtt,honorer,tetap_yayasan,gty',
            'jenis_guru' => 'required|in:umum,tahfidz,keduanya,pendamping',
            'pendidikan' => 'nullable|string|max:100',
            'grade_guru' => 'nullable|string|max:50',
            'status_pernikahan' => 'required|in:belum_menikah,menikah,cerai_hidup,cerai_mati',
            'tanggal_masuk' => 'required|date',
            'tanggal_lahir' => 'nullable|date',
            'status_aktif' => 'required|boolean',
        ];

        if ($this->niy) {
            $rules['niy'] = 'unique:guru,nip,' . ($this->guruId ?? 'NULL');
        }

        if (!$this->guruId) {
            $rules['password'] = 'required|string|min:6';
        }

        return $rules;
    }

    public function setGuru(Guru $guru): void
    {
        $this->guruId = $guru->id;
        $this->nama = $guru->user->nama ?? '';
        $this->username = $guru->user->username ?? '';
        $this->email = $guru->user->email ?? '';
        $this->password = '';
        $this->niy = $guru->niy ?? $guru->nip ?? '';
        $this->nik = $guru->nik ?? '';
        $this->tempat_lahir = $guru->tempat_lahir ?? '';
        $this->tanggal_lahir = $guru->tanggal_lahir ? $guru->tanggal_lahir->format('Y-m-d') : null;
        $this->status_kepegawaian = $guru->status_kepegawaian;
        $this->jenis_guru = $guru->jenis_guru ?? 'umum';
        $this->pendidikan = $guru->pendidikan ?? '';
        $this->grade_guru = $guru->grade_guru ?? '';
        $this->status_pernikahan = $guru->status_pernikahan ?? 'belum_menikah';
        $this->tanggal_masuk = $guru->tanggal_masuk ? $guru->tanggal_masuk->format('Y-m-d') : null;
        $this->status_aktif = (bool) $guru->status_aktif;
        $this->no_hp = $guru->user->no_hp ?? '';
        $this->alamat = $guru->user->alamat ?? '';
    }

    public function resetForm(): void
    {
        $this->reset([
            'guruId', 'nama', 'username', 'email', 'password', 'niy', 'nik',
            'tempat_lahir', 'tanggal_lahir', 'pendidikan', 'grade_guru', 'no_hp', 'alamat'
        ]);
        $this->status_kepegawaian = 'honorer';
        $this->jenis_guru = 'umum';
        $this->status_pernikahan = 'belum_menikah';
        $this->status_aktif = true;
        $this->tanggal_masuk = date('Y-m-d');
    }

    public function store(): Guru
    {
        $roleGuru = Role::firstOrCreate(
            ['nama' => 'guru'],
            ['deskripsi' => 'Guru / Tenaga Pendidik']
        );

        if ($this->guruId) {
            $guru = Guru::findOrFail($this->guruId);

            $guru->user->update([
                'nama' => $this->nama,
                'username' => $this->username,
                'email' => $this->email ?: null,
                'no_hp' => $this->no_hp,
                'alamat' => $this->alamat,
                'status' => $this->status_aktif ? 'aktif' : 'nonaktif',
            ]);

            if ($this->password) {
                $guru->user->update(['password' => Hash::make($this->password)]);
            }

            $guru->update([
                'nip' => $this->niy ?: null,
                'nik' => $this->nik ?: null,
                'tempat_lahir' => $this->tempat_lahir ?: null,
                'tanggal_lahir' => $this->tanggal_lahir ?: null,
                'status_kepegawaian' => $this->status_kepegawaian,
                'jenis_guru' => $this->jenis_guru,
                'pendidikan' => $this->pendidikan ?: null,
                'grade_guru' => $this->grade_guru ?: null,
                'status_pernikahan' => $this->status_pernikahan,
                'tanggal_masuk' => $this->tanggal_masuk,
                'status_aktif' => $this->status_aktif,
            ]);

            return $guru;
        }

        $user = User::create([
            'nama' => $this->nama,
            'username' => $this->username,
            'email' => $this->email ?: null,
            'password' => Hash::make($this->password),
            'role_id' => $roleGuru->id,
            'no_hp' => $this->no_hp,
            'alamat' => $this->alamat,
            'status' => 'aktif',
        ]);

        return Guru::create([
            'user_id' => $user->id,
            'nip' => $this->niy ?: ('GURU-' . str_pad($user->id, 5, '0', STR_PAD_LEFT)),
            'nik' => $this->nik ?: null,
            'tempat_lahir' => $this->tempat_lahir ?: null,
            'tanggal_lahir' => $this->tanggal_lahir ?: null,
            'status_kepegawaian' => $this->status_kepegawaian,
            'jenis_guru' => $this->jenis_guru,
            'pendidikan' => $this->pendidikan ?: null,
            'grade_guru' => $this->grade_guru ?: null,
            'status_pernikahan' => $this->status_pernikahan,
            'tanggal_masuk' => $this->tanggal_masuk,
            'status_aktif' => true,
        ]);
    }
}
