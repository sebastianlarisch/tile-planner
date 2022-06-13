<?php

declare(strict_types=1);

namespace Tests\Unit\LayerPlanner;

use App\Form\LayerPlannerType;
use App\LayerPlanner\Creator\RowCreatorInterface;
use App\LayerPlanner\LayerPlanner;
use App\LayerPlanner\LayerPlannerConstants;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\Rest;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Row;
use App\LayerPlanner\Models\Tile;
use PHPUnit\Framework\TestCase;

final class LayerPlannerTest extends TestCase
{
    public function test_plan_created_successful(): void
    {
        $rowCreator = $this->createStub(RowCreatorInterface::class);
        $rowCreator->method('createRow')->willReturn(
            (new Row())
                ->addTile(Tile::create(25,100))
        );

        $rests = new Rests();
        $rests::setRest(
            [
                LayerPlannerConstants::RESTS_LEFT => [],
                LayerPlannerConstants::RESTS_RIGHT => [],
            ]
        );

        $planner = new LayerPlanner($rowCreator, $rests);

        $layerInput = LayerPlanInput::fromFormData(
            [
                'room_width' => '100',
                'room_depth' => '25',
                'tile_width' => '25',
                'tile_length' => '50',
                'min_tile_length' => '20',
                'gap_width' => '0',
                'laying_type' => LayerPlannerType::TYPE_OFFSET,
                'costs_per_square' => '2',
            ]
        );

        $plan = $planner->createPlan($layerInput);

        self::assertCount(1, $plan->getRows());
        self::assertEquals(0.25, $plan->getTotalArea());
        self::assertEquals(0.5, $plan->getTotalPrice());
    }
}