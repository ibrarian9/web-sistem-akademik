<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->foreignId('siswa_id')->nullable()->after('causer_id')->constrained('siswa')->nullOnDelete();
            $table->string('ip_address', 45)->nullable()->after('properties');
            
            $table->index(['siswa_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropForeign(['siswa_id']);
            $table->dropColumn(['siswa_id', 'ip_address']);
        });
    }
};
