<?php

namespace App\Livewire\Finance\Concerns;

use App\Models\GajiGuru;
use App\Models\Guru;
use App\Models\Peminjaman;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Services\SalaryCalculationService;
use Illuminate\Support\Facades\DB;

trait HasSalaryCreation
{
    public bool $showCreateModal = false;
    public ?int $createGuruId = null;
    public string $createBulan = 'Januari';
    public int $createTahun = 2026;
    public ?string $createJabatan = '';
    public ?string $createJamKerja = '07.00-14.00';
    public ?string $createSumberDana = 'Yayasan';
    public string $createStatus = 'draft';
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

    public $createBuktiFoto = null;

    public function openCreateModal(): void
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

    public function updatedCreateGuruId($guruId): void
    {
        if ($guruId) {
            $guru = Guru::with('user')->find($guruId);
            if ($guru) {
                $this->populateCreateDefaults($guru);
            }
        }
    }

    /**
     * Populate create form with data from the teacher's most recent salary record.
     * Falls back to status-based defaults when no salary history exists.
     */
    protected function populateCreateDefaults(Guru $guru): void
    {
        // Try to find the most recent salary for this teacher
        $lastSalary = $this->findPreviousSalary($guru->id, $this->createBulan, $this->createTahun);

        if ($lastSalary) {
            $this->populateFromPreviousSalary($guru, $lastSalary);
        } else {
            $this->populateFromStatusDefaults($guru);
        }

        // Always refresh loan deduction from active loan data
        $activeLoan = Peminjaman::where('guru_id', $guru->id)
            ->where('status', 'berjalan')
            ->where('sisa_pinjaman', '>', 0)
            ->first();

        $this->createPotonganPinjaman = $activeLoan
            ? min(floatval($activeLoan->cicilan_per_bulan), floatval($activeLoan->sisa_pinjaman))
            : 0.00;

        $this->calculateCreateTotal();
    }

    /**
     * Find the most recent salary record for a teacher, prioritizing the
     * month immediately before the target period.
     */
    protected function findPreviousSalary(int $guruId, string $bulan, int $tahun): ?GajiGuru
    {
        $bulanIndex = array_search($bulan, $this->listBulan);

        // Calculate previous month
        if ($bulanIndex !== false && $bulanIndex > 0) {
            $prevBulan = $this->listBulan[$bulanIndex - 1];
            $prevTahun = $tahun;
        } else {
            // January -> previous is December of last year
            $prevBulan = 'Desember';
            $prevTahun = $tahun - 1;
        }

        // First try exact previous month
        $prevSalary = GajiGuru::where('guru_id', $guruId)
            ->where('bulan', $prevBulan)
            ->where('tahun', $prevTahun)
            ->first();

        if ($prevSalary) {
            return $prevSalary;
        }

        // Fallback: get the most recent salary record regardless of period
        return GajiGuru::where('guru_id', $guruId)
            ->latest('id')
            ->first();
    }

    /**
     * Fill the create form fields from a previous salary record.
     */
    protected function populateFromPreviousSalary(Guru $guru, GajiGuru $prevSalary): void
    {
        $this->createJabatan = $prevSalary->jabatan ?: ($guru->jabatan ?? 'Guru Pengajar');
        $this->createJamKerja = $prevSalary->jam_kerja ?: '07.00-14.00';
        $this->createSumberDana = $prevSalary->sumber_dana ?: 'Yayasan';

        // Earnings
        $this->createGajiPokok = floatval($prevSalary->gaji_pokok);
        $this->createGajiBerkala = floatval($prevSalary->gaji_berkala);
        $this->createJumlahEkskul = intval($prevSalary->jumlah_ekskul);
        $this->createHonorEkskul = floatval($prevSalary->honor_ekskul);
        $this->createInsentif = floatval($prevSalary->insentif);
        $this->createInsentifBpjs = floatval($prevSalary->insentif_bpjs);
        $this->createInsentifMaghrib = floatval($prevSalary->insentif_maghrib_mengaji);

        // Deductions (except loan, which is refreshed from active loan data)
        $this->createPotonganSosial = floatval($prevSalary->potongan_sosial ?: 10000.00);
        $this->createPotonganBpjstk = floatval($prevSalary->potongan_bpjstk);
        $this->createPotonganLainnya = floatval($prevSalary->potongan_lainnya);
    }

    /**
     * Fill the create form with hardcoded defaults based on employment status.
     * Used when there is no previous salary history.
     */
    protected function populateFromStatusDefaults(Guru $guru): void
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
    }

    public function calculateCreateTotal(): void
    {
        $calc = app(SalaryCalculationService::class);
        $this->createTotalBruto = $calc->calculateBruto(
            floatval($this->createGajiPokok),
            floatval($this->createGajiBerkala),
            floatval($this->createHonorEkskul),
            floatval($this->createInsentif),
            floatval($this->createInsentifBpjs),
            floatval($this->createInsentifMaghrib)
        );

        $this->createTotalPotongan = $calc->calculatePotongan(
            floatval($this->createPotonganSosial),
            floatval($this->createPotonganPinjaman),
            floatval($this->createPotonganBpjstk),
            floatval($this->createPotonganLainnya)
        );

        $this->createTotalDiterima = $calc->calculateNetTakeHomePay($this->createTotalBruto, $this->createTotalPotongan);
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->createBuktiFoto = null;
    }

    public function saveCreate(): void
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->sanitizeCurrencies([
            'createGajiPokok',
            'createGajiBerkala',
            'createHonorEkskul',
            'createInsentif',
            'createInsentifBpjs',
            'createInsentifMaghrib',
            'createPotonganSosial',
            'createPotonganPinjaman',
            'createPotonganBpjstk',
            'createPotonganLainnya',
        ]);

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
            'createBuktiFoto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'createBuktiFoto.image' => 'File bukti pembayaran harus berupa gambar/foto.',
            'createBuktiFoto.mimes' => 'Format foto hanya boleh JPG, JPEG, PNG, atau WEBP.',
            'createBuktiFoto.max' => 'Ukuran foto bukti pembayaran maksimal 2MB.',
        ]);

        $existingGaji = GajiGuru::withTrashed()
            ->where('guru_id', $this->createGuruId)
            ->where('bulan', $this->createBulan)
            ->where('tahun', $this->createTahun)
            ->first();

        if ($existingGaji && !$existingGaji->trashed()) {
            $this->addError('createGuruId', 'Gaji untuk guru ini pada periode tersebut sudah ada.');
            return;
        }

        $this->calculateCreateTotal();

        $pathBukti = null;
        if ($this->createStatus === 'dibayar' && $this->createBuktiFoto) {
            $pathBukti = $this->createBuktiFoto->store('bukti-gaji', 'public');
        }

        try {
            DB::transaction(function () use ($pathBukti, $existingGaji) {
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
                        'bukti' => $pathBukti,
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

                $gajiData = [
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
                    'bukti_bayar' => $pathBukti,
                    'sumber_dana' => $this->createSumberDana ?: 'Yayasan',
                    'jam_kerja' => $this->createJamKerja ?: '07.00-14.00',
                    'jabatan' => $this->createJabatan ?: ($guru->jabatan ?? 'Guru'),
                ];

                if ($existingGaji && $existingGaji->trashed()) {
                    $existingGaji->restore();
                    $existingGaji->update($gajiData);
                } else {
                    GajiGuru::create($gajiData);
                }
            });
        } catch (\Illuminate\Database\UniqueConstraintViolationException | \Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), '1062 Duplicate entry')) {
                $this->addError('createGuruId', "Gaji untuk pegawai ini pada periode {$this->createBulan} {$this->createTahun} sudah ada di sistem.");
                return;
            }
            throw $e;
        }

        session()->flash('message', 'Gaji pegawai berhasil dibuat dan disimpan.');
        $this->closeCreateModal();
    }
}
