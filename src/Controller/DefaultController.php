<?php

namespace App\Controller;

use App\Repository\FixtureList;
use App\Repository\GoalRepository;
use App\Repository\PredictionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @var FixtureList
     */
    private $fixtureList;
    /**
     * @var PredictionRepository
     */
    private $predictions;
    /**
     * @var GoalRepository
     */
    private $goalRepository;

    public function __construct(
        FixtureList $fixtureList,
        PredictionRepository $predictions,
        GoalRepository $goalRepository
    ) {
        $this->fixtureList = $fixtureList;
        $this->predictions = $predictions;
        $this->goalRepository = $goalRepository;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        $nextMatch = $this->fixtureList->findNextMatch();
        $predictions = $goals = [];
        if ($nextMatch) {
            $predictions = $this->predictions->findByMatch($nextMatch);
            $goals = $this->goalRepository->findByMatch($nextMatch);
        }
        return $this->render('index.html.twig', [
            'match' => $nextMatch,
            'predictions' => $predictions,
            'goals' => $goals,
        ]);
    }
}