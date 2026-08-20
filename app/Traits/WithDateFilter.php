<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait WithDateFilter
{
    public string $filterPeriode = 'semua'; // 'semua', 'hari_ini', 'kemarin', 'minggu_ini', 'bulan_ini', 'custom'
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function updatedFilterPeriode($value)
    {
        if ($value === 'hari_ini') {
            $this->startDate = Carbon::today()->format('Y-m-d');
            $this->endDate = Carbon::today()->format('Y-m-d');
        } elseif ($value === 'kemarin') {
            $this->startDate = Carbon::yesterday()->format('Y-m-d');
            $this->endDate = Carbon::yesterday()->format('Y-m-d');
        } elseif ($value === 'minggu_ini') {
            $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
            $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        } elseif ($value === 'bulan_ini') {
            $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        } elseif ($value === 'semua') {
            $this->startDate = null;
            $this->endDate = null;
        }

        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    public function updatedStartDate()
    {
        $this->filterPeriode = 'custom';
        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    public function updatedEndDate()
    {
        $this->filterPeriode = 'custom';
        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    public function setPeriode(string $periode)
    {
        $this->filterPeriode = $periode;
        $this->updatedFilterPeriode($periode);
    }

    public function applyDateFilter($query, string $column = 'tanggal')
    {
        if ($this->filterPeriode === 'hari_ini') {
            return $query->whereDate($column, Carbon::today());
        }

        if ($this->filterPeriode === 'kemarin') {
            return $query->whereDate($column, Carbon::yesterday());
        }

        if ($this->filterPeriode === 'minggu_ini') {
            return $query->whereBetween($column, [
                Carbon::now()->startOfWeek()->format('Y-m-d'),
                Carbon::now()->endOfWeek()->format('Y-m-d')
            ]);
        }

        if ($this->filterPeriode === 'bulan_ini') {
            return $query->whereBetween($column, [
                Carbon::now()->startOfMonth()->format('Y-m-d'),
                Carbon::now()->endOfMonth()->format('Y-m-d')
            ]);
        }

        if ($this->filterPeriode === 'custom' || ($this->startDate && $this->endDate)) {
            if ($this->startDate && $this->endDate) {
                return $query->whereBetween($column, [$this->startDate, $this->endDate]);
            } elseif ($this->startDate) {
                return $query->whereDate($column, '>=', $this->startDate);
            } elseif ($this->endDate) {
                return $query->whereDate($column, '<=', $this->endDate);
            }
        }

        return $query;
    }
}
