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
            if (!Schema::hasColumn('nilai_tahfidz', 'tanggal')) {
                $table->date('tanggal')->nullable()->after('semester_id');
            }
        });

        // Populate existing null tanggal records with created_at date
        \Illuminate\Support\Facades\DB::table('nilai_tahfidz')
            ->whereNull('tanggal')
            ->update([
                'tanggal' => \Illuminate\Support\Facades\DB::raw('DATE(created_at)')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_tahfidz', function (Blueprint $table) {
            if (Schema::hasColumn('nilai_tahfidz', 'tanggal')) {
                $table->dropColumn('tanggal');
            }
        });
    }
};
