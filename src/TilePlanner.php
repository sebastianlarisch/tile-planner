<?php

declare(strict_types=1);

namespace App;

use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\TilePlannerFactory;

final class TilePlanner
{
    public static function createPlan(array $inputData): TilePlan
    {
        $tileInput = TilePlanInput::fromData($inputData);
        $factory = new TilePlannerFactory();

        return $factory
            ->createTilePlanCreator($tileInput->getLayingType())
            ->create($tileInput);
    }
}