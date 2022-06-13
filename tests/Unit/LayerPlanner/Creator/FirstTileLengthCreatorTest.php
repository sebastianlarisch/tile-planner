<?php

declare(strict_types=1);

namespace Tests\Unit\LayerPlanner\Creator;

use App\LayerPlanner\Creator\FirstTileCreator\FirstTileCreatorInterface;
use App\LayerPlanner\Creator\FirstTileLengthCreator;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Tile;
use PHPUnit\Framework\TestCase;

final class FirstTileLengthCreatorTest extends TestCase
{
    private LayerPlanInput $layerPlanInput;

    private const PLAN_INPUT_DATA = [
        'room_width' => '400',
        'room_depth' => '300',
        'tile_width' => '20',
        'tile_length' => '100',
        'min_tile_length' => '30',
        'gap_width' => '5',
        'laying_type' => 'offset',
        'costs_per_square' => '20',
    ];

    public function setUp(): void
    {
        $this->layerPlanInput = LayerPlanInput::fromFormData(self::PLAN_INPUT_DATA);
    }

    public function test_calculate_uses_defaults_without_any_calculator(): void
    {
        $calculator = new FirstTileLengthCreator([]);

        $rests = new Rests();
        $plan = new LayerPlan();

        $actualTile = $calculator->create($this->layerPlanInput, $plan, $rests);

        self::assertEquals(self::PLAN_INPUT_DATA['tile_length'], $actualTile->getLength());
        self::assertEquals(self::PLAN_INPUT_DATA['tile_width'], $actualTile->getWidth());
    }

    public function test_calculate_uses_returned_tile_from_calculator(): void
    {
        $calculatorStack = $this->createMock(FirstTileCreatorInterface::class);
        $calculatorStack->method('create')->willReturn(Tile::create(25, 120));

        $calculator = new FirstTileLengthCreator(
            [
            $calculatorStack
            ]
        );

        $rests = new Rests();
        $plan = new LayerPlan();

        $actualTile = $calculator->create($this->layerPlanInput, $plan, $rests);

        self::assertEquals(120, $actualTile->getLength());
        self::assertEquals(25, $actualTile->getWidth());
    }

    public function test_calculate_uses_defaults_when_returned_tile_from_calculator_is_null(): void
    {
        $calculatorStack = $this->createMock(FirstTileCreatorInterface::class);
        $calculatorStack->method('create')->willReturn(null);

        $calculator = new FirstTileLengthCreator(
            [
            $calculatorStack
            ]
        );

        $rests = new Rests();
        $plan = new LayerPlan();

        $actualTile = $calculator->create($this->layerPlanInput, $plan, $rests);

        self::assertEquals(self::PLAN_INPUT_DATA['tile_length'], $actualTile->getLength());
        self::assertEquals(self::PLAN_INPUT_DATA['tile_width'], $actualTile->getWidth());
    }
}