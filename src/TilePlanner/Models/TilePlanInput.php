<?php

declare(strict_types=1);

namespace App\TilePlanner\Models;

use Assert\Assert;

final class TilePlanInput
{
    private float $roomWidth;
    private float $roomDepth;
    private float $tileWidth;
    private float $tileLength;
    private float $minTileLength;
    private float $gapWidth;
    private string $layingType;
    private float $costsPerSquare;

    private function __construct(
        float $roomWidth,
        float $roomDepth,
        float $tileWidth,
        float $tileLength,
        float $minTileLength,
        float $gapWidth,
        string $layingType,
        float $costsPerSquare,
    ) {
        $this->roomWidth = $roomWidth;
        $this->roomDepth = $roomDepth;
        $this->tileWidth = $tileWidth;
        $this->tileLength = $tileLength;
        $this->minTileLength = $minTileLength;
        $this->gapWidth = $this->convertToCm($gapWidth);
        $this->layingType = $layingType;
        $this->costsPerSquare = $costsPerSquare;

        Assert::that($roomWidth)->notEmpty();
    }

    public static function fromFormData(array $formData): self
    {
        return new self(
            (float)$formData['room_width'],
            (float)$formData['room_depth'],
            (float)$formData['tile_width'],
            (float)$formData['tile_length'],
            (float)$formData['min_tile_length'],
            (float)$formData['gap_width'],
            (string)$formData['laying_type'],
            (float)$formData['costs_per_square'],
        );
    }

    public function getRoomWidth(): float
    {
        if ($this->gapWidth > 0) {
            return $this->roomWidth - ($this->gapWidth * $this->getTotalHorizontalGaps());
        }

        return $this->roomWidth;
    }

    public function getMinTileLength(): float
    {
        return $this->minTileLength;
    }

    public function getRoomDepth(): float
    {
        if ($this->gapWidth > 0) {
            return $this->roomDepth - ($this->gapWidth * $this->getTotalVerticalGaps());
        }

        return $this->roomDepth;
    }

    public function getTileWidth(): float
    {
        return $this->tileWidth;
    }

    public function getTileLength(): float
    {
        return $this->tileLength;
    }

    public function getGapWidth(): float
    {
        return $this->gapWidth;
    }

    public function setGapWidth(float $gapWidth): void
    {
        $this->gapWidth = $gapWidth;
    }

    private function convertToCm(float $gapWidthInMm): float
    {
        return $gapWidthInMm / 10;
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

    public function setLayingType(string $layingType): void
    {
        $this->layingType = $layingType;
    }

    public function getRoomWidthWithGaps(): float
    {
        return $this->roomWidth;
    }

    public function getRoomDepthWithGaps(): float
    {
        return $this->roomDepth;
    }

    public function getCostsPerSquare(): float
    {
        return $this->costsPerSquare;
    }
}
