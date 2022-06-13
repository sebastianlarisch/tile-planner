<?php

declare(strict_types=1);

namespace Tests\Unit\LayerPlanner\Creator\FirstTileCreator;

use App\Form\LayerPlannerType;
use App\LayerPlanner\Creator\FirstTileCreator\ChessTileCreator;
use App\LayerPlanner\Creator\TileLengthRangeCreatorInterface;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\LengthRange;
use App\LayerPlanner\Models\LengthRangeBag;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Validator\RangeValidatorInterface;
use PHPUnit\Framework\TestCase;

final class ChessTileCreatorTest extends TestCase
{
    public function test_create_tile_with_full_with_if_length_is_in_range(): void
    {
        $validator = $this->createMock(RangeValidatorInterface::class);
        $validator->method('isInRange')->willReturn(true);

        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(new LengthRangeBag());

        $creator = new ChessTileCreator($validator, $rangeCreator);

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
        $rests = new Rests();

        $actualTile = $creator->create($layerInput, $plan, $rests);

        self::assertEquals(20, $actualTile->getWidth());
        self::assertEquals(50, $actualTile->getLength());
    }

    public function test_create_tile_with_min_with_and_rest_if_length_is_not_in_range(): void
    {
        $validator = $this->createMock(RangeValidatorInterface::class);
        $validator->method('isInRange')->willReturn(false);

        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );

        $creator = new ChessTileCreator($validator, $rangeCreator);

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
        $rests = new Rests();

        $actualTile = $creator->create($layerInput, $plan, $rests);

        self::assertEquals(20, $actualTile->getWidth());
        self::assertEquals(10, $actualTile->getLength());
    }
}