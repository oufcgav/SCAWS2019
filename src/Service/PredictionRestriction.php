<?php

namespace App\Service;

use App\Entity\GoalTimes;
use App\Entity\Positions;
use App\Repository\FixtureList;
use App\Repository\PredictionRepository;
use App\Security\User;
use Psr\Log\LoggerInterface;

class PredictionRestriction
{
    /**
     * @var FixtureList
     */
    private $fixtureList;
    /**
     * @var PredictionRepository
     */
    private $predictionRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        FixtureList $fixtureList,
        PredictionRepository $predictionRepository,
        LoggerInterface $logger
    ) {
        $this->fixtureList = $fixtureList;
        $this->predictionRepository = $predictionRepository;
        $this->logger = $logger;
    }

    public function getPositions(User $user): array
    {
        $nextMatch = $this->fixtureList->findNextMatch();
        $positionsExcluded = $this->predictionRepository->getExcludedPositions(
            $nextMatch,
            $user->getUsername()
        );
        $this->logger->info('Positions excluded: '.implode(', ', $positionsExcluded));

        $positions = array_combine(array_values(Positions::toArray()), array_values(Positions::toArray()));

        return array_diff($positions, $positionsExcluded);
    }

    public function getTimings(User $user): array
    {
        $nextMatch = $this->fixtureList->findNextMatch();
        $lastTimePredicted = $this->predictionRepository->getLastTimePeriodPredicted(
            $nextMatch,
            $user->getUsername()
        );
        $this->logger->info('Last time predicted: '.$lastTimePredicted);
        $times = array_combine(array_values(GoalTimes::toArray()), array_values(GoalTimes::toArray()));

        return array_diff($times, [$lastTimePredicted]);
    }
}
