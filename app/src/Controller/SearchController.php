<?php

namespace App\Controller;

use App\Entity\Catalogue;
use App\Entity\Country;
use App\Entity\EcAp;
use App\Entity\Ecofunctional;
use App\Entity\Mamias;
use App\Entity\Origin;
use App\Entity\RegionalSea;
use App\Entity\SearchSpecies;
use App\Entity\Status;
use App\Entity\SuccessType;
use App\Entity\VectorName;
use App\Form\SearchType;
use App\Entity\CountryDistribution;
use App\Repository\CountryDistributionRepository;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class SearchController extends AbstractController
{
    /**
     * @Route("services/search", name="search",  options={"sitemap" = true})
     */
    public function index(Request $request, Breadcrumbs $breadcrumbs)
    {
        $breadcrumbs->prependRouteItem("Home", "home");
        $breadcrumbs->addItem("MAMIAS services/Mamias search", $this->get("router")->generate("search"));
        $sId = null;
        $eco = null;
        $origin = null;
        $su = null;
        $year = null;
        $country = null;
        $reg = null;
        $ecapmed = null;
        $status = null;
        $pathway = null;
        $ec = null;
        $search = new SearchSpecies();
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //On récupère les données entrées dans le formulaire par l'utilisateur
            $em = $this->getDoctrine()->getManager();
            $data = $form->getData();
            $speciesName = $data->getSpeciesName();
            if ('' != $speciesName) {
                $sId = $em->getRepository(Catalogue::class)->findOneBy(['Species' => $speciesName])->getId();
            }
            $e = $data->getEcofunctional();
            if ('' != $e) {
                $eco = $em->getRepository(Ecofunctional::class)->findOneBy(['ecofunctional' => $e])->getId();
            }
            $o = $data->getOrigin();
            if ('' != $o) {
                $origin = $em->getRepository(Origin::class)->findOneBy(['originRegion' => $o])->getId();
            }
            $sucess = $data->getSuccessType();
            if ('' != $sucess) {
                $su = $em->getRepository(SuccessType::class)->findOneBy(['successType' => $sucess])->getId();
            }
            $year = $data->getMed1stSighting();

            $c = $data->getCountry();
            if ('' != $c) {
                $country = $em->getRepository(Country::class)->findOneBy(['country' => $c])->getId();
            }
            //$r = $data->getRegionalSea ();
            //if ('' != $r) {
            //    $reg = $em->getRepository (RegionalSea::class)->findOneBy (['regionalSea' => $r])->getId ();
            //}
            $ec = $data->getEcap();
            if ('' != $ec) {
                $ecapmed = $em->getRepository(EcAp::class)->findOneBy(['ecap' => $ec])->getId();
            }

            $st = $data->getStatus();

            if ('' != $st) {
                $status = $em->getRepository(Status::class)->findOneBy(['status' => $st])->getId();
            }

            $pat = $data->getvectorName();
            if ('' != $pat) {
                $pathway = $em->getRepository(VectorName::class)->findOneBy(['vectorName' => $pat])->getId();
            }
            //dump($st, $pat, $status, $pathway);die;

            $species = $em->getRepository(Mamias::class)->findSpeciesByParametres(
                $sId, $eco, $origin, $su, $year, $country, $ecapmed, $status, $pathway);
            //dump ($species); die;
        } else {
            $em = $this->getDoctrine()->getManager();
            $species = $em->getRepository(Mamias::class)->findAllS();
            //dump($species);die;
        }
        $data = (object)[
            'data' => [[
                $species
            ]]
        ];
        $r2 = json_encode(array('data' => $species));
        //dump ($r2);die;
        return $this->render('search/index.html.twig', ['form' => $form->createView(), 'species' => $species, 'response' => $r2]);
    }

    /**
     * @Route("services/search/{id}/show", name="species_fiche")
     */
    public function show($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository(Mamias::class)->find($id);
        $NbperCountry = json_encode(
            $em->getRepository(CountryDistribution::class)->findSpeciesByParametres($id)
        );

        $NbperCountry1 =
            $em->getRepository(CountryDistribution::class)->findSpeciesByParametres($id);

        foreach ($NbperCountry1 as $key => $value) {
            $data[$key] = [$value['country'], $value['count']];
        }
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find the Species.');
        }
        //$species= $this->getDoctrine()->getRepository(Mamias::class)->find($id);
        return $this->render(
            'search/species.html.twig',
            [
                'entity' => $entity,
                'NbperCountry1' => $NbperCountry1,
                'data1' => $data,
                'NbperCountry' => $NbperCountry


            ]
        );
    }
}
