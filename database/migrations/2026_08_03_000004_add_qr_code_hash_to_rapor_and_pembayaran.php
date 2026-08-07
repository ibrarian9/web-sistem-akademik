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
        Schema::table('rapor', function (Blueprint $table) {
            if (!Schema::hasColumn('rapor', 'qr_code_hash')) {
                $table->string('qr_code_hash', 64)->nullable()->unique()->after('tanggal_terbit');
            }
            if (!Schema::hasColumn('rapor', 'status_terbit')) {
                $table->boolean('status_terbit')->default(true)->after('qr_code_hash');
            }
        });

        Schema::table('rapor_detail', function (Blueprint $table) {
            if (!Schema::hasColumn('rapor_detail', 'deskripsi_tertinggi')) {
                $table->text('deskripsi_tertinggi')->nullable()->after('predikat');
            }
            if (!Schema::hasColumn('rapor_detail', 'deskripsi_terendah')) {
                $table->text('deskripsi_terendah')->nullable()->after('deskripsi_tertinggi');
            }
            if (!Schema::hasColumn('rapor_detail', 'narasi_capaian_full')) {
                $table->text('narasi_capaian_full')->nullable()->after('deskripsi_terendah');
            }
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            if (!Schema::hasColumn('pembayaran', 'qr_code_hash')) {
                $table->string('qr_code_hash', 64)->nullable()->unique()->after('no_resi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn('qr_code_hash');
        });

        Schema::table('rapor_detail', function (Blueprint $table) {
            $table->dropColumn(['deskripsi_tertinggi', 'deskripsi_terendah', 'narasi_capaian_full']);
        });

        Schema::table('rapor', function (Blueprint $table) {
            $table->dropColumn(['qr_code_hash', 'status_terbit']);
        });
    }
};
