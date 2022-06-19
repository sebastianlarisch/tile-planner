<?php

declare(strict_types=1);

namespace App\TilePlanner\Creator\FirstTileCreator;

use App\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use App\TilePlanner\TilePlannerConstants;
use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\LengthRangeBag;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Models\Tile;
use App\TilePlanner\Validator\DeviationValidatorInterface;

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

    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): ?Tile
    {
        $tileMinLength = $tileInput->getMinTileLength();
        $tileLength = $tileInput->getTileLength();

        $tileRanges = $this->rangeCalculator->calculateRanges($tileInput);
        $maxLengthOfFirstRange = $tileRanges->getMaxOfFirstRange();

        if ($this->canUseMaxLengthOfFirstRange($plan, $tileInput, $tileRanges)) {
            $tile = Tile::create($tileInput->getTileWidth(), $maxLengthOfFirstRange);

            $restOfTile = $tileLength - $maxLengthOfFirstRange;

            $rests->addRest(
                $restOfTile,
                $tileMinLength,
                TilePlannerConstants::RESTS_RIGHT,
                $tile->getNumber()
            );

            return $tile;
        }

        return null;
    }

    private function canUseMaxLengthOfFirstRange(
        TilePlan       $plan,
        TilePlanInput  $tileInput,
        LengthRangeBag $tileRanges
    ): bool {
        if (
            $this->deviationValidator->isValidDeviation(
                $tileRanges->getMaxOfFirstRange(),
                $plan->getLastRowLength(),
                $tileInput->getMinTileLength(),
                TilePlannerConstants::MIN_DEVIATION
            )
        ) {
            return true;
        }

        return false;
    }
}
