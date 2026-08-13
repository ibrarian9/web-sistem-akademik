<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('guru', 'status_kepegawaian')) {
            try {
                DB::statement("ALTER TABLE `guru` MODIFY `status_kepegawaian` VARCHAR(50) NOT NULL DEFAULT 'honorer'");
            } catch (\Throwable $e) {
                Schema::table('guru', function (Blueprint $table) {
                    $table->string('status_kepegawaian', 50)->default('honorer')->change();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('guru', 'status_kepegawaian')) {
            try {
                DB::statement("ALTER TABLE `guru` MODIFY `status_kepegawaian` ENUM('pns', 'gtt', 'honorer', 'tetap_yayasan') NOT NULL DEFAULT 'honorer'");
            } catch (\Throwable $e) {
                // Ignore rollback truncation error
            }
        }
    }
};
