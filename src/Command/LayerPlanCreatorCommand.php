<?php

declare(strict_types=1);

namespace App\Command;

use App\LayerPlanner\LayerPlannerFacadeInterface;
use App\LayerPlanner\Models\LayerPlanInput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class LayerPlanCreatorCommand extends Command
{
    protected static $defaultName = 'app:create-layer-plan';
    private LayerPlannerFacadeInterface $layerPlannerFacade;

    public function __construct(LayerPlannerFacadeInterface $layerPlannerFacade)
    {
        $this->layerPlannerFacade = $layerPlannerFacade;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Create LayerPlan');
        $this->addOption('room-width', 'w', InputOption::VALUE_REQUIRED, 'Room Width');
        $this->addOption('room-depth', 'd', InputOption::VALUE_REQUIRED, 'Room Depth');
        $this->addOption('tile-width', 'i', InputOption::VALUE_REQUIRED, 'Tile Width');
        $this->addOption('tile-length', 'l', InputOption::VALUE_REQUIRED, 'Tile Length');
        $this->addOption('min-tile-length', 'm', InputOption::VALUE_REQUIRED, 'Min Tile Length');
        $this->addOption('gap-width', 'g', InputOption::VALUE_OPTIONAL, 'Gap Width');
        $this->addOption('laying-type', 't', InputOption::VALUE_REQUIRED, 'Laying Type');
        $this->addOption('costs-per-square', 'c', InputOption::VALUE_OPTIONAL, 'Costs per Square');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $layerPlanInput = [];

        $layerPlanInput['room_width'] = $input->getOption('room-width');
        $layerPlanInput['room_depth'] = $input->getOption('room-depth');
        $layerPlanInput['tile_width'] = $input->getOption('tile-width');
        $layerPlanInput['tile_length'] = $input->getOption('tile-length');
        $layerPlanInput['min_tile_length'] = $input->getOption('min-tile-length');
        $layerPlanInput['gap_width'] = $input->getOption('gap-width');
        $layerPlanInput['laying_type'] = $input->getOption('laying-type');
        $layerPlanInput['costs_per_square'] = $input->getOption('costs-per-square');

        $layerInput = LayerPlanInput::fromFormData($layerPlanInput);

        $layerPlan = $this->layerPlannerFacade->createPlan($layerInput);

        print json_encode($layerPlan);

        return self::SUCCESS;
    }
}