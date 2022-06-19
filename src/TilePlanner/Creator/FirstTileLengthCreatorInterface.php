<?php

namespace App\TilePlanner\Creator;

use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Models\Tile;

interface FirstTileLengthCreatorInterface
{
    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): Tile;
}
