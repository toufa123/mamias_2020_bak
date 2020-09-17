<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Catalogue;
use App\Entity\CountryDistribution;
use App\Entity\Mamias;

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
                    //dd($sheetData[$row][0],$sheetData[$row][1], $sheetData[$row][2], $sheetData[$row][3], $sheetData[$row][4] );
                    $species = $this->getDoctrine()->getManager()->getRepository(Catalogue::class)->findOneBy(['Species' => $sheetData[$row][0]]);
                    if ($species) {
                        $species_id = $species->getId();
                        $em = $this->getDoctrine()->getManager()->getRepository(CountryDistribution::class);
                        $s = $em->findOneBy(['mamias' => $species_id]);
                        fwrite($fp, $species_id . '-' . $sheetData[$row][2] . '-' . $sheetData[$row][5] . "\n");
                        if ($s) {
                            $CD = new CountryDistribution();
                            $CD->setMamias($species_id);
                            $CD->setAreaSighting($sheetData[$row][2]);
                            $CD->setStatus('Non Validated');
                            $CD->setCertainty($sheetData[$row][5]);

                            //$CD->setCreatedAt(datetime('now'));
                            //$em->persist($CD);
                            //$em->flush();
                            //dd($CD);
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
