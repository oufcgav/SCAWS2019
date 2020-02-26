<?php

namespace App\Controller;

use App\Entity\Goal;
use App\Form\Type\GoalType;
use App\Repository\FixtureList;
use App\Repository\PredictionRepository;
use App\Service\ScoreCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoalController extends AbstractController
{
    /**
     * @var FixtureList
     */
    private $fixtureList;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var ScoreCalculator
     */
    private $scoreCalculator;
    /**
     * @var PredictionRepository
     */
    private $predictionRepository;

    public function __construct(
        FixtureList $fixtureList,
        ScoreCalculator $scoreCalculator,
        PredictionRepository $predictionRepository,
        EntityManagerInterface $em
    ) {
        $this->fixtureList = $fixtureList;
        $this->em = $em;
        $this->scoreCalculator = $scoreCalculator;
        $this->predictionRepository = $predictionRepository;
    }

    /**
     * @Route("/goal", name="add_goal")
     */
    public function index(Request $request)
    {
        $currentMatch = $this->fixtureList->findNextMatch();
        if (!$currentMatch) {
            $this->addFlash('error', 'You cannot add a goal as there is no current match.');
            return $this->redirectToRoute('homepage');
        }

        $goal = (new Goal())
            ->setMatch($currentMatch)
        ;
        $form = $this->createForm(GoalType::class, $goal);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $currentPredictions = $this->predictionRepository->findByMatch($currentMatch);
            $this->em->persist($goal);
            $scores = $this->scoreCalculator->calculate($goal, $currentPredictions);
            foreach ($scores as $score) {
                $this->em->persist($score);
            }
            $this->em->flush();
            return $this->redirectToRoute('homepage');
        }

        return $this->render('goal.html.twig', [
            'form' => $form->createView(),
            'match' => $currentMatch,
        ]);
    }
}