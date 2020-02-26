<?php

namespace App\Repository;

use App\Entity\Pint;
use App\Security\User;
use App\Service\ScoreCalculator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;


class PointsTable extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pint::class);
    }

    /**
     * @param User[] $users
     * @return User[]
     */
    public function loadCurrent(array $users): array
    {
        $usernames = array_map(function (User $user) {
            return $user->getUsername();
        }, $users);
        $users = array_combine($usernames, $users);
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
                   SUM(
                        IFNULL(
                            (SELECT SUM(s.points) FROM score AS s WHERE s.prediction_id=p.id),
                            0
                        )
                    ) AS points,
                   IFNULL(
                      (SELECT SUM(count)
                      FROM pint
                      WHERE pint.user = p.user),
                      0
                   ) AS pints_drunk
                FROM prediction AS p
                GROUP BY p.user
                ORDER BY points DESC, bonus_points DESC, pints_drunk DESC, played ASC, p.user ASC';
        $stats = $this->_em->getConnection()->fetchAll($sql, [ScoreCalculator::BONUS_POINT]);
        foreach ($stats as $userStats) {
            /** @var User $user */
            $user = $users[$userStats['user']];
            $user->setTableData(
                (int)$userStats['played'],
                (int)$userStats['pints_drunk'],
                (int)$userStats['bonus_points'],
                (int)$userStats['points']
            );
        }

        return $users;
    }
}
