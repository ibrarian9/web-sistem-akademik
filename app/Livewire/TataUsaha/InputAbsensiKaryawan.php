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

    public function downloadTemplate()
    {
        $filename = 'template-absen-guru-karyawan-' . ($this->tanggal ?: date('Y-m-d')) . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $data = $this->attendanceData;

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Microsoft Excel
            fputs($file, "\xEF\xBB\xBF");

            // Header columns
            fputcsv($file, [
                'NIP / Username',
                'Nama Karyawan',
                'Peran / Jabatan',
                'Status Kehadiran',
                'Jam Datang (HH:MM)',
                'Jam Pulang (HH:MM)',
                'Catatan'
            ]);

            foreach ($data as $item) {
                fputcsv($file, [
                    $item['nip'] ?: $item['nama'],
                    $item['nama'],
                    $item['role'],
                    $item['status'] ?: 'hadir',
                    $item['waktu_datang'] ?: '07:00',
                    $item['waktu_pulang'] ?: '15:00',
                    $item['catatan'] ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    public function exportAttendance()
    {
        $filename = 'data-presensi-karyawan-guru-' . ($this->tanggal ?: date('Y-m-d')) . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $data = $this->attendanceData;
        $tanggal = $this->tanggal;

        $callback = function () use ($data, $tanggal) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'No',
                'NIP / Username',
                'Nama Karyawan',
                'Peran / Jabatan',
                'Tanggal',
                'Status Kehadiran',
                'Jam Datang',
                'Jam Pulang',
                'Catatan'
            ]);

            $no = 1;
            foreach ($data as $item) {
                $statusLabels = [
                    'hadir' => 'Hadir',
                    'telat' => 'Terlambat',
                    'izin' => 'Izin',
                    'sakit' => 'Sakit',
                    'tidak_hadir' => 'Alpa / Tidak Hadir',
                    'alpa' => 'Alpa / Tidak Hadir',
                ];
                $statusText = $statusLabels[$item['status']] ?? ucfirst($item['status']);

                fputcsv($file, [
                    $no++,
                    $item['nip'] ?: '-',
                    $item['nama'],
                    $item['role'],
                    $tanggal,
                    $statusText,
                    $item['waktu_datang'] ?: '-',
                    $item['waktu_pulang'] ?: '-',
                    $item['catatan'] ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    public function uploadCsv()
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path = $this->csvFile->getRealPath();
        
        // Auto-detect delimiter
        $firstLine = file_exists($path) ? fgets(fopen($path, 'r')) : '';
        $delimiter = (strpos($firstLine, ';') !== false && strpos($firstLine, ',') === false) ? ';' : ',';

        $file = fopen($path, 'r');
        $header = fgetcsv($file, 0, $delimiter); // Read header line
        
        // Clean BOM from first header column if present
        if ($header && isset($header[0])) {
            $header[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header[0]);
        }

        $uploaded = 0;

        // Detect if the file uses 7-column template format or 4-column compact format
        $isTemplateFormat = false;
        if ($header && count($header) >= 6) {
            $headerLower = array_map(fn($h) => strtolower(trim($h)), $header);
            if (in_array('status kehadiran', $headerLower) || in_array('status', $headerLower) || in_array('nama karyawan', $headerLower)) {
                $isTemplateFormat = true;
            }
        }

        while (($row = fgetcsv($file, 0, $delimiter)) !== false) {
            if (count($row) < 2) continue;

            $nipOrUsername = trim($row[0] ?? '');
            if (empty($nipOrUsername) || strtolower($nipOrUsername) === 'nip / username' || strtolower($nipOrUsername) === 'nip') {
                continue;
            }

            if ($isTemplateFormat || count($row) >= 6) {
                // Template format: NIP, Nama, Peran, Status, JamDatang, JamPulang, Catatan
                $status = strtolower(trim($row[3] ?? 'hadir'));
                $waktuDatang = isset($row[4]) ? trim($row[4]) : '07:00';
                $waktuPulang = isset($row[5]) ? trim($row[5]) : '15:00';
                $catatan = isset($row[6]) ? trim($row[6]) : null;
            } else {
                // Compact format: NIP, Status, JamDatang, JamPulang, Catatan (optional)
                $status = strtolower(trim($row[1] ?? 'hadir'));
                $waktuDatang = isset($row[2]) ? trim($row[2]) : '07:00';
                $waktuPulang = isset($row[3]) ? trim($row[3]) : '15:00';
                $catatan = isset($row[4]) ? trim($row[4]) : null;
            }

            if (!in_array($status, ['hadir', 'telat', 'sakit', 'izin', 'alpa', 'tidak_hadir'])) {
                $status = 'hadir';
            }

            $user = User::where('username', $nipOrUsername)
                ->orWhere('nip', $nipOrUsername)
                ->orWhereHas('guru', fn($q) => $q->where('nip', $nipOrUsername))
                ->first();

            if ($user && !in_array($user->role?->nama, ['super_admin', 'pengawas', 'koordinator'])) {
                $guruId = $user->guru?->id;
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

                if ($guruId) {
                    AbsensiGuru::updateOrCreate(
                        [
                            'guru_id' => $guruId,
                            'tanggal' => $this->tanggal,
                        ],
                        [
                            'waktu_datang' => $waktuDatang ? (strlen($waktuDatang) === 5 ? $waktuDatang . ':00' : $waktuDatang) : null,
                            'waktu_pulang' => $waktuPulang ? (strlen($waktuPulang) === 5 ? $waktuPulang . ':00' : $waktuPulang) : null,
                            'status' => $status === 'alpa' ? 'tidak_hadir' : $status,
                            'catatan' => $catatan ?: null,
                            'diinput_oleh' => auth()->id(),
                        ]
                    );
                    $uploaded++;
                }
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
