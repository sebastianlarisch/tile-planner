<?php

declare(strict_types=1);

namespace Tests\Unit\TilePlanner\Creator\FirstTileCreator;

use App\Form\TilePlannerType;
use App\TilePlanner\Creator\FirstTileCreator\MinimumTileCreator;
use App\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\LengthRange;
use App\TilePlanner\Models\LengthRangeBag;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Models\Row;
use App\TilePlanner\Models\Tile;
use App\TilePlanner\Validator\DeviationValidatorInterface;
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

        $tileInput = TilePlanInput::fromData(
            [
            'room_width' => '200',
            'room_depth' => '100',
            'tile_width' => '20',
            'tile_length' => '50',
            'min_tile_length' => '20',
            'gap_width' => '0',
            'laying_type' => TilePlannerType::TYPE_OFFSET,
            'costs_per_square' => '0',
            ]
        );

        $plan = (new TilePlan())
            ->setRows(
                [
                (new Row())->addTile(Tile::create(20, 30))
                ]
            );
        $rests = new Rests();

        $actualTile = $creator->create($tileInput, $plan, $rests);

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

        $tileInput = TilePlanInput::fromData(
            [
            'room_width' => '200',
            'room_depth' => '100',
            'tile_width' => '20',
            'tile_length' => '30',
            'min_tile_length' => '10',
            'gap_width' => '0',
            'laying_type' => TilePlannerType::TYPE_OFFSET,
            'costs_per_square' => '0',
            ]
        );

        $plan = (new TilePlan())
            ->setRows(
                [
                (new Row())->addTile(Tile::create(20, 30))
                ]
            );
        $rests = new Rests();

        $actualTile = $creator->create($tileInput, $plan, $rests);

        self::assertEquals(10, $actualTile->getLength());
    }
}
