<?php

declare(strict_types=1);

namespace App\TilePlanner\Creator;

use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Models\Row;
use App\TilePlanner\Models\Tile;

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
        TilePlanInput $tileInput,
        TilePlan      $plan,
        Rests         $rest
    ): Row {
        $row = new Row();
        $tileCounter = 1;

        while (!$this->isRowEnd($tileInput->getRoomWidth())) {
            $tile = $this->calculateTile(
                $tileInput,
                $tileCounter,
                $plan,
                $rest
            );

            $tile->setLengthPercent($tileInput->getRoomWidth());
            $row->addTile($tile);

            $this->usedRowLength += $tile->getLength();
            $tileCounter++;
        }

        $row->setWidthPercent($tileInput->getRoomDepth(), $tileInput->getTileWidth());
        $this->usedRowLength = 0;

        return $row;
    }

    private function isRowEnd(float $roomWidth): bool
    {
        return $this->usedRowLength >= $roomWidth;
    }

    private function calculateTile(TilePlanInput $tileInput, int $tileCounter, TilePlan $plan, Rests $rests): Tile
    {
        if ($tileCounter === 1) {
            return $this->firstTileLengthCalculator->create(
                $tileInput,
                $plan,
                $rests
            );
        }

        if ($this->isLastTileOfRow($tileInput)) {
            return $this->lastTileLengthCalculator->create(
                $tileInput,
                $plan,
                $rests,
                $this->usedRowLength
            );
        }

        return $this->createTile(
            $tileInput->getTileWidth(),
            $tileInput->getTileLength()
        );
    }

    private function isLastTileOfRow(TilePlanInput $tileInput): bool
    {
        $restOfRow = $tileInput->getRoomWidth() - $this->usedRowLength;

        return $restOfRow < $tileInput->getTileLength();
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
