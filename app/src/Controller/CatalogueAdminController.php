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

final class CatalogueAdminController extends CRUDController
{
    /**
     * @Route("catalogue/importcatalogue", name="importcatalogue")
     */

    public function importcatalogueAction(Request $request)
    {

        $session = $request->getSession();
        //$tmp_name = $_FILES['catalogue']['tmp_name'];
        //dump($request);die;
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/catalogue-import-" . date('d-m-y H:i:s') . ".txt", "wb");

        $request->request->set('_sonata_admin', 'admin.template');
        if (isset($_POST["submit"])) {
            $tmp_name = $_FILES['catalogue']['tmp_name'];
            //$destination = $this->getParameter('kernel.project_dir') . '/public/resources/catalogue/import/';
            //$uploadfile = $destination . basename($_FILES['catalogue']['name']);
            $arr_file = explode('.', $_FILES['catalogue']['name']);
            $extension = end($arr_file);

            if ('xls' == $extension) {
                $reader = new XlsReader();
                $spreadsheet = $reader->load($tmp_name);
                $sheetData = $spreadsheet->getActiveSheet()->toArray();
                $worksheet = $spreadsheet->getActiveSheet();
                // Get the highest row number and column letter referenced in the worksheet
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
                $sheetData = $spreadsheet->getActiveSheet()->toArray();
                $worksheet = $spreadsheet->getActiveSheet();
                // Get the highest row number and column letter referenced in the worksheet
                $highestRow = $worksheet->getHighestRow() - 1; // e.g. 10
                $highestColumn = $worksheet->getHighestColumn();
                //$highestColumn++;
                //dump($highestColumn);die;
                fwrite($fp, $_FILES['catalogue']['name'] . "\n");
                fwrite($fp, $highestRow . "\n");
                $em = $this->getDoctrine()->getManager();
                //dump($em);die;
                $catalogue = new Catalogue();
                for ($row = 1; $row <= $highestRow; ++$row) {
                    for ($col = 'A'; $col != $highestColumn; ++$col) {
                        $Species_catalogues = $em->getRepository(Catalogue::class)->findBy(array('Species' => $worksheet->getCell($col . $row)
                            ->getValue()));

                        foreach ($Species_catalogues as $S) {
                            if ($S->getID() != '') {
                                fwrite($fp, $worksheet->getCell($col . $row)
                                        ->getValue() . '-Exist already' . "\n");

                            } else {
                                $catalogue->setSpecies($worksheet->getCell($col . $row)
                                    ->getValue());
                                $catalogue->setStatus('Pending');
                                $em->prePersist($catalogue);
                                $em->flush();
                                $this->addFlash('success', 'Article Created! Knowledge is power!');
                                fwrite($fp, $worksheet->getCell($col . $row)
                                        ->getValue() . '- Already Addedd' . "\n");

                            }
                            fclose($fp);
                        }

                    }

                }


                //dump($highestRow,$highestColumn);die;

                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'File is valid, and was successfully uploaded.!');
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
