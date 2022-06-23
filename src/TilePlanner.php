<?php

declare(strict_types=1);

namespace App;

use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\TilePlannerFactory;

final class TilePlanner
{
    public static function createPlan(TilePlanInput $planInput): TilePlan
    {
        $factory = new TilePlannerFactory();

        return $factory
            ->createTilePlanCreator($planInput->getLayingType())
            ->create($planInput);
    }
}