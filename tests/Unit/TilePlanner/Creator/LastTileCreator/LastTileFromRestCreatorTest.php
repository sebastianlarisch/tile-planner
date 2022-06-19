<?php

declare(strict_types=1);

namespace Tests\Unit\TilePlanner\Creator\LastTileCreator;

use App\Form\TilePlannerType;
use App\TilePlanner\Creator\LastTileCreator\LastTileFromRestCreator;
use App\TilePlanner\TilePlannerConstants;
use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\Rest;
use App\TilePlanner\Models\Rests;
use PHPUnit\Framework\TestCase;

final class LastTileFromRestCreatorTest extends TestCase
{
    private TilePlanInput $tileInput;

    public function setUp(): void
    {
        $this->tileInput = TilePlanInput::fromFormData(
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

    public function test_return_null_when_no_rests_available(): void
    {
        $creator = new LastTileFromRestCreator();

        $plan = new TilePlan();
        $rests = new Rests();

        $actualTile = $creator->create($this->tileInput, $plan, $rests, 150);

        self::assertNull($actualTile);
    }

    public function test_return_matching_tile_from_rest(): void
    {
        $creator = new LastTileFromRestCreator();

        $plan = new TilePlan();
        $rests = new Rests();
        $rests::setRest(
            [
                TilePlannerConstants::RESTS_RIGHT => [
                    Rest::create(50, 1)
                ]
            ]
        );

        $actualTile = $creator->create($this->tileInput, $plan, $rests, 150);

        self::assertEquals(50, $actualTile->getLength());
    }

    public function test_return_tile_cut_of_from_lowest_found_rest_having_one_rest(): void
    {
        $creator = new LastTileFromRestCreator();

        $plan = new TilePlan();
        $rests = new Rests();
        $rests::setRest(
            [
                TilePlannerConstants::RESTS_RIGHT => [
                    Rest::create(80, 1),
                ]
            ]
        );

        $actualTile = $creator->create($this->tileInput, $plan, $rests, 140);

        self::assertEquals(60, $actualTile->getLength());
        self::assertEmpty($rests->getRests(TilePlannerConstants::RESTS_RIGHT));
    }

    public function test_return_tile_cut_of_from_lowest_found_rest_having_multiple_rests(): void
    {
        $creator = new LastTileFromRestCreator();

        $plan = new TilePlan();
        $rests = new Rests();
        $rests::setRest(
            [
                TilePlannerConstants::RESTS_RIGHT => [
                    Rest::create(80, 1),
                    Rest::create(70, 2),
                    Rest::create(20, 2),
                ]
            ]
        );

        $actualTile = $creator->create($this->tileInput, $plan, $rests, 140);

        $expectedRest = [
            Rest::create(80, 1),
            Rest::create(20, 2),
        ];

        self::assertEquals(60, $actualTile->getLength());
        self::assertEquals($expectedRest, array_values($rests->getRests(TilePlannerConstants::RESTS_RIGHT)));
    }
}