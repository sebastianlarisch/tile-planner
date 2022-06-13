<?php

declare(strict_types=1);

namespace Tests\Unit\LayerPlanner\Creator;

use App\Form\LayerPlannerType;
use App\LayerPlanner\Creator\TileLengthRangeCreator;
use App\LayerPlanner\Models\LayerPlanInput;
use PHPUnit\Framework\TestCase;

final class TileLengthRangeCreatorTest extends TestCase
{
    public function test_calculation_result_will_have_two_ranges(): void
    {
        $calculator = new TileLengthRangeCreator();
        $calculator::$rangeBag = null;

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
        $actualRanges = $calculator->calculateRanges($layerInput);

        $this->assertCount(2, $actualRanges->getRanges());
        $this->assertEquals(20, $actualRanges->getMinOfFirstRange());
        $this->assertEquals(30, $actualRanges->getMaxOfFirstRange());
    }

    public function test_calculation_result_will_have_one_range(): void
    {
        $calculator = new TileLengthRangeCreator();
        $calculator::$rangeBag = null;

        $layerInput = LayerPlanInput::fromFormData(
            [
            'room_width' => '200',
            'room_depth' => '100',
            'tile_width' => '20',
            'tile_length' => '50',
            'min_tile_length' => '30',
            'gap_width' => '0',
            'laying_type' => LayerPlannerType::TYPE_OFFSET,
            'costs_per_square' => '0',
            ]
        );
        $actualRanges = $calculator->calculateRanges($layerInput);

        $this->assertCount(1, $actualRanges->getRanges());
        $this->assertEquals(50, $actualRanges->getMinOfFirstRange());
        $this->assertEquals(50, $actualRanges->getMaxOfFirstRange());
    }
}