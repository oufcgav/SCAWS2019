<?php

namespace App\Repository;

use App\Entity\Match;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FixtureList extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Match::class);
    }

    public function findNextMatch(\DateTimeImmutable $now = null): ?Match
    {
        $now = $now ?? new \DateTimeImmutable();

        return $this->createQueryBuilder('m')
            ->where('m.date >= :now')
            ->orderBy('m.date', 'ASC')
            ->orderBy('m.id', 'DESC')
            ->setParameter('now', $now->format('Y-m-d'))
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }
}
