<?php

declare(strict_types=1);

namespace Tests\Unit\TilePlanner\Models;

use App\TilePlanner\Models\Tile;
use PHPUnit\Framework\TestCase;

final class TileTest extends TestCase
{
    public function test_room_parameters(): void
    {
        $room = Tile::create(10, 20);

        self::assertEquals(10, $room->getWidth());
        self::assertEquals(20, $room->getLength());
    }
}