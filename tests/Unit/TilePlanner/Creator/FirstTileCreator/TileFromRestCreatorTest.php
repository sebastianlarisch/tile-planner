<?php

declare(strict_types=1);

namespace Tests\Unit\TilePlanner\Creator\FirstTileCreator;

use App\Form\TilePlannerType;
use App\TilePlanner\Creator\FirstTileCreator\TileFromRestCreator;
use App\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use App\TilePlanner\TilePlannerConstants;
use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\LengthRange;
use App\TilePlanner\Models\LengthRangeBag;
use App\TilePlanner\Models\Rest;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Validator\DeviationValidatorInterface;
use App\TilePlanner\Validator\RangeValidatorInterface;
use PHPUnit\Framework\TestCase;

final class TileFromRestCreatorTest extends TestCase
{
    private TilePlanInput $tileInput;

    public function setUp(): void
    {
        $this->tileInput = TilePlanInput::fromData(
            [
            'room_width' => '200',
            'room_depth' => '100',
            'tile_width' => '20',
            'tile_length' => '50',
            'min_tile_length' => '10',
            'gap_width' => '0',
            'laying_type' => TilePlannerType::TYPE_OFFSET,
            'costs_per_square' => '0',
            ]
        );
    }

    public function test_return_null_if_has_no_rests_for_left_side(): void
    {
        $rangeValidator = $this->createMock(RangeValidatorInterface::class);
        $rangeValidator->method('isInRange')->willReturn(false);

        $deviationValidator = $this->createStub(DeviationValidatorInterface::class);
        $deviationValidator->method('isValidDeviation')->willReturn(true);

        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );

        $creator = new TileFromRestCreator($rangeValidator, $deviationValidator, $rangeCreator);

        $plan = new TilePlan();
        $rests = new Rests();
        $rests::setRest([]);

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertNull($actualTile);
    }

    public function test_return_first_found_and_matching_tile_from_rest(): void
    {
        $rangeValidator = $this->createMock(RangeValidatorInterface::class);
        $rangeValidator->method('isInRange')->willReturn(true);

        $deviationValidator = $this->createStub(DeviationValidatorInterface::class);
        $deviationValidator->method('isValidDeviation')->willReturn(true);

        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );

        $creator = new TileFromRestCreator($rangeValidator, $deviationValidator, $rangeCreator);

        $plan = new TilePlan();
        $rests = new Rests();
        $rests::setRest(
            [
                TilePlannerConstants::RESTS_LEFT => [
                    Rest::create(35, 1)
                ]
            ]
        );

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertEquals(35, $actualTile->getLength());
        self::assertEquals(1, $actualTile->getNumber());
    }

    public function test_return_tile_cut_of_from_lowest_found_rest(): void
    {
        $rangeValidator = $this->createMock(RangeValidatorInterface::class);
        $rangeValidator->method('isInRange')->willReturn(false);

        $deviationValidator = $this->createStub(DeviationValidatorInterface::class);
        $deviationValidator->method('isValidDeviation')->willReturn(true);

        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );

        $creator = new TileFromRestCreator($rangeValidator, $deviationValidator, $rangeCreator);

        $plan = new TilePlan();
        $rests = new Rests();
        $rests::setRest(
            [
                TilePlannerConstants::RESTS_LEFT => [
                    Rest::create(80, 1),
                    Rest::create(70, 2),
                    Rest::create(50, 2),
                ]
            ]
        );

        $expectedRest = [
            Rest::create(80, 1),
            Rest::create(70, 2),
        ];

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertEquals(30, $actualTile->getLength());
        self::assertEquals($expectedRest, $rests->getRests(TilePlannerConstants::RESTS_LEFT));
    }

    public function test_return_tile_cut_of_from_one_found_rest(): void
    {
        $rangeValidator = $this->createMock(RangeValidatorInterface::class);
        $rangeValidator->method('isInRange')->willReturn(false);

        $deviationValidator = $this->createStub(DeviationValidatorInterface::class);
        $deviationValidator->method('isValidDeviation')->willReturn(true);

        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );

        $creator = new TileFromRestCreator($rangeValidator, $deviationValidator, $rangeCreator);

        $plan = new TilePlan();
        $rests = new Rests();
        $rests::setRest(
            [
                TilePlannerConstants::RESTS_LEFT => [
                    Rest::create(80, 1),
                ]
            ]
        );

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertEquals(30, $actualTile->getLength());
        self::assertEmpty($rests->getRests(TilePlannerConstants::RESTS_LEFT));
    }
}