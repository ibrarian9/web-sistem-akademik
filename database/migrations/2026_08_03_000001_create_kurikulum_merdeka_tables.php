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
        Schema::create('lingkup_materi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapel_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->string('nama_lingkup_materi');
            $table->string('kategori')->nullable();
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });

        Schema::create('tujuan_pembelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lingkup_materi_id')->constrained('lingkup_materi')->onDelete('cascade');
            $table->text('deskripsi_tp');
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });

        Schema::create('template_deskripsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapel_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->string('frasa_tertinggi')->default('menunjukkan penguasaan dalam');
            $table->string('frasa_terendah')->default('membutuhkan penguatan dalam');
            $table->timestamps();
        });

        Schema::create('nilai_sumatif_tp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('tp_id')->constrained('tujuan_pembelajaran')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->decimal('nilai', 5, 2);
            $table->timestamps();

            $table->unique(['siswa_id', 'tp_id', 'semester_id']);
        });

        Schema::create('nilai_sas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('mapel_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->decimal('nilai', 5, 2);
            $table->timestamps();

            $table->unique(['siswa_id', 'mapel_id', 'semester_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_sas');
        Schema::dropIfExists('nilai_sumatif_tp');
        Schema::dropIfExists('template_deskripsi');
        Schema::dropIfExists('tujuan_pembelajaran');
        Schema::dropIfExists('lingkup_materi');
    }
};
