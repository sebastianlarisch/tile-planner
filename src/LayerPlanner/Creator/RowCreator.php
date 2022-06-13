<?php

declare(strict_types=1);

namespace App\LayerPlanner\Creator;

use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Row;
use App\LayerPlanner\Models\Tile;

final class RowCreator implements RowCreatorInterface
{
    private float $usedRowLength = 0;
    private FirstTileLengthCreatorInterface $firstTileLengthCalculator;
    private LastTileLengthCreatorInterface $lastTileLengthCalculator;

    public function __construct(
        FirstTileLengthCreatorInterface $firstTileLengthCalculator,
        LastTileLengthCreatorInterface $lastTileLengthCalculator
    ) {
        $this->firstTileLengthCalculator = $firstTileLengthCalculator;
        $this->lastTileLengthCalculator = $lastTileLengthCalculator;
    }

    public function createRow(
        LayerPlanInput $layerInput,
        LayerPlan $plan,
        Rests $rest
    ): Row {
        $row = new Row();
        $tileCounter = 1;

        while (!$this->isRowEnd($layerInput->getRoomWidth())) {
            $tile = $this->calculateTile(
                $layerInput,
                $tileCounter,
                $plan,
                $rest
            );

            $tile->setLengthPercent($layerInput->getRoomWidth());
            $row->addTile($tile);

            $this->usedRowLength += $tile->getLength();
            $tileCounter++;
        }

        $row->setWidthPercent($layerInput->getRoomDepth(), $layerInput->getTileWidth());
        $this->usedRowLength = 0;

        return $row;
    }

    private function isRowEnd(float $roomWidth): bool
    {
        return $this->usedRowLength >= $roomWidth;
    }

    private function calculateTile(LayerPlanInput $layerInput, int $tileCounter, LayerPlan $plan, Rests $rests): Tile
    {
        if ($tileCounter === 1) {
            return $this->firstTileLengthCalculator->create(
                $layerInput,
                $plan,
                $rests
            );
        }

        if ($this->isLastTileOfRow($layerInput)) {
            return $this->lastTileLengthCalculator->create(
                $layerInput,
                $plan,
                $rests,
                $this->usedRowLength
            );
        }

        return $this->createTile(
            $layerInput->getTileWidth(),
            $layerInput->getTileLength()
        );
    }

    private function isLastTileOfRow(LayerPlanInput $layerInput): bool
    {
        $restOfRow = $layerInput->getRoomWidth() - $this->usedRowLength;

        return $restOfRow < $layerInput->getTileLength();
    }

    private function createTile(float $width, float $length, ?int $number = null): Tile
    {
        return Tile::create(
            $width,
            $length,
            $number
        );
    }
}
