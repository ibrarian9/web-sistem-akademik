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
        if (Schema::hasTable('approval_keuangan')) {
            $driver = DB::getDriverName();
            if (in_array($driver, ['mysql', 'mariadb'])) {
                DB::statement("ALTER TABLE approval_keuangan MODIFY COLUMN status ENUM('menunggu', 'disetujui', 'ditolak', 'dibatalkan') NOT NULL DEFAULT 'menunggu'");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('approval_keuangan')) {
            $driver = DB::getDriverName();
            if (in_array($driver, ['mysql', 'mariadb'])) {
                DB::statement("ALTER TABLE approval_keuangan MODIFY COLUMN status ENUM('menunggu', 'disetujui', 'ditolak') NOT NULL DEFAULT 'menunggu'");
            }
        }
    }
};
