<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\NilaiTahfidz;
use App\Models\Notifikasi;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Livewire\Murid\SetoranTahfidz;
use App\Livewire\Guru\InputNilaiTahfidz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TahfidzParentFeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_view_tahfidz_setoran_and_submit_feedback(): void
    {
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        $guruRole = Role::where('nama', 'guru')->first();
        $muridRole = Role::where('nama', 'murid')->first();

        // 1. Create Active Semester & Year
        Semester::query()->update(['status_aktif' => false]);
        $ta = TahunAjaran::create([
            'nama' => '2026/2027',
            'status_aktif' => true,
        ]);
        $semester = Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'semester' => 1,
            'status_aktif' => true,
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
        ]);

        // 2. Create Guru Tahfidz User
        $userGuru = User::factory()->create([
            'nama' => 'Ustadz Abdullah',
            'username' => 'ustadzabdullah',
            'role_id' => $guruRole->id,
        ]);
        $guru = Guru::create([
            'user_id' => $userGuru->id,
            'nip' => '1234567890',
            'jenis_guru' => 'tahfidz',
            'tanggal_masuk' => date('Y-m-d'),
        ]);

        // 3. Create Kelas
        $kelas = Kelas::create([
            'nama_kelas' => 'Halaqah Al-Fatih',
            'tingkat' => 1,
            'semester_id' => $semester->id,
            'guru_tahfidz_id' => $guru->id,
        ]);

        // 4. Create Student User & Siswa
        $userMurid = User::factory()->create([
            'nama' => 'Ahmad Fauzi',
            'username' => 'ahmadfauzi',
            'role_id' => $muridRole->id,
        ]);
        $siswa = Siswa::create([
            'user_id' => $userMurid->id,
            'kelas_id' => $kelas->id,
            'nis' => '1001',
            'nisn' => '001001',
            'jenis_kelamin' => 'L',
            'tanggal_masuk' => date('Y-m-d'),
            'status' => 'aktif',
        ]);

        // 5. Create Nilai Tahfidz record
        $nilaiTahfidz = NilaiTahfidz::create([
            'siswa_id' => $siswa->id,
            'semester_id' => $semester->id,
            'surah' => 'Al-Baqarah',
            'juz' => 1,
            'materi_tahsin' => 'Surah Al-Baqarah 1-20',
            'nilai_tahsin' => 90,
            'materi_ziyadah' => 'Surah Al-Baqarah 21-30',
            'nilai_ziyadah' => 88,
            'catatan_ustadz' => 'Sangat baik, tajwid dan makhraj lancar.',
            'predikat_keagamaan' => 'Sangat Baik',
        ]);

        // 6. Test Murid/Parent access and feedback submission
        $this->actingAs($userMurid);

        Livewire::test(SetoranTahfidz::class)
            ->assertStatus(200)
            ->assertSee('Setoran Tahfizh')
            ->assertSee('Al-Baqarah')
            ->call('openFeedbackModal', $nilaiTahfidz->id)
            ->set('dikirim_oleh_nama', 'Ayahanda Ahmad Fauzi')
            ->set('tanggapan_orang_tua', 'Alhamdulillah di rumah ananda rutin murajaah Ba\'da Maghrib, mohon bimbingannya Ustadz.')
            ->call('submitFeedback')
            ->assertHasNoErrors();

        // 7. Verify Database state
        $nilaiTahfidz->refresh();
        $this->assertEquals('Alhamdulillah di rumah ananda rutin murajaah Ba\'da Maghrib, mohon bimbingannya Ustadz.', $nilaiTahfidz->tanggapan_orang_tua);
        $this->assertEquals('Ayahanda Ahmad Fauzi', $nilaiTahfidz->dikirim_oleh_nama);
        $this->assertNotNull($nilaiTahfidz->tanggal_tanggapan);

        // 8. Verify Notification sent to Guru Tahfidz
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $userGuru->id,
            'judul' => 'Tanggapan Orang Tua Santri Baru',
        ]);

        // 9. Test Guru Tahfidz viewing parent feedback
        $this->actingAs($userGuru);

        Livewire::test(InputNilaiTahfidz::class)
            ->assertStatus(200)
            ->assertSee('Ayahanda Ahmad Fauzi')
            ->assertSee('Alhamdulillah di rumah ananda rutin murajaah Ba\'da Maghrib, mohon bimbingannya Ustadz.');
    }
}
