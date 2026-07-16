<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nip')->unique();
            $table->enum('jenis_guru', ['umum', 'tahfidz']);
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_masuk');
            $table->boolean('status_aktif')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->string('tingkat');
            $table->foreignId('semester_id')->constrained('semester');
            $table->foreignId('guru_umum_id')->nullable()->constrained('guru');
            $table->foreignId('guru_tahfidz_id')->nullable()->constrained('guru');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nis')->unique();
            $table->string('nisn')->nullable()->unique();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('nama_wali')->nullable();
            $table->string('no_hp_wali')->nullable();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->date('tanggal_masuk');
            $table->enum('status', ['aktif', 'lulus', 'pindah', 'keluar'])->default('aktif');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('siswa_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->foreignId('semester_id')->constrained('semester');
            $table->enum('status', ['aktif', 'pindah', 'naik_kelas'])->default('aktif');
            $table->timestamps();

            $table->unique(['siswa_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_kelas');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('guru');
    }
};
