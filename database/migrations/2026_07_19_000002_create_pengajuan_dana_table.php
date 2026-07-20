<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_dana', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('kategori'); // Pembelian Buku, Seragam, Operasional, Sarana Prasarana, dll.
            $table->decimal('nominal', 12, 2);
            $table->date('tanggal_pengajuan');
            $table->text('keterangan');
            $table->string('bukti_proposal')->nullable();
            $table->foreignId('pemohon_id')->constrained('users');
            $table->enum('status', [
                'menunggu_koordinator',
                'menunggu_kepala_yayasan',
                'disetujui',
                'ditolak',
                'direalisasi'
            ])->default('menunggu_koordinator');
            $table->foreignId('disetujui_koordinator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_persetujuan_koordinator')->nullable();
            $table->foreignId('disetujui_kepala_yayasan_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_persetujuan_kepala_yayasan')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->foreignId('pengeluaran_id')->nullable()->constrained('pengeluaran')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_dana');
    }
};
