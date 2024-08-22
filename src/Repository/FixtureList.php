<?php

namespace App\Repository;

use App\Entity\MatchDay;
use App\Entity\Season;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

class FixtureList extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatchDay::class);
    }

    public function findNextMatch(\DateTimeImmutable $now = null): ?MatchDay
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

    public function findPreviousMatches(Season $season, MatchDay $match): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.date < :current')
            ->andWhere('m.season = :season')
            ->orderBy('m.date', 'DESC')
            ->setParameter('current', $match->getDate()->format('Y-m-d'))
            ->setParameter('season', $season)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findLastMatch(Season $season): ?MatchDay
    {
        try {
            return $this->createQueryBuilder('m')
                ->where('m.season = :season')
                ->orderBy('m.date', 'DESC')
                ->setParameter('season', $season)
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch (Exception $e) {
            return null;
        }
    }
}
