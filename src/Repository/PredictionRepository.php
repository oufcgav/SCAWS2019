<?php

namespace App\Repository;

use App\Entity\Match;
use App\Entity\Positions;
use App\Entity\Prediction;
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

    public function findByMatch(Match $match)
    {
        return $this->findBy(['match' => $match]);
    }

    public function findByMatchAndUser(Match $match, User $user)
    {
        return $this->findOneBy(['match' => $match, 'user' => $user->getUsername()]);
    }

    public function getLastTimePeriodPredicted(Match $currentMatch, string $human) {
        $sql = "SELECT time 
                    FROM prediction p
                    WHERE p.user = ?
                    AND p.match_id = (
                        SELECT id 
                        FROM `match` m
                        WHERE date < ?
                        ORDER BY date DESC
                        LIMIT 1
                    )";
        return $lastTimePredicted = $this->_em->getConnection()->fetchOne($sql,
            [
                $human,
                $currentMatch->getDate()->format('Y-m-d')
            ],
            [
                ParameterType::STRING,
                ParameterType::STRING
            ]
        );
    }

    public function getExcludedPositions(Match $currentMatch, $human) {
        $positionsExcluded = $this->getLastPredictions($currentMatch, $human, 3);
        $this->logger->info('Excluding positions:', ['excluded' => $positionsExcluded]);
        return count($positionsExcluded) < 3 ? $positionsExcluded : [];
    }

    public function getLastPredictions(Match $currentMatch, $human, $numPredictions) {
        $this->logger->info("Getting last  predictions", ['num' => $numPredictions, 'user' => $human, 'match' => $currentMatch]);
        $sql = "SELECT id 
                FROM `match` m
                WHERE date < ?
                ORDER BY date DESC
                LIMIT ?";

        $lastMatches = $this->_em->getConnection()->fetchAllAssociative($sql,
            [$currentMatch->getDate()->format('Y-m-d'), $numPredictions],
            [ParameterType::STRING, ParameterType::INTEGER]
        );
        $matchesToExclude = array_map(function ($lastMatch) {
            return $lastMatch['id'];
        }, $lastMatches);
        $this->logger->info('Predictions for matches', ['exclude' => $matchesToExclude]);
        $sql = "SELECT position, reset 
                FROM prediction p
                WHERE p.user = ?
                AND p.match_id IN (?)
                ORDER BY p.match_id DESC";

        $positionsPredicted = $this->_em->getConnection()->fetchAllAssociative($sql,
            [
                $human,
                $matchesToExclude
            ],
            [
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY
            ]
        );
        $positionsExcluded = [];
        foreach ($positionsPredicted as $positionPredicted) {
            if ($positionPredicted['position'] === Positions::GOALKEEPER()->getValue()
                || $positionPredicted['reset'] === '1') {
                break;
            }
            $positionsExcluded[] = $positionPredicted['position'];
        }
        return array_unique($positionsExcluded);
    }
}
