<?php

namespace App\Controller;

use App\Entity\Prediction;
use App\Entity\Score;
use App\Entity\Season;
use App\Repository\PredictionRepository;
use App\Repository\SeasonList;
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
    /**
     * @var SeasonList
     */
    private $seasonList;

    public function __construct(
        UserProvider $userProvider,
        PredictionRepository $predictionRepository,
        ScoreCalculator $scoreCalculator,
        SeasonList $seasonList
    ) {
        $this->userProvider = $userProvider;
        $this->predictionRepository = $predictionRepository;
        $this->scoreCalculator = $scoreCalculator;
        $this->seasonList = $seasonList;
    }

    /**
     * @Route("/user/{username}", name="user")
     * @Route("/{season}/user/{username}", name="user_old")
     */
    public function index(string $username, ?Season $season = null)
    {
        $season = $season ?? $this->seasonList->findCurrentSeason();
        $user = $this->userProvider->loadUserByUsername($username);
        $predictionData = array_map(function (Prediction $prediction) {
            $goalData = array_reduce($prediction->getScores(), function ($goalData, Score $score) {
                $goal = $score->getGoal();
                if (!$goal) {
                    return [];
                }
                if (!isset($goalData[$goal->getId()])) {
                    $goalData[$goal->getId()] = ['goal' => $goal, 'reasons' => [], 'points' => 0];
                }
                $goalData[$goal->getId()]['points'] += $score->getPoints();
                $goalData[$goal->getId()]['reasons'][] = $this->scoreCalculator->getReasonName($score->getReason());

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
        }, $this->predictionRepository->findByUserAndSeason($user, $season));

        return $this->render('user.html.twig', ['user' => $user, 'predictions' => $predictionData, 'season' => $season]);
    }
}
