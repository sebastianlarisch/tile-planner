<?php

declare(strict_types=1);

namespace App\TilePlanner\Validator;

interface RangeValidatorInterface
{
    public function isInRange(float $length, array $firstTileRanges): bool;
}
