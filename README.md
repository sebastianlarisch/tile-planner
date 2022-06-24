<a href="https://github.com/sebastianlarisch/tile-planner/actions">
    <img src="https://github.com/sebastianlarisch/tile-planner/actions/workflows/build.yml/badge.svg" alt="GitHub Build Status">
</a>
<a href="https://scrutinizer-ci.com/g/sebastianlarisch/tile-planner/?branch=master">
    <img src="https://scrutinizer-ci.com/g/sebastianlarisch/tile-planner/badges/quality-score.png?b=master" alt="Scrutinizer Code Quality" />
</a>

# Tile Planner

## Description

The Tile Planner helps you to get the best laying pattern for your room. 
Based on the measurements of your room and your tiles it will calculate a plan, trying to waste as less as possible of your material.

This tool will give you a plan with rows, tiles, your waste in a JSON format.

If you like to see it in action: https://verlegeplaner.baublog-werder.de/

## Installation

1. Get Repository rom GitHub
```
composer require sebastianlarisch/tile-planner
```

2. Create a plan
```
$inputData = (new TilePlanInput())
      ->setRoomWidth(300)
      ->setRoomDepth(230)
      ->setTileLength(110)
      ->setTileWidth(20)
      ->setMinTileLength(30);

$plan = TilePlanner::createPlan($inputData);
```
