<?php

declare(strict_types=1);

namespace Tests\Unit\LayerPlanner\Creator;

use App\Form\LayerPlannerType;
use App\LayerPlanner\Creator\FirstTileCreator\FirstTileCreatorInterface;
use App\LayerPlanner\Creator\FirstTileLengthCreator;
use App\LayerPlanner\Creator\LastTileCreator\LastTileCreatorInterface;
use App\LayerPlanner\Creator\LastTileLengthCreator;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Tile;
use PHPUnit\Framework\TestCase;

final class LastTileLengthCreatorTest extends TestCase
{
    public function test_use_input_tile_length_if_no_creator_was_passed(): void
    {
        $creator = new LastTileLengthCreator([]);

        $layerInput = LayerPlanInput::fromFormData(
            [
            'room_width' => '200',
            'room_depth' => '100',
            'tile_width' => '20',
            'tile_length' => '59',
            'min_tile_length' => '20',
            'gap_width' => '0',
            'laying_type' => LayerPlannerType::TYPE_OFFSET,
            'costs_per_square' => '0',
            ]
        );
        $plan = new LayerPlan();
        $rests = new Rests();

        $actualTile = $creator->create($layerInput, $plan, $rests, 100);

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

        $layerInput = LayerPlanInput::fromFormData(
            [
            'room_width' => '200',
            'room_depth' => '100',
            'tile_width' => '20',
            'tile_length' => '59',
            'min_tile_length' => '20',
            'gap_width' => '0',
            'laying_type' => LayerPlannerType::TYPE_OFFSET,
            'costs_per_square' => '0',
            ]
        );
        $plan = new LayerPlan();
        $rests = new Rests();

        $actualTile = $creator->create($layerInput, $plan, $rests, 100);

        self::assertEquals(65, $actualTile->getLength());
    }
}