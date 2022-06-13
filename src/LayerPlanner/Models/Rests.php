<?php

declare(strict_types=1);

namespace App\LayerPlanner\Models;

use App\LayerPlanner\LayerPlannerConstants;

final class Rests
{
    private static array $rest = [
        LayerPlannerConstants::RESTS_LEFT => [],
        LayerPlannerConstants::RESTS_RIGHT => []
    ];

    private static array $trash = [];

    public static function setRest(array $rests): void
    {
        self::$rest = $rests;
    }

    /**
     * @return list<Rest>
     */
    public function getRests(string $side): array
    {
        return self::$rest[$side];
    }

    public function addRest(float $length, float $tileMinLength, string $side, int $number): self
    {
        $rest = Rest::create($length, $number);

        if ($length >= $tileMinLength) {
            self::$rest[$side][] = $rest;
        } else {
            self::$trash[] = $rest->getLength();
        }

        return $this;
    }

    public function removeRest(float $length, string $side): self
    {
        foreach (self::$rest[$side] as $key => $rest) {
            if ($rest->getLength() === $length) {
                unset(self::$rest[$side][$key]);
            }
        }

        return $this;
    }

    public function hasRest(string $side): bool
    {
        return !empty(self::$rest[$side]);
    }

    public function addThrash(float $trash): self
    {
        self::$trash[] = $trash;

        return $this;
    }

    public function getTrash(): array
    {
        return self::$trash;
    }

    public function getSumOfAll(): float
    {
        return array_sum($this->getRests(LayerPlannerConstants::RESTS_LEFT)) + array_sum($this->getRests(LayerPlannerConstants::RESTS_RIGHT)) + array_sum($this->getTrash());
    }
}
