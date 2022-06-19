<?php

declare(strict_types=1);

namespace App\TilePlanner\Models;

use JsonSerializable;

final class Tile implements JsonSerializable
{
    private float $width;

    private float $length;

    private int $number;

    private float $lengthPercent;

    private static int $numberCounter = 1;

    public static function create(float $width, float $length, ?int $number = null): self
    {
        return new self($width, $length, $number);
    }

    private function __construct(float $width, float $length, ?int $number)
    {
        $this->width = $width;
        $this->length = $length;

        if ($number !== null) {
            $this->number = $number;
        } else {
            $this->number = self::$numberCounter;
            self::$numberCounter++;
        }
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setLengthPercent(float $roomWidth): void
    {
        $this->lengthPercent = round($this->length * 100 / $roomWidth, 3);
    }

    public function getLengthPercent(): float
    {
        return $this->lengthPercent;
    }

    public function jsonSerialize(): object
    {
        return (object)get_object_vars($this);
    }
}
