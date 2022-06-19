<?php

declare(strict_types=1);

namespace App\TilePlanner;

use App\Form\TilePlannerType;
use App\TilePlanner\Creator\FirstTileCreator\ChessTileCreator;
use App\TilePlanner\Creator\FirstTileCreator\FirstTileCreatorInterface;
use App\TilePlanner\Creator\FirstTileCreator\FullTileCreator;
use App\TilePlanner\Creator\FirstTileCreator\MaximumTileCreator;
use App\TilePlanner\Creator\FirstTileCreator\MinimumTileCreator;
use App\TilePlanner\Creator\FirstTileCreator\TileFromRestCreator;
use App\TilePlanner\Creator\FirstTileLengthCreator;
use App\TilePlanner\Creator\LastTileCreator\LastTileCreatorInterface;
use App\TilePlanner\Creator\LastTileCreator\LastTileFittingCreator;
use App\TilePlanner\Creator\LastTileCreator\LastTileFromRestCreator;
use App\TilePlanner\Creator\LastTileCreator\LastTileFromRestForChessTypeCreator;
use App\TilePlanner\Creator\LastTileLengthCreator;
use App\TilePlanner\Creator\TileLengthRangeCreator;
use App\TilePlanner\Creator\RowCreator;
use App\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Validator\DeviationValidator;
use App\TilePlanner\Validator\RangeValidator;
use App\TilePlanner\Validator\RangeValidatorInterface;

final class TilePlannerFactory
{
    public function createtilePlannerByType(string $layingType): TilePlanner
    {
        return new TilePlanner(
            $this->createRowCreator($layingType),
            $this->createRest()
        );
    }

    public function createRowCreator(string $layingType): RowCreator
    {
        $firstTileCalculator = $this->createFirstTileLengthCalculator();
        $lastTileCalculator = $this->createLastTileLengthCalculator();

        if ($layingType === TilePlannerType::TYPE_CHESS) {
            $firstTileCalculator = $this->createFirstTileLengthCalculatorForChessType();
            $lastTileCalculator = $this->createLastTileLengthCalculatorForChessType();
        }

        return new RowCreator($firstTileCalculator, $lastTileCalculator);
    }

    private function createRest(): Rests
    {
        return new Rests();
    }

    public function createTileLengthRangeCalculator(): TileLengthRangeCreatorInterface
    {
        return new TileLengthRangeCreator();
    }

    private function createFirstTileLengthCalculator(): FirstTileLengthCreator
    {
        return new FirstTileLengthCreator(
            [
                $this->createTileFromRestCalculator(),
                $this->createFullTileCalculator(),
                $this->createMaximumTileCreator(),
                $this->createMinimumTileCalculator(),
            ],
        );
    }

    private function createFirstTileLengthCalculatorForChessType(): FirstTileLengthCreator
    {
        return new FirstTileLengthCreator(
            [
                $this->createChessTileCalculator()
            ]
        );
    }

    private function createTileFromRestCalculator(): FirstTileCreatorInterface
    {
        return new TileFromRestCreator(
            $this->createRangeValidator(),
            $this->createDeviationValidator(),
            $this->createTileLengthRangeCalculator()
        );
    }

    private function createFullTileCalculator(): FirstTileCreatorInterface
    {
        return new FullTileCreator(
            $this->createRangeValidator(),
            $this->createDeviationValidator(),
            $this->createTileLengthRangeCalculator(),
        );
    }

    private function createMinimumTileCalculator(): FirstTileCreatorInterface
    {
        return new MinimumTileCreator(
            $this->createTileLengthRangeCalculator(),
            $this->createDeviationValidator()
        );
    }

    private function createMaximumTileCreator(): FirstTileCreatorInterface
    {
        return new MaximumTileCreator(
            $this->createDeviationValidator(),
            $this->createTileLengthRangeCalculator()
        );
    }

    private function createRangeValidator(): RangeValidatorInterface
    {
        return new RangeValidator();
    }

    private function createDeviationValidator(): DeviationValidator
    {
        return new DeviationValidator();
    }

    private function createChessTileCalculator(): FirstTileCreatorInterface
    {
        return new ChessTileCreator(
            $this->createRangeValidator(),
            $this->createTileLengthRangeCalculator()
        );
    }

    private function createLastTileLengthCalculator(): LastTileLengthCreator
    {
        return new LastTileLengthCreator(
            [
                $this->createLastTileFromRestCreator(),
                $this->createLastTileFittingCreator()
            ]
        );
    }

    private function createLastTileFromRestCreator(): LastTileCreatorInterface
    {
        return new LastTileFromRestCreator();
    }

    private function createLastTileFittingCreator(): LastTileCreatorInterface
    {
        return new LastTileFittingCreator();
    }

    private function createLastTileLengthCalculatorForChessType(): LastTileLengthCreator
    {
        return new LastTileLengthCreator(
            [
                $this->createLastTileFromRestForChessTypeCreator(),
                $this->createLastTileFittingCreator()
            ]
        );
    }

    private function createLastTileFromRestForChessTypeCreator(): LastTileCreatorInterface
    {
        return new LastTileFromRestForChessTypeCreator();
    }
}
