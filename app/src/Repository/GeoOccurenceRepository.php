<?php

namespace App\Repository;

use App\Entity\GeoOccurence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GeoOccurence|null find($id, $lockMode = null, $lockVersion = null)
 * @method GeoOccurence|null findOneBy(array $criteria, array $orderBy = null)
 * @method GeoOccurence[]    findAll()
 * @method GeoOccurence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeoOccurenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeoOccurence::class);
    }

    // /**
    //  * @return GeoOccurence[] Returns an array of GeoOccurence objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GeoOccurence
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getNb()
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->Where('a.status = :status')
            ->setParameter('status', 'Validated')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
