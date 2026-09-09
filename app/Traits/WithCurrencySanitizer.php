<?php

namespace App\Traits;

trait WithCurrencySanitizer
{
    /**
     * Sanitize a single currency value or property to a pure numeric float.
     * Example: $this->sanitizeCurrency('nominal') or $this->sanitizeCurrency($val)
     *
     * @param mixed $value
     * @param float $default
     * @return float
     */
    public function sanitizeCurrency(mixed $value, float $default = 0.0): float
    {
        if (is_string($value) && (property_exists($this, $value) || isset($this->{$value}))) {
            $val = $this->{$value};
            $clean = unmask_rupiah($val, $default);
            $this->{$value} = $clean;
            return $clean;
        }

        return unmask_rupiah($value, $default);
    }

    /**
     * Sanitize multiple component properties before validation or database saving.
     * Example: $this->sanitizeCurrencies(['nominal', 'edit_nominal', 'jumlah']);
     *
     * @param array|string $properties
     * @return void
     */
    public function sanitizeCurrencies(array|string $properties): void
    {
        $properties = (array) $properties;

        foreach ($properties as $prop) {
            if (property_exists($this, $prop) || isset($this->{$prop})) {
                $val = $this->{$prop};
                if (is_string($val) && $val !== '') {
                    $this->{$prop} = unmask_rupiah($val);
                } elseif ($val === '' || $val === null) {
                    $this->{$prop} = 0.0;
                }
            }
        }
    }
}
