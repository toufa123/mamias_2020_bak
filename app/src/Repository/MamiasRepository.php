<?php

namespace App\Repository;

use App\Entity\Mamias;
use App\Entity\SuccessType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Mamias|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mamias|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mamias[]    findAll()
 * @method Mamias[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MamiasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mamias::class);
    }

    // /**
    //  * @return Mamias[] Returns an array of Mamias objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Mamias
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findAllSpecies()
    {
        return $this->createQueryBuilder('a')
            //->from('a')
            ->select('count(a.id)')
            ->where('a.Success != 7')
            //->join('a.relation', 'b')
            ->getQuery()
            ->getSingleScalarResult();
        //->getOneOrNullResult();

    }

    public function findAllS()
    {
        return $this->createQueryBuilder('z')
            ->Select('z.id')
            ->AddSelect('z.firstMedSighting')
            ->leftJoin('z.relation', 'Catalogue')
            ->addSelect('Catalogue.Species')
            ->leftJoin('z.Ecofunctional', 'Ecofunctional')
            ->addSelect('Ecofunctional.ecofunctional')
            ->leftJoin('z.Origin', 'Origin')
            ->addSelect('Origin.originRegion')
            //->addSelect('z.Success')

            //->setParameter('S', 7)
            ->leftJoin('z.Success', 'success')
            ->addSelect('success.successType')
            ->where('success.id != 7')
            ->leftJoin('z.speciesstatus', 'speciesstatus')
            ->addSelect('speciesstatus.status')
            //->leftJoin ('z.Distribution', 'd')
            //->join ('d.country', 'c')
            //->addSelect ('c.country')
            //->join ('d.ecap', 'e')
            //->addSelect ('e.ecap')
            ->orderBy('z.id')
            ->groupBy('z.id', 'Catalogue.Species', 'Ecofunctional.ecofunctional', 'Origin.originRegion', 'success.successType', 'speciesstatus.status')
            ->getQuery()
            ->getArrayResult();
    }

    public function getNbEstablished()
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->Where('a.Success=6')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getNbInvasive()
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->Where('a.Success=8')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Mamias[] Returns an array of Mamias objects
     */
    public function findSpeciesByParametres($sId, $eco, $origin, $su, $year, $country, $ecapmed, $status, $pathway)
    {

        $query = $this->createQueryBuilder('m');
        $query = $query
            ->select('m')
            ->AddSelect('m.id')
            ->AddSelect('m.firstMedSighting')
            ->leftJoin('m.Distribution', 'c')
            ->addSelect('c')
            ->leftJoin('m.relation', 'Catalogue')
            ->addSelect('Catalogue.Species')
            ->leftJoin('m.Ecofunctional', 'Ecofunctional')
            ->addSelect('Ecofunctional.ecofunctional')
            ->leftJoin('m.Origin', 'Origin')
            ->addSelect('Origin.originRegion')
            ->leftJoin('m.Success', 'success')
            ->where('m.Success != 7')
            ->addSelect('success.successType')
            ->leftJoin('m.speciesstatus', 'speciesstatus')
            ->addSelect('speciesstatus.status')
            ->leftJoin('m.Pathway', 'Pathway')
            ->leftJoin('Pathway.VectorName', 'vector')
            ->addSelect('vector.vectorName');
        if (!empty($sId)) {
            $query = $query->Where('m.relation = :val')
                ->setParameter('val', $sId);
        }
        if (!empty($eco)) {
            $query = $query->andWhere('m.Ecofunctional = :val1')
                ->setParameter('val1', $eco);
        }
        if (!empty($origin)) {
            $query = $query->andWhere('m.Origin = :val3')
                ->setParameter('val3', $origin);
        }
        if (!empty($year)) {
            $query = $query->andWhere('m.firstMedSighting = :val2')
                ->setParameter('val2', $year);
        }
        if (!empty($su)) {
            $query = $query->andWhere('m.Success = :val4')
                ->setParameter('val4', $su);
        }
        if (!empty($country)) {
            $query = $query->andWhere('c.country = :val4')
                ->setParameter('val4', $country);
        }
        //if (!empty($reg)) {
        //    $query = $query->andWhere ('c.regionalSea = :val4')
        //        ->setParameter ('val4', $reg);
        //}
        if (!empty($ecapmed)) {
            $query = $query->andWhere('c.ecap = :val7')
                ->setParameter('val', $ecapmed);
        }
        if (!empty($status)) {
            $query = $query->andWhere('m.speciesstatus = :val5')
                ->setParameter('val5', $status);
        }
        if (!empty($pathway)) {
            $query = $query->andWhere('Pathway.VectorName = :val6')
                ->setParameter('val6', $pathway);
        }
        return $query->getQuery()->getArrayResult();
    }

    /*
    public function findOneBySomeField($value): ?Mamias
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;

    }
    */

    //Mediterranean level

    public function getSpeciesPerCountry()
    {
        $rawSql = 'SELECT country.country As Country, (SELECT COUNT(DISTINCT mamias.id)) As z '
            . 'FROM mamias , country_distribution , country '
            . ' WHERE mamias.id = country_distribution.mamias_id AND country_distribution.country_id = country.id AND mamias.success_id != 7'
            . ' GROUP BY country.country ORDER BY country.country ASC';
        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute([]);
        return $stmt->fetchAll();
    }

    public function gettotal()
    {
        $rawSql1 = 'SELECT mamias.first_med_sighting, (SELECT COUNT(DISTINCT mamias.id)) AS Total ' .
            'FROM mamias WHERE mamias.first_med_sighting IS NOT NULL AND mamias.first_med_sighting != \'\' AND mamias.success_id != 7 GROUP BY mamias.first_med_sighting ORDER BY mamias.first_med_sighting';

        $stmt1 = $this->getEntityManager()->getConnection()->prepare($rawSql1);
        $stmt1->execute([]);

        return $stmt1->fetchAll();
    }

    public function getcumulative()
    {
        $rawSql1 = 'SELECT mamias.first_med_sighting, sum(count(DISTINCT mamias.id)) OVER (ORDER BY mamias.first_med_sighting) as cumulative ' .
            'FROM mamias WHERE mamias.first_med_sighting IS NOT NULL AND length(mamias.first_med_sighting) > 0 AND mamias.success_id != 7 GROUP BY mamias.first_med_sighting ORDER BY mamias.first_med_sighting';

        $stmt1 = $this->getEntityManager()->getConnection()->prepare($rawSql1);
        $stmt1->execute([]);

        return $stmt1->fetchAll();
    }

    public function getSpeciesbyGroup()
    {
        $rawSql1 = 'SELECT ecofunctional.ecofunctional , (SELECT COUNT(DISTINCT mamias.id)) AS Value ' .
            'FROM mamias, ecofunctional WHERE mamias.ecofunctional_id = ecofunctional.id  AND mamias.success_id != 7 GROUP BY ecofunctional.ecofunctional';

        $stmt1 = $this->getEntityManager()->getConnection()->prepare($rawSql1);
        $stmt1->execute([]);

        return $stmt1->fetchAll();
    }

    public function getSpeciesbyestablishement()
    {
        $rawSql1 = 'SELECT success_type.success_type AS Success, (SELECT COUNT(DISTINCT mamias.id)) AS Value ' .
            'FROM mamias, success_type WHERE mamias.success_id = success_type.id  AND mamias.success_id != 7 GROUP BY success_type.success_type';

        $stmt1 = $this->getEntityManager()->getConnection()->prepare($rawSql1);
        $stmt1->execute([]);

        return $stmt1->fetchAll();
    }

    public function getSpeciesbystatus()
    {

        $rawSql1 = 'SELECT status.status AS status, (SELECT COUNT(DISTINCT mamias.id)) AS Value ' .
            'FROM mamias, status WHERE mamias.speciesstatus_id = status.id  AND mamias.success_id != 7 GROUP BY status.status';

        $stmt1 = $this->getEntityManager()->getConnection()->prepare($rawSql1);
        $stmt1->execute([]);

        return $stmt1->fetchAll();
    }

    public function getSpeciesbyOrigins()
    {
        $rawSql1 = "SELECT split_part( origin.origin_region, ' ' , 1 ) As origin, (SELECT COUNT(DISTINCT mamias.id)) AS Value " .
            'FROM mamias, origin WHERE mamias.origin_id = origin.id  AND mamias.success_id != 7 GROUP BY origin ';

        $stmt1 = $this->getEntityManager()->getConnection()->prepare($rawSql1);
        $stmt1->execute([]);

        return $stmt1->fetchAll();
    }

    public function getSpeciesbyEcap()
    {
        $rawSql1 = 'SELECT ecap.ecap As ecap, (SELECT COUNT(DISTINCT mamias.id)) AS Value ' .
            ' FROM mamias, country_distribution, ecap WHERE mamias.id = country_distribution.mamias_id AND mamias.success_id != 7' .
            ' AND country_distribution.ecap_id = ecap.id GROUP BY ecap ';

        $stmt1 = $this->getEntityManager()->getConnection()->prepare($rawSql1);
        $stmt1->execute([]);

        return $stmt1->fetchAll();
    }

    public function findnumbersBycountry($co)
    {
        $rawSql = 'SELECT country.country As Country, (SELECT DISTINCT COUNT( mamias.id)) As Value '
            . ' FROM mamias , country_distribution , country '
            . ' WHERE mamias.id = country_distribution.mamias_id AND country_distribution.country_id = country.id '
            . ' AND country.id = :country AND mamias.success_id != 7'
            . ' GROUP BY country.country ORDER BY country.country ASC';

        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute(['country' => $co]);

        return $stmt->fetchColumn('1');
    }

    public function getcumulativeBycountry($co)
    {
        $rawSql = 'SELECT mamias.first_med_sighting, sum(count(DISTINCT mamias.id)) OVER (ORDER BY mamias.first_med_sighting) as cumulative '
            . '  FROM mamias , country_distribution , country WHERE mamias.first_med_sighting IS NOT NULL AND  length(mamias.first_med_sighting) > 0  '
            . '  WHERE mamias.id = country_distribution.mamias_id AND country_distribution.country_id = country.id AND mamias.success_id != 7 '
            . '  AND country.id = :country  '
            . '  GROUP BY mamias.first_med_sighting, country.country ORDER BY mamias.first_med_sighting';

        $stmt1 = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt1->execute(['country' => $co], []);

        return $stmt1->fetchAll();
    }

    public function findnumbersByestablished($co)
    {
        $rawSql = 'SELECT country.country As Country, (SELECT DISTINCT COUNT(mamias.id)) As Value '
            . ' FROM mamias , country_distribution , country '
            . ' WHERE mamias.id = country_distribution.mamias_id AND country_distribution.country_id = country.id '
            . ' AND mamias.success_id = 6'
            . ' AND country.id = :country'
            . ' GROUP BY country.country ORDER BY country.country ASC';

        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute(['country' => $co]);

        return $stmt->fetchColumn('1');
    }

    public function findnumbersByInvasive($co)
    {
        $rawSql = 'SELECT country.country As Country, (SELECT DISTINCT COUNT(mamias.id)) As Value '
            . ' FROM mamias , country_distribution , country '
            . ' WHERE mamias.id = country_distribution.mamias_id AND country_distribution.country_id = country.id '
            . ' AND mamias.success_id = 8'
            . ' AND country.id = :country'
            . ' GROUP BY country.country ORDER BY country.country ASC';

        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute(['country' => $co]);

        return $stmt->fetchColumn('1');
    }

    public function getcumulativebyCountry1($co)
    {

        $rawSql1 = 'SELECT mamias.first_med_sighting, sum(count(DISTINCT mamias.id)) OVER (ORDER BY mamias.first_med_sighting) as cumulative '
            . ' FROM mamias , country_distribution , country '
            . ' WHERE mamias.id = country_distribution.mamias_id AND country_distribution.country_id = country.id AND mamias.success_id != 7'
            . ' AND mamias.first_med_sighting IS NOT NULL AND country.id = :country GROUP BY  mamias.first_med_sighting, country.country ORDER BY mamias.first_med_sighting';

        $stmt1 = $this->getEntityManager()->getConnection()->prepare($rawSql1);
        $stmt1->execute(['country' => $co], []);

        return $stmt1->fetchAll();
    }

    public function getSpeciesbyGroupandCountry($co)
    {
        $rawSql1 = 'SELECT ecofunctional.ecofunctional , (SELECT DISTINCT COUNT(mamias.id)) AS Value '
            . ' FROM mamias, ecofunctional , country_distribution , country WHERE mamias.ecofunctional_id = ecofunctional.id '
            . ' AND mamias.id = country_distribution.mamias_id AND country_distribution.country_id = country.id '
            . '  AND country.id = :country GROUP BY ecofunctional.ecofunctional, country.country ORDER BY ecofunctional.ecofunctional';

        $stmt1 = $this->getEntityManager()->getConnection()->prepare($rawSql1);
        $stmt1->execute(['country' => $co], []);

        return $stmt1->fetchAll();
    }

    public function getSpeciesbyOriginsandCountry($co)
    {
        $rawSql1 = "SELECT split_part( origin.origin_region, ' ' , 1 ) As origin, (SELECT DISTINCT COUNT(mamias.id)) AS Value "
            . 'FROM mamias, origin, country_distribution , country WHERE mamias.origin_id = origin.id '
            . ' AND mamias.id = country_distribution.mamias_id AND country_distribution.country_id = country.id '
            . '  AND country.id = :country  GROUP BY origin ';

        $stmt1 = $this->getEntityManager()->getConnection()->prepare($rawSql1);
        $stmt1->execute(['country' => $co], []);

        return $stmt1->fetchAll();
    }

    public function findSpeciesByCountry($country)
    {
        $query = $this->createQueryBuilder('m');
        $query = $query
            ->select('m', 'c')
            ->where('m.Success  != 7')
            ->leftJoin('m.Distribution', 'c')
            ->addSelect('c');

        if (!empty($country)) {
            $query = $query->andWhere('c.country = :val4')
                ->setParameter('val4', $country);
        }
        //->orderBy('m.id', 'ASC')
        //->setMaxResults(10)

        return $query->getQuery()->getResult();
    }

    public function getlastadmddate()
    {
        return $this->createQueryBuilder('a')
            ->select('a, MAX(a.firstMedSighting) AS max_sig')
            ->where('a.firstMedSighting IS NOT NULL')
            ->groupBy('a.id')
            ->orderBy('max_sig', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }


    public function getnumberbypathways()
    {
        $rawSql8 = ' SELECT DISTINCT COUNT(mamias.relation_id), vectors.vector_name'
            . ' FROM mamias INNER JOIN mamias_vectors ON mamias.id = mamias_vectors.mamias_id AND mamias.success_id != 7'
            . ' INNER JOIN vectors ON mamias_vectors.vectors_id = vectors.id GROUP BY vectors.vector_name ORDER BY vectors.vector_name ';
        $stmt8 = $this->getEntityManager()->getConnection()->prepare($rawSql8);
        $stmt8->execute();
        return $stmt8->fetchAll();
    }

    public function getnumberbypathwayspercountry($co)
    {
        $rawSql9 = ' SELECT DISTINCT COUNT(mamias.relation_id), vectors.vector_name, country.id '

            . ' FROM mamias INNER JOIN mamias_vectors ON mamias.id = mamias_vectors.mamias_id '
            . ' INNER JOIN vectors 	ON 	mamias_vectors.vectors_id = vectors.id '
            . ' INNER JOIN catalogue ON mamias.relation_id = catalogue.id AND mamias.success_id != 7'
            . ' INNER JOIN country_distribution ON  mamias.id = country_distribution.mamias_id '
            . ' INNER JOIN country ON country_distribution.country_id = country.id '
            . ' WHERE country.id = :country '
            . ' GROUP BY vectors.vector_name, country.id '
            . ' ORDER BY vectors.vector_name ';
        $stmt9 = $this->getEntityManager()->getConnection()->prepare($rawSql9);
        $stmt9->execute(['country' => $co]);
        return $stmt9->fetchAll();
    }
}
