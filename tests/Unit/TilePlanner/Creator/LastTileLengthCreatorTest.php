<?php

declare(strict_types=1);

namespace Tests\Unit\TilePlanner\Creator;

use App\Form\TilePlannerType;
use App\TilePlanner\Creator\FirstTileCreator\FirstTileCreatorInterface;
use App\TilePlanner\Creator\FirstTileLengthCreator;
use App\TilePlanner\Creator\LastTileCreator\LastTileCreatorInterface;
use App\TilePlanner\Creator\LastTileLengthCreator;
use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Models\Tile;
use PHPUnit\Framework\TestCase;

final class LastTileLengthCreatorTest extends TestCase
{
    public function test_use_input_tile_length_if_no_creator_was_passed(): void
    {
        $creator = new LastTileLengthCreator([]);

        $tileInput = TilePlanInput::fromFormData(
            [
            'room_width' => '200',
            'room_depth' => '100',
            'tile_width' => '20',
            'tile_length' => '59',
            'min_tile_length' => '20',
            'gap_width' => '0',
            'laying_type' => TilePlannerType::TYPE_OFFSET,
            'costs_per_square' => '0',
            ]
        );
        $plan = new TilePlan();
        $rests = new Rests();

        $actualTile = $creator->create($tileInput, $plan, $rests, 100);

        self::assertEquals(59, $actualTile->getLength());
    }

    public function test_use_returned_tile_length_from_creator(): void
    {
        $firstTileCreator = $this->createMock(LastTileCreatorInterface::class);
        $firstTileCreator
            ->method('create')
            ->willReturn(Tile::create(45, 65));

        $creator = new LastTileLengthCreator(
            [
            $firstTileCreator
            ]
        );

        $tileInput = TilePlanInput::fromFormData(
            [
            'room_width' => '200',
            'room_depth' => '100',
            'tile_width' => '20',
            'tile_length' => '59',
            'min_tile_length' => '20',
            'gap_width' => '0',
            'laying_type' => TilePlannerType::TYPE_OFFSET,
            'costs_per_square' => '0',
            ]
        );
        $plan = new TilePlan();
        $rests = new Rests();

        $actualTile = $creator->create($tileInput, $plan, $rests, 100);

        self::assertEquals(65, $actualTile->getLength());
    }
}