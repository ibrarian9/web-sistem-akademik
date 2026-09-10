<?php

namespace App\Traits;

trait WithCurrencyFormatting
{
    use WithCurrencySanitizer;

    /**
     * Format any numeric value to standard Indonesian Rupiah.
     */
    public function formatRupiah(float|int|string|null $value, bool $prefix = true): string
    {
        $numeric = $this->parseRupiah($value);
        $formatted = number_format($numeric, 0, ',', '.');

        return $prefix ? 'Rp ' . $formatted : $formatted;
    }

    /**
     * Parse raw/formatted currency string to float.
     */
    public function parseRupiah(mixed $value, float $default = 0.0): float
    {
        return $this->sanitizeCurrency($value, $default);
    }
}
