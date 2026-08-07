<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            if (!Schema::hasColumn('guru', 'nik')) {
                $table->string('nik', 20)->nullable()->after('nip');
            }
            if (!Schema::hasColumn('guru', 'pendidikan')) {
                $table->string('pendidikan', 100)->nullable()->after('status_kepegawaian');
            }
            if (!Schema::hasColumn('guru', 'grade_guru')) {
                $table->string('grade_guru', 50)->nullable()->after('pendidikan');
            }
            if (!Schema::hasColumn('guru', 'status_pernikahan')) {
                $table->enum('status_pernikahan', ['belum_menikah', 'menikah', 'cerai_hidup', 'cerai_mati'])->default('belum_menikah')->after('grade_guru');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('guru', 'nik')) $columns[] = 'nik';
            if (Schema::hasColumn('guru', 'pendidikan')) $columns[] = 'pendidikan';
            if (Schema::hasColumn('guru', 'grade_guru')) $columns[] = 'grade_guru';
            if (Schema::hasColumn('guru', 'status_pernikahan')) $columns[] = 'status_pernikahan';

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
