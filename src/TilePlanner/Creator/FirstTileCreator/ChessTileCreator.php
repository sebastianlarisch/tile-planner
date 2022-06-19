<?php

declare(strict_types=1);

namespace App\TilePlanner\Creator\FirstTileCreator;

use App\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use App\TilePlanner\TilePlannerConstants;
use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Models\Tile;
use App\TilePlanner\Validator\RangeValidatorInterface;

final class ChessTileCreator implements FirstTileCreatorInterface
{
    private RangeValidatorInterface $rangeValidator;
    private TileLengthRangeCreatorInterface $rangeCreator;

    public function __construct(
        RangeValidatorInterface $rangeValidator,
        TileLengthRangeCreatorInterface $rangeCreator
    ) {
        $this->rangeValidator = $rangeValidator;
        $this->rangeCreator = $rangeCreator;
    }

    public function create(
        TilePlanInput $tileInput,
        TilePlan      $plan,
        Rests         $rests
    ): ?Tile {
        $tileLength = $tileInput->getTileLength();
        $tileRanges = $this->rangeCreator->calculateRanges($tileInput);

        if ($this->rangeValidator->isInRange($tileLength, $tileRanges->getRanges())) {
            return Tile::create($tileInput->getTileWidth(), $tileLength);
        }

        $tile = Tile::create($tileInput->getTileWidth(), $tileRanges->getMinOfFirstRange());

        $rests->addRest(
            $tileLength - $tileRanges->getMinOfFirstRange(),
            $tileInput->getMinTileLength(),
            TilePlannerConstants::RESTS_LEFT,
            $tile->getNumber()
        );

        return $tile;
    }
}
