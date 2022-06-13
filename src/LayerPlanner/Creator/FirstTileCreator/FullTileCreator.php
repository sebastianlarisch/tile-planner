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
use App\LayerPlanner\Validator\RangeValidatorInterface;

final class FullTileCreator implements FirstTileCreatorInterface
{
    private RangeValidatorInterface $rangeValidator;
    private DeviationValidatorInterface $deviationValidator;
    private TileLengthRangeCreatorInterface $rangeCalculator;

    public function __construct(
        RangeValidatorInterface $rangeValidator,
        DeviationValidatorInterface $deviationValidator,
        TileLengthRangeCreatorInterface $rangeCalculator
    ) {
        $this->rangeValidator = $rangeValidator;
        $this->deviationValidator = $deviationValidator;
        $this->rangeCalculator = $rangeCalculator;
    }

    public function create(LayerPlanInput $layerInput, LayerPlan $plan, Rests $rests): ?Tile
    {
        $tileMinLength = $layerInput->getMinTileLength();
        $tileLength = $layerInput->getTileLength();

        $lengthTileLastRow = $plan->getLastRowLength();
        $tileRanges = $this->rangeCalculator->calculateRanges($layerInput);

        if (
            $this->deviationValidator->isValidDeviation(
                $tileLength,
                $lengthTileLastRow,
                $tileMinLength,
                LayerPlannerConstants::MIN_DEVIATION
            )
            && $this->rangeValidator->isInRange($tileLength, $tileRanges->getRanges())
        ) {
            return Tile::create($layerInput->getTileWidth(), $tileLength);
        }

        return null;
    }
}
