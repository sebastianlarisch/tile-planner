<?php

declare(strict_types=1);

namespace App\LayerPlanner;

use App\LayerPlanner\Creator\RowCreatorInterface;
use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Row;
use App\LayerPlanner\Models\Tile;

final class LayerPlanner
{
    private RowCreatorInterface $creator;
    private Rests $rests;

    public function __construct(RowCreatorInterface $rowCreator, Rests $rests)
    {
        $this->creator = $rowCreator;
        $this->rests = $rests;
    }

    public function createPlan(LayerPlanInput $layerInput): LayerPlan
    {
        $plan = new LayerPlan();
        $totalRows = $this->getTotalRows($layerInput);

        for ($i = 1; $i <= $totalRows; $i++) {
            $row = $this->creator->createRow(
                $layerInput,
                $plan,
                $this->rests
            );

            $plan->addRow($row);
            $plan->setTotalTiles($this->getHighestTileNumberFromRow($row));
        }

        $plan->setTotalArea($layerInput->getRoomWidthWithGaps() * $layerInput->getRoomDepthWithGaps());
        $plan->setTotalPrice($plan->getTotalArea() * $layerInput->getCostsPerSquare());
        $plan->setRoomWidth($layerInput->getRoomWidthWithGaps());
        $plan->setRoomDepth($layerInput->getRoomDepthWithGaps());
        $plan->setTrash($this->mergeTiles($this->rests->getTrash()));
        $plan->setRests(
            array_merge(
                $this->rests->getRests(LayerPlannerConstants::RESTS_LEFT),
                $this->rests->getRests(LayerPlannerConstants::RESTS_RIGHT)
            )
        );
        $plan->setTotalRest($this->rests->getSumOfAll());

        return $plan;
    }

    private function getTotalRows(LayerPlanInput $input): int
    {
        return (int)floor(($input->getRoomDepth() / $input->getTileWidth()));
    }

    private function mergeTiles(array $tiles): array
    {
        $mergedTrash = [];

        foreach ($tiles as $trash) {
            if (!isset($mergedTrash[$trash])) {
                $mergedTrash[$trash] = 0;
            }

            $mergedTrash[$trash]++;
        }

        return $mergedTrash;
    }

    private function getHighestTileNumberFromRow(Row $row): int
    {
        $numbers = array_map(fn(Tile $tile) => $tile->getNumber(), $row->getTiles());

        return max($numbers);
    }
}
