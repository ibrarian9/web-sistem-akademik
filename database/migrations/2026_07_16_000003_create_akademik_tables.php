<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru_mapel_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru');
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->foreignId('mapel_id')->constrained('mata_pelajaran');
            $table->foreignId('semester_id')->constrained('semester');
            $table->timestamps();

            $table->unique(['guru_id', 'kelas_id', 'mapel_id', 'semester_id'], 'gmk_unique');
        });

        Schema::create('jadwal_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_mapel_kelas_id')->constrained('guru_mapel_kelas')->cascadeOnDelete();
            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();

            $table->unique(['guru_mapel_kelas_id', 'hari', 'jam_mulai'], 'jp_unique');
            $table->index(['guru_mapel_kelas_id', 'hari']);
        });

        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa');
            $table->foreignId('mapel_id')->constrained('mata_pelajaran');
            $table->foreignId('guru_id')->constrained('guru');
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->foreignId('semester_id')->constrained('semester');
            $table->foreignId('komponen_nilai_id')->constrained('komponen_nilai');
            $table->date('tanggal');
            $table->decimal('nilai', 5, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['siswa_id', 'semester_id']);
            $table->index('komponen_nilai_id');
        });

        Schema::create('rapor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa');
            $table->foreignId('semester_id')->constrained('semester');
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->text('catatan_wali_kelas')->nullable();
            $table->date('tanggal_terbit');
            $table->timestamps();

            $table->unique(['siswa_id', 'semester_id']);
        });

        Schema::create('rapor_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapor_id')->constrained('rapor')->cascadeOnDelete();
            $table->foreignId('mapel_id')->constrained('mata_pelajaran');
            $table->decimal('nilai_pengetahuan', 5, 2)->nullable();
            $table->decimal('nilai_keterampilan', 5, 2)->nullable();
            $table->decimal('nilai_sikap', 5, 2)->nullable();
            $table->decimal('nilai_keagamaan', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2);
            $table->string('predikat')->nullable();
            $table->timestamps();

            $table->unique(['rapor_id', 'mapel_id']);
            $table->index('rapor_id');
        });

        Schema::create('absensi_guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru');
            $table->date('tanggal');
            $table->time('waktu_datang')->nullable();
            $table->time('waktu_pulang')->nullable();
            $table->enum('status', ['hadir', 'tidak_hadir', 'izin', 'telat']);
            $table->text('catatan')->nullable();
            $table->foreignId('diinput_oleh')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['guru_id', 'tanggal']);
        });

        Schema::create('absensi_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa');
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->foreignId('guru_id')->constrained('guru');
            $table->date('tanggal');
            $table->enum('status', ['hadir', 'tidak_hadir', 'izin']);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'kelas_id', 'tanggal']);
            $table->index(['kelas_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_siswa');
        Schema::dropIfExists('absensi_guru');
        Schema::dropIfExists('rapor_detail');
        Schema::dropIfExists('rapor');
        Schema::dropIfExists('nilai');
        Schema::dropIfExists('jadwal_pelajaran');
        Schema::dropIfExists('guru_mapel_kelas');
    }
};
