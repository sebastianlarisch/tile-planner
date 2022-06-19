<?php

declare(strict_types=1);

namespace App\TilePlanner\Models;

use JsonSerializable;

final class TilePlan implements JsonSerializable
{
    private array $rows = [];

    private array $rests = [];

    private array $trash = [];

    private float $totalRest = 0;

    private int $totalTiles = 0;

    private float $totalArea;

    private float $totalPrice;

    private float $roomWidth;

    private float $roomDepth;

    public function getRows(): array
    {
        return $this->rows;
    }

    public function setRows(array $rows): self
    {
        $this->rows = $rows;

        return $this;
    }

    public function addRow(Row $row): void
    {
        $this->rows[] = $row;
    }

    public function getRests(): array
    {
        return $this->rests;
    }

    public function setRests(array $rests): void
    {
        $this->rests = $rests;
    }

    public function getTrash(): array
    {
        return $this->trash;
    }

    public function setTrash(array $trash): void
    {
        $this->trash = $trash;
    }

    public function getLastRowLength(): float
    {
        if (empty($this->rows)) {
            return 0;
        }

        $lastRow = array_reverse($this->rows)[0];

        if (empty($lastRow)) {
            return 0;
        }

        return $lastRow->getTiles()[0]->getLength() ?? 0;
    }

    public function getRowBeforeLastLength(): float
    {
        if (empty($this->rows)) {
            return 0;
        }

        $beforeLastRow = array_reverse($this->rows)[1] ?? null;

        if ($beforeLastRow === null) {
            return 0;
        }

        return $beforeLastRow->getTiles()[0]->getLength() ?? 0;
    }

    public function getTotalRest(): float
    {
        return (float)$this->totalRest;
    }

    public function setTotalRest(float $totalRest): self
    {
        $this->totalRest = $totalRest;

        return $this;
    }

    public function setTotalTiles(int $totalTiles): void
    {
        $this->totalTiles = $totalTiles;
    }

    public function getTotalTiles(): int
    {
        return $this->totalTiles;
    }

    public function setTotalArea(float $totalAreaInSquareCm): self
    {
        $this->totalArea = $totalAreaInSquareCm;

        return $this;
    }

    public function getTotalArea(): float
    {
        return $this->totalArea;
    }

    public function getTotalAreaInSquareMeter(): float
    {
        return $this->totalArea / 10000;
    }

    public function setTotalPrice(float $price): self
    {
        $this->totalPrice = $price;

        return $this;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function toArray(): array
    {
        return [
            'rows' => $this->rows,
            'rests' => $this->rests,
            'trash' => $this->trash,
            'totalTiles' => $this->totalTiles,
            'totalRest' => $this->totalRest
        ];
    }

    private function inSquareMeter(float $totalAreaInSquareCm): float
    {
        return round($totalAreaInSquareCm / 10000, 2);
    }

    public function getRoomWidth(): float
    {
        return $this->roomWidth;
    }

    public function setRoomWidth(float $roomWidth): TilePlan
    {
        $this->roomWidth = $roomWidth;

        return $this;
    }

    public function getRoomDepth(): float
    {
        return $this->roomDepth;
    }

    public function setRoomDepth(float $roomDepth): TilePlan
    {
        $this->roomDepth = $roomDepth;

        return $this;
    }

    public function jsonSerialize(): object
    {
        return (object)get_object_vars($this);
    }
}
