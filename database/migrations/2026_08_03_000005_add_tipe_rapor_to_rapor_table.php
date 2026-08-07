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
            if (!Schema::hasColumn('rapor', 'tipe_rapor')) {
                $table->string('tipe_rapor')->default('akademik')->after('kelas_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rapor', function (Blueprint $table) {
            if (Schema::hasColumn('rapor', 'tipe_rapor')) {
                $table->dropColumn('tipe_rapor');
            }
        });
    }
};
