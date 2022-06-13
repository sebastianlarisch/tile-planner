<?php

declare(strict_types=1);

namespace Tests\Unit\LayerPlanner\Creator\FirstTileCreator;

use App\Form\LayerPlannerType;
use App\LayerPlanner\Creator\FirstTileCreator\MinimumTileCreator;
use App\LayerPlanner\Creator\TileLengthRangeCreatorInterface;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\LengthRange;
use App\LayerPlanner\Models\LengthRangeBag;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Row;
use App\LayerPlanner\Models\Tile;
use App\LayerPlanner\Validator\DeviationValidatorInterface;
use PHPUnit\Framework\TestCase;

final class MinimumTileCreatorTest extends TestCase
{
    public function test_create_return_null_when_deviation_is_false(): void
    {
        $rangeCalculator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCalculator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );

        $deviationValidator = $this->createStub(DeviationValidatorInterface::class);
        $deviationValidator->method('isValidDeviation')->willReturn(false);

        $creator = new MinimumTileCreator($rangeCalculator, $deviationValidator);

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

        $plan = (new LayerPlan())
            ->setRows(
                [
                (new Row())->addTile(Tile::create(20, 30))
                ]
            );
        $rests = new Rests();

        $actualTile = $creator->create($layerInput, $plan, $rests);

        self::assertNull($actualTile);
    }

    public function test_create_return_tile_with_min_length_if_first_of_last_row_has_max_length(): void
    {
        $rangeCalculator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCalculator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );
        $deviationValidator = $this->createMock(DeviationValidatorInterface::class);
        $deviationValidator->method('isValidDeviation')->willReturn(true);

        $creator = new MinimumTileCreator($rangeCalculator, $deviationValidator);

        $layerInput = LayerPlanInput::fromFormData(
            [
            'room_width' => '200',
            'room_depth' => '100',
            'tile_width' => '20',
            'tile_length' => '30',
            'min_tile_length' => '10',
            'gap_width' => '0',
            'laying_type' => LayerPlannerType::TYPE_OFFSET,
            'costs_per_square' => '0',
            ]
        );

        $plan = (new LayerPlan())
            ->setRows(
                [
                (new Row())->addTile(Tile::create(20, 30))
                ]
            );
        $rests = new Rests();

        $actualTile = $creator->create($layerInput, $plan, $rests);

        self::assertEquals(10, $actualTile->getLength());
    }
}
