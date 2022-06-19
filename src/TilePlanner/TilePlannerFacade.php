<?php

declare(strict_types=1);

namespace App\TilePlanner;

use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;

final class TilePlannerFacade implements TilePlannerFacadeInterface
{
    private TilePlannerFactory $factory;

    public function __construct(TilePlannerFactory $factory)
    {
        $this->factory = $factory;
    }

    public function createPlan(TilePlanInput $tileInput): TilePlan
    {
        return $this->factory
            ->createTilePlanCreator($tileInput->getLayingType())
            ->create($tileInput);
    }
}