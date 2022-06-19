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

    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): ?Tile
    {
        $tileMinLength = $tileInput->getMinTileLength();
        $tileLength = $tileInput->getTileLength();

        $lengthTileLastRow = $plan->getLastRowLength();

        $tileRanges = $this->rangeCalculator->calculateRanges($tileInput);
        $minLengthOfFirstRange = $tileRanges->getMinOfFirstRange();

        if ($this->deviationValidator->isValidDeviation(
            $minLengthOfFirstRange,
            $lengthTileLastRow,
            $tileMinLength,
            TilePlannerConstants::MIN_DEVIATION)
        ) {
            $tile = Tile::create($tileInput->getTileWidth(), $minLengthOfFirstRange);
            $rests->addRest(
                $tileLength - $minLengthOfFirstRange,
                $tileMinLength,
                TilePlannerConstants::RESTS_RIGHT,
                $tile->getNumber()
            );

            return $tile;
        }

        return null;
    }
}
