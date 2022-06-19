<?php

declare(strict_types=1);

namespace Tests\Unit\TilePlanner\Creator\LastTileCreator;

use App\Form\TilePlannerType;
use App\TilePlanner\Creator\LastTileCreator\LastTileFittingCreator;
use App\TilePlanner\TilePlannerConstants;
use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\Rests;
use PHPUnit\Framework\TestCase;

final class LastTileFittingCreatorTest extends TestCase
{
    public function test_return_fitting_tile(): void
    {
        $creator = new LastTileFittingCreator();

        $tileInput = TilePlanInput::fromData(
            [
            'room_width' => '200',
            'room_depth' => '100',
            'tile_width' => '20',
            'tile_length' => '50',
            'min_tile_length' => '10',
            'gap_width' => '0',
            'laying_type' => TilePlannerType::TYPE_OFFSET,
            'costs_per_square' => '0',
            ]
        );

        $plan = new TilePlan();
        $rests = new Rests();
        $usedRowLength = 175;

        $actualTile = $creator->create($tileInput, $plan, $rests, $usedRowLength);

        self::assertEquals(25, $actualTile->getLength());
        self::assertCount(1, $rests->getRests(TilePlannerConstants::RESTS_LEFT));
    }
}