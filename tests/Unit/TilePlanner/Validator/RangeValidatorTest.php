<?php

declare(strict_types=1);

namespace Tests\Unit\TilePlanner\Validator;

use App\TilePlanner\Models\LengthRange;
use App\TilePlanner\Validator\RangeValidator;
use PHPUnit\Framework\TestCase;

final class RangeValidatorTest extends TestCase
{
    public function test_range_not_valid_without_length_ranges(): void
    {
        $validator = new RangeValidator();

        $actual = $validator->isInRange(10, []);

        self::assertFalse($actual);
    }

    public function test_range_returns_true_if_in_range(): void
    {
        $validator = new RangeValidator();

        $lengthRange = LengthRange::withMinAndMax(10, 20);

        $actual = $validator->isInRange(10, [$lengthRange]);

        self::assertTrue($actual);
    }

    public function test_range_returns_false_if_in_range(): void
    {
        $validator = new RangeValidator();

        $lengthRange = LengthRange::withMinAndMax(15, 20);

        $actual = $validator->isInRange(10, [$lengthRange]);

        self::assertFalse($actual);
    }
}