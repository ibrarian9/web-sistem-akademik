<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('rapor')->whereNull('tipe_rapor')->update(['tipe_rapor' => 'akademik']);

        try {
            $indexes = DB::select("SHOW INDEX FROM rapor WHERE Key_name = 'rapor_siswa_id_semester_id_unique'");
            if (!empty($indexes)) {
                Schema::table('rapor', function (Blueprint $table) {
                    try {
                        $table->index('siswa_id', 'rapor_siswa_id_fk_idx');
                    } catch (\Throwable $e) {}

                    try {
                        $table->dropUnique('rapor_siswa_id_semester_id_unique');
                    } catch (\Throwable $e) {}
                });
            }
        } catch (\Throwable $e) {}

        try {
            $newIndexes = DB::select("SHOW INDEX FROM rapor WHERE Key_name = 'rapor_siswa_semester_tipe_unique'");
            if (empty($newIndexes)) {
                Schema::table('rapor', function (Blueprint $table) {
                    try {
                        $table->unique(['siswa_id', 'semester_id', 'tipe_rapor'], 'rapor_siswa_semester_tipe_unique');
                    } catch (\Throwable $e) {}
                });
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
