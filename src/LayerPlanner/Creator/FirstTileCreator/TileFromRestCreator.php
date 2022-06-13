<?php

declare(strict_types=1);

namespace App\LayerPlanner\Creator\FirstTileCreator;

use App\LayerPlanner\Creator\TileLengthRangeCreator;
use App\LayerPlanner\Creator\TileLengthRangeCreatorInterface;
use App\LayerPlanner\LayerPlannerConstants;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\Rest;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Tile;
use App\LayerPlanner\Validator\DeviationValidator;
use App\LayerPlanner\Validator\DeviationValidatorInterface;
use App\LayerPlanner\Validator\RangeValidator;
use App\LayerPlanner\Validator\RangeValidatorInterface;

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

    public function create(LayerPlanInput $layerInput, LayerPlan $plan, Rests $rests): ?Tile
    {
        $tileMinLength = $layerInput->getMinTileLength();

        $lengthTileLastRow = $plan->getLastRowLength();
        $lengthTileBeforeLastRow = $plan->getRowBeforeLastLength();

        $tileRanges = $this->rangeCalculator->calculateRanges($layerInput);
        $maxLengthOfFirstRange = $tileRanges->getMaxOfFirstRange();

        if ($rests->hasRest(LayerPlannerConstants::RESTS_LEFT)) {
            foreach ($rests->getRests(LayerPlannerConstants::RESTS_LEFT) as $rest) {
                $restLength = $rest->getLength();

                if (
                    $restLength !== $lengthTileBeforeLastRow
                    && $this->deviationValidator->isValidDeviation(
                        $restLength,
                        $lengthTileLastRow,
                        $tileMinLength,
                        LayerPlannerConstants::MIN_DEVIATION
                    )
                    && $this->rangeValidator->isInRange($restLength, $tileRanges->getRanges())
                ) {
                    $rests->removeRest($restLength, LayerPlannerConstants::RESTS_LEFT);

                    return Tile::create($layerInput->getTileWidth(), $restLength, $rest->getNumber());
                }
            }

            $smallestRest = $this->getRestWithSmallestLength($rests->getRests(LayerPlannerConstants::RESTS_LEFT));
            if (
                $maxLengthOfFirstRange <= $smallestRest->getLength()
                && $this->deviationValidator->isValidDeviation(
                    $maxLengthOfFirstRange,
                    $lengthTileLastRow,
                    $tileMinLength,
                    LayerPlannerConstants::MIN_DEVIATION
                )
            ) {
                $rests->removeRest($smallestRest->getLength(), LayerPlannerConstants::RESTS_LEFT);

                $trash = $smallestRest->getLength() - $maxLengthOfFirstRange;

                $rests->addThrash($trash);

                return Tile::create($layerInput->getTileWidth(), $maxLengthOfFirstRange);
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
