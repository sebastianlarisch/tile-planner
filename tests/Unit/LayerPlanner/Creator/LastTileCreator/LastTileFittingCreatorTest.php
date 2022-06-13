<?php

declare(strict_types=1);

namespace Tests\Unit\LayerPlanner\Creator\LastTileCreator;

use App\Form\LayerPlannerType;
use App\LayerPlanner\Creator\LastTileCreator\LastTileFittingCreator;
use App\LayerPlanner\LayerPlannerConstants;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\Rests;
use PHPUnit\Framework\TestCase;

final class LastTileFittingCreatorTest extends TestCase
{
    public function test_return_fitting_tile(): void
    {
        $creator = new LastTileFittingCreator();

        $layerInput = LayerPlanInput::fromFormData(
            [
            'room_width' => '200',
            'room_depth' => '100',
            'tile_width' => '20',
            'tile_length' => '50',
            'min_tile_length' => '10',
            'gap_width' => '0',
            'laying_type' => LayerPlannerType::TYPE_OFFSET,
            'costs_per_square' => '0',
            ]
        );

        $plan = new LayerPlan();
        $rests = new Rests();
        $usedRowLength = 175;

        $actualTile = $creator->create($layerInput, $plan, $rests, $usedRowLength);

        self::assertEquals(25, $actualTile->getLength());
        self::assertCount(1, $rests->getRests(LayerPlannerConstants::RESTS_LEFT));
    }
}