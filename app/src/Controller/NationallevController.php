<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Country;
use App\Entity\Mamias;
use App\Entity\SearchCountry;
use App\Form\CountrySearchType;
use Ob\HighchartsBundle\Highcharts\Highchart;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class NationallevController extends AbstractController
{
    /**
     * @Route("services/dash/nat/", name="nat")
     */
    public function index(Request $request)
    {
        $n1 = null;
        $n2 = null;
        $n3 = null;
        $n6 = null;
        //$n4 = array();
        $cat = null;
        $c = null;
        $co = null;
        $ob = new Highchart();
        $ob2 = new Highchart();
        $ob3 = new Highchart();
        $ob4 = new Highchart();

        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->get('security.token_storage')->getToken()->getUser();
        //$hasAccess = in_array('ROLE_FOCALPOINT', $user->getRoles()); & in_array('ROLE_FOCALPOINT', $user->getRoles()) != false
        //dump($user);die;
        if ('' != $user & $user != 'anon.' & $user != 'admin_sparac') {
            $co = $user->getCountry()->getId();
            dump($co);
            die;
            $em = $this->getDoctrine()->getManager();

            $n1 = $em->getRepository(Mamias::class)->findnumbersBycountry($co);
            $n11 = $em->getRepository(Mamias::class)->findnumbersBycountry2($co);
            dump($n11);
            die;
            $n2 = $em->getRepository(Mamias::class)->findnumbersByestablished($co);
            $n3 = $em->getRepository(Mamias::class)->findnumbersByInvasive($co);
            $n4 = $em->getRepository(Mamias::class)->getcumulativebyCountry1($co);
            //$this->addFlash('success', 'The country Selected is ' . $c);
            //dump($n4);die;
            //Cumulative Number by Country
            $datacu = [];
            $datareg = [];
            foreach ($n4 as $values) {
                $cat[] = [$values['first_med_sighting']];
                $a = [$values['first_med_sighting'], $values['cumulative']];
                $b = [(int)$values['first_med_sighting'], (int)$values['cumulative']];
                array_push($datacu, $a);
                array_push($datareg, $b);
            }
            $ob = new Highchart();
            $ob->lang->noData("No Data to display ");
            $ob->chart->type('areaspline');
            $ob->chart->renderTo('linechart');
            $ob->title->text('numbers of new reported marine non-indigenous species');
            $ob->xAxis->categories($cat);
            $ob->xAxis->tickPositions($cat);
            $ob->title->style(['fontFamily' => 'Roboto light', 'fontSize' => '18px', 'color' => '#00AEEF', 'fontWeight' => 'bold']);
            $ob->credits->text('www.mamias.org');
            $ob->credits->href('http://www.mamias.org _target="blank"');
            //$ob->yAxis->tickInterval(10);
            $ob->xAxis->title(['text' => 'Years']);
            $ob->yAxis->title(['text' => 'Numbre of NIS']);
            $ob->yAxis->allowDecimals(false);
            $ob->plotOptions->line(
                [
                    'allowPointSelect' => true,
                    'cursor' => 'pointer',
                    //'dataLabels' => ['enabled' => true],
                    'showInLegend' => true,
                ]
            );

            $ob->series([['name' => 'Total Number of Reported NIS', 'color' => '#00AEEF', 'data' => $datareg]]);

            //groups per country
            $n6 = $em->getRepository(Mamias::class)->getSpeciesbyGroupandCountry($co);
            $data = [];
            foreach ($n6 as $values) {
                $a = [$values['ecofunctional'], $values['value']];
                array_push($data, $a);
            }
            $ob2 = new Highchart();
            $ob2->lang->noData("No Data to display");
            $ob2->chart->renderTo('piechart');
            $ob2->chart->type('pie');
            $ob2->chart->options3d(['enabled' => true, 'alpha' => '50', 'beta' => '0', 'depth' => '20', 'viewDistance' => '25']);
            $ob2->title->text('Ecofunctional Groups of NIS');
            $ob2->title->style(['fontFamily' => 'Roboto light', 'fontSize' => '18px', 'color' => '#00AEEF', 'fontWeight' => 'bold']);
            $ob2->credits->text('www.mamias.org');
            $ob2->credits->href('http://www.mamias.org _target="blank"');
            $ob2->plotOptions->pie(
                [
                    'allowPointSelect' => true,
                    'cursor' => 'pointer',
                    'depth' => '40',
                    'dataLabels' => [
                        'enabled' => true,
                        'connectorShape' => 'crookedLine',
                        'crookDistance' => '70%'
                    ],
                    'showInLegend' => false,
                ]
            );
            //$data = array($status,$results2);
            $ob2->series([['type' => 'pie', 'name' => 'Number of NIS', 'data' => $data]]);

            //origin for countries
            $n5 = $em->getRepository(Mamias::class)->getSpeciesbyOriginsandCountry($co);
            $data3 = [];
            $categories = [];
            foreach ($n5 as $values) {
                $categories[] = [$values['origin']];

                $a3 = [$values['origin'], $values['value']];
                array_push($data3, $a3);
            }
            //dump($categories); die;
            $ob3 = new Highchart();
            $ob3->lang->noData("No Data to display");
            $ob3->chart->options3d(['enabled' => true, 'alpha' => '5', 'beta' => '30', 'depth' => '100', 'viewDistance' => '25']);
            $ob3->chart->renderTo('barchart1');
            $ob3->title->text('Origin of the Reported NIS');
            $ob3->yAxis->title(['text' => 'Numbre of NIS']);
            $ob3->xAxis->categories($categories);
            $ob3->title->style(['fontFamily' => 'Roboto light', 'fontSize' => '18px', 'color' => '#00AEEF', 'fontWeight' => 'bold']);
            $ob3->credits->text('www.mamias.org');
            $ob3->credits->href('http://www.mamias.org _target="blank"');
            //$ob5->legend->enabled(true);
            $ob3->yAxis->allowDecimals(false);
            $ob3->labels->enabled(true);
            //$data = array($status,$results2);
            $ob3->series([['type' => 'column', 'name' => 'Number of NIS', 'color' => '#00AEEF', 'data' => $data3]]);
            //Pathways for countries
            $n6 = $em->getRepository(Mamias::class)->getnumberbypathwayspercountry($co);
            //dump($n6);die;
            $data4 = [];
            $categories = [];
            foreach ($n6 as $values) {
                $categories[] = [$values['vector_name']];

                $a3 = [$values['vector_name'], $values['count']];
                array_push($data4, $a3);
            }
            //dump($categories); die;
            $ob4 = new Highchart();

            $ob4->lang->noData("No Data to display");
            $ob4->chart->renderTo('barchart2');
            $ob4->title->text('Number of Reported NIS by Pathways/vectors');
            $ob4->yAxis->title(['text' => 'Numbre of NIS']);
            $ob4->xAxis->categories($categories);
            $ob4->yAxis->allowDecimals(false);
            $ob4->title->style(['fontFamily' => 'Roboto light', 'fontSize' => '18px', 'color' => '#00AEEF', 'fontWeight' => 'bold']);
            $ob4->credits->text('www.mamias.org');
            $ob4->credits->href('http://www.mamias.org _target="blank"');
            //$ob5->legend->enabled(true);
            $ob4->labels->enabled(true);

            //$data = array($status,$results2);
            $ob4->series([['type' => 'bar', 'name' => 'Number of NIS', 'color' => '#00AEEF', 'data' => $data4]]);


        return $this->render(
            'nationallev/index.html.twig',
            [

                //'form1' => $form1->createView(),//'form2' => $form2->createView (),
                'n1' => $n1,
                'n2' => $n2,
                'n3' => $n3,
                'linechart' => $ob,
                'piechart' => $ob2,
                'barchart1' => $ob3,
                'barchart2' => $ob4,
                'name' => $c,
            ]
        );
        } else {

            $search = new SearchCountry();
            $form1 = $this->createForm(CountrySearchType::class, $search);
            $form1->handleRequest($request);
            $country = $request->get('country');
            //dump($country);die;
            $em = $this->getDoctrine()->getManager();
            if ($form1->isSubmitted() && $form1->isValid()) {
                $data = $form1->getData();
                $c = $data->getCountry();
                //dump($c);die;
                //$c = 'Algeria';
                if ('' != $c) {
                    $co = $em->getRepository(Country::class)->findOneBy(['country' => $c])->getId();
                }
                $n1 = $em->getRepository(Mamias::class)->findnumbersBycountry($co);
                $n11 = $em->getRepository(Mamias::class)->findnumbersBycountry2($co);
                //dump($n11);die;
                $n2 = $em->getRepository(Mamias::class)->findnumbersByestablished($co);
                $n3 = $em->getRepository(Mamias::class)->findnumbersByInvasive($co);
                $n4 = $em->getRepository(Mamias::class)->getcumulativebyCountry1($co);
                //$this->addFlash('success', 'The country Selected is ' . $c);
                //dump($n4);die;
                //Cumulative Number by Country
                $datacu = [];
                $datareg = [];
                foreach ($n4 as $values) {
                    $cat[] = [$values['first_med_sighting']];
                    $a = [$values['first_med_sighting'], $values['cumulative']];
                    $b = [(int)$values['first_med_sighting'], (int)$values['cumulative']];
                    array_push($datacu, $a);
                    array_push($datareg, $b);
                }
                $ob = new Highchart();
                $ob->lang->noData("No Data to display ");
                $ob->chart->type('areaspline');
                $ob->chart->renderTo('linechart');
                $ob->title->text('numbers of new reported marine non-indigenous species');
                $ob->xAxis->categories($cat);
                $ob->title->style(['fontFamily' => 'Roboto light', 'fontSize' => '18px', 'color' => '#00AEEF', 'fontWeight' => 'bold']);
                $ob->credits->text('www.mamias.org');
                $ob->credits->href('http://www.mamias.org _target="blank"');
                //$ob->yAxis->tickInterval(10);
                $ob->xAxis->title(['text' => 'Years']);
                $ob->yAxis->title(['text' => 'Numbre of NIS']);
                $ob->yAxis->allowDecimals(false);
                $ob->plotOptions->line(
                    [
                        'allowPointSelect' => true,
                        'cursor' => 'pointer',
                        //'dataLabels' => ['enabled' => true],
                        'showInLegend' => true,
                    ]
                );

                $ob->series([['name' => 'Total Number of Reported NIS', 'color' => '#00AEEF', 'data' => $datareg]]);

                //groups per country
                $n6 = $em->getRepository(Mamias::class)->getSpeciesbyGroupandCountry($co);
                $data = [];
                foreach ($n6 as $values) {
                    $a = [$values['ecofunctional'], $values['value']];
                    array_push($data, $a);
                }
                $ob2 = new Highchart();
                $ob2->lang->noData("No Data to display");
                $ob2->chart->renderTo('piechart');
                $ob2->chart->type('pie');
                $ob2->chart->options3d(['enabled' => true, 'alpha' => '50', 'beta' => '0', 'depth' => '20', 'viewDistance' => '25']);
                $ob2->title->text('Ecofunctional Groups of NIS');
                $ob2->title->style(['fontFamily' => 'Roboto light', 'fontSize' => '18px', 'color' => '#00AEEF', 'fontWeight' => 'bold']);
                $ob2->credits->text('www.mamias.org');
                $ob2->credits->href('http://www.mamias.org _target="blank"');
                $ob2->plotOptions->pie(
                    [
                        'allowPointSelect' => true,
                        'cursor' => 'pointer',
                        'depth' => '40',
                        'dataLabels' => [
                            'enabled' => true,
                            'connectorShape' => 'crookedLine',
                            'crookDistance' => '70%'
                        ],
                        'showInLegend' => false,
                    ]
                );
                //$data = array($status,$results2);
                $ob2->series([['type' => 'pie', 'name' => 'Number of NIS', 'data' => $data]]);

                //origin for countries
                $n5 = $em->getRepository(Mamias::class)->getSpeciesbyOriginsandCountry($co);
                $data3 = [];
                $categories = [];
                foreach ($n5 as $values) {
                    $categories[] = [$values['origin']];

                    $a3 = [$values['origin'], $values['value']];
                    array_push($data3, $a3);
                }
                //dump($categories); die;
                $ob3 = new Highchart();
                $ob3->lang->noData("No Data to display");
                $ob3->chart->options3d(['enabled' => true, 'alpha' => '5', 'beta' => '30', 'depth' => '100', 'viewDistance' => '25']);
                $ob3->chart->renderTo('barchart1');
                $ob3->title->text('Origin of the Reported NIS');
                $ob3->yAxis->title(['text' => 'Numbre of NIS']);
                $ob3->xAxis->categories($categories);
                $ob3->title->style(['fontFamily' => 'Roboto light', 'fontSize' => '18px', 'color' => '#00AEEF', 'fontWeight' => 'bold']);
                $ob3->credits->text('www.mamias.org');
                $ob3->credits->href('http://www.mamias.org _target="blank"');
                //$ob5->legend->enabled(true);
                $ob3->yAxis->allowDecimals(false);
                $ob3->labels->enabled(true);
                //$data = array($status,$results2);
                $ob3->series([['type' => 'column', 'name' => 'Number of NIS', 'color' => '#00AEEF', 'data' => $data3]]);
                //Pathways for countries
                $n6 = $em->getRepository(Mamias::class)->getnumberbypathwayspercountry($co);
                //dump($n6);die;
                $data4 = [];
                $categories = [];
                foreach ($n6 as $values) {
                    $categories[] = [$values['vector_name']];

                    $a3 = [$values['vector_name'], $values['count']];
                    array_push($data4, $a3);
                }
                //dump($categories); die;
                $ob4 = new Highchart();

                $ob4->lang->noData("No Data to display");
                $ob4->chart->renderTo('barchart2');
                $ob4->title->text('Number of Reported NIS by Pathways/vectors');
                $ob4->yAxis->title(['text' => 'Numbre of NIS']);
                $ob4->xAxis->categories($categories);
                $ob4->yAxis->allowDecimals(false);
                $ob4->title->style(['fontFamily' => 'Roboto light', 'fontSize' => '18px', 'color' => '#00AEEF', 'fontWeight' => 'bold']);
                $ob4->credits->text('www.mamias.org');
                $ob4->credits->href('http://www.mamias.org _target="blank"');
                //$ob5->legend->enabled(true);
                $ob4->labels->enabled(true);

                //$data = array($status,$results2);
                $ob4->series([['type' => 'bar', 'name' => 'Number of NIS', 'color' => '#00AEEF', 'data' => $data4]]);
            }

            return $this->render(
                'nationallev/index.html.twig',
                [

                    'form1' => $form1->createView(),//'form2' => $form2->createView (),
                    'n1' => $n1,
                    'n2' => $n2,
                    'n3' => $n3,
                    'linechart' => $ob,
                    'piechart' => $ob2,
                    'barchart1' => $ob3,
                    'barchart2' => $ob4,
                    'name' => $c
                ]
            );

        }
    }
}
