<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->boolean('status_aktif')->default(false);
            $table->timestamps();
        });

        Schema::create('semester', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->enum('semester', ['ganjil', 'genap']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('status_aktif')->default(false);
            $table->timestamps();
        });

        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mapel');
            $table->enum('jenis', ['umum', 'tahfidz']);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('komponen_nilai', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('kategori', ['pengetahuan', 'keterampilan', 'sikap', 'keagamaan']);
            $table->decimal('bobot', 5, 2);
            $table->enum('berlaku_untuk', ['umum', 'tahfidz', 'semua']);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
        Schema::dropIfExists('komponen_nilai');
        Schema::dropIfExists('mata_pelajaran');
        Schema::dropIfExists('semester');
        Schema::dropIfExists('tahun_ajaran');
    }
};
