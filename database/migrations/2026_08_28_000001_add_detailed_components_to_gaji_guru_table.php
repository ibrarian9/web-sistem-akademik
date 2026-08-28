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
        Schema::table('gaji_guru', function (Blueprint $table) {
            $table->decimal('insentif_bpjs', 12, 2)->default(0.00)->change();
            $table->decimal('insentif_maghrib_mengaji', 12, 2)->default(0.00)->change();
            $table->decimal('potongan_peminjaman', 12, 2)->default(0.00)->change();
            $table->decimal('potongan_lainnya', 12, 2)->default(0.00)->change();

            $table->decimal('gaji_berkala', 12, 2)->default(0.00)->after('gaji_pokok');
            $table->integer('jumlah_ekskul')->default(0)->after('gaji_berkala');
            $table->decimal('honor_ekskul', 12, 2)->default(0.00)->after('jumlah_ekskul');
            $table->decimal('insentif', 12, 2)->default(0.00)->after('honor_ekskul');
            $table->decimal('potongan_sosial', 12, 2)->default(10000.00)->after('insentif_maghrib_mengaji');
            $table->decimal('potongan_bpjstk', 12, 2)->default(0.00)->after('potongan_peminjaman');
            $table->decimal('total_bruto', 12, 2)->default(0.00)->after('potongan_lainnya');
            $table->string('sumber_dana')->default('Yayasan')->after('status');
            $table->string('jam_kerja')->nullable()->after('sumber_dana');
            $table->string('jabatan')->nullable()->after('jam_kerja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gaji_guru', function (Blueprint $table) {
            $table->dropColumn([
                'gaji_berkala',
                'jumlah_ekskul',
                'honor_ekskul',
                'insentif',
                'potongan_sosial',
                'potongan_bpjstk',
                'total_bruto',
                'sumber_dana',
                'jam_kerja',
                'jabatan',
            ]);
        });
    }
};
