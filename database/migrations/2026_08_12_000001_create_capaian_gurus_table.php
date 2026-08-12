<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capaian_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->onDelete('cascade');
            $table->foreignId('penilai_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('judul');
            $table->string('kategori')->default('pengembangan_diri'); // pengembangan_diri, capaian_kinerja, pelatihan, sertifikasi
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajaran')->onDelete('set null');
            $table->foreignId('semester_id')->nullable()->constrained('semester')->onDelete('set null');
            $table->text('link_gdrive')->nullable();
            $table->text('deskripsi')->nullable();
            $table->decimal('skor_nilai', 5, 2)->nullable();
            $table->string('predikat')->nullable(); // Sangat Baik, Baik, Cukup, Perlu Bimbingan
            $table->text('catatan_evaluasi')->nullable();
            $table->enum('status_penilaian', ['diajukan', 'dinilai', 'perlu_revisi'])->default('diajukan');
            $table->date('tanggal_penilaian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capaian_gurus');
    }
};
