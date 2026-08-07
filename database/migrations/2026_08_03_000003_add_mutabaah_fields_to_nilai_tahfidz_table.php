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
            $table->string('materi_tahsin')->nullable()->after('surah');
            $table->decimal('nilai_tahsin', 5, 2)->nullable()->after('materi_tahsin');
            $table->string('murajaah_bersama')->nullable()->after('nilai_tahsin');
            $table->string('murajaah_mandiri')->nullable()->after('murajaah_bersama');
            $table->decimal('nilai_murajaah', 5, 2)->nullable()->after('murajaah_mandiri');
            $table->string('materi_kitabah')->nullable()->after('nilai_murajaah');
            $table->decimal('nilai_kitabah', 5, 2)->nullable()->after('materi_kitabah');
            $table->string('materi_ziyadah')->nullable()->after('nilai_kitabah');
            $table->decimal('nilai_ziyadah', 5, 2)->nullable()->after('materi_ziyadah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_tahfidz', function (Blueprint $table) {
            $table->dropColumn([
                'materi_tahsin',
                'nilai_tahsin',
                'murajaah_bersama',
                'murajaah_mandiri',
                'nilai_murajaah',
                'materi_kitabah',
                'nilai_kitabah',
                'materi_ziyadah',
                'nilai_ziyadah',
            ]);
        });
    }
};
