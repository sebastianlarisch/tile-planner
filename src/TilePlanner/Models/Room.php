<?php

declare(strict_types=1);

namespace App\TilePlanner\Models;

final class Room
{
    private const ROUND = 2;
    private float $width;
    private float $depth;

    public static function create(array $formData): self
    {
        return new self($formData);
    }

    private function __construct(array $formData)
    {
        $this->width = $formData['room_width'];
        $this->depth = $formData['room_depth'];
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getDepth(): float
    {
        return $this->depth;
    }

    public function getSize(): float
    {
        return round($this->depth * $this->width, self::ROUND);
    }
}
