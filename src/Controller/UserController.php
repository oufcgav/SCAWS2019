<?php

namespace App\Controller;

use App\Entity\Prediction;
use App\Entity\Score;
use App\Repository\PredictionRepository;
use App\Security\UserProvider;
use App\Service\ScoreCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @var UserProvider
     */
    private $userProvider;
    /**
     * @var PredictionRepository
     */
    private $predictionRepository;
    /**
     * @var ScoreCalculator
     */
    private $scoreCalculator;

    public function __construct(
        UserProvider $userProvider,
        PredictionRepository $predictionRepository,
        ScoreCalculator $scoreCalculator
    ) {
        $this->userProvider = $userProvider;
        $this->predictionRepository = $predictionRepository;
        $this->scoreCalculator = $scoreCalculator;
    }

    /**
     * @Route("/user/{username}", name="user")
     */
    public function index(string $username)
    {
        $user = $this->userProvider->loadUserByUsername($username);
        $predictionData = array_map(function (Prediction $prediction) {
            $goalData = array_reduce($prediction->getScores(), function ($goalData, Score $score) {
                if (!isset($goalData[$score->getGoal()->getId()])) {
                    $goalData[$score->getGoal()->getId()] = ['goal' => $score->getGoal(), 'reasons' => [], 'points' => 0];
                }
                $goalData[$score->getGoal()->getId()]['points'] += $score->getPoints();
                $goalData[$score->getGoal()->getId()]['reasons'][] = $this->scoreCalculator->getReasonName($score->getReason());

                return $goalData;
            }, []);
            $goalData = array_map(function ($goalData) {
                $goalData['points'] = sprintf('%dpt%s', $goalData['points'], ($goalData['points'] != 1 ? 's' : ''));
                $goalData['reasons'] = implode(', ', $goalData['reasons']);

                return $goalData;
            }, $goalData);

            return [
                'prediction' => $prediction,
                'match' => $prediction->getMatch(),
                'goals' => $goalData,
            ];
        }, $this->predictionRepository->findByUser($user));

        return $this->render('user.html.twig', ['user' => $user, 'predictions' => $predictionData]);
    }
}
