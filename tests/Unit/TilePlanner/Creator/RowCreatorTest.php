<?php

declare(strict_types=1);

namespace Tests\Unit\TilePlanner\Creator;

use App\Form\TilePlannerType;
use App\TilePlanner\Creator\FirstTileLengthCreatorInterface;
use App\TilePlanner\Creator\LastTileLengthCreatorInterface;
use App\TilePlanner\Creator\RowCreator;
use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Models\Tile;
use PHPUnit\Framework\TestCase;

final class RowCreatorTest extends TestCase
{
    public function test(): void
    {
        $firstTileLengthCalculator = $this->createMock(FirstTileLengthCreatorInterface::class);
        $firstTileLengthCalculator
            ->method('create')
            ->willReturn(Tile::create(15, 25));

        $lastTileLengthCalculator = $this->createMock(LastTileLengthCreatorInterface::class);
        $lastTileLengthCalculator
            ->method('create')
            ->willReturn(Tile::create(15, 25));

        $creator = new RowCreator($firstTileLengthCalculator, $lastTileLengthCalculator);

        $tileInput = TilePlanInput::fromFormData(
            [
            'room_width' => '200',
            'room_depth' => '100',
            'tile_width' => '20',
            'tile_length' => '50',
            'min_tile_length' => '20',
            'gap_width' => '0',
            'laying_type' => TilePlannerType::TYPE_OFFSET,
            'costs_per_square' => '0',
            ]
        );
        $plan = new TilePlan();
        $rest = new Rests();

        $actual = $creator->createRow($tileInput, $plan, $rest);

        self::assertCount(5, $actual->getTiles());
    }
}