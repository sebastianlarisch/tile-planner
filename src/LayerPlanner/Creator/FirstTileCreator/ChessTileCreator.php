<?php

declare(strict_types=1);

namespace App\LayerPlanner\Creator\FirstTileCreator;

use App\LayerPlanner\Creator\TileLengthRangeCreatorInterface;
use App\LayerPlanner\LayerPlannerConstants;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Tile;
use App\LayerPlanner\Validator\RangeValidatorInterface;

final class ChessTileCreator implements FirstTileCreatorInterface
{
    private RangeValidatorInterface $rangeValidator;
    private TileLengthRangeCreatorInterface $rangeCreator;

    public function __construct(
        RangeValidatorInterface $rangeValidator,
        TileLengthRangeCreatorInterface $rangeCreator
    ) {
        $this->rangeValidator = $rangeValidator;
        $this->rangeCreator = $rangeCreator;
    }

    public function create(
        LayerPlanInput $layerInput,
        LayerPlan $plan,
        Rests $rests
    ): ?Tile {
        $tileLength = $layerInput->getTileLength();
        $tileRanges = $this->rangeCreator->calculateRanges($layerInput);

        if ($this->rangeValidator->isInRange($tileLength, $tileRanges->getRanges())) {
            return Tile::create($layerInput->getTileWidth(), $tileLength);
        }

        $tile = Tile::create($layerInput->getTileWidth(), $tileRanges->getMinOfFirstRange());

        $rests->addRest(
            $tileLength - $tileRanges->getMinOfFirstRange(),
            $layerInput->getMinTileLength(),
            LayerPlannerConstants::RESTS_LEFT,
            $tile->getNumber()
        );

        return $tile;
    }
}
