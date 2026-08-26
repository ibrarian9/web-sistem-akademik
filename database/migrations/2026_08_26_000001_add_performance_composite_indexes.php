<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->index(['status', 'jatuh_tempo'], 'idx_tagihan_status_tempo');
            $table->index(['tahun_ajaran_id', 'bulan'], 'idx_tagihan_ta_bulan');
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->index(['tanggal_bayar', 'is_void'], 'idx_pembayaran_tgl_void');
        });

        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->index(['tanggal', 'kategori_pengeluaran_id'], 'idx_pengeluaran_tgl_kat');
        });

        Schema::table('absensi_guru', function (Blueprint $table) {
            $table->index(['tanggal', 'status'], 'idx_absensi_guru_tgl_status');
        });

        Schema::table('absensi_siswa', function (Blueprint $table) {
            $table->index(['tanggal', 'status'], 'idx_absensi_siswa_tgl_status');
        });

        Schema::table('nilai', function (Blueprint $table) {
            $table->index(['siswa_id', 'mapel_id', 'semester_id'], 'idx_nilai_siswa_mapel_smt');
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->index(['status', 'kelas_id'], 'idx_siswa_status_kelas');
        });

        Schema::table('guru', function (Blueprint $table) {
            $table->index(['status_aktif'], 'idx_guru_status_aktif');
        });
    }

    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropIndex('idx_tagihan_status_tempo');
            $table->dropIndex('idx_tagihan_ta_bulan');
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropIndex('idx_pembayaran_tgl_void');
        });

        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->dropIndex('idx_pengeluaran_tgl_kat');
        });

        Schema::table('absensi_guru', function (Blueprint $table) {
            $table->dropIndex('idx_absensi_guru_tgl_status');
        });

        Schema::table('absensi_siswa', function (Blueprint $table) {
            $table->dropIndex('idx_absensi_siswa_tgl_status');
        });

        Schema::table('nilai', function (Blueprint $table) {
            $table->dropIndex('idx_nilai_siswa_mapel_smt');
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropIndex('idx_siswa_status_kelas');
        });

        Schema::table('guru', function (Blueprint $table) {
            $table->dropIndex('idx_guru_status_aktif');
        });
    }
};
