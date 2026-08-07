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
        Schema::table('nilai_tahfidz', function (Blueprint $table) {
            if (!Schema::hasColumn('nilai_tahfidz', 'tanggapan_orang_tua')) {
                $table->text('tanggapan_orang_tua')->nullable()->after('catatan_ustadz');
            }
            if (!Schema::hasColumn('nilai_tahfidz', 'tanggal_tanggapan')) {
                $table->timestamp('tanggal_tanggapan')->nullable()->after('tanggapan_orang_tua');
            }
            if (!Schema::hasColumn('nilai_tahfidz', 'dikirim_oleh_nama')) {
                $table->string('dikirim_oleh_nama')->nullable()->after('tanggal_tanggapan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_tahfidz', function (Blueprint $table) {
            if (Schema::hasColumn('nilai_tahfidz', 'tanggapan_orang_tua')) {
                $table->dropColumn(['tanggapan_orang_tua', 'tanggal_tanggapan', 'dikirim_oleh_nama']);
            }
        });
    }
};
