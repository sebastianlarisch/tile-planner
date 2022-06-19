<?php

declare(strict_types=1);

namespace App\TilePlanner\Creator;

use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\LengthRange;
use App\TilePlanner\Models\LengthRangeBag;

final class TileLengthRangeCreator implements TileLengthRangeCreatorInterface
{
    public static ?LengthRangeBag $rangeBag = null;

    public function calculateRanges(TilePlanInput $tileInput): LengthRangeBag
    {
        if (isset(self::$rangeBag)) {
            return self::$rangeBag;
        }

        $rangeBag = new LengthRangeBag();

        $minTileWidth = $tileInput->getMinTileLength();
        $roomWidth =  $tileInput->getRoomWidth();
        $tileLength = $tileInput->getTileLength();

        $tileLengthWhenLastTileHasMinLength = fmod(($roomWidth - $minTileWidth), $tileLength);
        $fallbackMinLength = 0;

        if ($tileLengthWhenLastTileHasMinLength < $minTileWidth) {
            $fallbackMinLength = $tileLengthWhenLastTileHasMinLength + $minTileWidth;
            $rangeBag->addRange(
                LengthRange::withMinAndMax(
                    $fallbackMinLength,
                    $tileLength
                )
            );
        }

        if ($minTileWidth < $tileLengthWhenLastTileHasMinLength) {
            $rangeBag->addRange(
                LengthRange::withMinAndMax(
                    $minTileWidth,
                    $tileLengthWhenLastTileHasMinLength
                )
            );
        }

        $nextMin = ($tileLengthWhenLastTileHasMinLength + $minTileWidth) >= $tileLength
            ? $tileLength
            : $tileLengthWhenLastTileHasMinLength + $minTileWidth;

        if (
            $nextMin !== $fallbackMinLength
            && ($roomWidth % $tileLength > $minTileWidth
            || $roomWidth % $tileLength === 0)
        ) {
            $rangeBag->addRange(LengthRange::withMinAndMax($nextMin, $tileLength));
        }

        self::$rangeBag = $rangeBag;

        return $rangeBag;
    }
}
