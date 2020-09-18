<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Catalogue;
use App\Entity\Country;
use App\Entity\CountryDistribution;
use App\Entity\GeoOccurence;
use App\Entity\Mamias;
use App\Entity\Status;
use App\Entity\SuccessType;
use App\Entity\VectorName;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Xlsxwriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use CrEOF\Geo\WKT\Parser;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface;

final class CountryDistributionAdminController extends CRUDController
{

    /**
     * @Route("countrydistribution/importn", name="importnd")
     * @Security("is_granted('ROLE_ADMIN')")
     */

    public function importnAction(Request $request)
    {

        $session = $request->getSession();
        //$tmp_name = $_FILES['catalogue']['tmp_name'];
        //dump($request);die;
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/cd-import-" . date('d-m-y-H_i') . ".txt", "wb");
        $url = '/cd-import-' . date('d-m-y-H_i') . '.txt';
        $request->request->set('_sonata_admin', 'admin.template');
        if (isset($_POST["submit"])) {
            $tmp_name = $_FILES['nc']['tmp_name'];
            //$destination = $this->getParameter('kernel.project_dir') . '/public/resources/catalogue/import/';
            //$uploadfile = $destination . basename($_FILES['catalogue']['name']);
            $arr_file = explode('.', $_FILES['nc']['name']);
            $extension = end($arr_file);

            if ('xls' == $extension) {
                $reader = new XlsReader();
                $spreadsheet = $reader->load($tmp_name);
                $sheetData = $spreadsheet->getActiveSheet()->toArray();
                //dump($sheetData);
                die;
                $worksheet = $spreadsheet->getActiveSheet();
                // Get the highest row number and column letter referenced in the worksheet
                $highestRow = $worksheet->getHighestRow() - 1; // e.g. 10
                $highestColumn = $worksheet->getHighestColumn();


                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'File is valid, and was successfully uploaded.!');
                return $this->redirect($this->generateUrl('CountryD_list'));
            } elseif ('xlsx' == $extension) {
                $reader = new XlsxReader();
                $spreadsheet = $reader->load($tmp_name);
                $worksheet = $spreadsheet->getActiveSheet();
                $sheetData = $worksheet->toArray();
                $highestRow = $worksheet->getHighestRow() - 1; // e.g. 10
                //$highestColumn = $worksheet->getHighestColumn();
                //$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5
                //$highestColumn++;
                fwrite($fp, $_FILES['nc']['name'] . "\n");
                for ($row = 1; $row <= $highestRow; ++$row) {
                    //dd($sheetData[$row][0],$sheetData[$row][1], $sheetData[$row][2], $sheetData[$row][3], $sheetData[$row][4], $sheetData[$row][5], $sheetData[$row][6]  );
                    $species = $this->getDoctrine()->getManager()->getRepository(Catalogue::class)->findOneBy(['Species' => $sheetData[$row][0]]);
                    //dd($species);
                    if ($species) {
                        $species_id = $species->getId();
                        //dd($species_id);
                        $em1 = $this->getDoctrine()->getManager()->getRepository(Mamias::class);
                        $s = $em1->findOneBy(['relation' => $species_id]);
                        $country = $this->getDoctrine()->getManager()->getRepository(Country::class)->findOneBy(['country' => $sheetData[$row][9]]);
                        $v = $this->getDoctrine()->getManager()->getRepository(VectorName::class)->findOneBy(['vectorName' => $sheetData[$row][3]]);
                        $as = $this->getDoctrine()->getManager()->getRepository(SuccessType::class)->findOneBy(['successType' => $sheetData[$row][6]]);
                        $ns = $this->getDoctrine()->getManager()->getRepository(Status::class)->findOneBy(['status' => $sheetData[$row][5]]);
                        //dd($ns);
                        if ($s) {
                            //dd($s);
                            $em2 = $this->getDoctrine()->getManager();
                            $CD = new CountryDistribution();
                            $CD->setMamias($s->setRelation($species));
                            $CD->setCountry($country);
                            $CD->setAreaSighting((string)$sheetData[$row][2]);
                            $CD->setNationalstatus($ns);
                            $CD->setAreaSuccess($as);
                            $CD->setStatus('Non Validated');
                            if ($sheetData[$row][7] != '' & $sheetData[$row][8] != '') {
                                $GO = new GeoOccurence();
                                $GO->setCountry($country);
                                $GO->setMamias($s->setRelation($species));
                                $GO->setDateOccurence(\DateTime::createFromFormat('Y', (string)$sheetData[$row][2]));
                                $parser = new Parser('Point(' . $sheetData[$row][7] . ' ' . $sheetData[$row][8] . ')');
                                //dd($parser);
                                $geo = $parser->parse();
                                $g = new \CrEOF\Spatial\PHP\Types\Geometry\Point($geo['value'], '4326');
                                $GO->setLocation($g);
                                $GO->setStatus('Submitted');
                                $em2->persist($GO);
                            }
                            $em2->persist($CD);
                            $em2->flush();
                            fwrite($fp, $sheetData[$row][0] . '----added' . "\n");
                            //dd($CD);
                        } else {
                            $request->getSession()->getFlashBag()->add('error', 'no records to add' . '<br>' . "<a href=" . $url . ">Log Link</a>");
                        }
                    }
                }
                $request->getSession()->getFlashBag()->add('success', 'File is valid, and was successfully processed.!' . '<br>' . "<a href=" . $url . ">Log Link</a>");
                fclose($fp);
                return $this->redirect($this->generateUrl('CountryD_list'));
            } else {
                $request->getSession()
                    ->getFlashBag()
                    ->add('error', 'File is not valid.!');
                return $this->render('admin/import/importn.html.twig');
            }


        } else {
            return $this->render('admin/import/importn.html.twig');
            //return $this->renderWithExtraParams('admin/catalogue/importc.html.twig');
        }

    }

}
