<?php

namespace App\TilePlanner\Creator;

use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\LengthRangeBag;

interface TileLengthRangeCreatorInterface
{
    public function calculateRanges(TilePlanInput $tileInput): LengthRangeBag;
}
