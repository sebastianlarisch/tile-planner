<?php

declare(strict_types=1);

namespace App\Command;

use App\Form\LayerPlannerType;
use App\LayerPlanner\LayerPlannerFacadeInterface;
use App\LayerPlanner\Models\LayerPlanInput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Matthias\SymfonyConsoleForm\Console\Helper\FormHelper;

final class LayerPlanCreatorCommand extends Command
{
    protected static $defaultName = 'app:create-layer-plan';
    private LayerPlannerFacadeInterface $layerPlannerFacade;

    public function __construct(LayerPlannerFacadeInterface $layerPlannerFacade)
    {
        $this->layerPlannerFacade = $layerPlannerFacade;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $formHelper = $this->getHelper('form');
        $formData = $formHelper->interactUsingForm(LayerPlannerType::class, $input, $output);
        $layerInput = LayerPlanInput::fromFormData($formData);

        $layerPlan = $this->layerPlannerFacade->createPlan($layerInput);

        print json_encode($layerPlan);

        return self::SUCCESS;
    }
}