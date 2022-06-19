<?php

declare(strict_types=1);

namespace App\TilePlanner\Models;

final class Rest
{
    private float $length;
    private int $number;

    public static function create(float $length, int $number): self
    {
        return new self($length, $number);
    }

    private function __construct(float $length, int $number)
    {
        $this->length = $length;
        $this->number = $number;
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setLength(float $length): Rest
    {
        $this->length = $length;

        return $this;
    }
}
