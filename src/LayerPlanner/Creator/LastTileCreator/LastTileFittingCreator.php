<?php

declare(strict_types=1);

namespace App\LayerPlanner\Creator\LastTileCreator;

use App\LayerPlanner\LayerPlannerConstants;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Tile;

final class LastTileFittingCreator implements LastTileCreatorInterface
{
    public function create(LayerPlanInput $layerInput, LayerPlan $plan, Rests $rests, float $usedRowLength): ?Tile
    {
        $restOfRow = $layerInput->getRoomWidth() - $usedRowLength;
        $restOfTile = $layerInput->getTileLength() - $restOfRow;

        $tile = Tile::create($layerInput->getTileWidth(), $restOfRow);

        $rests->addRest(
            $restOfTile,
            $layerInput->getMinTileLength(),
            LayerPlannerConstants::RESTS_LEFT,
            $tile->getNumber()
        );

        return $tile;
    }
}
