<?php

namespace App\Repository;

use App\Entity\Pathway;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pathway|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pathway|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pathway[]    findAll()
 * @method Pathway[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PathwayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pathway::class);
    }

    // /**
    //  * @return Pathway[] Returns an array of Pathway objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pathway
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
