<?php

namespace App\LayerPlanner;

use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;

interface LayerPlannerFacadeInterface
{
    public function createPlan(LayerPlanInput $layerInput): LayerPlan;
}