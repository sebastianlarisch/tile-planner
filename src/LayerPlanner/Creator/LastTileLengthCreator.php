<?php

declare(strict_types=1);

namespace App\LayerPlanner\Creator;

use App\LayerPlanner\Creator\LastTileCreator\LastTileCreatorInterface;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Tile;

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
        LayerPlanInput $layerInput,
        LayerPlan $plan,
        Rests $rests,
        float $usedRowLength
    ): Tile {
        $tileLength = $layerInput->getTileLength();

        foreach ($this->lastTileLengthCalculator as $calculator) {
            $tile = $calculator->create($layerInput, $plan, $rests, $usedRowLength);

            if ($tile !== null) {
                return $tile;
            }
        }

        return Tile::create($layerInput->getTileWidth(), $tileLength);
    }
}
