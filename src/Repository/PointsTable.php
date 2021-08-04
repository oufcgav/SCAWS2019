<?php

namespace App\Repository;

use App\Entity\Match;
use App\Entity\Score;
use App\Entity\Season;
use App\Entity\TableEntry;
use App\Security\User;
use App\Service\ScoreCalculator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class PointsTable extends ServiceEntityRepository
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ManagerRegistry $registry,
        LoggerInterface $logger
    ) {
        parent::__construct($registry, Score::class);
        $this->logger = $logger;
    }

    /**
     * @param User[] $users
     *
     * @return User[]
     */
    public function loadCurrent(Season $season, array $users, ?Match $currentMatch = null): array
    {
        $usernames = array_map(function (User $user) {
            return $user->getUsername();
        }, $users);
        $users = array_combine($usernames, $users);
        $table = [];
        $sql = 'SELECT p.user,
                   COUNT(p.id) AS played,
                   FLOOR(
                      SUM(
                        IFNULL(
                            (SELECT SUM(s.points) FROM score AS s WHERE s.prediction_id=p.id AND s.reason = ?),
                            0
                        )
                      )
                   ) AS bonus_points,
                   SUM(p.points) AS points,
                   SUM(IFNULL(pint.count, 0)) AS pints_drunk
                FROM prediction AS p
                INNER JOIN `match` m ON p.match_id = m.id
                LEFT JOIN pint ON pint.user = p.user
                    AND p.match_id = pint.match_id
                WHERE m.season_id = ?
                GROUP BY p.user
                ORDER BY points DESC, bonus_points DESC, pints_drunk DESC, played ASC, p.user ASC';
        $stats = $this->_em->getConnection()->fetchAllAssociative(
            $sql,
            [ScoreCalculator::BONUS_POINT, $season->getId()],
            [ParameterType::INTEGER, ParameterType::INTEGER]
        );
        foreach ($stats as $position => $userStats) {
            /** @var User $user */
            $user = $users[$userStats['user']];
            if ($user) {
                $user->setTableData(
                    (int)$userStats['played'],
                    (int)$userStats['pints_drunk'],
                    (int)$userStats['bonus_points'],
                    (float)$userStats['points'],
                    $position
                );
                $table[] = $user;
            }
        }
        if ($currentMatch) {
            $sql = 'SELECT p.user,
                   COUNT(p.id) AS played,
                   FLOOR(
                      SUM(
                        IFNULL(
                            (SELECT SUM(s.points) FROM score AS s WHERE s.prediction_id=p.id AND s.reason = ?),
                            0
                        )
                      )
                   ) AS bonus_points,
                   SUM(p.points) AS points,
                   IFNULL(
                      (SELECT SUM(count)
                      FROM pint
                      WHERE pint.user = p.user),
                      0
                   ) AS pints_drunk
                FROM prediction AS p
                INNER JOIN `match` m ON p.match_id = m.id
                WHERE p.id < (
                    SELECT MIN(id)
                    FROM prediction AS old
                    WHERE old.match_id = ?
                )
                AND m.season_id = ?
                GROUP BY p.user
                ORDER BY points DESC, bonus_points DESC, pints_drunk DESC, played ASC, p.user ASC';
            $previousTable = $this->_em->getConnection()->fetchAllAssociative(
                $sql,
                [ScoreCalculator::BONUS_POINT, $currentMatch->getId(), $season->getId()],
                [ParameterType::INTEGER, ParameterType::INTEGER, ParameterType::INTEGER]
            );
            foreach ($previousTable as $position => $userStats) {
                /** @var User $user */
                $user = $users[$userStats['user']];
                $user->setPreviousPosition($position);
                $this->logger->debug('Set previous table position', ['user' => $user->getUsername(), 'previousPosition' => $position]);
            }
        }
        foreach ($users as $user) {
            $userInTable = array_filter($table, function (User $tableEntry) use ($user) {
                return $tableEntry->getUsername() === $user->getUsername();
            });
            if (empty($userInTable)) {
                $table[] = $user;
            }
        }

        return array_values($table);
    }

    public function getLastMatchSaved(): int
    {
        $sql = 'SELECT MAX(match_id) AS last_match FROM table_entry';
        $result = $this->_em->getConnection()->fetchOne($sql);

        return (int) $result['last_match'] ?? 0;
    }

    /**
     * @return TableEntry[]
     */
    public function loadSavedTable(Match $match): array
    {
        $query = $this->_em->createQuery('
            SELECT te FROM App\Entity\TableEntry te 
            WHERE te.match = :match
            ');
        $query->setParameter('match', $match);

        return $query->getResult();
    }
}
