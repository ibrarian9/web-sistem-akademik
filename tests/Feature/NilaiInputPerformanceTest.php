<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\KomponenNilai;
use App\Models\LingkupMateri;
use App\Models\TujuanPembelajaran;
use App\Models\Nilai;
use App\Models\NilaiSumatifTp;
use App\Models\NilaiSas;
use App\Models\Rapor;
use App\Models\RaporDetail;
use Livewire\Livewire;
use App\Livewire\Guru\InputNilaiSiswa;
use App\Livewire\Guru\InputNilaiSumatif;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class NilaiInputPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $guruUser;
    protected Guru $guru;
    protected Kelas $kelas;
    protected MataPelajaran $mapel;
    protected TahunAjaran $tahunAjaran;
    protected Semester $semester;
    protected KomponenNilai $komponen;

    protected function setUp(): void
    {
        parent::setUp();

        $roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);
        $roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);

        $this->guruUser = User::factory()->create([
            'username' => 'guru_perf_' . uniqid(),
            'nama' => 'Ustadz Ahmad, S.Pd.',
            'role_id' => $roleGuru->id,
            'status' => 'aktif',
        ]);

        $this->guru = Guru::create([
            'user_id' => $this->guruUser->id,
            'nip' => '198801012022011001',
            'jenis_guru' => 'umum',
            'status_kepegawaian' => 'tetap_yayasan',
            'tanggal_masuk' => '2022-01-01',
        ]);

        $this->tahunAjaran = TahunAjaran::create([
            'nama' => '2026/2027',
            'status_aktif' => true,
        ]);

        $this->semester = Semester::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'semester' => 'Ganjil',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
            'status_aktif' => true,
        ]);

        $this->kelas = Kelas::create([
            'nama_kelas' => '5A',
            'jenis_kelas' => 'umum',
            'tingkat' => 5,
            'guru_umum_id' => $this->guru->id,
            'semester_id' => $this->semester->id,
        ]);

        $this->mapel = MataPelajaran::create([
            'nama_mapel' => 'Matematika',
            'kode_mapel' => 'MTK-5',
            'kkm' => 75.00,
        ]);

        $this->komponen = KomponenNilai::create([
            'nama' => 'Ulangan Harian 1',
            'bobot' => 20,
            'urutan' => 1,
            'berlaku_untuk' => 'semua',
        ]);
    }

    public function test_input_nilai_siswa_load_and_save_executes_with_bounded_queries()
    {
        $roleMurid = Role::where('nama', 'murid')->first();
        $students = [];

        for ($i = 1; $i <= 15; $i++) {
            $u = User::factory()->create([
                'username' => 'murid_perf_' . $i . '_' . uniqid(),
                'role_id' => $roleMurid->id,
                'nama' => "Siswa Perf {$i}",
                'status' => 'aktif',
            ]);

            $students[] = Siswa::create([
                'user_id' => $u->id,
                'kelas_id' => $this->kelas->id,
                'nis' => (string)(2000 + $i),
                'nisn' => '002000' . str_pad((string)$i, 4, '0', STR_PAD_LEFT),
                'jenis_kelamin' => 'L',
                'tanggal_masuk' => '2026-07-01',
                'status' => 'aktif',
            ]);
        }

        $this->actingAs($this->guruUser);

        $component = Livewire::test(InputNilaiSiswa::class)
            ->set('kelas_id', $this->kelas->id)
            ->set('mapel_id', $this->mapel->id)
            ->set('komponen_nilai_id', $this->komponen->id)
            ->set('tanggal', '2026-09-26');

        // Test loading students specifically
        $queryCountOnLoad = 0;
        DB::listen(function () use (&$queryCountOnLoad) {
            $queryCountOnLoad++;
        });

        $component->call('loadStudents');

        $component->assertCount('grades', 15);
        $this->assertLessThan(6, $queryCountOnLoad, 'Explicitly loading 15 students and their grades should execute under 6 queries');

        // Test saving scores
        $gradesPayload = [];
        foreach ($students as $idx => $s) {
            $gradesPayload[] = [
                'siswa_id' => $s->id,
                'nama' => "Siswa Perf " . ($idx + 1),
                'nis' => (string)(2000 + $idx + 1),
                'nilai' => 85.5,
                'catatan' => 'Sangat baik',
            ];
        }

        $queryCountOnSave = 0;
        DB::listen(function () use (&$queryCountOnSave) {
            $queryCountOnSave++;
        });

        $component->set('grades', $gradesPayload)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Nilai siswa berhasil disimpan.');

        $this->assertLessThan(15, $queryCountOnSave, 'Saving 15 student grades should execute fewer than 15 queries');
        $this->assertEquals(15, Nilai::where('kelas_id', $this->kelas->id)->where('nilai', 85.5)->count());
    }

    public function test_input_nilai_sumatif_batch_upsert_and_auto_narasi_sync()
    {
        $roleMurid = Role::where('nama', 'murid')->first();
        $students = [];

        for ($i = 1; $i <= 10; $i++) {
            $u = User::factory()->create([
                'username' => 'murid_sumatif_' . $i . '_' . uniqid(),
                'role_id' => $roleMurid->id,
                'nama' => "Santri {$i}",
                'status' => 'aktif',
            ]);

            $students[] = Siswa::create([
                'user_id' => $u->id,
                'kelas_id' => $this->kelas->id,
                'nis' => (string)(3000 + $i),
                'nisn' => '003000' . str_pad((string)$i, 4, '0', STR_PAD_LEFT),
                'jenis_kelamin' => 'L',
                'tanggal_masuk' => '2026-07-01',
                'status' => 'aktif',
            ]);
        }

        // Lingkup Materi & TPs
        $lm1 = LingkupMateri::create([
            'mapel_id' => $this->mapel->id,
            'nama_lingkup_materi' => 'Bilangan Pecahan',
            'urutan' => 1,
        ]);
        $tp1 = TujuanPembelajaran::create([
            'lingkup_materi_id' => $lm1->id,
            'kode_tp' => 'TP-1',
            'deskripsi_tp' => 'memahami operasi penjumlahan pecahan',
            'urutan' => 1,
        ]);
        $tp2 = TujuanPembelajaran::create([
            'lingkup_materi_id' => $lm1->id,
            'kode_tp' => 'TP-2',
            'deskripsi_tp' => 'menyelesaikan soal cerita pembagian pecahan',
            'urutan' => 2,
        ]);

        $lm2 = LingkupMateri::create([
            'mapel_id' => $this->mapel->id,
            'nama_lingkup_materi' => 'Geometri Bangun Datar',
            'urutan' => 2,
        ]);
        $tp3 = TujuanPembelajaran::create([
            'lingkup_materi_id' => $lm2->id,
            'kode_tp' => 'TP-3',
            'deskripsi_tp' => 'menghitung keliling dan luas segitiga',
            'urutan' => 1,
        ]);

        $this->actingAs($this->guruUser);

        // Prepare matrix data: 10 students * 3 TPs = 30 TP scores + 10 SAS scores
        $tpMatrix = [];
        $sasMatrix = [];

        foreach ($students as $s) {
            $tpMatrix[$s->id] = [
                $tp1->id => 85,
                $tp2->id => 75,
                $tp3->id => 90,
            ];
            $sasMatrix[$s->id] = 80;
        }

        $test = Livewire::test(InputNilaiSumatif::class)
            ->set('kelas_id', $this->kelas->id)
            ->set('mapel_id', $this->mapel->id)
            ->set('semester_id', $this->semester->id)
            ->set('nilaiTpMatrix', $tpMatrix)
            ->set('nilaiSasMatrix', $sasMatrix);

        $queryLog = [];
        DB::listen(function ($query) use (&$queryLog) {
            $queryLog[] = $query->sql;
        });

        $test->call('saveMatrix');

        $test->assertHasNoErrors()
            ->assertDispatched('scores-saved');

        // Bounded queries verification
        $this->assertLessThan(35, count($queryLog), 'Saving 30 TP scores + 10 SAS scores + Rapor sync should execute under 35 queries');

        // Verify DB integrity
        $this->assertEquals(30, NilaiSumatifTp::where('semester_id', $this->semester->id)->count());
        $this->assertEquals(10, NilaiSas::where('mapel_id', $this->mapel->id)->where('semester_id', $this->semester->id)->count());
        $this->assertEquals(10, Rapor::where('semester_id', $this->semester->id)->where('tipe_rapor', 'akademik')->count());
        $this->assertEquals(10, RaporDetail::where('mapel_id', $this->mapel->id)->count());

        // Verify accurate calculated values on Rapor Detail
        $sampleDetail = RaporDetail::where('mapel_id', $this->mapel->id)->first();
        $this->assertNotNull($sampleDetail);
        $this->assertNotNull($sampleDetail->predikat);
        $this->assertNotEmpty($sampleDetail->deskripsi_tertinggi);
        $this->assertNotEmpty($sampleDetail->narasi_capaian_full);
    }
}
