<?php

declare(strict_types=1);

namespace App\TilePlanner;

use App\TilePlanner\Creator\RowCreatorInterface;
use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Models\Row;
use App\TilePlanner\Models\Tile;

final class TilePlanner
{
    private RowCreatorInterface $creator;
    private Rests $rests;

    public function __construct(RowCreatorInterface $rowCreator, Rests $rests)
    {
        $this->creator = $rowCreator;
        $this->rests = $rests;
    }

    public function createPlan(TilePlanInput $tileInput): TilePlan
    {
        $plan = new TilePlan();
        $totalRows = $this->getTotalRows($tileInput);

        for ($i = 1; $i <= $totalRows; $i++) {
            $row = $this->creator->createRow(
                $tileInput,
                $plan,
                $this->rests
            );

            $plan->addRow($row);
            $plan->setTotalTiles($this->getHighestTileNumberFromRow($row));
        }

        $plan->setTotalArea($tileInput->getRoomWidthWithGaps() * $tileInput->getRoomDepthWithGaps());
        $plan->setTotalPrice($plan->getTotalArea() * $tileInput->getCostsPerSquare());
        $plan->setRoomWidth($tileInput->getRoomWidthWithGaps());
        $plan->setRoomDepth($tileInput->getRoomDepthWithGaps());
        $plan->setTrash($this->mergeTiles($this->rests->getTrash()));
        $plan->setRests(
            array_merge(
                $this->rests->getRests(TilePlannerConstants::RESTS_LEFT),
                $this->rests->getRests(TilePlannerConstants::RESTS_RIGHT)
            )
        );
        $plan->setTotalRest($this->rests->getSumOfAll());

        return $plan;
    }

    private function getTotalRows(TilePlanInput $input): int
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
