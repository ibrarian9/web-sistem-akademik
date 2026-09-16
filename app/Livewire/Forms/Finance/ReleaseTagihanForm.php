<?php

namespace App\Livewire\Forms\Finance;

use Carbon\Carbon;
use Livewire\Form;

class ReleaseTagihanForm extends Form
{
    public string $releaseMode = 'bulk'; // 'single' | 'bulk'
    public string $bulkTarget = 'custom'; // 'custom' | 'class' | 'all'
    public string $periodeTipe = 'single'; // 'single', 'full_year_juli_juni', 'full_year_jan_des', 'custom_range'
    public ?int $jenis_tagihan_id = null;
    public mixed $nominal = 0.00;
    public string $bulan = 'Juli';
    public string $bulan_mulai = 'Juli';
    public string $bulan_selesai = 'Desember';
    public string $jatuh_tempo = '';
    public ?int $single_siswa_id = null;
    public ?int $release_kelas_id = null;
    public ?int $bulk_kelas_id = null;
    public string $bulk_target_siswa = 'aktif';
    public array $bulkSelectedSiswaIds = [];

    public array $standardMonths = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    public function rules(): array
    {
        $rules = [
            'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'nominal' => 'required',
        ];

        if ($this->releaseMode === 'single') {
            $rules['single_siswa_id'] = 'required|exists:siswa,id';
        }

        if ($this->periodeTipe === 'single') {
            $rules['bulan'] = 'required|string|max:50';
        } elseif ($this->periodeTipe === 'custom_range') {
            $rules['bulan_mulai'] = 'required|string|in:' . implode(',', $this->standardMonths);
            $rules['bulan_selesai'] = 'required|string|in:' . implode(',', $this->standardMonths);
        }

        return $rules;
    }

    public function getMonthsBetween(string $start, string $end): array
    {
        $academicOrder = [
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'
        ];

        $calendarOrder = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        if ($start === $end) {
            return [$start];
        }

        // Check in Academic Order first (most common for schools)
        $acadStart = array_search($start, $academicOrder);
        $acadEnd = array_search($end, $academicOrder);

        if ($acadStart !== false && $acadEnd !== false && $acadStart <= $acadEnd) {
            return array_slice($academicOrder, $acadStart, $acadEnd - $acadStart + 1);
        }

        // Check in Calendar Order
        $calStart = array_search($start, $calendarOrder);
        $calEnd = array_search($end, $calendarOrder);

        if ($calStart !== false && $calEnd !== false && $calStart <= $calEnd) {
            return array_slice($calendarOrder, $calStart, $calEnd - $calStart + 1);
        }

        // Cyclic fallback in Academic order
        if ($acadStart !== false && $acadEnd !== false) {
            $result = [];
            $curr = $acadStart;
            while (true) {
                $result[] = $academicOrder[$curr];
                if ($curr === $acadEnd) {
                    break;
                }
                $curr = ($curr + 1) % 12;
            }
            return $result;
        }

        return [$start];
    }

    public function getTargetMonths(): array
    {
        if ($this->periodeTipe === 'full_year_jan_des') {
            return [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
        } elseif ($this->periodeTipe === 'full_year_juli_juni') {
            return [
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'
            ];
        } elseif ($this->periodeTipe === 'custom_range') {
            return $this->getMonthsBetween($this->bulan_mulai, $this->bulan_selesai);
        }
        return [$this->bulan];
    }

    public function calculateDueDateForMonth(string $monthName, ?string $baseDueDate = null, ?string $tahunAjaranNama = null): string
    {
        $monthNumbers = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];

        if (!isset($monthNumbers[$monthName])) {
            return date('Y-m-10');
        }

        $targetMonth = $monthNumbers[$monthName];
        $targetDay = 10;

        $year = (int) date('Y');
        if ($tahunAjaranNama && str_contains($tahunAjaranNama, '/')) {
            $parts = explode('/', $tahunAjaranNama);
            $y1 = (int) trim($parts[0]);
            $y2 = (int) trim($parts[1]);

            if ($this->periodeTipe === 'full_year_jan_des') {
                if (!empty($baseDueDate)) {
                    try {
                        $year = Carbon::parse($baseDueDate)->year;
                    } catch (\Exception $e) {
                        $year = $y2;
                    }
                } else {
                    $year = $y2;
                }
            } else {
                $year = ($targetMonth >= 7) ? $y1 : $y2;
            }
        } elseif (!empty($baseDueDate)) {
            try {
                $year = Carbon::parse($baseDueDate)->year;
            } catch (\Exception $e) {
                $year = (int) date('Y');
            }
        }

        return sprintf('%04d-%02d-%02d', $year, $targetMonth, $targetDay);
    }
}
