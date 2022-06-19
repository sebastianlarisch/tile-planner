<?php

declare(strict_types=1);

namespace App\TilePlanner\Creator\LastTileCreator;

use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Models\Tile;

interface LastTileCreatorInterface
{
    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests, float $usedRowLength): ?Tile;
}
