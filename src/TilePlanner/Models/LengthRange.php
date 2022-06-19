<?php

declare(strict_types=1);

namespace App\TilePlanner\Models;

use Assert\Assertion;

final class LengthRange
{
    private float $min;

    private float $max;

    public static function withMinAndMax(float $min, float $max): self
    {
        Assertion::greaterOrEqualThan($max, $min);
        return new self($min, $max);
    }

    private function __construct(float $min, float $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function getMin(): float
    {
        return $this->min;
    }

    public function setMin(float $min): void
    {
        $this->min = $min;
    }

    public function getMax(): float
    {
        return $this->max;
    }

    public function setMax(float $max): void
    {
        $this->max = $max;
    }

    public function inRange(float $length): bool
    {
        return $length >= $this->getMin() && $length <= $this->getMax();
    }
}
