<?php

namespace App\Repository;

use App\Entity\EcAp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EcAp|null find($id, $lockMode = null, $lockVersion = null)
 * @method EcAp|null findOneBy(array $criteria, array $orderBy = null)
 * @method EcAp[]    findAll()
 * @method EcAp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EcApRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EcAp::class);
    }

    // /**
    //  * @return EcAp[] Returns an array of EcAp objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EcAp
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
