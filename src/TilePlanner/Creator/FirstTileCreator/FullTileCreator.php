<?php

declare(strict_types=1);

namespace App\TilePlanner\Creator\FirstTileCreator;

use App\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use App\TilePlanner\TilePlannerConstants;
use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Models\Tile;
use App\TilePlanner\Validator\DeviationValidatorInterface;
use App\TilePlanner\Validator\RangeValidatorInterface;

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

    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): ?Tile
    {
        $tileMinLength = $tileInput->getMinTileLength();
        $tileLength = $tileInput->getTileLength();

        $lengthTileLastRow = $plan->getLastRowLength();
        $tileRanges = $this->rangeCalculator->calculateRanges($tileInput);

        if (
            $this->deviationValidator->isValidDeviation(
                $tileLength,
                $lengthTileLastRow,
                $tileMinLength,
                TilePlannerConstants::MIN_DEVIATION
            )
            && $this->rangeValidator->isInRange($tileLength, $tileRanges->getRanges())
        ) {
            return Tile::create($tileInput->getTileWidth(), $tileLength);
        }

        return null;
    }
}
