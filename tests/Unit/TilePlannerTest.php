<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\TilePlanner;
use PHPUnit\Framework\TestCase;

final class TilePlannerTest extends TestCase
{
    public function test_plan_creation(): void
    {
        $inputData = [
            'room_width' => 300,
            'room_depth' => 230,
            'tile_width' => 20,
            'tile_length' => 110,
            'min_tile_length' => 30,
        ];

        $planner = TilePlanner::createPlan($inputData);

        $plan = $planner;

        self::assertEquals('69000', $plan->getTotalArea());
        self::assertEquals('230', $plan->getRoomDepth());
        self::assertEquals('300', $plan->getRoomWidth());
        self::assertCount(11, $plan->getRows());
    }
}