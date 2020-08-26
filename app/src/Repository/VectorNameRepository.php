<?php

namespace App\Repository;

use App\Entity\VectorName;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VectorName|null find($id, $lockMode = null, $lockVersion = null)
 * @method VectorName|null findOneBy(array $criteria, array $orderBy = null)
 * @method VectorName[]    findAll()
 * @method VectorName[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VectorNameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VectorName::class);
    }

    // /**
    //  * @return VectorName[] Returns an array of VectorName objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VectorName
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
