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
        $all = Pengaturan::all()->keyBy('key');

        // Definisi konfigurasi terpadu: cukup satu sumber data tunggal untuk identitas sekolah
        $canonicalKeys = [
            'nama_sekolah' => [
                'keterangan' => 'Nama Resmi Sekolah / Lembaga',
                'fallback' => 'nama_instansi',
                'default' => 'Yayasan Pendidikan Islam',
                'group' => 'sekolah',
                'type' => 'text',
            ],
            'no_telepon' => [
                'keterangan' => 'Nomor Telepon Resmi Sekolah',
                'fallback' => 'telepon_instansi',
                'default' => '(0274) 123456',
                'group' => 'sekolah',
                'type' => 'text',
            ],
            'alamat_sekolah' => [
                'keterangan' => 'Alamat Lengkap Sekolah / Lembaga',
                'fallback' => 'alamat_instansi',
                'default' => 'Jl. Kaliurang Km. 10, Sleman, D.I. Yogyakarta',
                'group' => 'sekolah',
                'type' => 'textarea',
            ],
            'kepala_sekolah_nama' => [
                'keterangan' => 'Nama Kepala Sekolah',
                'default' => 'Drs. H. Ahmad Fauzi, M.Pd.',
                'group' => 'pejabat',
                'type' => 'text',
            ],
            'kepala_sekolah_nip' => [
                'keterangan' => 'NIP / NIY Kepala Sekolah',
                'default' => '19750812 200003 1 001',
                'group' => 'pejabat',
                'type' => 'text',
            ],
            'kepala_sekolah_jabatan' => [
                'keterangan' => 'Jabatan Resmi Kepala Sekolah',
                'default' => 'Kepala Sekolah / Madrasah',
                'group' => 'pejabat',
                'type' => 'text',
            ],
            'bendahara_nama' => [
                'keterangan' => 'Nama Bendahara Keuangan',
                'default' => 'Siti Aminah, S.E.',
                'group' => 'pejabat',
                'type' => 'text',
            ],
            'bendahara_nip' => [
                'keterangan' => 'NIP / NIY Bendahara Keuangan',
                'default' => '19820415 200801 2 004',
                'group' => 'pejabat',
                'type' => 'text',
            ],
            'bendahara_jabatan' => [
                'keterangan' => 'Jabatan Bendahara Keuangan',
                'default' => 'Bendahara Keuangan Yayasan',
                'group' => 'pejabat',
                'type' => 'text',
            ],
            'tata_usaha_nama' => [
                'keterangan' => 'Nama Kepala Tata Usaha',
                'default' => 'Budi Santoso, S.Kom.',
                'group' => 'pejabat',
                'type' => 'text',
            ],
            'tata_usaha_nip' => [
                'keterangan' => 'NIP / NIY Kepala Tata Usaha',
                'default' => '19881120 201202 1 003',
                'group' => 'pejabat',
                'type' => 'text',
            ],
            'tata_usaha_jabatan' => [
                'keterangan' => 'Jabatan Kepala Tata Usaha',
                'default' => 'Kepala Tata Usaha',
                'group' => 'pejabat',
                'type' => 'text',
            ],
            'jam_masuk_guru' => [
                'keterangan' => 'Jam Masuk Standar Guru (WIB)',
                'default' => '07:00',
                'group' => 'presensi',
                'type' => 'text',
            ],
            'toleransi_telat_menit' => [
                'keterangan' => 'Toleransi Keterlambatan (Menit)',
                'default' => '15',
                'group' => 'presensi',
                'type' => 'text',
            ],
        ];

        $loaded = [];
        foreach ($canonicalKeys as $key => $meta) {
            $val = null;
            if (isset($all[$key]) && $all[$key]->value !== null && $all[$key]->value !== '') {
                $val = $all[$key]->value;
            } elseif (isset($meta['fallback']) && isset($all[$meta['fallback']]) && $all[$meta['fallback']]->value !== null && $all[$meta['fallback']]->value !== '') {
                $val = $all[$meta['fallback']]->value;
            } else {
                $val = $meta['default'] ?? '';
            }

            $loaded[] = [
                'key' => $key,
                'value' => (string) $val,
                'keterangan' => $meta['keterangan'],
                'group' => $meta['group'],
                'type' => $meta['type'],
            ];
        }

        $this->settings = $loaded;
    }

    public function save()
    {
        if (auth()->user()?->isSuperAdmin2()) {
            session()->flash('message', 'Akses ditolak: Akun Super Admin 2 hanya memiliki hak akses lihat.');
            return;
        }

        $this->validate();

        foreach ($this->settings as $setting) {
            $key = $setting['key'];
            $val = $setting['value'] ?? '';
            $ket = $setting['keterangan'] ?? null;

            Pengaturan::setValue($key, $val, $ket);
        }

        session()->flash('message', 'Pengaturan sekolah dan parameter sistem berhasil disimpan.');
        $this->loadSettings();
    }

    public function render()
    {
        return view('livewire.super-admin.tata-kelola.manajemen-pengaturan')
            ->layout('components.layouts.app', ['title' => 'Pengaturan Sekolah & Sistem']);
    }
}
