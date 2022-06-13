<?php

namespace App\LayerPlanner\Creator;

use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Row;

interface RowCreatorInterface
{
    public function createRow(
        LayerPlanInput $layerInput,
        LayerPlan $plan,
        Rests $rest
    ): Row;
}
