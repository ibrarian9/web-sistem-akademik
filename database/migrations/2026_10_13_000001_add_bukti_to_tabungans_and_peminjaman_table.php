<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tabungans') && !Schema::hasColumn('tabungans', 'bukti')) {
            Schema::table('tabungans', function (Blueprint $table) {
                $table->string('bukti')->nullable()->after('keterangan');
            });
        }

        if (Schema::hasTable('peminjaman') && !Schema::hasColumn('peminjaman', 'bukti')) {
            Schema::table('peminjaman', function (Blueprint $table) {
                $table->string('bukti')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tabungans') && Schema::hasColumn('tabungans', 'bukti')) {
            Schema::table('tabungans', function (Blueprint $table) {
                $table->dropColumn('bukti');
            });
        }

        if (Schema::hasTable('peminjaman') && Schema::hasColumn('peminjaman', 'bukti')) {
            Schema::table('peminjaman', function (Blueprint $table) {
                $table->dropColumn('bukti');
            });
        }
    }
};
