<?php

namespace App\Repository;

use App\Entity\Ecofunctional;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ecofunctional|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ecofunctional|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ecofunctional[]    findAll()
 * @method Ecofunctional[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EcofunctionalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ecofunctional::class);
    }

    // /**
    //  * @return Ecofunctional[] Returns an array of Ecofunctional objects
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
    public function findOneBySomeField($value): ?Ecofunctional
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
