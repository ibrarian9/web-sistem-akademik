<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gaji_guru', function (Blueprint $table) {
            if (!Schema::hasColumn('gaji_guru', 'bukti_bayar')) {
                $table->string('bukti_bayar')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('gaji_guru', function (Blueprint $table) {
            if (Schema::hasColumn('gaji_guru', 'bukti_bayar')) {
                $table->dropColumn('bukti_bayar');
            }
        });
    }
};
