<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'ttd_digital')) {
                $table->string('ttd_digital')->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'nip')) {
                $table->string('nip')->nullable()->after('ttd_digital');
            }
            if (!Schema::hasColumn('users', 'jabatan')) {
                $table->string('jabatan')->nullable()->after('nip');
            }
        });

        $defaultSettings = [
            ['key' => 'nama_instansi', 'value' => 'Yayasan Pendidikan Islam', 'keterangan' => 'Nama Instansi / Sekolah'],
            ['key' => 'alamat_instansi', 'value' => 'Jl. Kaliurang Km. 10, Sleman, D.I. Yogyakarta', 'keterangan' => 'Alamat Lengkap Instansi'],
            ['key' => 'telepon_instansi', 'value' => '(0274) 123456', 'keterangan' => 'Nomor Telepon Resmi Instansi'],
            ['key' => 'kepala_sekolah_nama', 'value' => 'Drs. H. Ahmad Fauzi, M.Pd.', 'keterangan' => 'Nama Kepala Sekolah'],
            ['key' => 'kepala_sekolah_nip', 'value' => '19750812 200003 1 001', 'keterangan' => 'NIP Kepala Sekolah'],
            ['key' => 'kepala_sekolah_jabatan', 'value' => 'Kepala Sekolah / Madrasah', 'keterangan' => 'Jabatan Resmi Kepala Sekolah'],
            ['key' => 'bendahara_nama', 'value' => 'Siti Aminah, S.E.', 'keterangan' => 'Nama Bendahara Keuangan'],
            ['key' => 'bendahara_nip', 'value' => '19820415 200801 2 004', 'keterangan' => 'NIP / ID Bendahara Keuangan'],
            ['key' => 'bendahara_jabatan', 'value' => 'Bendahara Keuangan Yayasan', 'keterangan' => 'Jabatan Bendahara Keuangan'],
            ['key' => 'tata_usaha_nama', 'value' => 'Budi Santoso, S.Kom.', 'keterangan' => 'Nama Kepala Tata Usaha'],
            ['key' => 'tata_usaha_nip', 'value' => '19881120 201202 1 003', 'keterangan' => 'NIP Kepala Tata Usaha'],
            ['key' => 'tata_usaha_jabatan', 'value' => 'Kepala Tata Usaha', 'keterangan' => 'Jabatan Kepala Tata Usaha'],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('pengaturan')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'keterangan' => $setting['keterangan'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ttd_digital', 'nip', 'jabatan']);
        });
    }
};
