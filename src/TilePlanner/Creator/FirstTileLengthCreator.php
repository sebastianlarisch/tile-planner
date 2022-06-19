<?php

declare(strict_types=1);

namespace App\TilePlanner\Creator;

use App\TilePlanner\Creator\FirstTileCreator\FirstTileCreatorInterface;
use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\LengthRangeBag;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Models\Tile;

final class FirstTileLengthCreator implements FirstTileLengthCreatorInterface
{
    /**
     * @var list<FirstTileCreatorInterface>
     */
    private array $firstTileLengthCalculator;

    public function __construct(array $firstTileLengthCalculator)
    {
        $this->firstTileLengthCalculator = $firstTileLengthCalculator;
    }

    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): Tile
    {
        $tileLength = $tileInput->getTileLength();

        foreach ($this->firstTileLengthCalculator as $calculator) {
            $tile = $calculator->create($tileInput, $plan, $rests);

            if ($tile !== null) {
                return $tile;
            }
        }

        return Tile::create($tileInput->getTileWidth(), $tileLength);
    }
}
