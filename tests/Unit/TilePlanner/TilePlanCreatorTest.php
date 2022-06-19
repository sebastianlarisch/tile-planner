<?php

declare(strict_types=1);

namespace Tests\Unit\TilePlanner;

use App\Form\TilePlannerType;
use App\TilePlanner\Creator\RowCreatorInterface;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Models\Row;
use App\TilePlanner\Models\Tile;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\TilePlanCreator;
use App\TilePlanner\TilePlannerConstants;
use PHPUnit\Framework\TestCase;

final class TilePlanCreatorTest extends TestCase
{
    public function test_plan_created_successful(): void
    {
        $rowCreator = $this->createStub(RowCreatorInterface::class);
        $rowCreator->method('createRow')->willReturn(
            (new Row())
                ->addTile(Tile::create(25,100))
        );

        $rests = new Rests();
        $rests::setRest(
            [
                TilePlannerConstants::RESTS_LEFT => [],
                TilePlannerConstants::RESTS_RIGHT => [],
            ]
        );

        $creator = new TilePlanCreator($rowCreator, $rests);

        $tileInput = TilePlanInput::fromData(
            [
                'room_width' => '100',
                'room_depth' => '25',
                'tile_width' => '25',
                'tile_length' => '50',
                'min_tile_length' => '20',
                'gap_width' => '0',
                'laying_type' => TilePlannerType::TYPE_OFFSET,
                'costs_per_square' => '2',
            ]
        );

        $plan = $creator->create($tileInput);

        self::assertCount(1, $plan->getRows());
        self::assertEquals(2500, $plan->getTotalArea());
        self::assertEquals(0.25, $plan->getTotalAreaInSquareMeter());
        self::assertEquals(0.5, $plan->getTotalPrice());
    }
}