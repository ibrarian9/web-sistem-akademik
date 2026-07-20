<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\PengajuanDana;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class PengajuanDanaIndex extends Component
{
    use WithPagination;

    // Filter properties
    public string $filterStatus = 'semua';
    public string $search = '';

    // Form Modal properties
    public bool $showModal = false;
    public string $judul = '';
    public string $kategori = 'Pembelian Buku';
    public float $jumlah = 0.00;
    public string $keterangan = '';
    public ?string $target_realisasi = null;

    // Reject Modal properties
    public bool $showRejectModal = false;
    public ?int $selectedId = null;
    public string $catatan_penolakan = '';

    public array $kategoriOptions = [
        'Pembelian Buku / Literasi',
        'Pembelian Seragam / Baju',
        'Pengadaan Alat Tulis & Kelas',
        'Renovasi & Pemeliharaan Fasilitas',
        'Kegiatan Siswa / Ekstrakurikuler',
        'Lainnya'
    ];

    protected $rules = [
        'judul' => 'required|string|max:255',
        'kategori' => 'required|string',
        'jumlah' => 'required|numeric|min:10000',
        'keterangan' => 'required|string|max:1000',
        'target_realisasi' => 'nullable|date',
    ];

    public function openModal()
    {
        $this->reset(['judul', 'jumlah', 'keterangan', 'target_realisasi']);
        $this->kategori = 'Pembelian Buku / Literasi';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function createPengajuan()
    {
        $this->validate();

        PengajuanDana::create([
            'pemohon_id' => auth()->id(),
            'judul' => $this->judul,
            'kategori' => $this->kategori,
            'nominal' => $this->jumlah,
            'tanggal_pengajuan' => date('Y-m-d'),
            'keterangan' => $this->keterangan,
            'status' => 'menunggu_koordinator',
        ]);

        session()->flash('message', 'Pengajuan penggunaan dana berhasil dibuat.');
        $this->closeModal();
        $this->resetPage();
    }

    public function approveByKoordinator(int $id)
    {
        $pengajuan = PengajuanDana::findOrFail($id);

        if ($pengajuan->status !== 'menunggu_koordinator') {
            session()->flash('error', 'Status pengajuan tidak valid untuk persetujuan Koordinator.');
            return;
        }

        // Threshold Rp 1.000.000
        $newStatus = floatval($pengajuan->nominal) <= 1000000 ? 'disetujui' : 'menunggu_kepala_yayasan';

        $pengajuan->update([
            'status' => $newStatus,
            'tanggal_persetujuan_koordinator' => now(),
            'disetujui_koordinator_id' => auth()->id(),
        ]);

        $msg = $newStatus === 'disetujui' 
            ? 'Pengajuan dana berhasil disetujui oleh Koordinator (di bawah Rp 1 Juta).' 
            : 'Pengajuan dana disetujui Koordinator dan diteruskan ke Kepala Yayasan (di atas Rp 1 Juta).';

        session()->flash('message', $msg);
    }

    public function approveByKepalaYayasan(int $id)
    {
        $pengajuan = PengajuanDana::findOrFail($id);

        if ($pengajuan->status !== 'menunggu_kepala_yayasan') {
            session()->flash('error', 'Status pengajuan tidak valid untuk persetujuan Kepala Yayasan.');
            return;
        }

        $pengajuan->update([
            'status' => 'disetujui',
            'tanggal_persetujuan_kepala_yayasan' => now(),
            'disetujui_kepala_yayasan_id' => auth()->id(),
        ]);

        session()->flash('message', 'Pengajuan dana disetujui oleh Kepala Yayasan.');
    }

    public function openRejectModal(int $id)
    {
        $this->selectedId = $id;
        $this->catatan_penolakan = '';
        $this->showRejectModal = true;
    }

    public function rejectPengajuan()
    {
        if (!$this->selectedId) return;

        $pengajuan = PengajuanDana::findOrFail($this->selectedId);

        $pengajuan->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $this->catatan_penolakan,
        ]);

        session()->flash('message', 'Pengajuan dana telah ditolak.');
        $this->showRejectModal = false;
        $this->selectedId = null;
    }

    public function realisasikanDana(int $id)
    {
        $pengajuan = PengajuanDana::findOrFail($id);

        if ($pengajuan->status !== 'disetujui') {
            session()->flash('error', 'Pengajuan dana belum disetujui sepenuhnya.');
            return;
        }

        DB::transaction(function () use ($pengajuan) {
            // Find or create default category
            $kategoriExp = KategoriPengeluaran::firstOrCreate(
                ['nama' => 'Operasional Yayasan'],
                ['deskripsi' => 'Pengeluaran operasional umum yayasan']
            );

            $kategoriId = $kategoriExp->id;

            // 1. Create Pengeluaran record automatically
            $pengeluaran = Pengeluaran::create([
                'kategori_pengeluaran_id' => $kategoriId,
                'jumlah' => $pengajuan->nominal,
                'tanggal' => date('Y-m-d'),
                'keterangan' => 'Realisasi Pengajuan - ' . $pengajuan->judul . ' (' . $pengajuan->kategori . ')',
                'petugas_id' => auth()->id(),
            ]);

            // 2. Update Pengajuan Status to direalisasi
            $pengajuan->update([
                'status' => 'direalisasi',
                'pengeluaran_id' => $pengeluaran->id,
            ]);
        });

        session()->flash('message', 'Dana berhasil direalisasikan & otomatis dicatat pada Pengeluaran Kas.');
    }

    public function render()
    {
        $user = auth()->user();
        $userRole = $user ? $user->role : '';

        $query = PengajuanDana::with(['pemohon', 'disetujuiKoordinator', 'disetujuiKepalaYayasan', 'pengeluaran'])
            ->latest();

        if ($this->filterStatus !== 'semua') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('judul', 'like', '%' . $this->search . '%')
                  ->orWhere('no_pengajuan', 'like', '%' . $this->search . '%')
                  ->orWhere('kategori', 'like', '%' . $this->search . '%');
            });
        }

        $pengajuans = $query->paginate(15);

        return view('livewire.finance.pengajuan-dana', [
            'pengajuans' => $pengajuans,
            'userRole' => $userRole,
        ])->layout('components.layouts.app', ['title' => 'Pengajuan Penggunaan Dana']);
    }
}
