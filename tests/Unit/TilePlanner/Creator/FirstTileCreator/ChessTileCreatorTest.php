<?php

declare(strict_types=1);

namespace Tests\Unit\TilePlanner\Creator\FirstTileCreator;

use App\Form\TilePlannerType;
use App\TilePlanner\Creator\FirstTileCreator\ChessTileCreator;
use App\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\LengthRange;
use App\TilePlanner\Models\LengthRangeBag;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Validator\RangeValidatorInterface;
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
        $plan = new TilePlan();
        $rests = new Rests();

        $actualTile = $creator->create($tileInput, $plan, $rests);

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
        $plan = new TilePlan();
        $rests = new Rests();

        $actualTile = $creator->create($tileInput, $plan, $rests);

        self::assertEquals(20, $actualTile->getWidth());
        self::assertEquals(10, $actualTile->getLength());
    }
}