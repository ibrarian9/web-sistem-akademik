<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\Notifikasi;
use Carbon\Carbon;

class GenerateMonthlySppCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagihan:generate-spp {bulan? : Nama bulan (contoh: Juli, Agustus)} {--nominal= : Nominal kustom SPP} {--due-day=10 : Tanggal jatuh tempo per bulan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate tagihan SPP bulanan secara otomatis untuk seluruh siswa aktif';

    /**
     * Array nama bulan Bahasa Indonesia
     */
    protected array $monthNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $bulanArg = $this->argument('bulan');
        $nominalOption = $this->option('nominal');
        $dueDay = (int) $this->option('due-day');

        $activeTA = TahunAjaran::where('status_aktif', true)->first();
        if (!$activeTA) {
            $this->error('Tahun ajaran aktif tidak ditemukan!');
            return Command::FAILURE;
        }

        $jenisSpp = JenisTagihan::firstOrCreate(
            ['nama' => 'SPP'],
            [
                'kategori' => 'rutin',
                'default_nominal' => 350000,
                'is_blocking' => true,
            ]
        );

        $targetBulan = $bulanArg ?: ($this->monthNames[date('n')] ?? 'Juli');
        $nominal = $nominalOption ? (float) $nominalOption : (float) $jenisSpp->default_nominal;

        $students = Siswa::where('status', 'aktif')->get();
        if ($students->isEmpty()) {
            $this->warn('Tidak ada siswa dengan status aktif.');
            return Command::SUCCESS;
        }

        $currentYear = date('Y');
        $dueMonth = date('m');
        $dueDate = sprintf('%s-%s-%02d', $currentYear, $dueMonth, $dueDay);

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($students as $siswa) {
            $existing = Tagihan::where([
                'siswa_id' => $siswa->id,
                'jenis_tagihan_id' => $jenisSpp->id,
                'tahun_ajaran_id' => $activeTA->id,
                'bulan' => $targetBulan,
            ])->first();

            if ($existing) {
                $skippedCount++;
                continue;
            }

            $tagihan = Tagihan::create([
                'siswa_id' => $siswa->id,
                'jenis_tagihan_id' => $jenisSpp->id,
                'tahun_ajaran_id' => $activeTA->id,
                'bulan' => $targetBulan,
                'nominal' => $nominal,
                'total_dibayar' => 0,
                'status' => 'belum_bayar',
                'jatuh_tempo' => $dueDate,
            ]);

            $createdCount++;

            // Send notification to student user
            if ($siswa->user_id) {
                Notifikasi::create([
                    'user_id' => $siswa->user_id,
                    'siswa_id' => $siswa->id,
                    'judul' => "Tagihan SPP Bulan {$targetBulan} Terbit",
                    'isi_pesan' => "Tagihan SPP bulan {$targetBulan} sebesar Rp " . number_format($nominal, 0, ',', '.') . " telah diterbitkan. Jatuh tempo: {$dueDate}.",
                    'jenis' => 'tagihan',
                    'channel' => 'in_app',
                    'status_kirim' => 'terkirim',
                    'dikirim_pada' => now(),
                ]);
            }
        }

        $this->info("Berhasil membuat {$createdCount} tagihan SPP untuk bulan {$targetBulan}. ({$skippedCount} siswa sudah memiliki tagihan ini)");
        return Command::SUCCESS;
    }
}
