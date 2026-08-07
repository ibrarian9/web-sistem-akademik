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
        Schema::create('target_hafalan_tahfidz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->string('tingkat')->nullable();
            $table->string('nama_surah');
            $table->integer('juz')->nullable();
            $table->string('target_ayat')->nullable();
            $table->timestamps();
        });

        Schema::create('nilai_tahfidz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->string('surah');
            $table->integer('juz')->nullable();
            $table->decimal('nilai_kelancaran', 5, 2)->default(0);
            $table->decimal('nilai_tajwid', 5, 2)->default(0);
            $table->string('predikat_keagamaan')->nullable();
            $table->text('catatan_ustadz')->nullable();
            $table->timestamps();
        });

        Schema::create('rapor_tahfidz_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapor_id')->constrained('rapor')->onDelete('cascade');
            $table->integer('total_juz_dihafal')->default(0);
            $table->text('daftar_surah_lulus')->nullable();
            $table->decimal('nilai_tajwid_rata', 5, 2)->default(0);
            $table->string('predikat_tahfidz')->nullable();
            $table->text('catatan_khusus')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapor_tahfidz_detail');
        Schema::dropIfExists('nilai_tahfidz');
        Schema::dropIfExists('target_hafalan_tahfidz');
    }
};
