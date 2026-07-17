<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Guru;
use App\Models\GajiGuru;
use App\Models\Peminjaman;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Services\NotificationService;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ManajemenGajiGuru extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $filterStatus = ''; // 'draft', 'dibayar'
    public string $filterBulan = '';
    public string $filterTahun = '';

    // Modals & Forms State
    public bool $showGenerateModal = false;
    public string $generateBulan = 'Januari';
    public int $generateTahun = 2026;

    public bool $showEditModal = false;
    public ?int $editingId = null;
    public float $editGajiPokok = 0.00;
    public float $editInsentifBpjs = 0.00;
    public float $editInsentifMaghrib = 0.00;
    public float $editPotonganPinjaman = 0.00;
    public float $editPotonganLainnya = 0.00;
    public float $editTotalDiterima = 0.00;
    public ?string $editGuruNama = '';

    public array $listBulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    public function mount()
    {
        $this->generateTahun = intval(date('Y'));
        $this->filterTahun = date('Y');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterBulan()
    {
        $this->resetPage();
    }

    public function updatingFilterTahun()
    {
        $this->resetPage();
    }

    // Modal control for generating drafts
    public function openGenerateModal()
    {
        $this->showGenerateModal = true;
    }

    public function closeGenerateModal()
    {
        $this->showGenerateModal = false;
    }

    // Generate Salary Drafts for all active teachers
    public function generateDrafts()
    {
        $activeGurus = Guru::where('status_aktif', true)->get();

        if ($activeGurus->isEmpty()) {
            session()->flash('error', 'Tidak ada guru aktif yang ditemukan.');
            $this->closeGenerateModal();
            return;
        }

        $createdCount = 0;

        foreach ($activeGurus as $guru) {
            // Check uniqueness constraint
            $exists = GajiGuru::where('guru_id', $guru->id)
                ->where('bulan', $this->generateBulan)
                ->where('tahun', $this->generateTahun)
                ->exists();

            if ($exists) {
                continue;
            }

            // Defaults
            $gajiPokok = 2500000.00;
            $insentifBpjs = 150000.00;
            $insentifMaghrib = 200000.00;
            $potonganLainnya = 0.00;

            // Check active loan
            $activeLoan = Peminjaman::where('guru_id', $guru->id)
                ->where('status', 'berjalan')
                ->where('sisa_pinjaman', '>', 0)
                ->first();

            $potonganPeminjaman = 0.00;
            if ($activeLoan) {
                $potonganPeminjaman = min($activeLoan->cicilan_per_bulan, $activeLoan->sisa_pinjaman);
            }

            $totalDiterima = ($gajiPokok + $insentifBpjs + $insentifMaghrib) - ($potonganPeminjaman + $potonganLainnya);

            GajiGuru::create([
                'guru_id' => $guru->id,
                'bulan' => $this->generateBulan,
                'tahun' => $this->generateTahun,
                'gaji_pokok' => $gajiPokok,
                'insentif_bpjs' => $insentifBpjs,
                'insentif_maghrib_mengaji' => $insentifMaghrib,
                'potongan_peminjaman' => $potonganPeminjaman,
                'potongan_lainnya' => $potonganLainnya,
                'total_diterima' => $totalDiterima,
                'tanggal_bayar' => now()->toDateString(),
                'status' => 'draft',
            ]);

            $createdCount++;
        }

        session()->flash('message', "Draf gaji berhasil digenerate untuk {$createdCount} guru.");
        $this->closeGenerateModal();
    }

    // Modal control for Editing
    public function openEditModal(int $id)
    {
        $gaji = GajiGuru::with('guru.user')->findOrFail($id);

        if ($gaji->status === 'dibayar') {
            session()->flash('error', 'Gaji yang sudah dibayarkan tidak dapat diedit.');
            return;
        }

        $this->editingId = $id;
        $this->editGuruNama = $gaji->guru->user->nama ?? '';
        $this->editGajiPokok = floatval($gaji->gaji_pokok);
        $this->editInsentifBpjs = floatval($gaji->insentif_bpjs);
        $this->editInsentifMaghrib = floatval($gaji->insentif_maghrib_mengaji);
        $this->editPotonganPinjaman = floatval($gaji->potongan_peminjaman);
        $this->editPotonganLainnya = floatval($gaji->potongan_lainnya);
        $this->calculateEditTotal();

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['editingId', 'editGuruNama', 'editGajiPokok', 'editInsentifBpjs', 'editInsentifMaghrib', 'editPotonganPinjaman', 'editPotonganLainnya', 'editTotalDiterima']);
    }

    public function calculateEditTotal()
    {
        $this->editTotalDiterima = ($this->editGajiPokok + $this->editInsentifBpjs + $this->editInsentifMaghrib) - ($this->editPotonganPinjaman + $this->editPotonganLainnya);
    }

    public function saveEdit()
    {
        $this->validate([
            'editGajiPokok' => 'required|numeric|min:0',
            'editInsentifBpjs' => 'required|numeric|min:0',
            'editInsentifMaghrib' => 'required|numeric|min:0',
            'editPotonganPinjaman' => 'required|numeric|min:0',
            'editPotonganLainnya' => 'required|numeric|min:0',
        ]);

        $gaji = GajiGuru::findOrFail($this->editingId);

        if ($gaji->status === 'dibayar') {
            session()->flash('error', 'Gaji yang sudah dibayarkan tidak dapat diedit.');
            $this->closeEditModal();
            return;
        }

        $this->calculateEditTotal();

        $gaji->update([
            'gaji_pokok' => $this->editGajiPokok,
            'insentif_bpjs' => $this->editInsentifBpjs,
            'insentif_maghrib_mengaji' => $this->editInsentifMaghrib,
            'potongan_peminjaman' => $this->editPotonganPinjaman,
            'potongan_lainnya' => $this->editPotonganLainnya,
            'total_diterima' => $this->editTotalDiterima,
        ]);

        session()->flash('message', 'Perubahan draf gaji berhasil disimpan.');
        $this->closeEditModal();
    }

    // Process Payment
    public function paySalary(int $id)
    {
        $gaji = GajiGuru::with('guru.user')->findOrFail($id);

        if ($gaji->status === 'dibayar') {
            session()->flash('error', 'Gaji ini sudah dibayarkan.');
            return;
        }

        DB::transaction(function () use ($gaji) {
            // Find or create category "Gaji Guru"
            $kategori = KategoriPengeluaran::firstOrCreate(
                ['nama' => 'Gaji Guru'],
                ['jenis' => 'operasional']
            );

            // Create expenditure transaction
            $pengeluaran = Pengeluaran::create([
                'kategori_pengeluaran_id' => $kategori->id,
                'jumlah' => $gaji->total_diterima,
                'tanggal' => now()->toDateString(),
                'keterangan' => "Pembayaran Gaji Guru: " . ($gaji->guru->user->nama ?? 'Guru') . " - Periode " . $gaji->bulan . " " . $gaji->tahun,
                'petugas_id' => auth()->id(),
            ]);

            // Deduct loan if any
            if ($gaji->potongan_peminjaman > 0) {
                $activeLoan = Peminjaman::where('guru_id', $gaji->guru_id)
                    ->where('status', 'berjalan')
                    ->where('sisa_pinjaman', '>', 0)
                    ->first();

                if ($activeLoan) {
                    $newSisa = max(0, $activeLoan->sisa_pinjaman - $gaji->potongan_peminjaman);
                    $status = $newSisa <= 0 ? 'lunas' : 'berjalan';

                    $activeLoan->update([
                        'sisa_pinjaman' => $newSisa,
                        'status' => $status
                    ]);
                }
            }

            // Update Gaji status
            $gaji->update([
                'status' => 'dibayar',
                'pengeluaran_id' => $pengeluaran->id,
                'tanggal_bayar' => now()->toDateString(),
            ]);

            // Send notification to the teacher
            if ($gaji->guru->user_id) {
                NotificationService::send(
                    $gaji->guru->user_id,
                    'Gaji Telah Dibayarkan',
                    "Gaji Anda untuk periode {$gaji->bulan} {$gaji->tahun} sebesar Rp " . number_format($gaji->total_diterima, 0, ',', '.') . " telah berhasil ditransfer pada " . date('d-m-Y') . ".",
                    'sistem',
                    ['in_app']
                );
            }
        });

        session()->flash('message', 'Pembayaran gaji berhasil diproses.');
    }

    // Delete Draft
    public function deleteDraft(int $id)
    {
        $gaji = GajiGuru::findOrFail($id);

        if ($gaji->status === 'dibayar') {
            session()->flash('error', 'Gaji yang sudah dibayarkan tidak dapat dihapus.');
            return;
        }

        $gaji->delete();
        session()->flash('message', 'Draf gaji berhasil dihapus.');
    }

    public function render()
    {
        $query = GajiGuru::with(['guru.user', 'pengeluaran']);

        if ($this->search) {
            $query->whereHas('guru.user', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterBulan) {
            $query->where('bulan', $this->filterBulan);
        }

        if ($this->filterTahun) {
            $query->where('tahun', $this->filterTahun);
        }

        $salaries = $query->latest()->paginate(15);

        return view('livewire.finance.manajemen-gaji-guru', [
            'salaries' => $salaries
        ])->layout('components.layouts.app', ['title' => 'Manajemen Gaji Guru']);
    }
}
