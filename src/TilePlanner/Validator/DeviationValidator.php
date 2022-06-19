<?php

declare(strict_types=1);

namespace App\TilePlanner\Validator;

final class DeviationValidator implements DeviationValidatorInterface
{
    public function isValidDeviation(
        float $currentLength,
        ?float $lastLength,
        float $tileMinLength,
        float $allowedDifference
    ): bool {
        if ($lastLength === null
            && $currentLength >= $tileMinLength) {
            return true;
        }

        if (
            $currentLength >= $tileMinLength
            && ($currentLength <= $lastLength - $allowedDifference
            || $currentLength >= $lastLength + $allowedDifference)
        ) {
            return true;
        }

        return false;
    }
}
