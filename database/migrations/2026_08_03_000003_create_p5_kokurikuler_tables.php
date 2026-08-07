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
        Schema::create('dimensi_p5', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dimensi');
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });

        Schema::create('subdimensi_p5', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimensi_id')->constrained('dimensi_p5')->onDelete('cascade');
            $table->string('nama_subdimensi');
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });

        Schema::create('proyek_p5', function (Blueprint $table) {
            $table->id();
            $table->string('nama_proyek'); // 'lintas_disiplin', '7kaih', 'cara_lain'
            $table->timestamps();
        });

        Schema::create('nilai_p5', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('proyek_id')->constrained('proyek_p5')->onDelete('cascade');
            $table->foreignId('subdimensi_p5_id')->constrained('subdimensi_p5')->onDelete('cascade');
            $table->smallInteger('titik_sumatif')->default(1); // 1..5
            $table->smallInteger('nilai')->default(1); // skala 1..4
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_p5');
        Schema::dropIfExists('proyek_p5');
        Schema::dropIfExists('subdimensi_p5');
        Schema::dropIfExists('dimensi_p5');
    }
};
