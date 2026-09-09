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
        Schema::create('catatan_pendampingan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('guru')->nullOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained('semester')->nullOnDelete();
            $table->date('tanggal');
            // 7 Aspek Pengamatan: Komunikasi, Sosial/Interaksi, Kemandirian, Perilaku, Konsentrasi/Fokus, Aktivitas Belajar, Motorik
            $table->string('aspek');
            // Standarisasi Penilaian Kualitatif: BB (Belum Berkembang), MB (Mulai Berkembang), BSH (Berkembang Sesuai Harapan), BSB (Berkembang Sangat Baik)
            $table->enum('hasil_perkembangan', ['BB', 'MB', 'BSH', 'BSB']);
            $table->text('catatan'); // Catatan / Deskripsi Perkembangan
            $table->text('rekomendasi')->nullable(); // Rekomendasi / Tindak Lanjut Guru Pendamping
            $table->enum('periode', ['tengah_semester', 'akhir_semester'])->default('tengah_semester');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['siswa_id', 'periode']);
            $table->index(['aspek', 'hasil_perkembangan']);
            $table->index(['tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_pendampingan');
    }
};
