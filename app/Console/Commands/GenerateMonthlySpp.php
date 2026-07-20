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

        // 2. Get SPP billing type
        $jtSpp = JenisTagihan::where('nama', 'SPP')->first();
        if (!$jtSpp) {
            $this->error('Failed: SPP billing type not found.');
            return Command::FAILURE;
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
                Tagihan::create([
                    'siswa_id' => $siswa->id,
                    'jenis_tagihan_id' => $jtSpp->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'bulan' => $currentMonthName,
                    'nominal' => $jtSpp->default_nominal,
                    'total_dibayar' => 0,
                    'status' => 'belum_bayar',
                    'jatuh_tempo' => Carbon::now()->startOfMonth()->addDays(9)->toDateString(), // 10th of current month
                ]);
                $countCreated++;
            } else {
                $countSkipped++;
            }
        }

        $this->info("SPP generation completed. Created: {$countCreated}, Skipped (Already existed): {$countSkipped}.");
        return Command::SUCCESS;
    }
}
