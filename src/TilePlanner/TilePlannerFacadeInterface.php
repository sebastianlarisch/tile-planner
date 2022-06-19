<?php

namespace App\TilePlanner;

use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;

interface TilePlannerFacadeInterface
{
    public function createPlan(TilePlanInput $tileInput): TilePlan;
}