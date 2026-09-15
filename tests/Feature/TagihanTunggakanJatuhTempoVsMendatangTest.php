<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\JenisTagihan;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Notifikasi;
use App\Livewire\Finance\Dashboard;
use App\Livewire\Finance\OverviewPembayaran;
use App\Livewire\Finance\Laporan\LaporanTunggakan;
use App\Livewire\Finance\DetailTagihanSiswa;
use Livewire\Livewire;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagihanTunggakanJatuhTempoVsMendatangTest extends TestCase
{
    use RefreshDatabase;

    protected User $financeUser;
    protected Siswa $siswa;
    protected TahunAjaran $tahunAjaran;
    protected JenisTagihan $jenisSpp;

    protected function setUp(): void
    {
        parent::setUp();

        $roleFinance = Role::firstOrCreate(['nama' => 'finance']);
        $this->financeUser = User::factory()->create([
            'role_id' => $roleFinance->id,
            'status' => 'aktif',
        ]);

        $this->tahunAjaran = TahunAjaran::firstOrCreate(
            ['nama' => '2026/2027'],
            ['status_aktif' => true, 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30']
        );

        $semester = Semester::firstOrCreate(
            ['tahun_ajaran_id' => $this->tahunAjaran->id, 'semester' => 'ganjil'],
            ['tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2026-12-31', 'status_aktif' => true]
        );

        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => '7A'],
            ['tingkat' => 7, 'kapasitas' => 30, 'semester_id' => $semester->id]
        );

        $roleMurid = Role::firstOrCreate(['nama' => 'murid']);
        $studentUser = User::factory()->create([
            'nama' => 'Santri Hasan',
            'role_id' => $roleMurid->id,
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'nis' => '9901',
            'kelas_id' => $kelas->id,
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
        ]);

        $this->jenisSpp = JenisTagihan::firstOrCreate(
            ['nama' => 'SPP'],
            ['kategori' => 'rutin', 'default_nominal' => 350000, 'is_blocking' => true]
        );
    }

    public function test_tagihan_scopes_correctly_separate_tunggakan_from_future_bills(): void
    {
        // 1. Tagihan bulan lalu (Juli) - Jatuh tempo lewat -> TUNGGAKAN
        $pastBill = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->subMonths(2)->startOfMonth()->addDays(9)->toDateString(),
        ]);

        // 2. Tagihan bulan depan (Oktober) - Jatuh tempo bulan depan -> MENDATANG
        $futureBill = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Oktober',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->addMonths(2)->startOfMonth()->addDays(9)->toDateString(),
        ]);

        $this->assertTrue($pastBill->is_tunggakan);
        $this->assertFalse($pastBill->is_mendatang);

        $this->assertFalse($futureBill->is_tunggakan);
        $this->assertTrue($futureBill->is_mendatang);

        $this->assertEquals(1, Tagihan::tunggakan()->count());
        $this->assertEquals($pastBill->id, Tagihan::tunggakan()->first()->id);

        $this->assertEquals(1, Tagihan::mendatang()->count());
        $this->assertEquals($futureBill->id, Tagihan::mendatang()->first()->id);
    }

    public function test_dashboard_outstanding_bills_only_includes_due_arrears(): void
    {
        // Past bill (Jatuh tempo lewat)
        Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Agustus',
            'nominal' => 300000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->subMonth()->startOfMonth()->addDays(9)->toDateString(),
        ]);

        // Future bill (3 bulan lagi)
        Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Desember',
            'nominal' => 300000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->addMonths(3)->startOfMonth()->addDays(9)->toDateString(),
        ]);

        $this->actingAs($this->financeUser);

        Livewire::test(Dashboard::class)
            ->assertSet('outstandingBills', 300000.00)
            ->assertSet('futureBills', 300000.00);
    }

    public function test_overview_pembayaran_marks_student_as_orderly_when_due_bills_are_paid(): void
    {
        // 1. Tagihan bulan berjalan (sudah dibayar lunas)
        $dueBill = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'September',
            'nominal' => 350000,
            'total_dibayar' => 350000,
            'status' => 'lunas',
            'jatuh_tempo' => Carbon::now()->startOfMonth()->addDays(9)->toDateString(),
        ]);

        // 2. Tagihan 3 bulan ke depan (belum dibayar, tapi belum jatuh tempo)
        $futureBill = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Desember',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->addMonths(3)->startOfMonth()->addDays(9)->toDateString(),
        ]);

        $this->actingAs($this->financeUser);

        $component = Livewire::test(OverviewPembayaran::class)
            ->assertViewHas('tunggakanCount', 0)
            ->assertViewHas('lunasCount', 1)
            ->assertViewHas('nominalTunggakan', 0.0)
            ->assertViewHas('nominalMendatang', 350000.0);

        // Reminder notification should refuse to send since student has no overdue arrears
        $component->call('kirimReminder', $this->siswa->id)
            ->assertSee("tidak memiliki tunggakan jatuh tempo");

        $this->assertEquals(0, Notifikasi::count());
    }

    public function test_laporan_tunggakan_defaults_to_bills_due_up_to_current_month(): void
    {
        // Arrears bill (overdue)
        $overdueBill = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Agustus',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->subMonth()->startOfMonth()->addDays(9)->toDateString(),
        ]);

        // Future bill (not overdue)
        $futureBill = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'November',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->addMonths(2)->startOfMonth()->addDays(9)->toDateString(),
        ]);

        $this->actingAs($this->financeUser);

        $component = Livewire::test(LaporanTunggakan::class);
        $tunggakans = $component->viewData('tunggakans');
        $this->assertTrue($tunggakans->pluck('id')->contains($overdueBill->id));
        $this->assertFalse($tunggakans->pluck('id')->contains($futureBill->id));
    }

    public function test_detail_tagihan_siswa_computes_arrears_vs_future_bills(): void
    {
        // Overdue bill
        Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->subMonths(2)->startOfMonth()->addDays(9)->toDateString(),
        ]);

        // Future bill
        Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Desember',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->addMonths(3)->startOfMonth()->addDays(9)->toDateString(),
        ]);

        $this->actingAs($this->financeUser);

        Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
            ->assertViewHas('totalTunggakan', 350000.00)
            ->assertViewHas('totalMendatang', 350000.00)
            ->assertViewHas('countTunggakan', 1)
            ->assertViewHas('countMendatang', 1);
    }
}
