<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Catalogue;
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

final class CatalogueAdminController extends CRUDController
{
    /**
     * @Route("admin/catalogue/importcatalogue", name="importcatalogue")
     * @Security("is_granted('ROLE_ADMIN')")
     */

    public function importcatalogueAction(Request $request)
    {

        $session = $request->getSession();
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/catalogue-import-" . date('d-m-y-H_i') . ".txt", "wb");
        $url = '/../public/catalogue-import-' . date('d-m-y-H_i') . '.txt';
        //dump($url);die;
        $request->request->set('_sonata_admin', 'admin.template');
        if (isset($_POST["submit"])) {
            $tmp_name = $_FILES['catalogue']['tmp_name'];
            $arr_file = explode('.', $_FILES['catalogue']['name']);
            $extension = end($arr_file);

            if ('xls' == $extension) {
                $reader = new XlsReader();
                $spreadsheet = $reader->load($tmp_name);
                //$sheetData = $spreadsheet->getActiveSheet()->toArray();
                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow() - 1; // e.g. 10
                $highestColumn = $worksheet->getHighestColumn();
                fwrite($fp, $_FILES['catalogue']['name'] . "\n");

                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'File is valid, and was successfully uploaded.!');
                return $this->redirect($this->generateUrl('Catalogue_list'));
            } elseif ('xlsx' == $extension) {
                $reader = new XlsxReader();
                $spreadsheet = $reader->load($tmp_name);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow(); // e.g. 10
                $highestColumn = $worksheet->getHighestColumn();
                fwrite($fp, "File: " . $_FILES['catalogue']['name'] . "\n");
                fwrite($fp, "Number of Row : " . $highestRow . "\n");
                fwrite($fp, "Highest Column : " . $highestColumn . "\n");
                $em = $this->getDoctrine()->getManager()->getRepository(Catalogue::class);

                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                for ($row = 0; $row <= $highestRow; $row++) {
                    for ($col = 0; $col <= $highestColumnIndex; $col++) {
                        $value = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                        //array_push($res,$value);
                        $Species_catalogues = $em->findOneBy(['Species' => $value]);
                    }
                    if ($Species_catalogues) {
                        if ($Species_catalogues->getSpecies() == $value) {
                            //dump($row, $Species_catalogues->getSpecies() , $value);die;
                            //$request->getSession()->getFlashBag()->add('sucess',   $row.'-'.$Species_catalogues . '---to be Added' . '<br>');
                            fwrite($fp, $row . '-' . $value . '-Exist already' . "\n");
                        } else {
                            //dump($Species_catalogues->getId());die;
                            fwrite($fp, $row . '-' . $value . '-to be Added' . "\n");
                            //$request->getSession()->getFlashBag()->add('error',   $row.'-'.$Species_catalogues . '---to be Added' . '<br>');
                        }

                    }
                    //fwrite($fp, $row.'-'.$value . "\n");
                }


                fclose($fp);


                $request->getSession()->getFlashBag()->add('success', 'File is valid, and was successfully processed.!' . '<br>' . "<a href=" . $url . ">Log Link</a>");
                return $this->redirect($this->generateUrl('Catalogue_list'));
            } else {
                $request->getSession()
                    ->getFlashBag()
                    ->add('error', 'File is not valid.!');
                return $this->render('admin/import/importc.html.twig');
                //return $this->redirect($this->generateUrl('importcatalogue'));

            }


        } else {
            return $this->render('admin/import/importc.html.twig');
            //return $this->renderWithExtraParams('admin/catalogue/importc.html.twig');
        }

    }

    protected function createDataFromSpreadsheet($spreadsheet)
    {
        $data = [];
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $worksheetTitle = $worksheet->getTitle();
            $data[$worksheetTitle] = [
                'columnNames' => [],
                'columnValues' => [],
            ];
            foreach ($worksheet->getRowIterator() as $row) {
                $rowIndex = $row->getRowIndex();
                if ($rowIndex > 1) {
                    $data[$worksheetTitle]['columnValues'][$rowIndex] = [];
                }
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); // Loop over all cells, even if it is not set
                foreach ($cellIterator as $cell) {
                    if ($rowIndex === 0) {
                        $data[$worksheetTitle]['columnNames'][] = $cell->getCalculatedValue();
                    }
                    if ($rowIndex > 1) {
                        $data[$worksheetTitle]['columnValues'][$rowIndex][] = $cell->getCalculatedValue();
                    }
                }
            }
        }

        return $data;
    }
}
