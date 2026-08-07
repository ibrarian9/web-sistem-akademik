<?php

namespace App\Livewire\SuperAdmin\TataKelola;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Guru;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ManajemenGuru extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    // Form fields
    public ?int $guruId = null;
    public string $nama = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $niy = ''; // NIY (Nomor Induk Yayasan)
    public string $nik = ''; // NIK (KTP)
    public string $tempat_lahir = '';
    public ?string $tanggal_lahir = null;
    public string $status_kepegawaian = 'honorer';
    public string $pendidikan = '';
    public string $grade_guru = '';
    public string $status_pernikahan = 'belum_menikah'; // belum_menikah, menikah, cerai_hidup, cerai_mati
    public ?string $tanggal_masuk = null;
    public bool $status_aktif = true;
    public string $no_hp = '';
    public string $alamat = '';

    public bool $isFormOpen = false;

    protected $queryString = ['search' => ['except' => '']];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->resetForm();
        $this->isFormOpen = true;
    }

    public function openEdit(int $id)
    {
        $this->resetForm();
        $guru = Guru::with('user')->findOrFail($id);
        $this->guruId = $guru->id;
        $this->nama = $guru->user->nama ?? '';
        $this->username = $guru->user->username ?? '';
        $this->email = $guru->user->email ?? '';
        $this->niy = $guru->niy ?? $guru->nip ?? '';
        $this->nik = $guru->nik ?? '';
        $this->tempat_lahir = $guru->tempat_lahir ?? '';
        $this->tanggal_lahir = $guru->tanggal_lahir ? $guru->tanggal_lahir->format('Y-m-d') : null;
        $this->status_kepegawaian = $guru->status_kepegawaian;
        $this->pendidikan = $guru->pendidikan ?? '';
        $this->grade_guru = $guru->grade_guru ?? '';
        $this->status_pernikahan = $guru->status_pernikahan ?? 'belum_menikah';
        $this->tanggal_masuk = $guru->tanggal_masuk ? $guru->tanggal_masuk->format('Y-m-d') : null;
        $this->status_aktif = (bool) $guru->status_aktif;
        $this->no_hp = $guru->user->no_hp ?? '';
        $this->alamat = $guru->user->alamat ?? '';

        $this->isFormOpen = true;
    }

    public function save()
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . ($this->guruId ? Guru::find($this->guruId)->user_id : 'NULL'),
            'nik' => 'nullable|string|max:20',
            'status_kepegawaian' => 'required|in:pns,gtt,honorer',
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

        $this->validate($rules);

        DB::transaction(function () {
            $roleGuru = Role::where('nama', 'guru')->firstOrFail();

            if ($this->guruId) {
                // Update
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
                    'pendidikan' => $this->pendidikan ?: null,
                    'grade_guru' => $this->grade_guru ?: null,
                    'status_pernikahan' => $this->status_pernikahan,
                    'tanggal_masuk' => $this->tanggal_masuk,
                    'status_aktif' => $this->status_aktif,
                ]);
            } else {
                // Create
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

                Guru::create([
                    'user_id' => $user->id,
                    'nip' => $this->niy ?: null,
                    'nik' => $this->nik ?: null,
                    'tempat_lahir' => $this->tempat_lahir ?: null,
                    'tanggal_lahir' => $this->tanggal_lahir ?: null,
                    'status_kepegawaian' => $this->status_kepegawaian,
                    'pendidikan' => $this->pendidikan ?: null,
                    'grade_guru' => $this->grade_guru ?: null,
                    'status_pernikahan' => $this->status_pernikahan,
                    'tanggal_masuk' => $this->tanggal_masuk,
                    'status_aktif' => true,
                ]);
            }
        });

        session()->flash('message', 'Data guru berhasil disimpan.');
        $this->isFormOpen = false;
        $this->resetForm();
    }

    public function delete(int $id)
    {
        DB::transaction(function () use ($id) {
            $guru = Guru::findOrFail($id);
            $user = $guru->user;
            
            $guru->delete();
            if ($user) {
                $user->delete();
            }
        });

        session()->flash('message', 'Data guru berhasil dihapus.');
    }

    private function resetForm()
    {
        $this->guruId = null;
        $this->nama = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->niy = '';
        $this->nik = '';
        $this->tempat_lahir = '';
        $this->tanggal_lahir = null;
        $this->status_kepegawaian = 'honorer';
        $this->pendidikan = '';
        $this->grade_guru = '';
        $this->status_pernikahan = 'belum_menikah';
        $this->tanggal_masuk = date('Y-m-d');
        $this->status_aktif = true;
        $this->no_hp = '';
        $this->alamat = '';
    }

    public function render()
    {
        $gurus = Guru::with('user')
            ->where(function ($query) {
                $query->where('nip', 'like', '%' . $this->search . '%')
                    ->orWhere('nik', 'like', '%' . $this->search . '%')
                    ->orWhere('grade_guru', 'like', '%' . $this->search . '%')
                    ->orWhere('pendidikan', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('nama', 'like', '%' . $this->search . '%')
                          ->orWhere('username', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.super-admin.tata-kelola.manajemen-guru', [
            'gurus' => $gurus,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Guru']);
    }
}
