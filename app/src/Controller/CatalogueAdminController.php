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
                //$sheetData = $spreadsheet->getActiveSheet()->toArray();
                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow(); // e.g. 10
                $highestColumn = $worksheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5
                $highestColumn++;
                //dump($highestColumn);die;
                fwrite($fp, "File: " . $_FILES['catalogue']['name'] . "\n");
                fwrite($fp, "Number of Row : " . $highestRow . "\n");
                $em = $this->getDoctrine()->getManager();
                $catalogue = new Catalogue();
                foreach ($worksheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
                    //    even if a cell value is not set.
                    // By default, only cells that have a value
                    //    set will be iterated.
                    foreach ($cellIterator as $cell) {
                        $em = $this->getDoctrine()->getManager();
                        $Species_catalogues = $em->getRepository(Catalogue::class)
                            ->findbySpecies($cell->getValue());
                        $lastQuestion = $em->getRepository(Catalogue::class)->findOneBy([], ['id' => 'desc']);
                        $lastId = $lastQuestion->getId();
                        //dump($lastId);die;

                        foreach ($Species_catalogues as $s) {
                            //dump($Species_catalogues, $s->getID());die;
                            if ($s->getID() != '') {
                                //dump($s->getId());die;
                                fwrite($fp, $cell->getValue() . '-Exist already' . "\n");
                            }
                            if ($s->getID() == '') {
                                $em = $this->getDoctrine()->getManager();
                                $catalogue->setId($lastId + 1);
                                $catalogue->setSpecies($cell->getValue());
                                $catalogue->setStatus('Pending');
                                $em->persist($catalogue);
                                $em->flush();
                                fwrite($fp, $cell->getValue() . '- To be addedd' . "\n");
                                $this->addFlash('success', 'Article Created! Knowledge is power!');
                            }

                        }
                    }

                }
                fclose($fp);
                $request->getSession()->getFlashBag()->add('success', 'File is valid, and was successfully processed.!');
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
}
