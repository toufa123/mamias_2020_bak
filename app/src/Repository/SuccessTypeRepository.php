<?php

namespace App\Repository;

use App\Entity\SuccessType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SuccessType|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuccessType|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuccessType[]    findAll()
 * @method SuccessType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuccessTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuccessType::class);
    }

    // /**
    //  * @return SuccessType[] Returns an array of SuccessType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SuccessType
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
