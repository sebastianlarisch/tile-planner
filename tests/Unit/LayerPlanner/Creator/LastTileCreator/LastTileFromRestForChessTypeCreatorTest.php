<?php

declare(strict_types=1);

namespace Tests\Unit\LayerPlanner\Creator\LastTileCreator;

use App\Form\LayerPlannerType;
use App\LayerPlanner\Creator\LastTileCreator\LastTileFromRestForChessTypeCreator;
use App\LayerPlanner\LayerPlannerConstants;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\Rest;
use App\LayerPlanner\Models\Rests;
use PHPUnit\Framework\TestCase;

final class LastTileFromRestForChessTypeCreatorTest extends TestCase
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
            'min_tile_length' => '10',
            'gap_width' => '0',
            'laying_type' => LayerPlannerType::TYPE_OFFSET,
            'costs_per_square' => '0',
            ]
        );
    }

    public function test_return_null_when_no_rests(): void
    {
        $creator = new LastTileFromRestForChessTypeCreator();

        $plan = new LayerPlan();
        $rests = new Rests();

        $actualTile = $creator->create($this->layerInput, $plan, $rests, 150);

        self::assertNull($actualTile);
    }

    public function test_return_matching_tile_from_rest(): void
    {
        $creator = new LastTileFromRestForChessTypeCreator();

        $plan = new LayerPlan();
        $rests = new Rests();
        $rests::setRest(
            [
                LayerPlannerConstants::RESTS_LEFT => [
                    Rest::create(25, 1)
                ]
            ]
        );

        $actualTile = $creator->create($this->layerInput, $plan, $rests, 175);

        self::assertEquals(25, $actualTile->getLength());
    }

    public function test_return_tile_cut_of_from_lowest_found_rest_having_one_rest(): void
    {
        $creator = new LastTileFromRestForChessTypeCreator();

        $plan = new LayerPlan();
        $rests = new Rests();
        $rests::setRest(
            [
                LayerPlannerConstants::RESTS_LEFT => [
                    Rest::create(80, 1),
                ]
            ]
        );
        $usedRowLength = 175;

        $actualTile = $creator->create($this->layerInput, $plan, $rests, $usedRowLength);

        self::assertEquals(25, $actualTile->getLength());
        self::assertCount(0, $rests->getRests(LayerPlannerConstants::RESTS_LEFT));
    }

    public function test_return_tile_cut_of_from_lowest_found_rest_having_multiple_rests(): void
    {
        $creator = new LastTileFromRestForChessTypeCreator();

        $plan = new LayerPlan();
        $rests = new Rests();
        $rests::setRest(
            [
                LayerPlannerConstants::RESTS_LEFT => [
                    Rest::create(80, 1),
                    Rest::create(70, 2),
                    Rest::create(50, 2),
                ]
            ]
        );
        $usedRowLength = 175;

        $actualTile = $creator->create($this->layerInput, $plan, $rests, $usedRowLength);

        self::assertEquals(25, $actualTile->getLength());
        self::assertCount(2, $rests->getRests(LayerPlannerConstants::RESTS_LEFT));
    }
}