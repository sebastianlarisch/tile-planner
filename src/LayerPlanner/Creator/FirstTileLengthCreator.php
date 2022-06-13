<?php

declare(strict_types=1);

namespace App\LayerPlanner\Creator;

use App\LayerPlanner\Creator\FirstTileCreator\FirstTileCreatorInterface;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\LengthRangeBag;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Tile;

final class FirstTileLengthCreator implements FirstTileLengthCreatorInterface
{
    /**
     * @var list<FirstTileCreatorInterface>
     */
    private array $firstTileLengthCalculator;

    public function __construct(array $firstTileLengthCalculator)
    {
        $this->firstTileLengthCalculator = $firstTileLengthCalculator;
    }

    public function create(LayerPlanInput $layerInput, LayerPlan $plan, Rests $rests): Tile
    {
        $tileLength = $layerInput->getTileLength();

        foreach ($this->firstTileLengthCalculator as $calculator) {
            $tile = $calculator->create($layerInput, $plan, $rests);

            if ($tile !== null) {
                return $tile;
            }
        }

        return Tile::create($layerInput->getTileWidth(), $tileLength);
    }
}
