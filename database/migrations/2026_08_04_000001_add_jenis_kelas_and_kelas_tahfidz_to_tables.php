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
        Schema::table('kelas', function (Blueprint $table) {
            if (!Schema::hasColumn('kelas', 'jenis_kelas')) {
                $table->enum('jenis_kelas', ['umum', 'tahfidz'])->default('umum')->after('nama_kelas');
            }
        });

        Schema::table('siswa', function (Blueprint $table) {
            if (!Schema::hasColumn('siswa', 'kelas_tahfidz_id')) {
                $table->foreignId('kelas_tahfidz_id')->nullable()->after('kelas_id')->constrained('kelas')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            if (Schema::hasColumn('siswa', 'kelas_tahfidz_id')) {
                $table->dropForeign(['kelas_tahfidz_id']);
                $table->dropColumn('kelas_tahfidz_id');
            }
        });

        Schema::table('kelas', function (Blueprint $table) {
            if (Schema::hasColumn('kelas', 'jenis_kelas')) {
                $table->dropColumn('jenis_kelas');
            }
        });
    }
};
