<?php

namespace App\Repository;

use App\Entity\MatchDay;
use App\Entity\Prediction;
use App\Entity\Season;
use App\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @method Prediction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prediction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prediction[]    findAll()
 * @method Prediction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PredictionRepository extends ServiceEntityRepository
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, Prediction::class);
        $this->logger = $logger;
    }

    public function findByMatch(MatchDay $match)
    {
        return $this->findBy(['match' => $match]);
    }

    public function findByMatchAndUser(MatchDay $match, User $user)
    {
        return $this->findOneBy(['match' => $match, 'user' => $user->getUsername()]);
    }

    public function findByUser(User $user)
    {
        return $this->findBy(['user' => $user->getUsername()]);
    }

    public function findByUserAndSeason(User $user, Season $season)
    {
        return $this->createQueryBuilder('p')
            ->join('p.match', 'm')
            ->where('p.user = :user')
            ->andWhere('m.season = :season')
            ->setParameter('user', $user->getUsername())
            ->setParameter('season', $season)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getLastTimePeriodPredicted(MatchDay $currentMatch, string $human)
    {
        $sql = 'SELECT time 
                    FROM prediction p
                    WHERE p.user = ?
                    AND p.match_id = (
                        SELECT id 
                        FROM `match` m
                        WHERE date < ?
                        ORDER BY date DESC
                        LIMIT 1
                    )';

        return $this->_em->getConnection()->fetchOne($sql,
            [
                $human,
                $currentMatch->getDate()->format('Y-m-d'),
            ],
            [
                ParameterType::STRING,
                ParameterType::STRING,
            ]
        );
    }

    public function getExcludedPositions(MatchDay $currentMatch, $human)
    {
        $positionsExcluded = $this->getLastPredictions($currentMatch, $human, 3);
        $this->logger->info('Excluding positions:', ['excluded' => $positionsExcluded]);

        return count($positionsExcluded) < 3 ? $positionsExcluded : [];
    }

    public function getLastPredictions(MatchDay $currentMatch, $human, $numPredictions)
    {
        $this->logger->info('Getting last  predictions', ['num' => $numPredictions, 'user' => $human, 'match' => $currentMatch]);
        if ($currentMatch->resetPositionChoices()) {
            return [];
        }
        $sql = 'SELECT id 
                FROM `match` m
                WHERE date >= (
                    SELECT MAX(date) 
                    FROM `match`
                    WHERE reset = 1 
                )';

        $lastMatches = $this->_em->getConnection()->fetchAllAssociative($sql);
        $matchesSinceReset = array_map(function ($lastMatch) {
            return $lastMatch['id'];
        }, $lastMatches);
        $this->logger->info('Predictions for matches', ['matchesSinceReset' => $matchesSinceReset]);
        $sql = 'SELECT position 
                FROM prediction p
                WHERE p.user = ?
                AND p.match_id IN (?)
                ORDER BY p.match_id DESC';

        $positionsPredicted = $this->_em->getConnection()->fetchAllAssociative($sql,
            [
                $human,
                $matchesSinceReset,
            ],
            [
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
            ]
        );
        $positionsExcluded = [];
        foreach ($positionsPredicted as $positionPredicted) {
            $positionsExcluded[] = $positionPredicted['position'];
        }

        return array_unique($positionsExcluded);
    }
}
