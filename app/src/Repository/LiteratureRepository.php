<?php

namespace App\Repository;

use App\Entity\Literature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Literature|null find($id, $lockMode = null, $lockVersion = null)
 * @method Literature|null findOneBy(array $criteria, array $orderBy = null)
 * @method Literature[]    findAll()
 * @method Literature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LiteratureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Literature::class);
    }

    // /**
    //  * @return Literature[] Returns an array of Literature objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Literature
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
