<?php

declare(strict_types=1);

namespace App\TilePlanner\Models;

final class LengthRangeBag
{
    /**
     * @var list<LengthRange> $ranges
     */
    private array $ranges = [];

    public function addRange(LengthRange $range): self
    {
        $this->ranges[] = $range;

        return $this;
    }

    public function getRanges(): array
    {
        return $this->ranges;
    }

    public function getMinOfFirstRange(): float
    {
        return $this->ranges[0]->getMin();
    }

    public function getMaxOfFirstRange(): float
    {
        return $this->ranges[0]->getMax();
    }
}
