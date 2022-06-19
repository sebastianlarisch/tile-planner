<?php

declare(strict_types=1);

namespace App\TilePlanner\Creator\LastTileCreator;

use App\TilePlanner\TilePlannerConstants;
use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\Rest;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Models\Tile;

final class LastTileFromRestCreator implements LastTileCreatorInterface
{
    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests, float $usedRowLength): ?Tile
    {
        $restOfRow = $tileInput->getRoomWidth() - $usedRowLength;
        $foundRest = $this->findTileInRests($restOfRow, $rests);

        if ($foundRest !== null) {
            return Tile::create($tileInput->getTileWidth(), $foundRest->getLength(), $foundRest->getNumber());
        }

        return null;
    }

    private function findTileInRests(float $length, Rests $rests): ?Rest
    {
        if ($rests->hasRest(TilePlannerConstants::RESTS_RIGHT)) {
            $possibleRests = [];
            foreach ($rests->getRests(TilePlannerConstants::RESTS_RIGHT) as $rest) {
                if ($rest->getLength() === $length) {
                    $rests->removeRest($rest->getLength(), TilePlannerConstants::RESTS_RIGHT);

                    return $rest;
                }

                if ($rest->getLength() > $length) {
                    $possibleRests[] = $rest;
                }
            }

            if (!empty($possibleRests)) {
                $smallestRest = $this->getRestWithSmallestLength($possibleRests);
                $rests->removeRest($smallestRest->getLength(), TilePlannerConstants::RESTS_RIGHT);

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
