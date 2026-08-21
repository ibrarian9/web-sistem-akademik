<?php

namespace App\Livewire\TataUsaha;

use App\Models\Guru;
use App\Models\RiwayatSurat;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithPagination;

class ManajemenSurat extends Component
{
    use WithPagination;

    // Active Tab & Template
    public string $activeTab = 'buat'; // 'buat' or 'riwayat'
    public string $jenis_surat = 'aktif_sekolah'; // 'aktif_sekolah', 'pengalaman_kerja', 'menerima_pindah', 'pindah_sekolah'

    // Dropdown Selectors
    public ?int $selected_siswa_id = null;
    public ?int $selected_guru_id = null;

    // Active Surat ID
    public ?int $suratId = null;

    // Common Letter Fields
    public string $nomor_surat = '';
    public string $tanggal_surat = '';
    public string $kota_surat = 'Pekanbaru';
    public string $penandatangan_nama = 'RINA, S.Pd., Gr.';
    public string $penandatangan_jabatan = 'Kepala SD Tahfizh F3';
    public string $penandatangan_niy = '198010052201907001';

    // Penerima Fields (Siswa / Guru)
    public string $penerima_nama = '';
    public string $penerima_nisn = '';
    public string $penerima_nis = '';
    public string $penerima_nik = '';
    public string $penerima_niy = '';
    public string $penerima_ttl = '';
    public string $penerima_gender = 'Laki-Laki';
    public string $penerima_kelas = '';
    public string $penerima_alamat = '';
    public string $penerima_pendidikan = '';

    // Specific Fields (Ortu, Mutasi, Pengalaman Kerja)
    public string $ortu_nama = '';
    public string $ortu_pekerjaan = '';
    public string $ortu_hubungan = 'Orang Tua';
    public string $sekolah_asal = '';
    public string $sekolah_tujuan = '';
    public string $alasan_pindah = 'Permintaan Orang Tua';
    public string $posisi_kerja = 'Guru Pengajar';
    public string $periode_kerja = 'November 2021 sampai Maret 2023';

    // Print & Preview State
    public bool $showPrintModal = false;
    public int $perPage = 10;
    public string $searchRiwayat = '';

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatingSearchRiwayat()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->tanggal_surat = date('Y-m-d');
        $this->autoGenerateNomorSurat();
    }

    public function updatedJenisSurat()
    {
        $this->selected_siswa_id = null;
        $this->selected_guru_id = null;
        $this->resetPenerimaFields();
        $this->autoGenerateNomorSurat();
    }

    public function updatedSelectedSiswaId()
    {
        if (!$this->selected_siswa_id) {
            return;
        }

        $siswa = Siswa::with(['user', 'kelas', 'kelasTahfidz'])->find($this->selected_siswa_id);
        if ($siswa) {
            $this->penerima_nama = $siswa->user->nama ?? '';
            $this->penerima_nisn = $siswa->nisn ?? '';
            $this->penerima_nis = $siswa->nis ?? '';
            $this->penerima_gender = $siswa->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-Laki';
            $this->penerima_kelas = $siswa->kelas->nama_kelas ?? '1';
            $this->penerima_alamat = $siswa->alamat ?? $siswa->user->alamat ?? '';
            
            $ttlArr = [];
            if ($siswa->tempat_lahir) $ttlArr[] = $siswa->tempat_lahir;
            if ($siswa->tanggal_lahir) $ttlArr[] = $siswa->tanggal_lahir->format('d F Y');
            $this->penerima_ttl = implode(', ', $ttlArr);

            $this->ortu_nama = $siswa->nama_wali ?? '';
            $this->ortu_pekerjaan = 'Wiraswasta';
        }
    }

    public function updatedSelectedGuruId()
    {
        if (!$this->selected_guru_id) {
            return;
        }

        $guru = Guru::with('user')->find($this->selected_guru_id);
        if ($guru) {
            $this->penerima_nama = $guru->user->nama ?? '';
            $this->penerima_niy = $guru->niy ?? $guru->nip ?? '';
            $this->penerima_nik = $guru->nik ?? '';
            $this->penerima_pendidikan = $guru->pendidikan ?? 'S1';
            $this->penerima_alamat = $guru->user->alamat ?? $guru->alamat ?? '';

            $ttlArr = [];
            if ($guru->tempat_lahir) $ttlArr[] = $guru->tempat_lahir;
            if ($guru->tanggal_lahir) $ttlArr[] = $guru->tanggal_lahir->format('d F Y');
            $this->penerima_ttl = implode(', ', $ttlArr);

            $this->posisi_kerja = 'Guru Pengajar SD Tahfizh F3';
            if ($guru->tanggal_masuk) {
                $this->periode_kerja = $guru->tanggal_masuk->format('d F Y') . ' sampai Sekarang';
            }
        }
    }

    public function autoGenerateNomorSurat()
    {
        $romanMonths = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
        $m = date('n');
        $y = date('Y');
        $rm = $romanMonths[$m] ?? 'VIII';

        $cnt = RiwayatSurat::whereYear('created_at', $y)->count() + 1;
        $seqNum = str_pad($cnt, 3, '0', STR_PAD_LEFT);

        if ($this->jenis_surat === 'pengalaman_kerja') {
            $this->nomor_surat = "{$seqNum}/SKPK/SDTF3/{$rm}/{$y}";
        } else {
            $this->nomor_surat = "{$seqNum}/SDTF3/{$rm}/{$y}";
        }
    }

    public function simpanDanCetak()
    {
        $this->validate([
            'nomor_surat' => 'required|string',
            'penerima_nama' => 'required|string',
            'tanggal_surat' => 'required|date',
            'penandatangan_nama' => 'required|string',
        ]);

        $payload = $this->getPayload();

        $surat = RiwayatSurat::updateOrCreate(
            ['nomor_surat' => $this->nomor_surat],
            [
                'jenis_surat' => $this->jenis_surat,
                'penerima_nama' => $this->penerima_nama,
                'tanggal_surat' => $this->tanggal_surat,
                'payload_json' => $payload,
                'created_by' => auth()->id(),
            ]
        );

        $this->suratId = $surat->id;
        session()->flash('message', 'Surat berhasil disimpan & siap dicetak/diunduh PDF.');
        $this->showPrintModal = true;
    }

    public function loadRiwayatSurat($id)
    {
        $surat = RiwayatSurat::findOrFail($id);
        $payload = $surat->payload_json ?? [];

        $this->suratId = $surat->id;
        $this->jenis_surat = $surat->jenis_surat;
        $this->nomor_surat = $surat->nomor_surat;
        $this->tanggal_surat = $surat->tanggal_surat ? $surat->tanggal_surat->format('Y-m-d') : date('Y-m-d');
        
        $this->penerima_nama = $payload['penerima_nama'] ?? '';
        $this->penerima_nisn = $payload['penerima_nisn'] ?? '';
        $this->penerima_nis = $payload['penerima_nis'] ?? '';
        $this->penerima_nik = $payload['penerima_nik'] ?? '';
        $this->penerima_niy = $payload['penerima_niy'] ?? '';
        $this->penerima_ttl = $payload['penerima_ttl'] ?? '';
        $this->penerima_gender = $payload['penerima_gender'] ?? 'Laki-Laki';
        $this->penerima_kelas = $payload['penerima_kelas'] ?? '';
        $this->penerima_alamat = $payload['penerima_alamat'] ?? '';
        $this->penerima_pendidikan = $payload['penerima_pendidikan'] ?? '';

        $this->ortu_nama = $payload['ortu_nama'] ?? '';
        $this->ortu_pekerjaan = $payload['ortu_pekerjaan'] ?? '';
        $this->ortu_hubungan = $payload['ortu_hubungan'] ?? 'Orang Tua';
        $this->sekolah_asal = $payload['sekolah_asal'] ?? '';
        $this->sekolah_tujuan = $payload['sekolah_tujuan'] ?? '';
        $this->alasan_pindah = $payload['alasan_pindah'] ?? '';
        $this->posisi_kerja = $payload['posisi_kerja'] ?? '';
        $this->periode_kerja = $payload['periode_kerja'] ?? '';

        $this->showPrintModal = true;
    }

    public function downloadCurrentPdf()
    {
        $payload = $this->getPayload();
        $pdf = Pdf::loadView('pdf.surat-template', $payload);
        $filename = str_replace('/', '_', $this->nomor_surat) . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function downloadPdfById($id)
    {
        $surat = RiwayatSurat::findOrFail($id);
        $payload = $surat->payload_json ?? [];
        $pdf = Pdf::loadView('pdf.surat-template', $payload);
        $filename = str_replace('/', '_', $surat->nomor_surat) . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function deleteRiwayat($id)
    {
        RiwayatSurat::findOrFail($id)->delete();
        session()->flash('message', 'Riwayat surat berhasil dihapus.');
    }

    private function getPayload(): array
    {
        return [
            'jenis_surat' => $this->jenis_surat,
            'nomor_surat' => $this->nomor_surat,
            'tanggal_surat' => $this->tanggal_surat,
            'kota_surat' => $this->kota_surat,
            'penandatangan_nama' => $this->penandatangan_nama,
            'penandatangan_jabatan' => $this->penandatangan_jabatan,
            'penandatangan_niy' => $this->penandatangan_niy,
            'penerima_nama' => $this->penerima_nama,
            'penerima_nisn' => $this->penerima_nisn,
            'penerima_nis' => $this->penerima_nis,
            'penerima_nik' => $this->penerima_nik,
            'penerima_niy' => $this->penerima_niy,
            'penerima_ttl' => $this->penerima_ttl,
            'penerima_gender' => $this->penerima_gender,
            'penerima_kelas' => $this->penerima_kelas,
            'penerima_alamat' => $this->penerima_alamat,
            'penerima_pendidikan' => $this->penerima_pendidikan,
            'ortu_nama' => $this->ortu_nama,
            'ortu_pekerjaan' => $this->ortu_pekerjaan,
            'ortu_hubungan' => $this->ortu_hubungan,
            'sekolah_asal' => $this->sekolah_asal,
            'sekolah_tujuan' => $this->sekolah_tujuan,
            'alasan_pindah' => $this->alasan_pindah,
            'posisi_kerja' => $this->posisi_kerja,
            'periode_kerja' => $this->periode_kerja,
        ];
    }

    private function resetPenerimaFields()
    {
        $this->penerima_nama = '';
        $this->penerima_nisn = '';
        $this->penerima_nis = '';
        $this->penerima_nik = '';
        $this->penerima_niy = '';
        $this->penerima_ttl = '';
        $this->penerima_gender = 'Laki-Laki';
        $this->penerima_kelas = '';
        $this->penerima_alamat = '';
        $this->penerima_pendidikan = '';
        $this->ortu_nama = '';
        $this->ortu_pekerjaan = '';
        $this->sekolah_tujuan = '';
        $this->sekolah_asal = '';
    }

    public function render()
    {
        $siswas = Siswa::with('user')->where('status', 'aktif')->get();
        $gurus = Guru::with('user')->where('status_aktif', true)->get();

        $riwayats = RiwayatSurat::with('creator')
            ->when(!empty($this->searchRiwayat), function ($q) {
                $q->where('nomor_surat', 'like', '%' . $this->searchRiwayat . '%')
                  ->orWhere('penerima_nama', 'like', '%' . $this->searchRiwayat . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.tata-usaha.manajemen-surat', [
            'siswas' => $siswas,
            'gurus' => $gurus,
            'riwayats' => $riwayats,
        ])->layout('components.layouts.app', ['title' => 'Layanan Persuratan & Dokumen Resmi']);
    }
}
