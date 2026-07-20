<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_tagihan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('kategori', ['rutin', 'one_time', 'tahunan']);
            $table->decimal('default_nominal', 12, 2);
            $table->boolean('is_blocking')->default(true);
            $table->timestamps();
        });

        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa');
            $table->foreignId('jenis_tagihan_id')->constrained('jenis_tagihan');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran');
            $table->string('bulan')->nullable();
            $table->decimal('nominal', 12, 2);
            $table->decimal('total_dibayar', 12, 2)->default(0);
            $table->enum('status', ['belum_bayar', 'sebagian', 'lunas', 'batal'])->default('belum_bayar');
            $table->date('jatuh_tempo');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['siswa_id', 'status']);
        });

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('no_resi')->nullable()->unique();
            $table->foreignId('tagihan_id')->constrained('tagihan');
            $table->date('tanggal_bayar');
            $table->decimal('nominal_dibayar', 12, 2);
            $table->decimal('kelebihan_bayar', 12, 2)->default(0.00);
            $table->string('metode_bayar');
            $table->string('bukti_bayar')->nullable();
            $table->boolean('is_void')->default(false);
            $table->foreignId('petugas_id')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('kategori_pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jenis')->nullable();
            $table->timestamps();
        });

        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_pengeluaran_id')->constrained('kategori_pengeluaran');
            $table->decimal('jumlah', 12, 2);
            $table->date('tanggal');
            $table->text('keterangan');
            $table->foreignId('petugas_id')->constrained('users');
            $table->string('bukti')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('gaji_guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru');
            $table->foreignId('pengeluaran_id')->nullable()->constrained('pengeluaran')->nullOnDelete();
            $table->string('bulan');
            $table->integer('tahun');
            $table->decimal('gaji_pokok', 12, 2);
            $table->decimal('insentif_bpjs', 12, 2);
            $table->decimal('insentif_maghrib_mengaji', 12, 2);
            $table->decimal('potongan_peminjaman', 12, 2);
            $table->decimal('potongan_lainnya', 12, 2);
            $table->decimal('total_diterima', 12, 2);
            $table->date('tanggal_bayar');
            $table->enum('status', ['draft', 'dibayar'])->default('draft');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['guru_id', 'bulan', 'tahun']);
        });

        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru');
            $table->date('tanggal_pinjam');
            $table->decimal('nominal', 12, 2);
            $table->integer('tenor_bulan');
            $table->decimal('cicilan_per_bulan', 12, 2);
            $table->decimal('sisa_pinjaman', 12, 2);
            $table->enum('status', ['berjalan', 'lunas'])->default('berjalan');
            $table->timestamps();
        });

        Schema::create('dana_bos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran');
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->date('tanggal');
            $table->decimal('nominal', 12, 2);
            $table->string('kategori');
            $table->text('keterangan');
            $table->string('bukti')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dana_bos');
        Schema::dropIfExists('peminjaman');
        Schema::dropIfExists('gaji_guru');
        Schema::dropIfExists('pengeluaran');
        Schema::dropIfExists('kategori_pengeluaran');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('tagihan');
        Schema::dropIfExists('jenis_tagihan');
    }
};
