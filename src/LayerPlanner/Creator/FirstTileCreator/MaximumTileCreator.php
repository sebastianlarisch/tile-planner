<?php

declare(strict_types=1);

namespace App\LayerPlanner\Creator\FirstTileCreator;

use App\LayerPlanner\Creator\TileLengthRangeCreatorInterface;
use App\LayerPlanner\LayerPlannerConstants;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\LengthRangeBag;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Tile;
use App\LayerPlanner\Validator\DeviationValidatorInterface;

final class MaximumTileCreator implements FirstTileCreatorInterface
{
    private DeviationValidatorInterface $deviationValidator;
    private TileLengthRangeCreatorInterface $rangeCalculator;

    public function __construct(
        DeviationValidatorInterface $deviationValidator,
        TileLengthRangeCreatorInterface $rangeCalculator
    ) {
        $this->deviationValidator = $deviationValidator;
        $this->rangeCalculator = $rangeCalculator;
    }

    public function create(LayerPlanInput $layerInput, LayerPlan $plan, Rests $rests): ?Tile
    {
        $tileMinLength = $layerInput->getMinTileLength();
        $tileLength = $layerInput->getTileLength();

        $tileRanges = $this->rangeCalculator->calculateRanges($layerInput);
        $maxLengthOfFirstRange = $tileRanges->getMaxOfFirstRange();

        if ($this->canUseMaxLengthOfFirstRange($plan, $layerInput, $tileRanges)) {
            $tile = Tile::create($layerInput->getTileWidth(), $maxLengthOfFirstRange);

            $restOfTile = $tileLength - $maxLengthOfFirstRange;

            $rests->addRest(
                $restOfTile,
                $tileMinLength,
                LayerPlannerConstants::RESTS_RIGHT,
                $tile->getNumber()
            );

            return $tile;
        }

        return null;
    }

    private function canUseMaxLengthOfFirstRange(
        LayerPlan $plan,
        LayerPlanInput $layerInput,
        LengthRangeBag $tileRanges
    ): bool {
        if (
            $this->deviationValidator->isValidDeviation(
                $tileRanges->getMaxOfFirstRange(),
                $plan->getLastRowLength(),
                $layerInput->getMinTileLength(),
                LayerPlannerConstants::MIN_DEVIATION
            )
        ) {
            return true;
        }

        return false;
    }
}
