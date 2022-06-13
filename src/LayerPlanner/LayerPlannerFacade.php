<?php

declare(strict_types=1);

namespace App\LayerPlanner;

use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;

final class LayerPlannerFacade implements LayerPlannerFacadeInterface
{
    private LayerPlannerFactory $factory;

    public function __construct(LayerPlannerFactory $factory)
    {
        $this->factory = $factory;
    }

    public function createPlan(LayerPlanInput $layerInput): LayerPlan {

        return $this->factory
            ->createLayerPlannerByType($layerInput->getLayingType())
            ->createPlan($layerInput);
    }
}