<?php

declare(strict_types=1);

namespace App\LayerPlanner\Models;

final class Row
{
    private array $tiles = [];

    private float $widthPercent;

    public function addTile(Tile $tile): self
    {
        $this->tiles[] = $tile;

        return $this;
    }

    public function getTiles(): array
    {
        return $this->tiles;
    }

    public function setWidthPercent(float $roomDepth, float $tileWidth): void
    {
        $this->widthPercent = round(100 / ($roomDepth / $tileWidth), 2);
    }

    public function getWidthPercent(): float
    {
        return $this->widthPercent;
    }
}
