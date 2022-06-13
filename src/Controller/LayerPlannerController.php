<?php

namespace App\Controller;

use App\Form\LayerPlannerType;
use App\LayerPlanner\LayerPlannerFacadeInterface;
use App\LayerPlanner\LayerPlannerFactory;
use App\LayerPlanner\Models\LayerPlanInput;
use Assert\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LayerPlannerController extends AbstractController
{
    private LayerPlannerFacadeInterface $layerPlannerFacade;

    public function __construct(LayerPlannerFacadeInterface $layerPlannerFacade)
    {
        $this->layerPlannerFacade = $layerPlannerFacade;
    }

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(LayerPlannerType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $layerInput = LayerPlanInput::fromFormData($form->getData());
            } catch (InvalidArgumentException $exception) {
                return $this->render(
                    'index.twig',
                    [
                        'layerPlannerInputForm' => $form->createView(),
                        'error' => 'Invalid input: ' . $exception->getMessage()
                    ]
                );
            }

            $plan = $this->layerPlannerFacade
                ->createPlan($layerInput);
        }

        return $this->render(
            'index.twig',
            [
                'layerPlannerInputForm' => $form->createView(),
                'layerPlannerInputData' => $layerInput ?? null,
                'plan' => $plan ?? null,
            ]
        );
    }
}
