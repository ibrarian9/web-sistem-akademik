<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add schedule and quota fields to existing ekstrakurikuler table
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            if (!Schema::hasColumn('ekstrakurikuler', 'hari')) {
                $table->string('hari', 20)->nullable()->after('deskripsi');
            }
            if (!Schema::hasColumn('ekstrakurikuler', 'jam_mulai')) {
                $table->string('jam_mulai', 10)->nullable()->after('hari');
            }
            if (!Schema::hasColumn('ekstrakurikuler', 'jam_selesai')) {
                $table->string('jam_selesai', 10)->nullable()->after('jam_mulai');
            }
            if (!Schema::hasColumn('ekstrakurikuler', 'tempat')) {
                $table->string('tempat', 100)->nullable()->after('jam_selesai');
            }
            if (!Schema::hasColumn('ekstrakurikuler', 'kuota')) {
                $table->integer('kuota')->default(30)->after('tempat');
            }
            if (!Schema::hasColumn('ekstrakurikuler', 'status_aktif')) {
                $table->boolean('status_aktif')->default(true)->after('kuota');
            }
        });

        // 2. Create kegiatan_ekstrakurikuler table (sesi/pertemuan)
        if (!Schema::hasTable('kegiatan_ekstrakurikuler')) {
            Schema::create('kegiatan_ekstrakurikuler', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ekstrakurikuler_id')->constrained('ekstrakurikuler')->cascadeOnDelete();
                $table->foreignId('semester_id')->nullable()->constrained('semester')->nullOnDelete();
                $table->date('tanggal');
                $table->string('nama_kegiatan');
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        // 3. Create presensi_ekstrakurikuler table (presensi & nilai berkala per sesi)
        if (!Schema::hasTable('presensi_ekstrakurikuler')) {
            Schema::create('presensi_ekstrakurikuler', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kegiatan_ekstrakurikuler_id')->constrained('kegiatan_ekstrakurikuler')->cascadeOnDelete();
                $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
                $table->enum('status_kehadiran', ['Hadir', 'Sakit', 'Izin', 'Alpa'])->default('Hadir');
                $table->decimal('nilai', 5, 2)->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->unique(['kegiatan_ekstrakurikuler_id', 'siswa_id'], 'kegiatan_siswa_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_ekstrakurikuler');
        Schema::dropIfExists('kegiatan_ekstrakurikuler');

        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            $table->dropColumn([
                'hari',
                'jam_mulai',
                'jam_selesai',
                'tempat',
                'kuota',
                'status_aktif',
            ]);
        });
    }
};
