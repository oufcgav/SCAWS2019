<?php

namespace App\Repository;

use App\Entity\Match;
use App\Entity\Prediction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Prediction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prediction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prediction[]    findAll()
 * @method Prediction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PredictionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prediction::class);
    }

    public function findByMatch(Match $match)
    {
        return $this->findBy(['match_id' => $match->getId()]);
    }
}
