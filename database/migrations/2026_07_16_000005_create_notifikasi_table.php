<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('siswa_id')->nullable()->constrained('siswa')->nullOnDelete();
            $table->string('judul');
            $table->text('isi_pesan');
            $table->string('jenis'); // 'tunggakan', 'rapor_terbit', 'absensi', 'pengumuman', 'sistem'
            $table->enum('channel', ['in_app', 'whatsapp', 'email'])->default('in_app');
            $table->enum('status_kirim', ['pending', 'terkirim', 'gagal'])->default('terkirim');
            $table->dateTime('dibaca_pada')->nullable();
            $table->dateTime('dikirim_pada')->nullable();
            $table->string('tabel_terkait')->nullable();
            $table->unsignedBigInteger('data_id_terkait')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'dibaca_pada']);
            $table->index('siswa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
