<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\TilePlanner;
use App\TilePlanner\Models\TilePlanInput;
use PHPUnit\Framework\TestCase;

final class TilePlannerTest extends TestCase
{
    public function test_plan_creation(): void
    {
        $inputData = (new TilePlanInput())
            ->setRoomWidth(300)
            ->setRoomDepth(230)
            ->setTileLength(110)
            ->setTileWidth(20)
            ->setMinTileLength(30);

        $planner = TilePlanner::createPlan($inputData);

        $plan = $planner;

        self::assertEquals('69000', $plan->getTotalArea());
        self::assertEquals('230', $plan->getRoomDepth());
        self::assertEquals('300', $plan->getRoomWidth());
        self::assertCount(11, $plan->getRows());
    }
}