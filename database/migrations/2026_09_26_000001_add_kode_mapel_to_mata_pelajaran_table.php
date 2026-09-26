<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            if (!Schema::hasColumn('mata_pelajaran', 'kode_mapel')) {
                $table->string('kode_mapel', 20)->nullable()->unique()->after('id');
            }
        });

        // Seed default codes for existing subjects
        $defaultCodes = [
            'Matematika' => 'MTK',
            'IPA' => 'IPA',
            'IPS' => 'IPS',
            'Bahasa Indonesia' => 'BIND',
            'Tahfidz Al-Quran' => 'THF',
            'Pendidikan Agama Islam' => 'PAI',
        ];

        foreach ($defaultCodes as $name => $code) {
            DB::table('mata_pelajaran')
                ->where('nama_mapel', $name)
                ->whereNull('kode_mapel')
                ->update(['kode_mapel' => $code]);
        }
    }

    public function down(): void
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            if (Schema::hasColumn('mata_pelajaran', 'kode_mapel')) {
                $table->dropColumn('kode_mapel');
            }
        });
    }
};
