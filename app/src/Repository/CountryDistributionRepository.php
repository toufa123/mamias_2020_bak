<?php

namespace App\Repository;

use App\Entity\CountryDistribution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CountryDistribution|null find($id, $lockMode = null, $lockVersion = null)
 * @method CountryDistribution|null findOneBy(array $criteria, array $orderBy = null)
 * @method CountryDistribution[]    findAll()
 * @method CountryDistribution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryDistributionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CountryDistribution::class);
    }

    // /**
    //  * @return CountryDistribution[] Returns an array of CountryDistribution objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CountryDistribution
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
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
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function findSpeciesByParametres($id)
    {
        $rawSql = 'SELECT country.country, 	SUM(CASE WHEN mamias_id = :id THEN 1 ELSE 0 END) as count '
            . ' FROM country_distribution INNER JOIN country ON	country_distribution.country_id = country.id '
            . ' INNER JOIN mamias ON country_distribution.mamias_id = mamias.id INNER JOIN catalogue ON mamias.relation_id = catalogue.id '
            . ' GROUP BY country.country ORDER BY country.country ASC ';

        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();
        //return $stmt->fetchAll (\PDO::FETCH_ASSOC);
        //return $stmt->fetchArray();
    }

}
