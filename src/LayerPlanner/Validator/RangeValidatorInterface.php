<?php

declare(strict_types=1);

namespace App\LayerPlanner\Validator;

interface RangeValidatorInterface
{
    public function isInRange(float $length, array $firstTileRanges): bool;
}
