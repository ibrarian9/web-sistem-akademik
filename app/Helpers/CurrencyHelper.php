<?php

if (!function_exists('format_rupiah')) {
    /**
     * Format a numeric or string value to Indonesian Rupiah thousands format.
     * Example: 1000000 -> "1.000.000", or with prefix: "Rp 1.000.000"
     *
     * @param mixed $nominal
     * @param bool $withPrefix
     * @param int $decimals
     * @return string
     */
    function format_rupiah(mixed $nominal, bool $withPrefix = false, int $decimals = 0): string
    {
        if ($nominal === null || $nominal === '') {
            return $withPrefix ? 'Rp 0' : '0';
        }

        if (is_string($nominal) && !is_numeric($nominal)) {
            $nominal = unmask_rupiah($nominal);
        }

        $num = (float) $nominal;
        $isNegative = $num < 0;
        $absNum = abs($num);

        $formatted = number_format($absNum, $decimals, ',', '.');

        if ($withPrefix) {
            return ($isNegative ? '-Rp ' : 'Rp ') . $formatted;
        }

        return ($isNegative ? '-' : '') . $formatted;
    }
}

if (!function_exists('unmask_rupiah')) {
    /**
     * Clean and parse an Indonesian currency string or formatted number into a pure float.
     * Example: "Rp 1.500.000" -> 1500000, "1.500.000,50" -> 1500000.50
     *
     * @param mixed $value
     * @param float $default
     * @return float
     */
    function unmask_rupiah(mixed $value, float $default = 0.0): float
    {
        if ($value === null || $value === '') {
            return $default;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $str = trim((string) $value);
        if ($str === '') {
            return $default;
        }

        $isNegative = str_starts_with($str, '-') || str_contains($str, '(-') || str_ends_with($str, '-');

        // Strip prefix "Rp" or "Rp."
        $cleanStr = preg_replace('/^(?:-?\s*Rp\.?\s*|\s*Rp\.?\s*)/i', '', $str);
        $cleanStr = trim($cleanStr);

        // Check if it's a standard SQL/PHP decimal string with 1-2 decimal places without thousands dots (e.g. "150000.00", "500.5", "0.00")
        if (preg_match('/^-?\d+\.\d{1,2}$/', $cleanStr)) {
            $num = (float) $cleanStr;
            return $isNegative ? -abs($num) : $num;
        }

        // Check if there's a comma decimal separator, e.g. "1.500.000,50"
        if (preg_match('/,(\d{1,2})$/', $cleanStr, $matches)) {
            $cents = $matches[1];
            // Strip everything except digits before the comma
            $mainPart = preg_replace('/,(\d{1,2})$/', '', $cleanStr);
            $cleanMain = preg_replace('/[^0-9]/', '', $mainPart);
            $result = (float) (($cleanMain !== '' ? $cleanMain : '0') . '.' . $cents);
        } else {
            // Strip everything except digits
            $clean = preg_replace('/[^0-9]/', '', $cleanStr);
            $result = $clean !== '' ? (float) $clean : $default;
        }

        return $isNegative ? -$result : $result;
    }
}
