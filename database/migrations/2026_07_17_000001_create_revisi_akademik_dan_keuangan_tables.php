<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add alumni fields to siswa table and kkm to mata_pelajaran
        Schema::table('siswa', function (Blueprint $table) {
            $table->year('tahun_lulus')->nullable()->after('status');
            $table->text('catatan_alumni')->nullable()->after('tahun_lulus');
        });

        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->decimal('kkm', 5, 2)->default(70.00)->after('deskripsi');
        });

        // 2. Create ekstrakurikuler table
        Schema::create('ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('pembina_guru_id')->nullable()->constrained('guru')->nullOnDelete();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 3. Create siswa_ekstrakurikuler table
        Schema::create('siswa_ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('ekstrakurikuler_id')->constrained('ekstrakurikuler')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semester')->cascadeOnDelete();
            $table->string('predikat'); // A, B, C, D
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'ekstrakurikuler_id', 'semester_id'], 'siswa_ekskul_semester_unique');
        });

        // 4. Create jadwal_piket_guru table
        Schema::create('jadwal_piket_guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat']);
            $table->foreignId('semester_id')->constrained('semester')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['guru_id', 'hari', 'semester_id'], 'guru_piket_hari_semester_unique');
        });

        // 5. Create bobot_nilai_guru table
        Schema::create('bobot_nilai_guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_mapel_kelas_id')->constrained('guru_mapel_kelas')->cascadeOnDelete();
            $table->foreignId('komponen_nilai_id')->constrained('komponen_nilai')->cascadeOnDelete();
            $table->decimal('bobot', 5, 2);
            $table->timestamps();

            $table->unique(['guru_mapel_kelas_id', 'komponen_nilai_id'], 'guru_mapel_komponen_unique');
        });

        // 6. Create pengajuan_koreksi_nilai table
        Schema::create('pengajuan_koreksi_nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nilai_id')->constrained('nilai')->cascadeOnDelete();
            $table->decimal('nilai_baru', 5, 2);
            $table->text('alasan');
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->foreignId('diajukan_oleh_guru_id')->constrained('guru')->cascadeOnDelete();
            $table->foreignId('disetujui_oleh_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_koreksi_nilai');
        Schema::dropIfExists('bobot_nilai_guru');
        Schema::dropIfExists('jadwal_piket_guru');
        Schema::dropIfExists('siswa_ekstrakurikuler');
        Schema::dropIfExists('ekstrakurikuler');

        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->dropColumn('kkm');
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn(['tahun_lulus', 'catatan_alumni']);
        });
    }
};
