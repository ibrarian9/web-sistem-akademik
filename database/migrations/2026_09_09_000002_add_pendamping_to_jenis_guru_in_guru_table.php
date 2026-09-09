<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('guru', 'jenis_guru')) {
            try {
                DB::statement("ALTER TABLE `guru` MODIFY `jenis_guru` VARCHAR(50) NOT NULL DEFAULT 'umum'");
            } catch (\Throwable $e) {
                Schema::table('guru', function (Blueprint $table) {
                    $table->string('jenis_guru', 50)->default('umum')->change();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('guru', 'jenis_guru')) {
            try {
                DB::statement("ALTER TABLE `guru` MODIFY `jenis_guru` ENUM('umum', 'tahfidz', 'keduanya') NOT NULL DEFAULT 'umum'");
            } catch (\Throwable $e) {
                // Ignore rollback truncation error
            }
        }
    }
};
