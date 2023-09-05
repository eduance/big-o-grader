<?php

namespace App\ValueObjects;
class Percent
{
    /**
     * Create a new percent instance.
     *
     * @param  float|null  $value
     * @return void
     */
    public function __construct(public readonly ?float $value)
    {
    }

    /**
     * Instantiate a Percent object using numerator and denominator.
     *
     * @param  float  $numerator
     * @param  float  $denominator
     * @return self
     *
     * @throws \InvalidArgumentException
     */
    public static function from(float $numerator, float $denominator): self
    {
        if ($denominator === 0.0) {
            throw new \InvalidArgumentException('Denominator cannot be zero.');
        }

        return new self(
            round(($numerator / $denominator) * 100, 1)
        );
    }
}
