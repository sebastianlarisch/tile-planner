<?php

declare(strict_types=1);

namespace App\LayerPlanner\Creator\LastTileCreator;

use App\LayerPlanner\LayerPlannerConstants;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\Rest;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Tile;

final class LastTileFromRestForChessTypeCreator implements LastTileCreatorInterface
{
    public function create(LayerPlanInput $layerInput, LayerPlan $plan, Rests $rests, float $usedRowLength): ?Tile
    {
        $restOfRow = $layerInput->getRoomWidth() - $usedRowLength;
        $foundRest = $this->findTileInRests($restOfRow, $rests);

        if ($foundRest !== null) {
            return Tile::create($layerInput->getTileWidth(), $foundRest->getLength(), $foundRest->getNumber());
        }

        return null;
    }

    private function findTileInRests(float $length, Rests $rests): ?Rest
    {
        if ($rests->hasRest(LayerPlannerConstants::RESTS_LEFT)) {
            $possibleRests = [];
            foreach ($rests->getRests(LayerPlannerConstants::RESTS_LEFT) as $rest) {
                if ($rest->getLength() === $length) {
                    $rests->removeRest($rest->getLength(), LayerPlannerConstants::RESTS_LEFT);

                    return $rest;
                }

                if ($rest->getLength() > $length) {
                    $possibleRests[] = $rest;
                }
            }

            if (!empty($possibleRests)) {
                $smallestRest = $this->getRestWithSmallestLength($possibleRests);
                $rests->removeRest($smallestRest->getLength(), LayerPlannerConstants::RESTS_LEFT);

                $trash = $smallestRest->getLength() - $length;
                $rests->addThrash($trash);

                return $smallestRest->setLength($length);
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
