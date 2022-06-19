<?php

declare(strict_types=1);

namespace App\TilePlanner\Creator\LastTileCreator;

use App\TilePlanner\TilePlannerConstants;
use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Models\Tile;

final class LastTileFittingCreator implements LastTileCreatorInterface
{
    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests, float $usedRowLength): ?Tile
    {
        $restOfRow = $tileInput->getRoomWidth() - $usedRowLength;
        $restOfTile = $tileInput->getTileLength() - $restOfRow;

        $tile = Tile::create($tileInput->getTileWidth(), $restOfRow);

        $rests->addRest(
            $restOfTile,
            $tileInput->getMinTileLength(),
            TilePlannerConstants::RESTS_LEFT,
            $tile->getNumber()
        );

        return $tile;
    }
}
