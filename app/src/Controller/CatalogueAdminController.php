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
use App\Entity\Synonym;
use GuzzleHttp\Client;

//use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

//use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use UniqueConstraintViolationException;

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
        $url = '/catalogue-import-' . date('d-m-y-H_i') . '.txt';
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
                //$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow() - 1; // e.g. 10
                $highestRow1 = $worksheet->getHighestDataRow();
                //dd($highestRow1);
                $highestColumn = $worksheet->getHighestColumn();
                fwrite($fp, "File: " . $_FILES['catalogue']['name'] . "\n");
                fwrite($fp, "Number of Row : " . $highestRow . "\n");
                fwrite($fp, "Highest Column : " . $highestColumn . "\n");

                $list = [];
                $it = $worksheet->getRowIterator(2);
                foreach ($it as $row) {
                    $cellIt = $row->getCellIterator();
                    $cellIt->setIterateOnlyExistingCells(false);
                    $r = [];
                    foreach ($cellIt as $cell) {
                        $r[] = $cell->getValue();
                    }
                    $list[] = $r;
                }

                for ($i = 0, $iMax = count($list); $i < $iMax; $i++) {
                    //dd($list[$i]);
                }


                foreach ($list as $x) {

                    $em = $this->getDoctrine()->getManager()->getRepository(Catalogue::class);
                    $s = $em->findOneBy(['Species' => $x[0]]);

                    if ($s) {  // $s is set, so the species exists
                        //$request->getSession()->getFlashBag()->add('error', $x[0] . '--- Already Existing' . '<br>');
                        fwrite($fp, $x[0] . '--- Already Exist in the catalogue' . "\n");
                    } else {  // Not found
                        if ($x[0]) {
                            //dd($x[0]);
                            fwrite($fp, $x[0] . '--- Added to the catalogue' . "\n");
                            $em = $this->getDoctrine()->getManager();
                            $catalogue = new Catalogue();
                            $catalogue->setSpecies($x[0]);
                            $catalogue->setStatus('Not checked');
                            $em->persist($catalogue);
                            $em->flush();

                        }
                    }
                }

                $request->getSession()->getFlashBag()->add('success', 'File is valid, and was successfully processed.!' . '<br>' . "<a href=" . $url . ">Log Link</a>");
                fclose($fp);
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

    public function batchActionworms(ProxyQueryInterface $selectedModelQuery)
    {
        $this->admin->checkAccess('edit');
        $this->admin->checkAccess('delete');
        $modelManager = $this->admin->getModelManager();
        $selectedModels = $selectedModelQuery->execute();

        foreach ($selectedModels as $selectedModel) {
            $S = $selectedModel->getSpecies();
            //dump($s);die;
            $client = new Client(['http_errors' => false]);
            $Species = str_replace(' ', '%20', "$S");
            $client = new Client(['http_errors' => false]);
            $res = $client->request('GET', 'http://www.marinespecies.org/rest/AphiaRecordsByMatchNames?scientificnames%5B%5D=' . $Species . '&marine_only=true');
            $code = $res->getStatusCode();
            $body = $res->getBody()->getContents();
            $d = (array)json_decode($body, true);
            if ('204' == $code or '400' == $code) {
                $selectedModel->setRefTax('');
                $selectedModel->setStatus('Pending');
                $this->getRequest()->getSession()->getFlashBag()->add('error', 'Nothing found or Bad or missing properties in WoRMS, for <i>' . $S . '</i>, Batch Action Stopped');
            } else {
                $AphiaID = $d[0][0]['AphiaID'];
                //classification
                $res2 = $client->request('GET', 'http://www.marinespecies.org/rest/AphiaRecordByAphiaID/' . $AphiaID);
                $body2 = $res2->getBody()->getContents();
                $data2 = json_decode($body2, true);
                //synonyms
                $res3 = $client->request('GET', 'http://www.marinespecies.org/rest/AphiaSynonymsByAphiaID/' . $AphiaID);
                $body3 = $res3->getBody()->getContents();
                $data3 = (array)json_decode($body3);
                //Itis ITIS Taxonomic Serial Number
                $resitis = $client->request('GET', 'http://www.marinespecies.org/rest/AphiaExternalIDByAphiaID/' . $AphiaID . '?type=tsn');
                $codeitis = $resitis->getStatusCode();
                //$bodyitis = $resitis->getBody()->getContents();
                $bodyitis = $resitis->getBody()->getContents();
                if (null != $bodyitis) {
                    $v = ['[', ']'];
                    $nitis = str_replace($v, '', $bodyitis);
                    $n = htmlentities($nitis, ENT_QUOTES | ENT_IGNORE, 'UTF-8');
                    $n1 = str_replace('&quot;', '', $n);
                    $selectedModel->setItisLink('https://www.itis.gov/servlet/SingleRpt/SingleRpt?search_topic=TSN&search_value=' . $n1 . '#null');
                } else {
                    //$n1 = null;
                    $selectedModel->setItisLink(null);
                }

                //Eol Encyclopedia of Life (EoL) page identifier
                $reseol = $client->request('GET', 'http://www.marinespecies.org/rest/AphiaExternalIDByAphiaID/' . $AphiaID . '?type=eol');
                $codeeol = $reseol->getStatusCode();
                $bodyeol = $reseol->getBody()->getContents();

                $v1 = ['[', ']'];
                $neol = str_replace($v1, '', $bodyeol);
                $n2 = htmlentities($neol, ENT_QUOTES | ENT_IGNORE, 'UTF-8');
                $n3 = str_replace('&quot;', '', $n2);

                //col Catologue of life
                $rescol = $client->request('GET', 'http://webservice.catalogueoflife.org/col/webservice?name=' . $Species . '&format=json');
                $codecol = $rescol->getStatusCode();
                $bodycol = $rescol->getBody()->getContents();
                $data4 = (array)json_decode($bodycol, true);

                if (0 != $data4['number_of_results_returned']) {
                    $uui = $data4['results'][0]['url'];
                    $selectedModel->setCoLlink($uui);
                    //dump($uui);die;
                } else {
                    $uui = null;
                    $selectedModel->setCoLlink($uui);
                }
                $entitymanager = $this->getDoctrine()->getManager();
                $jobs = $entitymanager->getRepository(Synonym::class)->findBy(['Catalogue' => $selectedModel->getId()]);
                //dump($jobs);die;
                foreach ($jobs as $job) {
                    $entitymanager->remove($job);
                }

                //$entitymanager->remove($jobs);
                $entitymanager->flush();
                foreach ($data3 as $a) {
                    //dump($a->scientificname);
                    $s = new Synonym();
                    //$se = $s->setSpeciesSynonym($a->scientificname . '(' . $a->unacceptreason . ')');

                    if (null == $a->unacceptreason) {
                        $se = $s->setSpeciesSynonym($a->scientificname . ' ' . $a->authority);
                    } else {
                        $se = $s->setSpeciesSynonym($a->scientificname . ' ' . $a->authority . ' (' . $a->unacceptreason . ')');
                    }

                    //$s = getSynonyms();
                    $selectedModel->addSynonym($se);
                    //$synonym []= $a->scientificname;
                }
                $pos = strpos($Species, '%20');
                $codespecies = $Species[0] . $Species[1] . $Species[2] . $Species[3] . $Species[$pos + 3] . $Species[$pos + 4] . $Species[$pos + 5];
                $selectedModel->setSpeciesCode(strtoupper($codespecies));
                $selectedModel->setAphia((int)$AphiaID);
                $selectedModel->setAuthority($data2['authority']);
                $selectedModel->setKingdom($data2['kingdom']);
                $selectedModel->setPhylum($data2['phylum']);
                $selectedModel->setClass($data2['class']);
                $selectedModel->setOrdersp($data2['order']);
                $selectedModel->setFamily($data2['family']);
                $selectedModel->setWormsUrl('http://www.marinespecies.org/aphia.php?p=taxdetails&id=' . $AphiaID);
                //$selectedModel->setItisLink('https://www.itis.gov/servlet/SingleRpt/SingleRpt?search_topic=TSN&search_value='.$n1.'#null');
                //$object->setItisLink('https://www.itis.gov/servlet/SingleRpt/SingleRpt?search_topic=TSN&search_value=' . $n3 . '#null');
                $selectedModel->setGBIFlink('https://www.gbif.org/species/search?q=' . $Species . '&rank=SPECIES&name_type=SCIENTIFIC&qField=SCIENTIFIC&status=ACCEPTED&advanced=1');
                $href = 'http://www.marinespecies.org/aphia.php?p=taxlist&tName=' . $Species;

                $selectedModel->setRefTax('WoRMS');
                $selectedModel->setStatus('Validated');
                $modelManager->update($selectedModel);
                $this->addFlash('sonata_flash_success', 'The Classification of <i>' . $S . '</i> with AphiaID : ' . $AphiaID . ' is retrieved from WoRMS <br>');
            }
        }

        return new RedirectResponse(
            $this->admin->generateUrl('list', [
                'filter' => $this->admin->getFilterParameters(),
            ])
        );
    }

    public function manualAction()
    {
        $entitymanager = $this->getDoctrine()->getManager();
        $Aphia = '';
        $object = $this->admin->getSubject();
        $Species = '';
        $Aphia = (int)$object->getAphia();
        $S = $object->getSpecies();
        $Species = str_replace(' ', '%20', "$S");
        //dump($Species);die;
        $client = new Client(['http_errors' => false]);

        //classification
        $res2 = $client->request('GET', 'http://www.marinespecies.org/rest/AphiaRecordByAphiaID/' . $Aphia);
        $body2 = $res2->getBody()->getContents();
        $data2 = json_decode($body2, true);
        $code = $res2->getStatusCode();

        $pos = strpos($Species, '%20');
        if (null != $Species[$pos + 3] && $Species[$pos + 4] && $Species[$pos + 5]) {
            $codespecies = $Species[0] . $Species[1] . $Species[2] . $Species[3] . $Species[$pos + 3] . $Species[$pos + 4] . $Species[$pos + 5];
        } else {
            $codespecies = $Species[0] . $Species[1] . $Species[2] . $Species[3];
        }

        $object->setSpeciesCode(strtoupper($codespecies));
        //$object->setAphia($Aphia);
        $object->setAuthority($data2['authority']);
        $object->setKingdom($data2['kingdom']);
        $object->setPhylum($data2['phylum']);
        $object->setClass($data2['class']);
        $object->setOrdersp($data2['order']);
        $object->setFamily($data2['family']);
        $object->setWormsUrl('http://www.marinespecies.org/aphia.php?p=taxdetails&id=' . $Aphia);
        $object->setGBIFlink('https://www.gbif.org/species/search?q=' . $Species . '&rank=SPECIES&name_type=SCIENTIFIC&qField=SCIENTIFIC&status=ACCEPTED&advanced=1');
        $href = 'http://www.marinespecies.org/aphia.php?p=taxlist&tName=' . $Aphia;
        $object->setRefTax('WoRMS');
        $object->setStatus('Validated');

        //synonyms
        $res3 = $client->request('GET', 'http://www.marinespecies.org/rest/AphiaSynonymsByAphiaID/' . $Aphia);
        $body3 = $res3->getBody()->getContents();
        $data3 = (array)json_decode($body3);

        //Itis ITIS Taxonomic Serial Number
        $resitis = $client->request('GET', 'http://www.marinespecies.org/rest/AphiaExternalIDByAphiaID/' . $Aphia . '?type=tsn');
        $codeitis = $resitis->getStatusCode();
        //$bodyitis = $resitis->getBody()->getContents();
        $bodyitis = $resitis->getBody()->getContents();

        if (null != $bodyitis) {
            $v = ['[', ']'];
            $nitis = str_replace($v, '', $bodyitis);
            $n = htmlentities($nitis, ENT_QUOTES | ENT_IGNORE, 'UTF-8');
            $n1 = str_replace('&quot;', '', $n);
            //dump($n1);die;
            $object->setItisLink('https://www.itis.gov/servlet/SingleRpt/SingleRpt?search_topic=TSN&search_value=' . $n1 . '#null');
        } else {
            //$n1 = null;
            $object->setItisLink(null);
        }

        //Eol Encyclopedia of Life (EoL) page identifier
        $reseol = $client->request('GET', 'http://www.marinespecies.org/rest/AphiaExternalIDByAphiaID/' . $Aphia . '?type=eol');
        $codeeol = $reseol->getStatusCode();
        $bodyeol = $reseol->getBody()->getContents();

        $v1 = ['[', ']'];
        $neol = str_replace($v1, '', $bodyeol);
        $n2 = htmlentities($neol, ENT_QUOTES | ENT_IGNORE, 'UTF-8');
        $n3 = str_replace('&quot;', '', $n2);

        //col Catologue of life
        $rescol = $client->request('GET', 'http://webservice.catalogueoflife.org/col/webservice?name=' . $Species . '&format=json');
        $codecol = $rescol->getStatusCode();
        $bodycol = $rescol->getBody()->getContents();
        $data4 = (array)json_decode($bodycol, true);

        if (0 != $data4['number_of_results_returned']) {
            $uui = $data4['results'][0]['url'];
            //dump($uui);die;
        } else {
            $uui = null;
        }
        //Synonyms
        foreach ($object->getSynonyms() as $Synonyms) {
            $Synonyms->setCatalogue($object);
        }

        $jobs = $entitymanager->getRepository(Synonym::class)->findBy(['Catalogue' => $object->getId()]);
        //dump($jobs);die;
        foreach ($jobs as $job) {
            $entitymanager->remove($job);
        }

        //$entitymanager->remove($jobs);
        $entitymanager->flush();

        foreach ($data3 as $a) {
            try {
                $s = new Synonym();
                //$a->unacceptreason = null;
                if (null == $a->unacceptreason) {
                    $object->addSynonym($s->setSpeciesSynonym($a->scientificname . ' ' . $a->authority));
                } else {
                    $object->addSynonym($s->setSpeciesSynonym($a->scientificname . ' ' . $a->authority . ' (' . $a->unacceptreason . ')'));
                }
            } catch (UniqueConstraintViolationException $e) {
                continue;
            }

            $this->admin->preUpdate($object);

            $this->getRequest()->getSession()->getFlashBag()->add('success', "The Classification of <i>$S</i> with the AphiaID ($Aphia)  is retrieved from WoRMS <br>");

            //$entitymanager->persist($object);

            // Étape 2 : On « flush » tout ce qui a été persisté avant
            //$entitymanager->flush();
        }

        return new RedirectResponse(
            $this->admin->generateUrl('edit', ['id' => $object->getId()])
        );
    }
}
