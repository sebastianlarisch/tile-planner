<?php

declare(strict_types=1);

namespace App\LayerPlanner\Creator\FirstTileCreator;

use App\LayerPlanner\Creator\TileLengthRangeCreatorInterface;
use App\LayerPlanner\LayerPlannerConstants;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Tile;
use App\LayerPlanner\Validator\DeviationValidatorInterface;

final class MinimumTileCreator implements FirstTileCreatorInterface
{
    private TileLengthRangeCreatorInterface $rangeCalculator;

    private DeviationValidatorInterface $deviationValidator;

    public function __construct(
        TileLengthRangeCreatorInterface $rangeCalculator,
        DeviationValidatorInterface $deviationValidator
    ) {
        $this->rangeCalculator = $rangeCalculator;
        $this->deviationValidator = $deviationValidator;
    }

    public function create(LayerPlanInput $layerInput, LayerPlan $plan, Rests $rests): ?Tile
    {
        $tileMinLength = $layerInput->getMinTileLength();
        $tileLength = $layerInput->getTileLength();

        $lengthTileLastRow = $plan->getLastRowLength();

        $tileRanges = $this->rangeCalculator->calculateRanges($layerInput);
        $minLengthOfFirstRange = $tileRanges->getMinOfFirstRange();

        if ($this->deviationValidator->isValidDeviation(
            $minLengthOfFirstRange,
            $lengthTileLastRow,
            $tileMinLength,
            LayerPlannerConstants::MIN_DEVIATION)
        ) {
            $tile = Tile::create($layerInput->getTileWidth(), $minLengthOfFirstRange);
            $rests->addRest(
                $tileLength - $minLengthOfFirstRange,
                $tileMinLength,
                LayerPlannerConstants::RESTS_RIGHT,
                $tile->getNumber()
            );

            return $tile;
        }

        return null;
    }
}
