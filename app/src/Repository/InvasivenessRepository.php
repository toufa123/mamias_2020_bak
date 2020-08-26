<?php

namespace App\Repository;

use App\Entity\Invasiveness;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Invasiveness|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invasiveness|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invasiveness[]    findAll()
 * @method Invasiveness[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvasivenessRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invasiveness::class);
    }

    // /**
    //  * @return Invasiveness[] Returns an array of Invasiveness objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Invasiveness
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
