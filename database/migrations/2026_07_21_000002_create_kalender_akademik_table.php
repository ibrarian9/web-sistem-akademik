<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kalender_akademik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajaran')->nullOnDelete();
            $table->string('nama_kegiatan');
            $table->enum('jenis', ['hari_libur', 'libur_semester', 'kegiatan_akademik', 'ujian'])->default('hari_libur');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('liburkan_presensi')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kalender_akademik');
    }
};
