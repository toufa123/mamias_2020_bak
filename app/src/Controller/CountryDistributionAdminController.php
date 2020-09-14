<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CountryDistribution;
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
                $sheetData = $spreadsheet->getActiveSheet()->toArray();
                dump($sheetData);
                die;
                $worksheet = $spreadsheet->getActiveSheet();
                // Get the highest row number and column letter referenced in the worksheet
                $highestRow = $worksheet->getHighestRow() - 1; // e.g. 10
                $highestColumn = $worksheet->getHighestColumn();
                $highestColumn++;

                $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/nc-import-" . date('d-m-y') . ".txt", "wb");
                fwrite($fp, $_FILES['nc']['name'] . "\n");
                //foreach ($worksheet->getRowIterator() as $row) {
                //    $cellIterator = $row->getCellIterator();
                //    $cellIterator->setIterateOnlyExistingCells(TRUE);
                //    foreach ($cellIterator as $cell) {
                //        fwrite($fp, $cell->getValue() . "\n");
                //    }

                //}
                $em = $this->getDoctrine()->getManager();

                for ($row = 2; $row <= $highestRow; ++$row) {
                    for ($col = 'A'; $col != $highestColumn; ++$col) {
                        $Species_catalogues = $em->getRepository(CountryDistribution::class)->findBy(array('Species' => $worksheet->getCell($col . $row)
                            ->getValue()));
                        if ($Species_catalogues != '') {
                            fwrite($fp, $worksheet->getCell($col . $row)
                                    ->getValue() . '-Already there' . "\n");
                        } else {
                            $Species_catalogues->setSpecies($worksheet->getCell($col . $row)
                                ->getValue());
                            $em->persist($Species_catalogues);
                            $em->flush();
                            fwrite($fp, $worksheet->getCell($col . $row)
                                    ->getValue() . '-Addedd' . "\n");

                        }
                    }

                }


                fwrite($fp, strval($highestRow));

                fclose($fp);

                //dump($highestRow,$highestColumn);die;

                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'File is valid, and was successfully uploaded.!');
                return $this->redirect($this->generateUrl('CountryD_list'));
            } else {
                $request->getSession()
                    ->getFlashBag()
                    ->add('error', 'File is not valid.!');
                return $this->render('admin/import/importn.html.twig');
                //return $this->redirect($this->generateUrl('importcatalogue'));

            }


        } else {
            return $this->render('admin/import/importn.html.twig');
            //return $this->renderWithExtraParams('admin/catalogue/importc.html.twig');
        }

    }

}
