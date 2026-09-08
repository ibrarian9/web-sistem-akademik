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
        Schema::table('nilai_sas', function (Blueprint $table) {
            $table->decimal('nilai_uh', 5, 2)->nullable()->after('nilai');
            $table->decimal('nilai_uts', 5, 2)->nullable()->after('nilai_uh');
            $table->decimal('nilai_sas', 5, 2)->nullable()->after('nilai_uts');
        });

        // Populate nilai_sas from existing nilai if present
        \Illuminate\Support\Facades\DB::table('nilai_sas')
            ->whereNotNull('nilai')
            ->update(['nilai_sas' => \Illuminate\Support\Facades\DB::raw('nilai')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_sas', function (Blueprint $table) {
            $table->dropColumn(['nilai_uh', 'nilai_uts', 'nilai_sas']);
        });
    }
};
