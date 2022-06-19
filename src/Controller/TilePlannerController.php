<?php

namespace App\Controller;

use App\Form\TilePlannerType;
use App\TilePlanner\TilePlannerFacadeInterface;
use App\TilePlanner\TilePlannerFactory;
use App\TilePlanner\Models\TilePlanInput;
use Assert\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TilePlannerController extends AbstractController
{
    private TilePlannerFacadeInterface $tilePlannerFacade;

    public function __construct(TilePlannerFacadeInterface $tilePlannerFacade)
    {
        $this->tilePlannerFacade = $tilePlannerFacade;
    }

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(TilePlannerType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $tileInput = TilePlanInput::fromFormData($form->getData());
            } catch (InvalidArgumentException $exception) {
                return $this->render(
                    'index.twig',
                    [
                        'tilePlannerInputForm' => $form->createView(),
                        'error' => 'Invalid input: ' . $exception->getMessage()
                    ]
                );
            }

            $plan = $this->tilePlannerFacade
                ->createPlan($tileInput);
        }

        return $this->render(
            'index.twig',
            [
                'tilePlannerInputForm' => $form->createView(),
                'tilePlannerInputData' => $tileInput ?? null,
                'plan' => $plan ?? null,
            ]
        );
    }
}
