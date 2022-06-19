<?php

declare(strict_types=1);

namespace App\TilePlanner\Models;

use App\Form\TilePlannerType;
use Assert\Assert;

final class TilePlanInput
{
    private float $roomWidth;
    private float $roomDepth;
    private float $tileWidth;
    private float $tileLength;
    private float $minTileLength;
    private float $gapWidth;
    private float $costsPerSquare;
    private string $layingType;

    private function __construct(
        float $roomWidth,
        float $roomDepth,
        float $tileWidth,
        float $tileLength,
        float $minTileLength,
        string $layingType,
        float $gapWidth,
        float $costsPerSquare,
    ) {
        $this->roomWidth = $roomWidth;
        $this->roomDepth = $roomDepth;
        $this->tileWidth = $tileWidth;
        $this->tileLength = $tileLength;
        $this->minTileLength = $minTileLength;
        $this->layingType = $layingType;
        $this->gapWidth = $this->convertToCm($gapWidth);
        $this->costsPerSquare = $costsPerSquare;

        Assert::that($roomWidth)->notEmpty();
    }

    public static function fromData(array $inputData): self
    {
        return new self(
            (float)$inputData['room_width'],
            (float)$inputData['room_depth'],
            (float)$inputData['tile_width'],
            (float)$inputData['tile_length'],
            (float)$inputData['min_tile_length'],
            (string)($inputData['laying_type'] ?? 'offset'),
            (float)($inputData['gap_width'] ?? 0),
            (float)($inputData['costs_per_square'] ?? 0),
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

    public function getCostsPerSquare(): float
    {
        return $this->costsPerSquare;
    }
}
