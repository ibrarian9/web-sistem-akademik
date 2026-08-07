<?php

namespace App\Livewire\Murid;

use App\Models\NilaiTahfidz;
use App\Models\Notifikasi;
use App\Models\Semester;
use App\Models\Siswa;
use Livewire\Component;

class SetoranTahfidz extends Component
{
    public $semester_id;
    public $selectedRecordId = null;

    // Feedback Form State
    public bool $showFeedbackModal = false;
    public $tanggapan_orang_tua = '';
    public $dikirim_oleh_nama = '';

    protected $rules = [
        'tanggapan_orang_tua' => 'required|string|min:5|max:1000',
        'dikirim_oleh_nama' => 'nullable|string|max:255',
    ];

    protected $messages = [
        'tanggapan_orang_tua.required' => 'Tanggapan/Catatan Orang Tua wajib diisi.',
        'tanggapan_orang_tua.min' => 'Tanggapan minimal 5 karakter.',
        'tanggapan_orang_tua.max' => 'Tanggapan maksimal 1000 karakter.',
    ];

    public function mount()
    {
        $activeSemester = Semester::where('status_aktif', true)->first() ?? Semester::first();
        if ($activeSemester) {
            $this->semester_id = $activeSemester->id;
        }

        $user = auth()->user();
        if ($user) {
            $this->dikirim_oleh_nama = 'Orang Tua / Wali dari ' . ($user->nama ?? 'Santri');
        }
    }

    public function openFeedbackModal($recordId)
    {
        $rec = NilaiTahfidz::find($recordId);
        if ($rec) {
            $this->selectedRecordId = $rec->id;
            $this->tanggapan_orang_tua = $rec->tanggapan_orang_tua ?? '';
            $this->showFeedbackModal = true;
        }
    }

    public function closeFeedbackModal()
    {
        $this->showFeedbackModal = false;
        $this->tanggapan_orang_tua = '';
        $this->selectedRecordId = null;
    }

    public function submitFeedback()
    {
        $this->validate();

        $rec = NilaiTahfidz::find($this->selectedRecordId);
        if (!$rec) {
            session()->flash('error', 'Catatan setoran tidak ditemukan.');
            return;
        }

        $rec->update([
            'tanggapan_orang_tua' => $this->tanggapan_orang_tua,
            'tanggal_tanggapan' => now(),
            'dikirim_oleh_nama' => $this->dikirim_oleh_nama ?: 'Orang Tua / Wali Santri',
        ]);

        // Send notification to Guru Tahfidz if assigned
        $siswa = Siswa::with('kelas.guruTahfidz.user')->find($rec->siswa_id);
        if ($siswa && $siswa->kelas && $siswa->kelas->guruTahfidz && $siswa->kelas->guruTahfidz->user) {
            Notifikasi::create([
                'user_id' => $siswa->kelas->guruTahfidz->user->id,
                'siswa_id' => $siswa->id,
                'judul' => 'Tanggapan Orang Tua Santri Baru',
                'isi_pesan' => 'Orang tua dari santri ' . ($siswa->user->nama ?? 'Santri') . ' telah memberikan feedback setoran Tahfizh.',
                'jenis' => 'sistem',
                'status_kirim' => 'terkirim',
            ]);
        }


        $this->showFeedbackModal = false;
        session()->flash('message', 'Feedback/Tanggapan Orang Tua berhasil dikirimkan ke Ustadz Pembimbing.');
    }

    public function render()
    {
        $user = auth()->user();
        $siswa = Siswa::with(['kelas.guruTahfidz.user'])->where('user_id', $user->id)->first();

        $semesters = Semester::orderBy('id', 'desc')->get();
        $setoranList = collect();
        $summary = [
            'total_setoran' => 0,
            'max_juz' => 1,
            'last_surah' => '-',
            'predikat' => 'Sangat Baik',
        ];

        if ($siswa) {
            $query = NilaiTahfidz::where('siswa_id', $siswa->id);
            if ($this->semester_id) {
                $query->where('semester_id', $this->semester_id);
            }
            $setoranList = $query->orderBy('updated_at', 'desc')->get();

            if ($setoranList->isNotEmpty()) {
                $summary['total_setoran'] = $setoranList->count();
                $summary['max_juz'] = $setoranList->max('juz') ?: 1;
                $latestRec = $setoranList->first();
                $summary['last_surah'] = $latestRec->surah ?: ($latestRec->materi_ziyadah ?: 'Al-Baqarah');
                $summary['predikat'] = $latestRec->predikat_keagamaan ?: 'Sangat Baik';
            }
        }

        return view('livewire.murid.setoran-tahfidz', [
            'siswa' => $siswa,
            'semesters' => $semesters,
            'setoranList' => $setoranList,
            'summary' => $summary,
        ])->layout('components.layouts.app', ['title' => 'Setoran Mutaba\'ah Tahfizh & Feedback']);
    }
}
