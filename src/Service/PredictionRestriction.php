<?php

namespace App\Service;

use App\Repository\FixtureList;
use App\Repository\PredictionRepository;
use App\Security\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
        $this->logger->info('Positions excluded: ' . implode(', ', $positionsExcluded));

        $positions = [
            'Goalkeeper' => 'Goalkeeper',
            'Defenders' => 'Defenders',
            'Midfielders' => 'Midfielders',
            'Strikers' => 'Strikers',
        ];

        return array_diff($positions, $positionsExcluded);
    }

    public function getTimings(User $user): array
    {
        $nextMatch = $this->fixtureList->findNextMatch();
        $lastTimePredicted = $this->predictionRepository->getLastTimePeriodPredicted(
            $nextMatch,
            $user->getUsername()
        );
        $this->logger->info('Last time predicted: ' . $lastTimePredicted);
        $times = [
            'First half' => 'First half',
            'Second half' => 'Second half',
            '1-15 mins' => '1-15 mins',
            '16-30 mins' => '16-30 mins',
            '31-45 mins' => '31-45 mins',
            '46-60 mins' => '46-60 mins',
            '61-75 mins' => '61-75 mins',
            '76-90 mins' => '76-90 mins',
            'Stoppage time' => 'Stoppage time',
            'Extra time/other' => 'Extra time/other',
        ];
        return array_diff($times, [$lastTimePredicted]);
    }
}
