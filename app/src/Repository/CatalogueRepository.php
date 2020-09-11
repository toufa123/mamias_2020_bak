<?php

namespace App\Repository;

use App\Entity\Catalogue;
use App\Entity\Mamias;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Catalogue|null find($id, $lockMode = null, $lockVersion = null)
 * @method Catalogue|null findOneBy(array $criteria, array $orderBy = null)
 * @method Catalogue[]    findAll()
 * @method Catalogue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatalogueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Catalogue::class);
    }

    /**
     * @return Catalogue[] Returns an array of Catalogue objects
     *
     *
     * public function findBySpecies($value)
    * {
     * return $this->createQueryBuilder('c')
     * ->Where('c.Species = :val')
     * ->setParameter('val', $value)
     * ->orderBy('c.id', 'ASC')
     * //->setMaxResults(10)
     * ->getQuery()
            * ->getResult();
     * }
     */


        public function findOneBySpecies($value): ?Catalogue
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.Species = :val')
                ->setParameter('val', $value)
                //->select('c.Species')
                //->orderBy('c.id', 'ASC')
                ->getQuery()
                ->getOneOrNullResult();
            ;
        }

    public function getNb()
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->Where('a.status = :Validated')
            ->setParameter('Validated', 'Validated')
            //->Where('Mamias.Success != :Success')
            //->setParameter('Success', '7')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getallNb()
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function getlastadded()
    {
        return $this->createQueryBuilder('a')
            ->select('a, MAX(a.createdAt) AS max_time')
            ->setMaxResults('15')
            ->groupBy('a.id')
            ->orderBy('max_time', 'DESC')
            ->getQuery()
            ->getScalarResult();
    }

    public function getlastaddeddate()
    {
        return $this->createQueryBuilder('a')
            ->select('a, MAX(a.createdAt) AS max_time')
            ->groupBy('a.id')
            ->orderBy('max_time', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

    }
}
