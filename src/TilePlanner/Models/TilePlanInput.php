<?php

declare(strict_types=1);

namespace App\TilePlanner\Models;

use App\Form\TilePlannerType;

final class TilePlanInput
{
    private float $roomWidth;
    private float $roomDepth;
    private float $tileWidth;
    private float $tileLength;
    private float $minTileLength;
    private ?float $gapWidth = null;
    private ?float $costsPerSquare = 0;
    private ?string $layingType = 'offset';

    public function __construct(){}

    public static function fromData(array $inputData): self
    {
        $plan = new TilePlanInput();

        $plan->setRoomWidth((float)$inputData['room_width']);
        $plan->setRoomDepth((float)$inputData['room_depth']);
        $plan->setTileLength((float)$inputData['tile_length']);
        $plan->setTileWidth((float)$inputData['tile_width']);
        $plan->setGapWidth((float)$inputData['gap_width']);
        $plan->setMinTileLength((float)$inputData['min_tile_length']);
        $plan->setCostsPerSquare((float)$inputData['costs_per_square']);

        return $plan;
    }

    public function setRoomWidth(float $roomWidth): self
    {
        $this->roomWidth = $roomWidth;

        return $this;
    }

    public function getRoomWidth(): float
    {
        if ($this->gapWidth > 0) {
            return $this->roomWidth - ($this->gapWidth * $this->getTotalHorizontalGaps());
        }

        return $this->roomWidth;
    }

    public function setMinTileLength(float $minTileLength): self
    {
        $this->minTileLength = $minTileLength;

        return $this;
    }

    public function getMinTileLength(): float
    {
        return $this->minTileLength;
    }

    public function setRoomDepth(float $roomDepth): self
    {
        $this->roomDepth = $roomDepth;

        return $this;
    }

    public function getRoomDepth(): float
    {
        if ($this->gapWidth !== null && $this->gapWidth > 0) {
            return $this->roomDepth - ($this->gapWidth * $this->getTotalVerticalGaps());
        }

        return $this->roomDepth;
    }

    public function setTileWidth(float $tileWidth): self
    {
        $this->tileWidth = $tileWidth;

        return $this;
    }

    public function getTileWidth(): float
    {
        return $this->tileWidth;
    }

    public function setTileLength(float $tileLength): self
    {
        $this->tileLength = $tileLength;

        return $this;
    }

    public function getTileLength(): float
    {
        return $this->tileLength;
    }

    public function setGapWidth(float $gapWidth): void
    {
        $this->gapWidth = $gapWidth;
    }

    private function getTotalHorizontalGaps(): int
    {
        return (int)floor($this->roomWidth / $this->tileLength);
    }

    private function getTotalVerticalGaps(): int
    {
        return (int)floor($this->roomDepth / $this->tileWidth);
    }

    public function getLayingType(): string
    {
        return $this->layingType;
    }

    public function isOffsetLayingType(bool $isOffset): void
    {
        $this->layingType = $isOffset ? TilePlannerType::TYPE_OFFSET : TilePlannerType::TYPE_CHESS;
    }

    public function getRoomWidthWithGaps(): float
    {
        return $this->roomWidth;
    }

    public function getRoomDepthWithGaps(): float
    {
        return $this->roomDepth;
    }

    public function setCostsPerSquare(float $costsPerSquare = 0): self
    {
        $this->costsPerSquare = $costsPerSquare;

        return $this;
    }

    public function getCostsPerSquare(): float
    {
        return $this->costsPerSquare;
    }
}
