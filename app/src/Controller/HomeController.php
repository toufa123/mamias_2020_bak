<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Application\Sonata\NewsBundle\Entity\Post;
use App\Entity\Catalogue;
use App\Entity\CountryDistribution;
use App\Entity\GeoOccurence;
use App\Entity\Mamias;
use Symfony\Component\Filesystem\Filesystem;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home",  options={"sitemap" = true})
     */
    public function index(Breadcrumbs $breadcrumbs)
    {

        $breadcrumbs->addItem("Home", $this->get("router")->generate("home"));
        $em = $this->getDoctrine()->getManager();

        //nombre total de NIS validated dans le catalogue
        $NISCount = $em->getRepository(Catalogue::class)->getNb();
        //nombre total de NIS validated in MAMIAS
        //$NIStotal = $em->getRepository(Catalogue::class)->getallNb();
        $NISMamias = $em->getRepository(Mamias::class)->findAllSpecies();
        //dump($NISMamias);die;

        //nombre total de national occurences dans MAMIAS
        $DistroCount = $em->getRepository(CountryDistribution::class)->getNb();
        //dump($DistroCount);die;
        //nombre total de national occurences dans MAMIAS
        $GeoCount = $em->getRepository(GeoOccurence::class)->getNb();
        //last date for the catalogue
        $catDate = $em->getRepository(Catalogue::class)->getlastaddeddate();
        $catalogueDate = $catDate['max_time'];

        //laste date mamias
        $mamDate = $em->getRepository(Mamias::class)->getlastadmddate();
        $mamiasDate = $mamDate['max_sig'];
        //dump ($mamDate);die;

        $NbperCountry = $em->getRepository(Mamias::class)->getSpeciesPerCountry();
        $NbperCountry1 = json_encode($em->getRepository(Mamias::class)->getSpeciesPerCountry());
        //$a = json_decode ($NbperCountry1, true);
        //dump ($NbperCountry1);die;
        $last = $em->getRepository(Catalogue::class)->getlastadded();
        //dump($_ENV);die;

        //draw cumulative numbre of NIS
        $total = $em->getRepository(Mamias::class)->gettotal();
        $cumulative = $em->getRepository(Mamias::class)->getcumulative();
        //dump($cumulative);die;
        $datacu = [];
        $datareg = [];
        foreach ($cumulative as $values) {
            $cat[] = [$values['first_med_sighting']];
            $a = [$values['first_med_sighting'], $values['cumulative']];
            $b = [(int)$values['first_med_sighting'], (int)$values['cumulative']];

            array_push($datacu, $a);
            array_push($datareg, $b);

        }
        $datacu1 = json_encode($datacu);
        $cat1 = json_encode($cat);


        $datareg1 = json_encode($datareg);
        //dump( $b, $datareg1);die;

        return $this->render('home/index.html.twig', [
            'NISCount' => $NISCount,
            'DistroCount' => $DistroCount,
            'GeoCount' => $GeoCount,
            'last' => $last,
            'NbperCountry1' => $NbperCountry1,
            'catalogueDate' => $catalogueDate,
            'mamiasDate' => $mamiasDate,
            //'NIStotal' => $NIStotal,
            'datacu1' => $datacu1,
            'cat1' => $cat1,
            'datareg1' => $datareg1,
            'NISMamias' => $NISMamias

        ]);
    }
}

