<?php

declare(strict_types=1);

namespace Tests\Unit\LayerPlanner\Creator;

use App\Form\LayerPlannerType;
use App\LayerPlanner\Creator\FirstTileLengthCreatorInterface;
use App\LayerPlanner\Creator\LastTileLengthCreatorInterface;
use App\LayerPlanner\Creator\RowCreator;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Tile;
use PHPUnit\Framework\TestCase;

final class RowCreatorTest extends TestCase
{
    public function test(): void
    {
        $firstTileLengthCalculator = $this->createMock(FirstTileLengthCreatorInterface::class);
        $firstTileLengthCalculator
            ->method('create')
            ->willReturn(Tile::create(15, 25));

        $lastTileLengthCalculator = $this->createMock(LastTileLengthCreatorInterface::class);
        $lastTileLengthCalculator
            ->method('create')
            ->willReturn(Tile::create(15, 25));

        $creator = new RowCreator($firstTileLengthCalculator, $lastTileLengthCalculator);

        $layerInput = LayerPlanInput::fromFormData(
            [
            'room_width' => '200',
            'room_depth' => '100',
            'tile_width' => '20',
            'tile_length' => '50',
            'min_tile_length' => '20',
            'gap_width' => '0',
            'laying_type' => LayerPlannerType::TYPE_OFFSET,
            'costs_per_square' => '0',
            ]
        );
        $plan = new LayerPlan();
        $rest = new Rests();

        $actual = $creator->createRow($layerInput, $plan, $rest);

        self::assertCount(5, $actual->getTiles());
    }
}