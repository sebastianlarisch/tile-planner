<?php

declare(strict_types=1);

namespace App\TilePlanner\Creator;

use App\TilePlanner\Creator\LastTileCreator\LastTileCreatorInterface;
use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Models\Tile;

final class LastTileLengthCreator implements LastTileLengthCreatorInterface
{
    /**
     * @var list<LastTileCreatorInterface>
     */
    private array $lastTileLengthCalculator;

    public function __construct(array $lastTileLengthCalculator)
    {
        $this->lastTileLengthCalculator = $lastTileLengthCalculator;
    }

    public function create(
        TilePlanInput $tileInput,
        TilePlan      $plan,
        Rests         $rests,
        float         $usedRowLength
    ): Tile {
        $tileLength = $tileInput->getTileLength();

        foreach ($this->lastTileLengthCalculator as $calculator) {
            $tile = $calculator->create($tileInput, $plan, $rests, $usedRowLength);

            if ($tile !== null) {
                return $tile;
            }
        }

        return Tile::create($tileInput->getTileWidth(), $tileLength);
    }
}
