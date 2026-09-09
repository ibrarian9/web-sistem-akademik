<?php

namespace App\Console\Commands;

use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateMonthlySpp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-monthly-spp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate SPP bill automatically for all active students on the first of each month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting monthly SPP generation...');

        // 1. Get the current active school year
        $tahunAjaran = TahunAjaran::where('status_aktif', true)->first();
        if (!$tahunAjaran) {
            $this->error('Failed: No active school year found.');
            return Command::FAILURE;
        }

        // 2. Get or create SPP billing type with is_blocking = true
        $jtSpp = JenisTagihan::firstOrCreate(
            ['nama' => 'SPP'],
            [
                'kategori' => 'rutin',
                'default_nominal' => 350000,
                'is_blocking' => true,
            ]
        );

        // Ensure is_blocking is true
        if (!$jtSpp->is_blocking) {
            $jtSpp->update(['is_blocking' => true]);
        }

        // 3. Determine current Indonesian month name and year
        $indonesianMonths = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $currentMonthNum = Carbon::now()->month;
        $currentMonthName = $indonesianMonths[$currentMonthNum];
        $currentYear = Carbon::now()->year;
        $dueDate = Carbon::now()->startOfMonth()->addDays(9)->toDateString(); // 10th of current month

        // 4. Retrieve all active students
        $activeStudents = Siswa::where('status', 'aktif')->get();
        $this->info("Found {$activeStudents->count()} active students.");

        $countCreated = 0;
        $countSkipped = 0;

        foreach ($activeStudents as $siswa) {
            // Check if student already has SPP billing for this month
            $exists = Tagihan::where('siswa_id', $siswa->id)
                ->where('jenis_tagihan_id', $jtSpp->id)
                ->where('tahun_ajaran_id', $tahunAjaran->id)
                ->where('bulan', $currentMonthName)
                ->whereYear('jatuh_tempo', $currentYear)
                ->exists();

            if (!$exists) {
                $nominal = floatval($jtSpp->default_nominal ?: 350000);
                $status = ($nominal <= 0) ? 'lunas' : 'belum_bayar';

                $tagihan = Tagihan::create([
                    'siswa_id' => $siswa->id,
                    'jenis_tagihan_id' => $jtSpp->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'bulan' => $currentMonthName,
                    'nominal' => $nominal,
                    'total_dibayar' => 0,
                    'status' => $status,
                    'jatuh_tempo' => $dueDate,
                ]);

                if ($siswa->user_id) {
                    \App\Models\Notifikasi::create([
                        'user_id' => $siswa->user_id,
                        'siswa_id' => $siswa->id,
                        'judul' => "Tagihan SPP Bulan {$currentMonthName} Terbit",
                        'isi_pesan' => "Tagihan SPP bulan {$currentMonthName} sebesar Rp " . number_format($nominal, 0, ',', '.') . " telah diterbitkan. Jatuh tempo: {$dueDate}.",
                        'jenis' => 'tagihan',
                        'channel' => 'in_app',
                        'status_kirim' => 'terkirim',
                        'dikirim_pada' => now(),
                    ]);
                }

                $countCreated++;
            } else {
                $countSkipped++;
            }
        }

        $this->info("SPP generation completed. Created: {$countCreated}, Skipped (Already existed): {$countSkipped}.");
        return Command::SUCCESS;
    }
}
