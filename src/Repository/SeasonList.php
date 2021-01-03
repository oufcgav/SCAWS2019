<?php

namespace App\Repository;

use App\Entity\Season;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SeasonList extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Season::class);
    }

    public function findCurrentSeason(\DateTimeImmutable $now = null): ?Season
    {
        $now = $now ?? new \DateTimeImmutable();

        return $this->createQueryBuilder('s')
            ->where('s.startDate <= :now')
            ->andWhere('s.endDate >= :now')
            ->setParameter('now', $now->format('Y-m-d'))
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function findPreviousSeason(Season $season): ?Season
    {
        return $this->createQueryBuilder('s')
            ->where('s.endDate < :startDate')
            ->setParameter('startDate', $season->getStartDate()->format('Y-m-d'))
            ->orderBy('s.endDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findNextSeason(Season $season): ?Season
    {
        return $this->createQueryBuilder('s')
            ->where('s.startDate > :endDate')
            ->setParameter('endDate', $season->getEndDate()->format('Y-m-d'))
            ->orderBy('s.startDate', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
