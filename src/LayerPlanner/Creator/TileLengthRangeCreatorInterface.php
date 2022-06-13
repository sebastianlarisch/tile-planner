<?php

namespace App\LayerPlanner\Creator;

use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\LengthRangeBag;

interface TileLengthRangeCreatorInterface
{
    public function calculateRanges(LayerPlanInput $layerInput): LengthRangeBag;
}
