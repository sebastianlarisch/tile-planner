<?php

declare(strict_types=1);

namespace App\TilePlanner\Creator\FirstTileCreator;

use App\TilePlanner\Creator\TileLengthRangeCreator;
use App\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use App\TilePlanner\TilePlannerConstants;
use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\Rest;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Models\Tile;
use App\TilePlanner\Validator\DeviationValidator;
use App\TilePlanner\Validator\DeviationValidatorInterface;
use App\TilePlanner\Validator\RangeValidator;
use App\TilePlanner\Validator\RangeValidatorInterface;

final class TileFromRestCreator implements FirstTileCreatorInterface
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

        $lengthTileLastRow = $plan->getLastRowLength();
        $lengthTileBeforeLastRow = $plan->getRowBeforeLastLength();

        $tileRanges = $this->rangeCalculator->calculateRanges($tileInput);
        $maxLengthOfFirstRange = $tileRanges->getMaxOfFirstRange();

        if ($rests->hasRest(TilePlannerConstants::RESTS_LEFT)) {
            foreach ($rests->getRests(TilePlannerConstants::RESTS_LEFT) as $rest) {
                $restLength = $rest->getLength();

                if (
                    $restLength !== $lengthTileBeforeLastRow
                    && $this->deviationValidator->isValidDeviation(
                        $restLength,
                        $lengthTileLastRow,
                        $tileMinLength,
                        TilePlannerConstants::MIN_DEVIATION
                    )
                    && $this->rangeValidator->isInRange($restLength, $tileRanges->getRanges())
                ) {
                    $rests->removeRest($restLength, TilePlannerConstants::RESTS_LEFT);

                    return Tile::create($tileInput->getTileWidth(), $restLength, $rest->getNumber());
                }
            }

            $smallestRest = $this->getRestWithSmallestLength($rests->getRests(TilePlannerConstants::RESTS_LEFT));
            if (
                $maxLengthOfFirstRange <= $smallestRest->getLength()
                && $this->deviationValidator->isValidDeviation(
                    $maxLengthOfFirstRange,
                    $lengthTileLastRow,
                    $tileMinLength,
                    TilePlannerConstants::MIN_DEVIATION
                )
            ) {
                $rests->removeRest($smallestRest->getLength(), TilePlannerConstants::RESTS_LEFT);

                $trash = $smallestRest->getLength() - $maxLengthOfFirstRange;

                $rests->addThrash($trash);

                return Tile::create($tileInput->getTileWidth(), $maxLengthOfFirstRange);
            }
        }

        return null;
    }

    /**
     * @param  list<Rest> $possibleRests
     * @return Rest
     */
    private function getRestWithSmallestLength(array $possibleRests): Rest
    {
        if (count($possibleRests) === 1) {
            return array_pop($possibleRests);
        }

        usort($possibleRests, static fn(Rest $a, Rest $b) => $a->getLength() <=> $b->getLength());

        return $possibleRests[0];
    }
}
