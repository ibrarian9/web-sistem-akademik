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

    // Modals & Forms State - Generate Massal dengan Pratinjau & Edit Pra-Generate
    public bool $showGenerateModal = false;
    public string $generateBulan = 'Januari';
    public int $generateTahun = 2026;
    public array $generateItems = [];
    public bool $generateSelectAll = true;

    // Modals & Forms State - Buat Gaji Manual (Satuan)
    public bool $showCreateModal = false;
    public ?int $createGuruId = null;
    public string $createBulan = 'Januari';
    public int $createTahun = 2026;
    public ?string $createJabatan = '';
    public ?string $createJamKerja = '07.00-14.00';
    public ?string $createSumberDana = 'Yayasan';
    public string $createStatus = 'draft'; // 'draft' or 'dibayar'
    public ?string $createTanggalBayar = '';

    public float $createGajiPokok = 0.00;
    public float $createGajiBerkala = 0.00;
    public int $createJumlahEkskul = 0;
    public float $createHonorEkskul = 0.00;
    public float $createInsentif = 0.00;
    public float $createInsentifBpjs = 0.00;
    public float $createInsentifMaghrib = 0.00;
    public float $createTotalBruto = 0.00;

    public float $createPotonganSosial = 10000.00;
    public float $createPotonganPinjaman = 0.00;
    public float $createPotonganBpjstk = 0.00;
    public float $createPotonganLainnya = 0.00;
    public float $createTotalPotongan = 0.00;
    public float $createTotalDiterima = 0.00;

    // Modals & Forms State - Edit Gaji
    public bool $showEditModal = false;
    public ?int $editingId = null;
    public ?string $editGuruNama = '';
    public ?string $editJabatan = '';
    public ?string $editJamKerja = '07.00-14.00';
    public ?string $editSumberDana = 'Yayasan';
    public string $editStatus = 'draft';
    public ?string $editTanggalBayar = '';
    public string $editBulan = 'Januari';
    public int $editTahun = 2026;

    // Earnings
    public float $editGajiPokok = 0.00;
    public float $editGajiBerkala = 0.00;
    public int $editJumlahEkskul = 0;
    public float $editHonorEkskul = 0.00;
    public float $editInsentif = 0.00;
    public float $editInsentifBpjs = 0.00;
    public float $editInsentifMaghrib = 0.00;
    public float $editTotalBruto = 0.00;

    // Deductions
    public float $editPotonganSosial = 10000.00;
    public float $editPotonganPinjaman = 0.00;
    public float $editPotonganBpjstk = 0.00;
    public float $editPotonganLainnya = 0.00;
    public float $editTotalPotongan = 0.00;

    // Net THP
    public float $editTotalDiterima = 0.00;
    public string $edit_alasan = '';

    // Detail Modal State
    public bool $showDetailModal = false;
    public ?GajiGuru $selectedSalaryDetail = null;

    // Salary History Modal State
    public bool $showHistoryModal = false;
    public ?int $historyGuruId = null;
    public ?Guru $historyGuru = null;
    public string $historyFilterTahun = '';

    // PDF Preview Modal
    public bool $showPreviewModal = false;
    public ?int $previewSalaryId = null;
    public $previewSalary = null;

    // Bulk Actions State
    public array $selectedGajiIds = [];
    public bool $selectAll = false;

    public array $listBulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    public function mount()
    {
        $this->generateTahun = intval(date('Y'));
        $this->createTahun = intval(date('Y'));
        $this->filterTahun = date('Y');
        $this->generateBulan = $this->listBulan[intval(date('n')) - 1] ?? 'Januari';
        $this->createBulan = $this->listBulan[intval(date('n')) - 1] ?? 'Januari';
        $this->createTanggalBayar = date('Y-m-d');
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $query = GajiGuru::query();
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
            $this->selectedGajiIds = $query->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedGajiIds = [];
        }
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

    // =========================================================================
    // 1. GENERATE DRAF MASSAL DENGAN PRATINJAU & EDIT LANGSUNG PRA-GENERATE
    // =========================================================================
    public function openGenerateModal()
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->loadGeneratePreview();
        $this->showGenerateModal = true;
    }

    public function updatedGenerateBulan()
    {
        $this->loadGeneratePreview();
    }

    public function updatedGenerateTahun()
    {
        $this->loadGeneratePreview();
    }

    public function updatedGenerateSelectAll($value)
    {
        foreach ($this->generateItems as $guruId => $item) {
            $this->generateItems[$guruId]['selected'] = (bool)$value;
        }
    }

    public function loadGeneratePreview()
    {
        $activeGurus = Guru::with('user')->where('status_aktif', true)->get();
        $this->generateItems = [];

        foreach ($activeGurus as $guru) {
            $alreadyExists = GajiGuru::where('guru_id', $guru->id)
                ->where('bulan', $this->generateBulan)
                ->where('tahun', $this->generateTahun)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            $isTetap = in_array(strtolower($guru->status_kepegawaian ?? ''), ['tetap_yayasan', 'gty', 'pns']);
            $gajiPokok = $isTetap ? 2000000.00 : 1000000.00;
            $gajiBerkala = $isTetap ? 120000.00 : 0.00;
            $jumlahEkskul = 0;
            $honorEkskul = 0.00;
            $insentif = $isTetap ? 500000.00 : 150000.00;
            $insentifBpjs = $isTetap ? 17928.00 : 0.00;
            $insentifMaghrib = 0.00;

            $potonganSosial = 10000.00;
            $potonganBpjstk = $isTetap ? 17928.00 : 0.00;
            $potonganLainnya = 0.00;

            $activeLoan = Peminjaman::where('guru_id', $guru->id)
                ->where('status', 'berjalan')
                ->where('sisa_pinjaman', '>', 0)
                ->first();

            $potonganPeminjaman = 0.00;
            if ($activeLoan) {
                $potonganPeminjaman = min(floatval($activeLoan->cicilan_per_bulan), floatval($activeLoan->sisa_pinjaman));
            }

            $totalBruto = floatval($gajiPokok) + floatval($gajiBerkala) + floatval($honorEkskul) + floatval($insentif) + floatval($insentifBpjs) + floatval($insentifMaghrib);
            $totalPotongan = floatval($potonganSosial) + floatval($potonganPeminjaman) + floatval($potonganBpjstk) + floatval($potonganLainnya);
            $totalDiterima = max(0, $totalBruto - $totalPotongan);

            $jabatan = $guru->jabatan ?: ($guru->jenis_guru === 'tahfidz' ? 'Wali Tahfizh' : 'Guru Pengajar');
            $jamKerja = $isTetap ? '07.00-14.00 (Fleksibel)' : '07.00-14.00';

            $this->generateItems[$guru->id] = [
                'selected' => true,
                'guru_id' => $guru->id,
                'nama' => $guru->user->nama ?? '-',
                'nip' => $guru->niy ?? ($guru->nip ?? '-'),
                'jabatan' => $jabatan,
                'jam_kerja' => $jamKerja,
                'sumber_dana' => 'Yayasan',
                'gaji_pokok' => floatval($gajiPokok),
                'gaji_berkala' => floatval($gajiBerkala),
                'jumlah_ekskul' => intval($jumlahEkskul),
                'honor_ekskul' => floatval($honorEkskul),
                'insentif' => floatval($insentif),
                'insentif_bpjs' => floatval($insentifBpjs),
                'insentif_maghrib_mengaji' => floatval($insentifMaghrib),
                'potongan_sosial' => floatval($potonganSosial),
                'potongan_peminjaman' => floatval($potonganPeminjaman),
                'potongan_bpjstk' => floatval($potonganBpjstk),
                'potongan_lainnya' => floatval($potonganLainnya),
                'total_bruto' => floatval($totalBruto),
                'total_diterima' => floatval($totalDiterima),
            ];
        }

        $this->generateSelectAll = true;
    }

    public function recalculateGenerateRow($guruId)
    {
        if (isset($this->generateItems[$guruId])) {
            $item = &$this->generateItems[$guruId];
            $bruto = floatval($item['gaji_pokok'] ?? 0)
                + floatval($item['gaji_berkala'] ?? 0)
                + floatval($item['honor_ekskul'] ?? 0)
                + floatval($item['insentif'] ?? 0)
                + floatval($item['insentif_bpjs'] ?? 0)
                + floatval($item['insentif_maghrib_mengaji'] ?? 0);

            $potongan = floatval($item['potongan_sosial'] ?? 0)
                + floatval($item['potongan_peminjaman'] ?? 0)
                + floatval($item['potongan_bpjstk'] ?? 0)
                + floatval($item['potongan_lainnya'] ?? 0);

            $item['total_bruto'] = $bruto;
            $item['total_diterima'] = max(0, $bruto - $potongan);
        }
    }

    public function closeGenerateModal()
    {
        $this->showGenerateModal = false;
        $this->generateItems = [];
    }

    public function generateDrafts()
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        if (empty($this->generateItems)) {
            $this->loadGeneratePreview();
        }

        $selectedItems = array_filter($this->generateItems, fn($item) => !empty($item['selected']));

        if (empty($selectedItems)) {
            session()->flash('error', 'Silakan pilih setidaknya satu guru untuk di-generate.');
            return;
        }

        $createdCount = 0;

        DB::transaction(function () use ($selectedItems, &$createdCount) {
            foreach ($selectedItems as $item) {
                $exists = GajiGuru::where('guru_id', $item['guru_id'])
                    ->where('bulan', $this->generateBulan)
                    ->where('tahun', $this->generateTahun)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $bruto = floatval($item['gaji_pokok'])
                    + floatval($item['gaji_berkala'])
                    + floatval($item['honor_ekskul'])
                    + floatval($item['insentif'])
                    + floatval($item['insentif_bpjs'])
                    + floatval($item['insentif_maghrib_mengaji']);

                $potongan = floatval($item['potongan_sosial'])
                    + floatval($item['potongan_peminjaman'])
                    + floatval($item['potongan_bpjstk'])
                    + floatval($item['potongan_lainnya']);

                $thp = max(0, $bruto - $potongan);

                GajiGuru::create([
                    'guru_id' => $item['guru_id'],
                    'bulan' => $this->generateBulan,
                    'tahun' => $this->generateTahun,
                    'gaji_pokok' => floatval($item['gaji_pokok']),
                    'gaji_berkala' => floatval($item['gaji_berkala']),
                    'jumlah_ekskul' => intval($item['jumlah_ekskul']),
                    'honor_ekskul' => floatval($item['honor_ekskul']),
                    'insentif' => floatval($item['insentif']),
                    'insentif_bpjs' => floatval($item['insentif_bpjs']),
                    'insentif_maghrib_mengaji' => floatval($item['insentif_maghrib_mengaji']),
                    'potongan_sosial' => floatval($item['potongan_sosial']),
                    'potongan_peminjaman' => floatval($item['potongan_peminjaman']),
                    'potongan_bpjstk' => floatval($item['potongan_bpjstk']),
                    'potongan_lainnya' => floatval($item['potongan_lainnya']),
                    'total_bruto' => $bruto,
                    'total_diterima' => $thp,
                    'tanggal_bayar' => now()->toDateString(),
                    'status' => 'draft',
                    'sumber_dana' => $item['sumber_dana'] ?: 'Yayasan',
                    'jam_kerja' => $item['jam_kerja'] ?: '07.00-14.00',
                    'jabatan' => $item['jabatan'] ?: 'Guru',
                ]);

                $createdCount++;
            }
        });

        session()->flash('message', "Draf gaji berhasil digenerate dan disimpan untuk {$createdCount} pegawai.");
        $this->closeGenerateModal();
    }

    // ==========================================
    // 2. BUAT GAJI MANUAL (SATUAN)
    // ==========================================
    public function openCreateModal()
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $firstGuru = Guru::with('user')->where('status_aktif', true)->first();
        $this->createGuruId = $firstGuru?->id;
        $this->createBulan = $this->listBulan[intval(date('n')) - 1] ?? 'Januari';
        $this->createTahun = intval(date('Y'));
        $this->createTanggalBayar = date('Y-m-d');
        $this->createStatus = 'draft';

        if ($firstGuru) {
            $this->populateCreateDefaults($firstGuru);
        }

        $this->showCreateModal = true;
    }

    public function updatedCreateGuruId($guruId)
    {
        if ($guruId) {
            $guru = Guru::with('user')->find($guruId);
            if ($guru) {
                $this->populateCreateDefaults($guru);
            }
        }
    }

    protected function populateCreateDefaults(Guru $guru)
    {
        $isTetap = in_array(strtolower($guru->status_kepegawaian ?? ''), ['tetap_yayasan', 'gty', 'pns']);
        $this->createJabatan = $guru->jabatan ?: ($guru->jenis_guru === 'tahfidz' ? 'Wali Tahfizh' : 'Guru Pengajar');
        $this->createJamKerja = $isTetap ? '07.00-14.00 (Fleksibel)' : '07.00-14.00';
        $this->createSumberDana = 'Yayasan';

        $this->createGajiPokok = $isTetap ? 2000000.00 : 1000000.00;
        $this->createGajiBerkala = $isTetap ? 120000.00 : 0.00;
        $this->createJumlahEkskul = 0;
        $this->createHonorEkskul = 0.00;
        $this->createInsentif = $isTetap ? 500000.00 : 150000.00;
        $this->createInsentifBpjs = $isTetap ? 17928.00 : 0.00;
        $this->createInsentifMaghrib = 0.00;

        $this->createPotonganSosial = 10000.00;
        $this->createPotonganBpjstk = $isTetap ? 17928.00 : 0.00;
        $this->createPotonganLainnya = 0.00;

        $activeLoan = Peminjaman::where('guru_id', $guru->id)
            ->where('status', 'berjalan')
            ->where('sisa_pinjaman', '>', 0)
            ->first();

        $this->createPotonganPinjaman = $activeLoan ? min(floatval($activeLoan->cicilan_per_bulan), floatval($activeLoan->sisa_pinjaman)) : 0.00;

        $this->calculateCreateTotal();
    }

    public function calculateCreateTotal()
    {
        $this->createTotalBruto = floatval($this->createGajiPokok)
            + floatval($this->createGajiBerkala)
            + floatval($this->createHonorEkskul)
            + floatval($this->createInsentif)
            + floatval($this->createInsentifBpjs)
            + floatval($this->createInsentifMaghrib);

        $this->createTotalPotongan = floatval($this->createPotonganSosial)
            + floatval($this->createPotonganPinjaman)
            + floatval($this->createPotonganBpjstk)
            + floatval($this->createPotonganLainnya);

        $this->createTotalDiterima = max(0, $this->createTotalBruto - $this->createTotalPotongan);
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function saveCreate()
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->validate([
            'createGuruId' => 'required|exists:guru,id',
            'createBulan' => 'required|string',
            'createTahun' => 'required|integer',
            'createGajiPokok' => 'required|numeric|min:0',
            'createGajiBerkala' => 'required|numeric|min:0',
            'createJumlahEkskul' => 'required|integer|min:0',
            'createHonorEkskul' => 'required|numeric|min:0',
            'createInsentif' => 'required|numeric|min:0',
            'createInsentifBpjs' => 'required|numeric|min:0',
            'createInsentifMaghrib' => 'required|numeric|min:0',
            'createPotonganSosial' => 'required|numeric|min:0',
            'createPotonganPinjaman' => 'required|numeric|min:0',
            'createPotonganBpjstk' => 'required|numeric|min:0',
            'createPotonganLainnya' => 'required|numeric|min:0',
            'createSumberDana' => 'required|string|max:100',
            'createStatus' => 'required|in:draft,dibayar',
        ]);

        $exists = GajiGuru::where('guru_id', $this->createGuruId)
            ->where('bulan', $this->createBulan)
            ->where('tahun', $this->createTahun)
            ->exists();

        if ($exists) {
            $this->addError('createGuruId', 'Gaji untuk guru ini pada periode tersebut sudah ada.');
            return;
        }

        $this->calculateCreateTotal();

        DB::transaction(function () {
            $pengeluaranId = null;
            $guru = Guru::with('user')->findOrFail($this->createGuruId);

            if ($this->createStatus === 'dibayar') {
                $kategori = KategoriPengeluaran::firstOrCreate(
                    ['nama' => 'Gaji Guru'],
                    ['jenis' => 'operasional']
                );

                $pengeluaran = Pengeluaran::create([
                    'kategori_pengeluaran_id' => $kategori->id,
                    'jumlah' => $this->createTotalDiterima,
                    'tanggal' => $this->createTanggalBayar ?: now()->toDateString(),
                    'keterangan' => "Honorarium Pegawai Yayasan: " . ($guru->user->nama ?? 'Guru') . " - Periode " . $this->createBulan . " " . $this->createTahun,
                    'petugas_id' => auth()->id(),
                ]);

                $pengeluaranId = $pengeluaran->id;

                if ($this->createPotonganPinjaman > 0) {
                    $activeLoan = Peminjaman::where('guru_id', $this->createGuruId)
                        ->where('status', 'berjalan')
                        ->where('sisa_pinjaman', '>', 0)
                        ->first();

                    if ($activeLoan) {
                        $newSisa = max(0, $activeLoan->sisa_pinjaman - $this->createPotonganPinjaman);
                        $status = $newSisa <= 0 ? 'lunas' : 'berjalan';
                        $activeLoan->update([
                            'sisa_pinjaman' => $newSisa,
                            'status' => $status
                        ]);
                    }
                }
            }

            GajiGuru::create([
                'guru_id' => $this->createGuruId,
                'pengeluaran_id' => $pengeluaranId,
                'bulan' => $this->createBulan,
                'tahun' => $this->createTahun,
                'gaji_pokok' => $this->createGajiPokok,
                'gaji_berkala' => $this->createGajiBerkala,
                'jumlah_ekskul' => $this->createJumlahEkskul,
                'honor_ekskul' => $this->createHonorEkskul,
                'insentif' => $this->createInsentif,
                'insentif_bpjs' => $this->createInsentifBpjs,
                'insentif_maghrib_mengaji' => $this->createInsentifMaghrib,
                'potongan_sosial' => $this->createPotonganSosial,
                'potongan_peminjaman' => $this->createPotonganPinjaman,
                'potongan_bpjstk' => $this->createPotonganBpjstk,
                'potongan_lainnya' => $this->createPotonganLainnya,
                'total_bruto' => $this->createTotalBruto,
                'total_diterima' => $this->createTotalDiterima,
                'tanggal_bayar' => $this->createTanggalBayar ?: now()->toDateString(),
                'status' => $this->createStatus,
                'sumber_dana' => $this->createSumberDana ?: 'Yayasan',
                'jam_kerja' => $this->createJamKerja ?: '07.00-14.00',
                'jabatan' => $this->createJabatan ?: ($guru->jabatan ?? 'Guru'),
            ]);
        });

        session()->flash('message', 'Gaji pegawai berhasil dibuat dan disimpan.');
        $this->closeCreateModal();
    }

    // ==========================================
    // 3. UBAH / EDIT RINCIAN GAJI
    // ==========================================
    public function openEditModal(int $id)
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $gaji = GajiGuru::with('guru.user')->findOrFail($id);

        $this->editingId = $id;
        $this->edit_alasan = '';
        $this->editGuruNama = $gaji->guru->user->nama ?? '';
        $this->editBulan = $gaji->bulan;
        $this->editTahun = intval($gaji->tahun);
        $this->editJabatan = $gaji->jabatan ?: ($gaji->guru->jabatan ?? 'Guru');
        $this->editJamKerja = $gaji->jam_kerja ?: '07.00-14.00';
        $this->editSumberDana = $gaji->sumber_dana ?: 'Yayasan';
        $this->editStatus = $gaji->status;
        $this->editTanggalBayar = $gaji->tanggal_bayar ? $gaji->tanggal_bayar->format('Y-m-d') : date('Y-m-d');

        $this->editGajiPokok = floatval($gaji->gaji_pokok);
        $this->editGajiBerkala = floatval($gaji->gaji_berkala);
        $this->editJumlahEkskul = intval($gaji->jumlah_ekskul);
        $this->editHonorEkskul = floatval($gaji->honor_ekskul);
        $this->editInsentif = floatval($gaji->insentif);
        $this->editInsentifBpjs = floatval($gaji->insentif_bpjs);
        $this->editInsentifMaghrib = floatval($gaji->insentif_maghrib_mengaji);

        $this->editPotonganSosial = floatval($gaji->potongan_sosial ?: 10000.00);
        $this->editPotonganPinjaman = floatval($gaji->potongan_peminjaman);
        $this->editPotonganBpjstk = floatval($gaji->potongan_bpjstk);
        $this->editPotonganLainnya = floatval($gaji->potongan_lainnya);

        $this->calculateEditTotal();

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset([
            'editingId', 'editGuruNama', 'editJabatan', 'editJamKerja', 'editSumberDana',
            'editBulan', 'editTahun',
            'editGajiPokok', 'editGajiBerkala', 'editJumlahEkskul', 'editHonorEkskul',
            'editInsentif', 'editInsentifBpjs', 'editInsentifMaghrib', 'editTotalBruto',
            'editPotonganSosial', 'editPotonganPinjaman', 'editPotonganBpjstk', 'editPotonganLainnya', 'editTotalPotongan',
            'editTotalDiterima', 'editStatus', 'editTanggalBayar'
        ]);
    }

    public function calculateEditTotal()
    {
        $this->editTotalBruto = floatval($this->editGajiPokok)
            + floatval($this->editGajiBerkala)
            + floatval($this->editHonorEkskul)
            + floatval($this->editInsentif)
            + floatval($this->editInsentifBpjs)
            + floatval($this->editInsentifMaghrib);

        $this->editTotalPotongan = floatval($this->editPotonganSosial)
            + floatval($this->editPotonganPinjaman)
            + floatval($this->editPotonganBpjstk)
            + floatval($this->editPotonganLainnya);

        $this->editTotalDiterima = max(0, $this->editTotalBruto - $this->editTotalPotongan);
    }

    public function saveEdit()
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $rules = [
            'editBulan' => 'required|string|in:' . implode(',', $this->listBulan),
            'editTahun' => 'required|integer|min:2020|max:2035',
            'editGajiPokok' => 'required|numeric|min:0',
            'editGajiBerkala' => 'required|numeric|min:0',
            'editJumlahEkskul' => 'required|integer|min:0',
            'editHonorEkskul' => 'required|numeric|min:0',
            'editInsentif' => 'required|numeric|min:0',
            'editInsentifBpjs' => 'required|numeric|min:0',
            'editInsentifMaghrib' => 'required|numeric|min:0',
            'editPotonganSosial' => 'required|numeric|min:0',
            'editPotonganPinjaman' => 'required|numeric|min:0',
            'editPotonganBpjstk' => 'required|numeric|min:0',
            'editPotonganLainnya' => 'required|numeric|min:0',
            'editSumberDana' => 'required|string|max:100',
        ];

        $userRole = auth()->user()->role->nama ?? '';
        if ($userRole === 'finance') {
            $rules['edit_alasan'] = 'required|string|min:5|max:500';
        }

        $this->validate($rules, [
            'edit_alasan.required' => 'Alasan perubahan rincian gaji wajib diisi untuk permohonan persetujuan Super Admin / Super Admin 2.',
        ]);

        $gaji = GajiGuru::with('guru.user')->findOrFail($this->editingId);
        $this->calculateEditTotal();

        if ($userRole === 'finance') {
            \App\Services\FinancialApprovalService::createRequest(
                auth()->user(),
                'edit',
                'gaji_guru',
                $gaji,
                [
                    'bulan' => $this->editBulan,
                    'tahun' => $this->editTahun,
                    'jabatan' => $this->editJabatan,
                    'jam_kerja' => $this->editJamKerja,
                    'sumber_dana' => $this->editSumberDana,
                    'gaji_pokok' => $this->editGajiPokok,
                    'gaji_berkala' => $this->editGajiBerkala,
                    'jumlah_ekskul' => $this->editJumlahEkskul,
                    'honor_ekskul' => $this->editHonorEkskul,
                    'insentif' => $this->editInsentif,
                    'insentif_bpjs' => $this->editInsentifBpjs,
                    'insentif_maghrib_mengaji' => $this->editInsentifMaghrib,
                    'potongan_sosial' => $this->editPotonganSosial,
                    'potongan_peminjaman' => $this->editPotonganPinjaman,
                    'potongan_bpjstk' => $this->editPotonganBpjstk,
                    'potongan_lainnya' => $this->editPotonganLainnya,
                    'total_bruto' => $this->editTotalBruto,
                    'total_diterima' => $this->editTotalDiterima,
                    'tanggal_bayar' => $this->editTanggalBayar ?: $gaji->tanggal_bayar,
                ],
                $this->edit_alasan,
                "Edit Gaji Guru: " . ($gaji->guru->user->nama ?? 'Guru') . " - {$this->editBulan} {$this->editTahun} (THP: Rp " . number_format($this->editTotalDiterima, 0, ',', '.') . ")"
            );

            session()->flash('message', 'Permohonan edit gaji guru telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.');
            $this->closeEditModal();
            return;
        }

        $oldPotonganPinjaman = floatval($gaji->potongan_peminjaman);

        DB::transaction(function () use ($gaji, $oldPotonganPinjaman) {
            $gaji->update([
                'bulan' => $this->editBulan,
                'tahun' => $this->editTahun,
                'jabatan' => $this->editJabatan,
                'jam_kerja' => $this->editJamKerja,
                'sumber_dana' => $this->editSumberDana,
                'gaji_pokok' => $this->editGajiPokok,
                'gaji_berkala' => $this->editGajiBerkala,
                'jumlah_ekskul' => $this->editJumlahEkskul,
                'honor_ekskul' => $this->editHonorEkskul,
                'insentif' => $this->editInsentif,
                'insentif_bpjs' => $this->editInsentifBpjs,
                'insentif_maghrib_mengaji' => $this->editInsentifMaghrib,
                'potongan_sosial' => $this->editPotonganSosial,
                'potongan_peminjaman' => $this->editPotonganPinjaman,
                'potongan_bpjstk' => $this->editPotonganBpjstk,
                'potongan_lainnya' => $this->editPotonganLainnya,
                'total_bruto' => $this->editTotalBruto,
                'total_diterima' => $this->editTotalDiterima,
                'tanggal_bayar' => $this->editTanggalBayar ?: $gaji->tanggal_bayar,
            ]);

            if ($gaji->status === 'dibayar' && $gaji->pengeluaran_id) {
                $pengeluaran = Pengeluaran::find($gaji->pengeluaran_id);
                if ($pengeluaran) {
                    $pengeluaran->update([
                        'keterangan' => "Pembayaran Gaji " . ($gaji->guru->user->nama ?? 'Guru') . " ({$this->editBulan} {$this->editTahun})",
                        'jumlah' => $gaji->total_diterima,
                        'tanggal' => $this->editTanggalBayar ?: $pengeluaran->tanggal,
                    ]);
                }

                $diffLoan = $this->editPotonganPinjaman - $oldPotonganPinjaman;
                if ($diffLoan != 0) {
                    $activeLoan = Peminjaman::where('guru_id', $gaji->guru_id)
                        ->where('status', 'berjalan')
                        ->first();

                    if ($activeLoan) {
                        $newSisa = max(0, $activeLoan->sisa_pinjaman - $diffLoan);
                        $status = $newSisa <= 0 ? 'lunas' : 'berjalan';
                        $activeLoan->update([
                            'sisa_pinjaman' => $newSisa,
                            'status' => $status
                        ]);
                    }
                }
            }
        });

        session()->flash('message', 'Perubahan rincian gaji berhasil disimpan.');
        $this->closeEditModal();
    }

    // ==========================================
    // 4. BAYAR / PROSES PENCAIRAN
    // ==========================================
    public function paySalary(int $id)
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $gaji = GajiGuru::with('guru.user')->findOrFail($id);

        if ($gaji->status === 'dibayar') {
            session()->flash('error', 'Gaji ini sudah dibayarkan.');
            return;
        }

        DB::transaction(function () use ($gaji) {
            $kategori = KategoriPengeluaran::firstOrCreate(
                ['nama' => 'Gaji Guru'],
                ['jenis' => 'operasional']
            );

            $pengeluaran = Pengeluaran::create([
                'kategori_pengeluaran_id' => $kategori->id,
                'jumlah' => $gaji->total_diterima,
                'tanggal' => now()->toDateString(),
                'keterangan' => "Honorarium Pegawai Yayasan: " . ($gaji->guru->user->nama ?? 'Guru') . " (" . ($gaji->jabatan ?: 'Guru') . ") - Periode " . $gaji->bulan . " " . $gaji->tahun,
                'petugas_id' => auth()->id(),
            ]);

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

            $gaji->update([
                'status' => 'dibayar',
                'pengeluaran_id' => $pengeluaran->id,
                'tanggal_bayar' => now()->toDateString(),
            ]);

            if ($gaji->guru->user_id) {
                NotificationService::send(
                    $gaji->guru->user_id,
                    'Gaji Telah Dibayarkan',
                    "Honorarium/Gaji Anda untuk periode {$gaji->bulan} {$gaji->tahun} sebesar Rp " . number_format($gaji->total_diterima, 0, ',', '.') . " telah berhasil diproses pada " . date('d-m-Y') . ".",
                    'sistem',
                    ['in_app']
                );
            }
        });

        session()->flash('message', 'Pembayaran gaji berhasil diproses dan dicatat ke kas pengeluaran.');
    }

    // ==========================================
    // 5. BATALKAN PEMBAYARAN (KEMBALIKAN KE DRAFT)
    // ==========================================
    public function revertToDraft(int $id)
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        if (auth()->user()->role?->nama === 'finance') {
            session()->flash('error', 'Akses Ditolak: Pembatalan status gaji yang telah dibayarkan hanya dapat dilakukan oleh Super Admin.');
            return;
        }

        $gaji = GajiGuru::findOrFail($id);

        if ($gaji->status !== 'dibayar') {
            session()->flash('error', 'Hanya gaji yang sudah dibayar yang dapat dibatalkan.');
            return;
        }

        DB::transaction(function () use ($gaji) {
            if ($gaji->pengeluaran_id) {
                $pengeluaran = Pengeluaran::find($gaji->pengeluaran_id);
                if ($pengeluaran) {
                    $pengeluaran->delete();
                }
            }

            if ($gaji->potongan_peminjaman > 0) {
                $loan = Peminjaman::where('guru_id', $gaji->guru_id)->first();
                if ($loan) {
                    $loan->update([
                        'sisa_pinjaman' => $loan->sisa_pinjaman + $gaji->potongan_peminjaman,
                        'status' => 'berjalan'
                    ]);
                }
            }

            $gaji->update([
                'status' => 'draft',
                'pengeluaran_id' => null,
            ]);
        });

        session()->flash('message', 'Status gaji berhasil dikembalikan ke Draf dan catatan kas pengeluaran telah disesuaikan.');
    }

    // ==========================================
    // 6. HAPUS GAJI
    // ==========================================
    public function deleteSalary(int $id, ?string $alasan = null)
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $gaji = GajiGuru::with('guru.user')->findOrFail($id);
        $userRole = auth()->user()->role->nama ?? '';

        if ($userRole === 'finance' && $gaji->status === 'dibayar') {
            $reason = $alasan ?: 'Penghapusan data gaji diajukan oleh staf keuangan';
            \App\Services\FinancialApprovalService::createRequest(
                auth()->user(),
                'hapus',
                'gaji_guru',
                $gaji,
                null,
                $reason,
                "Hapus Gaji Guru: " . ($gaji->guru->user->nama ?? 'Guru') . " - {$gaji->bulan} {$gaji->tahun} (THP: Rp " . number_format($gaji->total_diterima, 0, ',', '.') . ")"
            );

            $msg = 'Permohonan penghapusan data gaji telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.';
            session()->flash('message', $msg);
            $this->dispatch('notify', ['type' => 'success', 'message' => $msg]);
            $this->dispatch('modal-alert', ['type' => 'create', 'title' => 'Permohonan Diajukan', 'message' => $msg]);
            return;
        }

        DB::transaction(function () use ($gaji) {
            if ($gaji->status === 'dibayar') {
                if ($gaji->pengeluaran_id) {
                    $pengeluaran = Pengeluaran::find($gaji->pengeluaran_id);
                    if ($pengeluaran) {
                        $pengeluaran->delete();
                    }
                }

                if ($gaji->potongan_peminjaman > 0) {
                    $loan = Peminjaman::where('guru_id', $gaji->guru_id)->first();
                    if ($loan) {
                        $loan->update([
                            'sisa_pinjaman' => $loan->sisa_pinjaman + $gaji->potongan_peminjaman,
                            'status' => 'berjalan'
                        ]);
                    }
                }
            }

            $gaji->delete();
        });

        $msg = 'Data gaji berhasil dihapus.';
        session()->flash('message', $msg);
        $this->dispatch('notify', ['type' => 'success', 'message' => $msg]);
        $this->dispatch('modal-alert', ['type' => 'delete', 'title' => 'Data Dihapus', 'message' => $msg]);
    }

    public function deleteDraft(int $id)
    {
        $this->deleteSalary($id);
    }

    public function deleteSelected()
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.']);
            return;
        }

        if (empty($this->selectedGajiIds)) {
            session()->flash('error', 'Silakan pilih data gaji yang ingin dihapus terlebih dahulu.');
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Silakan pilih data gaji yang ingin dihapus terlebih dahulu.']);
            return;
        }

        $salaries = GajiGuru::with('guru.user')->whereIn('id', $this->selectedGajiIds)->get();
        $count = $salaries->count();
        $userRole = auth()->user()->role->nama ?? '';

        if ($userRole === 'finance') {
            $submittedCount = 0;
            $deletedDraftCount = 0;

            DB::transaction(function () use ($salaries, &$submittedCount, &$deletedDraftCount) {
                foreach ($salaries as $gaji) {
                    if ($gaji->status === 'dibayar') {
                        \App\Services\FinancialApprovalService::createRequest(
                            auth()->user(),
                            'hapus',
                            'gaji_guru',
                            $gaji,
                            null,
                            'Penghapusan batch diajukan oleh staf keuangan',
                            "Hapus Gaji Guru: " . ($gaji->guru->user->nama ?? 'Guru') . " - {$gaji->bulan} {$gaji->tahun} (THP: Rp " . number_format($gaji->total_diterima, 0, ',', '.') . ")"
                        );
                        $submittedCount++;
                    } else {
                        $gaji->delete();
                        $deletedDraftCount++;
                    }
                }
            });

            $this->selectedGajiIds = [];
            $this->selectAll = false;

            $msg = [];
            if ($deletedDraftCount > 0) {
                $msg[] = "{$deletedDraftCount} data draf gaji berhasil dihapus.";
            }
            if ($submittedCount > 0) {
                $msg[] = "{$submittedCount} permohonan hapus gaji berstatus dibayar diajukan ke Super Admin untuk disetujui.";
            }

            $messageText = implode(' ', $msg) ?: 'Aksi batch selesai.';
            session()->flash('message', $messageText);
            $this->dispatch('notify', ['type' => 'success', 'message' => $messageText]);
            $this->dispatch('modal-alert', ['type' => 'create', 'title' => 'Permohonan Berhasil Diajukan', 'message' => $messageText]);
            return;
        }

        DB::transaction(function () use ($salaries) {
            foreach ($salaries as $gaji) {
                if ($gaji->status === 'dibayar') {
                    if ($gaji->pengeluaran_id) {
                        $pengeluaran = Pengeluaran::find($gaji->pengeluaran_id);
                        if ($pengeluaran) {
                            $pengeluaran->delete();
                        }
                    }

                    if ($gaji->potongan_peminjaman > 0) {
                        $loan = Peminjaman::where('guru_id', $gaji->guru_id)->first();
                        if ($loan) {
                            $loan->update([
                                'sisa_pinjaman' => $loan->sisa_pinjaman + $gaji->potongan_peminjaman,
                                'status' => 'berjalan'
                            ]);
                        }
                    }
                }

                $gaji->delete();
            }
        });

        $this->selectedGajiIds = [];
        $this->selectAll = false;
        $messageText = "Berhasil menghapus {$count} data gaji terpilih.";
        session()->flash('message', $messageText);
        $this->dispatch('notify', ['type' => 'success', 'message' => $messageText]);
        $this->dispatch('modal-alert', ['type' => 'delete', 'title' => 'Data Berhasil Dihapus', 'message' => $messageText]);
    }

    public function openDetailModal(int $id)
    {
        $this->selectedSalaryDetail = GajiGuru::with(['guru.user', 'pengeluaran'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedSalaryDetail = null;
    }

    public function openHistoryModal(int $guruId)
    {
        $this->historyGuruId = $guruId;
        $this->historyGuru = Guru::with('user')->find($guruId);
        $this->historyFilterTahun = '';
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal()
    {
        $this->showHistoryModal = false;
        $this->historyGuruId = null;
        $this->historyGuru = null;
        $this->historyFilterTahun = '';
    }

    public function openPreview(int $id)
    {
        $salary = GajiGuru::with('guru.user')->findOrFail($id);
        $this->previewSalaryId = $salary->id;
        $this->previewSalary = $salary;
        $this->showPreviewModal = true;
    }

    public function closePreview()
    {
        $this->showPreviewModal = false;
        $this->previewSalaryId = null;
        $this->previewSalary = null;
    }

    public function render()
    {
        $query = GajiGuru::with(['guru.user', 'pengeluaran']);

        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('guru.user', function ($sub) {
                    $sub->where('nama', 'like', '%' . $this->search . '%');
                })->orWhere('jabatan', 'like', '%' . $this->search . '%');
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

        // Base Query for Stats (matching active search & filters)
        $statsQuery = clone $query;
        $allMatchingSalaries = $statsQuery->get();

        $statTotalAnggaran = $allMatchingSalaries->sum('total_diterima');
        $statTotalDibayar = $allMatchingSalaries->where('status', 'dibayar')->sum('total_diterima');
        $statCountDibayar = $allMatchingSalaries->where('status', 'dibayar')->count();
        $statTotalDraft = $allMatchingSalaries->where('status', 'draft')->sum('total_diterima');
        $statCountDraft = $allMatchingSalaries->where('status', 'draft')->count();
        $statTotalKasbon = $allMatchingSalaries->sum('potongan_peminjaman');

        $salaries = $query->latest('id')->paginate(15);
        $activeGurusList = Guru::with('user')->where('status_aktif', true)->get();

        // History data for teacher if history modal is open
        $historySalaries = collect();
        $totalHistoryDibayarkan = 0;
        $totalHistoryKasbon = 0;
        $totalHistoryBulan = 0;

        if ($this->showHistoryModal && $this->historyGuruId) {
            $historyQuery = GajiGuru::where('guru_id', $this->historyGuruId);
            if ($this->historyFilterTahun) {
                $historyQuery->where('tahun', $this->historyFilterTahun);
            }
            $historySalaries = $historyQuery->orderBy('tahun', 'desc')->orderBy('id', 'desc')->get();
            $paidHistories = $historySalaries->where('status', 'dibayar');
            $totalHistoryDibayarkan = $paidHistories->sum('total_diterima');
            $totalHistoryKasbon = $paidHistories->sum('potongan_peminjaman');
            $totalHistoryBulan = $paidHistories->count();
        }

        $pendingApprovalIds = \App\Models\ApprovalKeuangan::where('model_type', GajiGuru::class)
            ->where('status', 'menunggu')
            ->pluck('model_id')
            ->toArray();

        return view('livewire.finance.manajemen-gaji-guru', [
            'salaries' => $salaries,
            'pendingApprovalIds' => $pendingApprovalIds,
            'activeGurusList' => $activeGurusList,
            'historySalaries' => $historySalaries,
            'totalHistoryDibayarkan' => $totalHistoryDibayarkan,
            'totalHistoryKasbon' => $totalHistoryKasbon,
            'totalHistoryBulan' => $totalHistoryBulan,
            'statTotalAnggaran' => $statTotalAnggaran,
            'statTotalDibayar' => $statTotalDibayar,
            'statCountDibayar' => $statCountDibayar,
            'statTotalDraft' => $statTotalDraft,
            'statCountDraft' => $statCountDraft,
            'statTotalKasbon' => $statTotalKasbon,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Gaji Guru - Yayasan F3']);
    }
}
