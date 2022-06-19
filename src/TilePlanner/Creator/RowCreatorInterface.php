<?php

namespace App\TilePlanner\Creator;

use App\TilePlanner\Models\TilePlan;
use App\TilePlanner\Models\TilePlanInput;
use App\TilePlanner\Models\Rests;
use App\TilePlanner\Models\Row;

interface RowCreatorInterface
{
    public function createRow(
        TilePlanInput $tileInput,
        TilePlan      $plan,
        Rests         $rest
    ): Row;
}
