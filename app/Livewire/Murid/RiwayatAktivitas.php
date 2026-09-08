<?php

namespace App\Livewire\Murid;

use Livewire\Component;
use Livewire\WithPagination;

class RiwayatAktivitas extends Component
{
    use WithPagination;

    public string $filterType = 'all';
    public string $search = '';

    public function setFilter(string $type): void
    {
        $this->filterType = $type;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function formatLogItem($log): array
    {
        $subjectType = str_replace('App\\Models\\', '', $log->subject_type ?? '');
        $description = (string) ($log->description ?? '');
        
        $modelNames = [
            'Nilai' => 'Nilai Akademik',
            'NilaiSumatifTp' => 'Nilai Sumatif Pembelajaran',
            'NilaiSas' => 'Nilai Akhir Semester (SAS)',
            'NilaiTahfidz' => 'Penilaian & Setoran Tahfizh',
            'NilaiP5' => 'Penilaian Projek P5',
            'Rapor' => 'Rapor Akademik',
            'RaporDetail' => 'Catatan Rapor Akademik',
            'RaporTahfidzDetail' => 'Catatan Rapor Tahfizh',
            'JadwalRemedial' => 'Jadwal Remedial',
            'AbsensiSiswa' => 'Presensi Kehadiran',
            'Pembayaran' => 'Pembayaran SPP / Sekolah',
            'Tagihan' => 'Tagihan Pendidikan',
            'Tabungan' => 'Tabungan Santri',
            'Siswa' => 'Biodata Santri',
            'User' => 'Akun Pengguna',
        ];

        // Determine category and title
        if (in_array($subjectType, ['Nilai', 'NilaiSumatifTp', 'NilaiSas', 'NilaiTahfidz', 'NilaiP5', 'Rapor', 'RaporDetail', 'RaporTahfidzDetail', 'JadwalRemedial'])) {
            $type = 'nilai';
            $category = 'Nilai & Rapor';
            $title = $modelNames[$subjectType] ?? 'Pembaruan Nilai';
        } elseif ($subjectType === 'AbsensiSiswa') {
            $type = 'kehadiran';
            $category = 'Presensi Siswa';
            $title = 'Pencatatan Kehadiran';
        } elseif (in_array($subjectType, ['Pembayaran', 'Tagihan', 'Tabungan', 'JenisTagihan'])) {
            $type = 'keuangan';
            $category = 'Administrasi Keuangan';
            $title = $modelNames[$subjectType] ?? 'Transaksi Keuangan';
        } elseif ($subjectType === 'Siswa') {
            $type = 'sistem';
            $category = 'Data Profil';
            $title = 'Pembaruan Profil Santri';
        } else {
            $type = 'sistem';
            $category = 'Informasi Akun';
            $title = 'Pemberitahuan Sistem';
        }

        // Clean description & eliminate technical identifiers/IDs
        $cleanDesc = $description;

        // Pattern: "Membuat data Nilai (#14)" or "Memperbarui data AbsensiSiswa (#102)"
        if (preg_match('/^(Membuat|Memperbarui|Menghapus)\s+data\s+(\w+)(?:\s*\((.*?)\))?/i', $cleanDesc, $matches)) {
            $action = strtolower($matches[1]);
            $model = $matches[2];
            $identifier = $matches[3] ?? '';

            $friendlyModel = $modelNames[$model] ?? 'data akademik';
            
            // If identifier is a database ID like "#14" or "14", remove it
            $cleanIdentifier = preg_replace('/^#?\d+$/', '', trim($identifier));
            
            $actionPhrase = match ($action) {
                'membuat' => "Pencatatan {$friendlyModel} baru",
                'memperbarui' => "Pembaruan catatan {$friendlyModel}",
                'menghapus' => "Penyesuaian catatan {$friendlyModel}",
                default => "Aktivitas pada {$friendlyModel}",
            };

            if (!empty($cleanIdentifier) && !preg_match('/^#\d+$/', $cleanIdentifier)) {
                $cleanDesc = "{$actionPhrase} ({$cleanIdentifier})";
            } else {
                $cleanDesc = $actionPhrase;
            }
        }

        // Clean out any remaining ID patterns (e.g., "(#123)", " ID 123", "ID #123", "#123")
        $cleanDesc = preg_replace('/\s*\(\s*#?\d+\s*\)/', '', $cleanDesc);
        $cleanDesc = preg_replace('/\s+ID\s+#?\d+/i', '', $cleanDesc);
        $cleanDesc = preg_replace('/\s*#\d+/', '', $cleanDesc);
        $cleanDesc = trim($cleanDesc);

        if (empty($cleanDesc)) {
            $cleanDesc = 'Pencatatan aktivitas pada akun santri.';
        }

        // Actor formatting
        $actor = $log->causer ? $log->causer->nama : 'Petugas / Ustadz';

        return [
            'type' => $type,
            'category' => $category,
            'title' => $title,
            'time' => $log->created_at ? $log->created_at->isoFormat('D MMMM Y, HH:mm') : '-',
            'time_ago' => $log->created_at ? $log->created_at->diffForHumans() : '-',
            'description' => $cleanDesc,
            'actor' => $actor,
        ];
    }

    public function getActivityLogsProperty()
    {
        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            return collect();
        }

        if (!class_exists(\Spatie\Activitylog\Models\Activity::class)) {
            return collect();
        }

        $query = \Spatie\Activitylog\Models\Activity::where('siswa_id', $siswa->id)
            ->with('causer')
            ->orderBy('created_at', 'desc');

        $activities = $query->limit(50)->get();

        $formatted = $activities->map(fn($log) => $this->formatLogItem($log));

        if ($this->filterType !== 'all') {
            $formatted = $formatted->where('type', $this->filterType);
        }

        if (!empty(trim($this->search))) {
            $search = strtolower(trim($this->search));
            $formatted = $formatted->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['title']), $search)
                    || str_contains(strtolower($item['description']), $search)
                    || str_contains(strtolower($item['actor']), $search)
                    || str_contains(strtolower($item['category']), $search);
            });
        }

        return $formatted;
    }

    public function render()
    {
        return view('livewire.murid.riwayat-aktivitas', [
            'activityLogs' => $this->activityLogs,
        ])->layout('components.layouts.app', ['title' => 'Riwayat Aktivitas Santri']);
    }
}

