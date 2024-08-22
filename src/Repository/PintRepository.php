<?php

namespace App\Repository;

use App\Entity\MatchDay;
use App\Entity\Pint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pint|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pint|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pint[]    findAll()
 * @method Pint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PintRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pint::class);
    }

    public function findByUserAndMatch($username, MatchDay $match)
    {
        return $this->findOneBy(['match' => $match, 'user' => $username]);
    }
}
