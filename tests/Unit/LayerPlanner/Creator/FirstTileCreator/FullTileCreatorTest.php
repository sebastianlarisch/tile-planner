<?php

declare(strict_types=1);

namespace Tests\Unit\LayerPlanner\Creator\FirstTileCreator;

use App\Form\LayerPlannerType;
use App\LayerPlanner\Creator\FirstTileCreator\FullTileCreator;
use App\LayerPlanner\Creator\TileLengthRangeCreatorInterface;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\LengthRangeBag;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Validator\DeviationValidatorInterface;
use App\LayerPlanner\Validator\RangeValidatorInterface;
use PHPUnit\Framework\TestCase;

final class FullTileCreatorTest extends TestCase
{
    public function test_create_returns_tile_with_full_length(): void
    {
        $rangeValidator = $this->createMock(RangeValidatorInterface::class);
        $rangeValidator->method('isInRange')->willReturn(true);

        $deviationValidator = $this->createMock(DeviationValidatorInterface::class);
        $deviationValidator->method('isValidDeviation')->willReturn(true);

        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(new LengthRangeBag());

        $creator = new FullTileCreator($rangeValidator, $deviationValidator, $rangeCreator);

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

        self::assertEquals(50, $actualTile->getLength());
    }

    public function test_create_returns_null_when_deviation_is_not_valid(): void
    {
        $rangeValidator = $this->createStub(RangeValidatorInterface::class);

        $deviationValidator = $this->createMock(DeviationValidatorInterface::class);
        $deviationValidator->method('isValidDeviation')->willReturn(false);

        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(new LengthRangeBag());

        $creator = new FullTileCreator($rangeValidator, $deviationValidator, $rangeCreator);

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

        self::assertNull($actualTile);
    }

    public function test_create_returns_null_when_not_in_range(): void
    {
        $rangeValidator = $this->createMock(RangeValidatorInterface::class);
        $rangeValidator->method('isInRange')->willReturn(false);

        $deviationValidator = $this->createStub(DeviationValidatorInterface::class);

        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(new LengthRangeBag());

        $creator = new FullTileCreator($rangeValidator, $deviationValidator, $rangeCreator);

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

        self::assertNull($actualTile);
    }
}