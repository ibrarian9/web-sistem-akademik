<?php

namespace App\Livewire\TataUsaha;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Guru;
use App\Models\AbsensiGuru;
use Carbon\Carbon;

class InputAbsensiKaryawan extends Component
{
    use WithFileUploads, WithPagination;

    public string $tanggal = '';
    public string $search = '';
    public string $filterRole = 'semua';
    public $csvFile = null;

    public array $attendanceData = [];

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $this->loadEmployees();
    }

    public function updatedTanggal()
    {
        $this->loadEmployees();
    }

    public function updatedFilterRole()
    {
        $this->loadEmployees();
    }

    public function loadEmployees()
    {
        // Fetch all users except super_admin and pengawas/koordinator
        $query = User::with(['role', 'guru'])
            ->whereHas('role', function ($q) {
                $q->whereNotIn('nama', ['super_admin', 'pengawas', 'koordinator']);
            })
            ->where('status', 'aktif');

        if ($this->filterRole !== 'semua') {
            $query->whereHas('role', function ($q) {
                $q->where('nama', $this->filterRole);
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('username', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->orderBy('nama')->get();

        // Fetch existing attendance records for the selected date
        $existingRecords = AbsensiGuru::whereDate('tanggal', $this->tanggal)
            ->get()
            ->keyBy('guru_id');

        $this->attendanceData = [];

        foreach ($users as $user) {
            $guruId = $user->guru?->id;
            // Handle users who might not have a guru profile record yet
            if (!$guruId && in_array($user->role?->nama, ['tata_usaha', 'finance', 'kepala_sekolah'])) {
                $guruRecord = Guru::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nip' => $user->nip ?: 'STAFF-' . $user->id,
                        'jenis_guru' => 'umum',
                        'no_hp' => $user->no_hp ?? '-',
                        'alamat' => $user->alamat ?? '-',
                        'tanggal_masuk' => date('Y-m-d'),
                        'status_aktif' => true,
                    ]
                );
                $guruId = $guruRecord->id;
            }

            if (!$guruId) continue;

            $record = $existingRecords->get($guruId);

            $this->attendanceData[$guruId] = [
                'user_id' => $user->id,
                'guru_id' => $guruId,
                'nama' => $user->nama,
                'role' => ucwords(str_replace('_', ' ', $user->role?->nama ?? '-')),
                'nip' => $user->nip ?: ($user->guru?->nip ?? '-'),
                'status' => $record ? $record->status : 'hadir',
                'waktu_datang' => $record && $record->waktu_datang ? date('H:i', strtotime($record->waktu_datang)) : '07:00',
                'waktu_pulang' => $record && $record->waktu_pulang ? date('H:i', strtotime($record->waktu_pulang)) : '15:00',
                'catatan' => $record ? $record->catatan : '',
            ];
        }
    }

    public function setStatusAll(string $status)
    {
        foreach ($this->attendanceData as $guruId => &$data) {
            $data['status'] = $status;
        }
    }

    public function saveAttendance()
    {
        if (empty($this->attendanceData)) {
            session()->flash('error', 'Tidak ada data karyawan untuk disimpan.');
            return;
        }

        $count = 0;
        foreach ($this->attendanceData as $guruId => $data) {
            AbsensiGuru::updateOrCreate(
                [
                    'guru_id' => $guruId,
                    'tanggal' => $this->tanggal,
                ],
                [
                    'waktu_datang' => !empty($data['waktu_datang']) ? $data['waktu_datang'] . ':00' : null,
                    'waktu_pulang' => !empty($data['waktu_pulang']) ? $data['waktu_pulang'] . ':00' : null,
                    'status' => $data['status'],
                    'catatan' => $data['catatan'] ?? null,
                    'diinput_oleh' => auth()->id(),
                ]
            );
            $count++;
        }

        session()->flash('message', "Presensi $count karyawan berhasil disimpan untuk tanggal " . date('d M Y', strtotime($this->tanggal)) . ".");
        $this->loadEmployees();
    }

    public function uploadCsv()
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path = $this->csvFile->getRealPath();
        $file = fopen($path, 'r');

        $header = fgetcsv($file); // Read header line
        $uploaded = 0;

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) < 3) continue;

            $nipOrUsername = trim($row[0]);
            $status = strtolower(trim($row[1]));
            $waktuDatang = isset($row[2]) ? trim($row[2]) : '07:00';
            $waktuPulang = isset($row[3]) ? trim($row[3]) : '15:00';

            if (!in_array($status, ['hadir', 'telat', 'sakit', 'izin', 'alpa', 'tidak_hadir'])) {
                $status = 'hadir';
            }

            $user = User::where('username', $nipOrUsername)
                ->orWhereHas('guru', fn($q) => $q->where('nip', $nipOrUsername))
                ->first();

            if ($user && $user->guru && !in_array($user->role?->nama, ['super_admin', 'pengawas', 'koordinator'])) {
                AbsensiGuru::updateOrCreate(
                    [
                        'guru_id' => $user->guru->id,
                        'tanggal' => $this->tanggal,
                    ],
                    [
                        'waktu_datang' => $waktuDatang ? (strlen($waktuDatang) === 5 ? $waktuDatang . ':00' : $waktuDatang) : null,
                        'waktu_pulang' => $waktuPulang ? (strlen($waktuPulang) === 5 ? $waktuPulang . ':00' : $waktuPulang) : null,
                        'status' => $status === 'alpa' ? 'tidak_hadir' : $status,
                        'diinput_oleh' => auth()->id(),
                    ]
                );
                $uploaded++;
            }
        }

        fclose($file);
        $this->csvFile = null;

        session()->flash('message', "Berhasil mengunggah $uploaded data presensi karyawan dari file CSV.");
        $this->loadEmployees();
    }

    public function render()
    {
        return view('livewire.tata-usaha.input-absensi-karyawan')
            ->layout('components.layouts.app', ['title' => 'Input Absensi Karyawan']);
    }
}
