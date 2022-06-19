<?php

declare(strict_types=1);

namespace App\Command;

use App\Form\TilePlannerType;
use App\TilePlanner\TilePlannerFacadeInterface;
use App\TilePlanner\Models\TilePlanInput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class TilePlanCreatorCommand extends Command
{
    protected static $defaultName = 'app:create-layer-plan';
    private TilePlannerFacadeInterface $tilePlannerFacade;

    public function __construct(TilePlannerFacadeInterface $tilePlannerFacade)
    {
        $this->tilePlannerFacade = $tilePlannerFacade;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $formHelper = $this->getHelper('form');
        $formData = $formHelper->interactUsingForm(TilePlannerType::class, $input, $output);
        $tileInput = TilePlanInput::fromFormData($formData);

        $tilePlan = $this->tilePlannerFacade->createPlan($tileInput);

        print json_encode($tilePlan);

        return self::SUCCESS;
    }
}