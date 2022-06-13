<?php

declare(strict_types=1);

namespace App\LayerPlanner;

use App\Form\LayerPlannerType;
use App\LayerPlanner\Creator\FirstTileCreator\ChessTileCreator;
use App\LayerPlanner\Creator\FirstTileCreator\FirstTileCreatorInterface;
use App\LayerPlanner\Creator\FirstTileCreator\FullTileCreator;
use App\LayerPlanner\Creator\FirstTileCreator\MaximumTileCreator;
use App\LayerPlanner\Creator\FirstTileCreator\MinimumTileCreator;
use App\LayerPlanner\Creator\FirstTileCreator\TileFromRestCreator;
use App\LayerPlanner\Creator\FirstTileLengthCreator;
use App\LayerPlanner\Creator\LastTileCreator\LastTileCreatorInterface;
use App\LayerPlanner\Creator\LastTileCreator\LastTileFittingCreator;
use App\LayerPlanner\Creator\LastTileCreator\LastTileFromRestCreator;
use App\LayerPlanner\Creator\LastTileCreator\LastTileFromRestForChessTypeCreator;
use App\LayerPlanner\Creator\LastTileLengthCreator;
use App\LayerPlanner\Creator\TileLengthRangeCreator;
use App\LayerPlanner\Creator\RowCreator;
use App\LayerPlanner\Creator\TileLengthRangeCreatorInterface;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Validator\DeviationValidator;
use App\LayerPlanner\Validator\RangeValidator;
use App\LayerPlanner\Validator\RangeValidatorInterface;

final class LayerPlannerFactory
{
    public function createLayerPlannerByType(string $layingType): LayerPlanner
    {
        return new LayerPlanner(
            $this->createRowCreator($layingType),
            $this->createRest()
        );
    }

    public function createRowCreator(string $layingType): RowCreator
    {
        $firstTileCalculator = $this->createFirstTileLengthCalculator();
        $lastTileCalculator = $this->createLastTileLengthCalculator();

        if ($layingType === LayerPlannerType::TYPE_CHESS) {
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
