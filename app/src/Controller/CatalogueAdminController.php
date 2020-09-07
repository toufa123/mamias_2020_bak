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
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                //dump($sheetData);die;
                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow(); // e.g. 10
                $highestColumn = $worksheet->getHighestColumn();
                //$reader->setReadDataOnly(true); $reader->setReadEmptyCells(false);
                //$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5
                //$highestColumn++;
                //dump($highestColumn);die;
                fwrite($fp, "File: " . $_FILES['catalogue']['name'] . "\n");
                fwrite($fp, "Number of Row : " . $highestRow . "\n");
                $em = $this->getDoctrine()->getManager();
                array_shift($sheetData);
                $i = 0;
                $test_array = array();


                foreach ($sheetData as $key => $val) {
                    if ($i < $highestRow)
                        $test_array[$i] = $val;
                    $repository = $this->admin->getConfigurationPool()->getContainer()->get('doctrine')->getManager()->getRepository(Catalogue::class);
                    $Species_catalogues = $repository->findOneBy(array('Species' => $val['A']));

                    //$Species =  $Species_catalogues->getSpecies();
                    //dump($Species_catalogues);die;
                    $em = $this->getDoctrine()->getManager();

                    if (!$repository->findOneBy(array('Species' => $val['A']))) {
                        dump($Species_catalogues, $val['A'], $repository->findOneBy(array('Species' => $val['A'])));
                        die;
                        fwrite($fp, $val['A'] . '-Exist already' . "\n");


                    } else {
                        fwrite($fp, $val['A'] . '-to be Added' . "\n");
                        $this->addFlash('error', $val['A'] . '---to be Added' . '<br>');
                    }


                    $i++;
                    //dump($Species_catalogues,$Species_catalogues->getID() );die;
                }

                fclose($fp);
                $request->getSession()->getFlashBag()->add('success', 'File is valid, and was successfully processed.!' . '<br>' . 'heref');
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
