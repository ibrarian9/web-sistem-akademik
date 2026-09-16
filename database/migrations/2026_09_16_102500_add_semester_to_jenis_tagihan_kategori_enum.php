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
        DB::statement("ALTER TABLE jenis_tagihan MODIFY COLUMN kategori ENUM('rutin', 'one_time', 'tahunan', 'semester', 'per_6_bulan') NOT NULL DEFAULT 'rutin'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE jenis_tagihan MODIFY COLUMN kategori ENUM('rutin', 'one_time', 'tahunan') NOT NULL DEFAULT 'rutin'");
    }
};
