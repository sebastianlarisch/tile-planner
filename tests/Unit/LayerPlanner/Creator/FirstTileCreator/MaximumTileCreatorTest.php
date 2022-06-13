<?php

declare(strict_types=1);

namespace Tests\Unit\LayerPlanner\Creator\FirstTileCreator;

use App\Form\LayerPlannerType;
use App\LayerPlanner\Creator\FirstTileCreator\MaximumTileCreator;
use App\LayerPlanner\Creator\TileLengthRangeCreatorInterface;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\LengthRange;
use App\LayerPlanner\Models\LengthRangeBag;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Validator\DeviationValidatorInterface;
use App\LayerPlanner\Validator\RangeValidatorInterface;
use PHPUnit\Framework\TestCase;

final class MaximumTileCreatorTest extends TestCase
{
    private LayerPlanInput $layerInput;

    public function setUp(): void
    {
        $this->layerInput = LayerPlanInput::fromFormData(
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
    }

    public function test_create_returns_null_if_length_is_in_range(): void
    {
        $deviationValidator = $this->createStub(DeviationValidatorInterface::class);

        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );

        $creator = new MaximumTileCreator($deviationValidator, $rangeCreator);

        $plan = new LayerPlan();
        $rests = new Rests();

        $actualTile = $creator->create($this->layerInput, $plan, $rests);

        self::assertNull($actualTile);
    }

    public function test_create_returns_null_if_length_is_not_in_range_and_deviation_not_valid(): void
    {
        $deviationValidator = $this->createStub(DeviationValidatorInterface::class);

        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );

        $creator = new MaximumTileCreator($deviationValidator, $rangeCreator);

        $plan = new LayerPlan();
        $rests = new Rests();

        $actualTile = $creator->create($this->layerInput, $plan, $rests);

        self::assertNull($actualTile);
    }

    public function test_create_returns_max_of_first_range_if_tile_length_is_not_in_range(): void
    {
        $deviationValidator = $this->createStub(DeviationValidatorInterface::class);
        $deviationValidator->method('isValidDeviation')->willReturn(true);

        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );

        $creator = new MaximumTileCreator($deviationValidator, $rangeCreator);

        $plan = new LayerPlan();
        $rests = new Rests();

        $actualTile = $creator->create($this->layerInput, $plan, $rests);

        self::assertEquals(30, $actualTile->getLength());
    }
}