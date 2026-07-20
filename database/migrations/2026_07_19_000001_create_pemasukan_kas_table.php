<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemasukan_kas', function (Blueprint $table) {
            $table->id();
            $table->string('kategori'); // Infaq, Sedekah Subuh, Maghrib Mengaji, Donasi, Lainnya
            $table->decimal('jumlah', 12, 2);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->foreignId('petugas_id')->constrained('users');
            $table->string('bukti')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasukan_kas');
    }
};
