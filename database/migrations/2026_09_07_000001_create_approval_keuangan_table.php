<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ensure role 'super_admin_2' exists in roles table
        if (Schema::hasTable('roles')) {
            $exists = DB::table('roles')->where('nama', 'super_admin_2')->exists();
            if (!$exists) {
                DB::table('roles')->insert([
                    'nama' => 'super_admin_2',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 2. Create approval_keuangan table
        Schema::create('approval_keuangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemohon_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipe_aksi', ['edit', 'hapus']);
            $table->string('fitur', 50); // 'tagihan', 'pembayaran', 'tabungan', 'arus_kas', 'dana_bos', 'gaji_guru'
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->string('judul');
            $table->json('data_lama')->nullable();
            $table->json('data_baru')->nullable();
            $table->text('alasan');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('tanggal_disetujui')->nullable();
            $table->text('catatan_approval')->nullable();
            $table->timestamps();

            $table->index(['status', 'tipe_aksi']);
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_keuangan');
    }
};
