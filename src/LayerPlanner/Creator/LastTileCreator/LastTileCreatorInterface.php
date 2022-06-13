<?php

declare(strict_types=1);

namespace App\LayerPlanner\Creator\LastTileCreator;

use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Tile;

interface LastTileCreatorInterface
{
    public function create(LayerPlanInput $layerInput, LayerPlan $plan, Rests $rests, float $usedRowLength): ?Tile;
}
