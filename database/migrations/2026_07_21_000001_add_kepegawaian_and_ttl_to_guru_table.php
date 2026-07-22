<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            if (!Schema::hasColumn('guru', 'status_kepegawaian')) {
                $table->enum('status_kepegawaian', ['pns', 'gtt', 'honorer'])->default('honorer')->after('jenis_guru');
            }
            if (!Schema::hasColumn('guru', 'tempat_lahir')) {
                $table->string('tempat_lahir')->nullable()->after('status_kepegawaian');
            }
            if (!Schema::hasColumn('guru', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('guru', 'status_kepegawaian')) $columns[] = 'status_kepegawaian';
            if (Schema::hasColumn('guru', 'tempat_lahir')) $columns[] = 'tempat_lahir';
            if (Schema::hasColumn('guru', 'tanggal_lahir')) $columns[] = 'tanggal_lahir';

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
