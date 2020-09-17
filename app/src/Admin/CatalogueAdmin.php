<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Catalogue;
use App\Entity\Synonym;
use App\Entity\Synonyms;
use GuzzleHttp\Client;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use UniqueConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
final class CatalogueAdmin extends AbstractAdmin
{

    protected function configureRoutes(RouteCollection $collection)
    {

        parent::configureRoutes($collection);
        $collection->add('importcatalogue');

    }

    public function configureActionButtons($action, $object = null)
    {
        $list = parent::configureActionButtons($action, $object);

        $list['import']['template'] = 'admin/import/import_buttonc.html.twig';

        return $list;
    }

    public function getDashboardActions()
    {
        $actions = parent::getDashboardActions();

        $actions['import'] = array(
            'label' => 'Import',
            'url' => $this->generateUrl('importcatalogue'),
            'icon' => 'upload',
            'template' => 'SonataAdminBundle:CRUD:dashboard__action.html.twig', // optional
        );

        return $actions;
    }


    protected $perPageOptions = [10, 20, 50, 100, 'All'];
    protected $maxPerPage = '50';
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'Species',
        '_per_page' => '20',
    ];


    protected $baseRouteName = 'Catalogue';
    protected $classnameLabel = 'Non-Indigenous Species';

    public function getExportFormats()
    {
        return ['xls', 'xml', 'json'];
    }

    public function prePersist($object)
    {
        $Species = '';
        $Aphia = (int)$object->getAphia();
        $S = $object->getSpecies();

        $result = urlencode($S);
        $Species = str_replace('%C2%A0', '%20', $result);
        //dump($S,$result,$Species);die;
        $client = new Client(['http_errors' => false]);
        $res = $client->request(
            'GET',
            'http://www.marinespecies.org/rest/AphiaRecordsByMatchNames?scientificnames%5B%5D=' . $Species . '&marine_only=true'
        );
        $code = $res->getStatusCode();
        $body = $res->getBody()->getContents();
        $d = (array)json_decode($body, true);
        if ('204' == $code or '400' == $code) {
            $object->setRefTax('');
            $object->setStatus('Pending');
            $this->getRequest()->getSession()->getFlashBag()->add(
                'error',
                'Nothing found or Bad or missing properties in WoRMS, for <i>' . $S . '</i>'
            );
        } else {
            $AphiaID = $d[0][0]['AphiaID'];
            //classification
            $res2 = $client->request('GET', 'http://www.marinespecies.org/rest/AphiaRecordByAphiaID/' . $AphiaID);
            $body2 = $res2->getBody()->getContents();
            $data2 = json_decode($body2, true);
            $code = $res2->getStatusCode();
            //synonyms
            $res3 = $client->request(
                'GET',
                'http://www.marinespecies.org/rest/AphiaSynonymsByAphiaID/' . $AphiaID
            );
            $body3 = $res3->getBody()->getContents();
            $data3 = (array)json_decode($body3);

            //Itis ITIS Taxonomic Serial Number
            $resitis = $client->request(
                'GET',
                'http://www.marinespecies.org/rest/AphiaExternalIDByAphiaID/' . $AphiaID . '?type=tsn'
            );
            $codeitis = $resitis->getStatusCode();
            //$bodyitis = $resitis->getBody()->getContents();
            $bodyitis = $resitis->getBody()->getContents();

            if (null != $bodyitis) {
                $v = ['[', ']'];
                $nitis = str_replace($v, '', $bodyitis);
                $n = htmlentities($nitis, ENT_QUOTES | ENT_IGNORE, 'UTF-8');
                $n1 = str_replace('&quot;', '', $n);
                //dump($n1);die;
                $object->setItisLink(
                    'https://www.itis.gov/servlet/SingleRpt/SingleRpt?search_topic=TSN&search_value=' . $n1 . '#null'
                );
            } else {
                //$n1 = null;
                $object->setItisLink(null);
            }

            //Eol Encyclopedia of Life (EoL) page identifier
            $reseol = $client->request(
                'GET',
                'http://www.marinespecies.org/rest/AphiaExternalIDByAphiaID/' . $AphiaID . '?type=eol'
            );
            $codeeol = $reseol->getStatusCode();
            $bodyeol = $reseol->getBody()->getContents();

            $v1 = ['[', ']'];
            $neol = str_replace($v1, '', $bodyeol);
            $n2 = htmlentities($neol, ENT_QUOTES | ENT_IGNORE, 'UTF-8');
            $n3 = str_replace('&quot;', '', $n2);

            //col Catologue of life
            $rescol = $client->request(
                'GET',
                'http://webservice.catalogueoflife.org/col/webservice?name=' . $Species . '&format=json'
            );
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
            $entitymanager = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
            $jobs = $entitymanager->getRepository(Synonym::class)->findBy(['Catalogue' => $object->getId()]);
            //dump($jobs);die;
            foreach ($jobs as $job) {
                $entitymanager->remove($job);
            }
            foreach ($object->getSynonyms() as $Synonyms) {
                $Synonyms->setCatalogue($object);
            }
            //$entitymanager->remove($jobs);
            $entitymanager->flush();

            foreach ($data3 as $a) {
                //try {
                $s = new Synonym();
                //$a->unacceptreason = null;
                if (null == $a->unacceptreason) {
                    $object->addSynonym($s->setSpeciesSynonym($a->scientificname . ' ' . $a->authority));
                } else {
                    $object->addSynonym(
                        $s->setSpeciesSynonym(
                            $a->scientificname . ' ' . $a->authority . ' (' . $a->unacceptreason . ')'
                        )
                    );
                }
                //} catch (UniqueConstraintViolationException $e) {
                //   continue;
                //}
            }

            //flushing
            $pos = strpos($Species, '%20');
            if (null != $Species[$pos + 3] && $Species[$pos + 4] && $Species[$pos + 5]) {
                $codespecies = $Species[0] . $Species[1] . $Species[2] . $Species[3] . $Species[$pos + 3] . $Species[$pos + 4] . $Species[$pos + 5];
            } else {
                $codespecies = $Species[0] . $Species[1] . $Species[2] . $Species[3];
            }
            //$codespecies = $Species[0].$Species[1].$Species[2].$Species[3].$Species[$pos + 3].$Species[$pos + 4].$Species[$pos + 5];
            //dump ( $S,$Species, $pos, $codespecies);
            //die;
            //$object->setAphia((int)$body);
            $object->setSpeciesCode(strtoupper($codespecies));
            $object->setAphia($AphiaID);
            $object->setAuthority($data2['authority']);
            $object->setKingdom($data2['kingdom']);
            $object->setPhylum($data2['phylum']);
            $object->setClass($data2['class']);
            $object->setOrdersp($data2['order']);
            $object->setFamily($data2['family']);
            $object->setWormsUrl('http://www.marinespecies.org/aphia.php?p=taxdetails&id=' . $AphiaID);
            //$object->setCoLlink($uui);
            //$object->setItisLink('https://www.itis.gov/servlet/SingleRpt/SingleRpt?search_topic=TSN&search_value='.$n1.'#null');
            //$object->setEoLlink('' . $n3 );
            $object->setGBIFlink(
                'https://www.gbif.org/species/search?q=' . $Species . '&rank=SPECIES&name_type=SCIENTIFIC&qField=SCIENTIFIC&status=ACCEPTED&advanced=1'
            );
            $href = 'http://www.marinespecies.org/aphia.php?p=taxlist&tName=' . $AphiaID;

            if ('200' == $code) {
                $object->setRefTax('WoRMS');
                $object->setStatus('Validated');
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'success',
                    "The Classification of <i>$S</i> with the AphiaID ($AphiaID)  is retrieved from WoRMS <br>"
                );
            }
        }

        return new RedirectResponse($this->generateUrl('create'));
    }

    public function preUpdate($object)
    {
        if (null == $object->getAphia()) {
            $Species = '';
            //$Aphia = (int) $object->getAphia();
            $S = $object->getSpecies();
            $result = urlencode($S);
            $Species = str_replace('%C2%A0', '%20', $result);
            $client = new Client(['http_errors' => false]);
            $res = $client->request(
                'GET',
                'http://www.marinespecies.org/rest/AphiaRecordsByMatchNames?scientificnames%5B%5D=' . $Species . '&marine_only=true'
            );
            $code = $res->getStatusCode();
            $body = $res->getBody()->getContents();
            $d = (array)json_decode($body, true);
            if ('204' == $code or '400' == $code) {
                $object->setRefTax('');
                $object->setStatus('Pending');
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'error',
                    'Nothing found or Bad or missing properties in WoRMS, for <i>' . $S . '</i>'
                );
            } else {
                $AphiaID = $d[0][0]['AphiaID'];
                //dump($Species,$AphiaID);die;
                //classification
                $res2 = $client->request(
                    'GET',
                    'http://www.marinespecies.org/rest/AphiaRecordByAphiaID/' . $AphiaID
                );
                $body2 = $res2->getBody()->getContents();
                $data2 = json_decode($body2, true);
                $code = $res2->getStatusCode();
                //synonyms
                $res3 = $client->request(
                    'GET',
                    'http://www.marinespecies.org/rest/AphiaSynonymsByAphiaID/' . $AphiaID
                );
                $body3 = $res3->getBody()->getContents();
                $data3 = (array)json_decode($body3);

                //Itis ITIS Taxonomic Serial Number
                $resitis = $client->request(
                    'GET',
                    'http://www.marinespecies.org/rest/AphiaExternalIDByAphiaID/' . $AphiaID . '?type=tsn'
                );
                $codeitis = $resitis->getStatusCode();
                //$bodyitis = $resitis->getBody()->getContents();
                $bodyitis = $resitis->getBody()->getContents();

                if (null != $bodyitis) {
                    $v = ['[', ']'];
                    $nitis = str_replace($v, '', $bodyitis);
                    $n = htmlentities($nitis, ENT_QUOTES | ENT_IGNORE, 'UTF-8');
                    $n1 = str_replace('&quot;', '', $n);
                    //dump($n1);die;
                    $object->setItisLink(
                        'https://www.itis.gov/servlet/SingleRpt/SingleRpt?search_topic=TSN&search_value=' . $n1 . '#null'
                    );
                } else {
                    //$n1 = null;
                    $object->setItisLink(null);
                }

                //Eol Encyclopedia of Life (EoL) page identifier
                $reseol = $client->request(
                    'GET',
                    'http://www.marinespecies.org/rest/AphiaExternalIDByAphiaID/' . $AphiaID . '?type=eol'
                );
                $codeeol = $reseol->getStatusCode();
                $bodyeol = $reseol->getBody()->getContents();

                $v1 = ['[', ']'];
                $neol = str_replace($v1, '', $bodyeol);
                $n2 = htmlentities($neol, ENT_QUOTES | ENT_IGNORE, 'UTF-8');
                $n3 = str_replace('&quot;', '', $n2);

                //col Catologue of life
                $rescol = $client->request(
                    'GET',
                    'http://webservice.catalogueoflife.org/col/webservice?name=' . $Species . '&format=json'
                );
                $codecol = $rescol->getStatusCode();
                $bodycol = $rescol->getBody()->getContents();
                $data4 = (array)json_decode($bodycol, true);

                if (0 != $data4['number_of_results_returned']) {
                    $uui = $data4['results'][0]['url'];
                    //dump($uui);die;
                } else {
                    $uui = null;
                }

                foreach ($object->getSynonyms() as $Synonyms) {
                    $Synonyms->setCatalogue($object);
                }
                $entitymanager = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
                $jobs = $entitymanager->getRepository(Synonym::class)->findBy(['Catalogue' => $object->getId()]);
                //dump($jobs);die;
                foreach ($jobs as $job) {
                    $entitymanager->remove($job);
                }
                foreach ($object->getSynonyms() as $Synonyms) {
                    $Synonyms->setCatalogue($object);
                }
                //$entitymanager->remove($jobs);
                $entitymanager->flush();

                foreach ($data3 as $a) {
                    //try {
                    $s = new Synonym();
                    //$a->unacceptreason = null;
                    if (null == $a->unacceptreason) {
                        $object->addSynonym($s->setSpeciesSynonym($a->scientificname . ' ' . $a->authority));
                    } else {
                        $object->addSynonym(
                            $s->setSpeciesSynonym(
                                $a->scientificname . ' ' . $a->authority . ' (' . $a->unacceptreason . ')'
                            )
                        );
                    }
                    //} catch (UniqueConstraintViolationException $e) {
                    //   continue;
                    //}
                }
                //flushing
                $pos = strpos($Species, '%20');
                if (null != $Species[$pos + 3] && $Species[$pos + 4] && $Species[$pos + 5]) {
                    $codespecies = $Species[0] . $Species[1] . $Species[2] . $Species[3] . $Species[$pos + 3] . $Species[$pos + 4] . $Species[$pos + 5];
                } else {
                    $codespecies = $Species[0] . $Species[1] . $Species[2] . $Species[3];
                }
                //$codespecies = $Species[0].$Species[1].$Species[2].$Species[3].$Species[$pos + 3].$Species[$pos + 4].$Species[$pos + 5];
                //dump ( $S,$Species, $pos, $codespecies);
                //die;
                //$object->setAphia((int)$body);
                $object->setSpeciesCode(strtoupper($codespecies));
                $object->setAphia($AphiaID);
                $object->setAuthority($data2['authority']);
                $object->setKingdom($data2['kingdom']);
                $object->setPhylum($data2['phylum']);
                $object->setClass($data2['class']);
                $object->setOrdersp($data2['order']);
                $object->setFamily($data2['family']);
                $object->setWormsUrl('http://www.marinespecies.org/aphia.php?p=taxdetails&id=' . $AphiaID);
                //$object->setCoLlink($uui);
                //$object->setItisLink('https://www.itis.gov/servlet/SingleRpt/SingleRpt?search_topic=TSN&search_value='.$n1.'#null');
                //$object->setEoLlink('' . $n3 );
                $object->setGBIFlink(
                    'https://www.gbif.org/species/search?q=' . $Species . '&rank=SPECIES&name_type=SCIENTIFIC&qField=SCIENTIFIC&status=ACCEPTED&advanced=1'
                );
                $href = 'http://www.marinespecies.org/aphia.php?p=taxlist&tName=' . $AphiaID;

                if ('200' == $code) {
                    $object->setRefTax('WoRMS');
                    $object->setStatus('Validated');
                    $this->getRequest()->getSession()->getFlashBag()->add(
                        'success',
                        "The Classification of <i>$S</i> with the AphiaID ($AphiaID)  is retrieved from WoRMS <br>"
                    );
                }
            }

            return new RedirectResponse($this->generateUrl('create'));
        } else {
            $Species = '';
            $Aphia = (int)$object->getAphia();
            $S = $object->getSpecies();
            $result = urlencode($S);
            $Species = str_replace('%C2%A0', '%20', $result);
            $client = new Client(['http_errors' => false]);
            //$res = $client->request('GET', 'http://www.marinespecies.org/rest/AphiaRecordsByMatchNames?scientificnames%5B%5D='.$Species.'&marine_only=true');
            //$code = $res->getStatusCode();
            //$body = $res->getBody()->getContents();
            //$d = (array) json_decode($body, true);
            //if ('204' == $code or '400' == $code) {
            //    $object->setRefTax('');
            //    $object->setStatus('Pending');
            //    $this->getRequest()->getSession()->getFlashBag()->add('error', 'Nothing found or Bad or missing properties in WoRMS, for <i>'.$S.'</i>');
            //} else {
            //    $AphiaID = $d[0][0]['AphiaID'];
            //dump($Species,$AphiaID);die;
            //classification
            $res2 = $client->request('GET', 'http://www.marinespecies.org/rest/AphiaRecordByAphiaID/' . $Aphia);
            $body2 = $res2->getBody()->getContents();
            $data2 = json_decode($body2, true);
            $code = $res2->getStatusCode();
            //synonyms
            $res3 = $client->request('GET', 'http://www.marinespecies.org/rest/AphiaSynonymsByAphiaID/' . $Aphia);
            $body3 = $res3->getBody()->getContents();
            $data3 = (array)json_decode($body3);

            //Itis ITIS Taxonomic Serial Number
            $resitis = $client->request(
                'GET',
                'http://www.marinespecies.org/rest/AphiaExternalIDByAphiaID/' . $Aphia . '?type=tsn'
            );
            $codeitis = $resitis->getStatusCode();
            //$bodyitis = $resitis->getBody()->getContents();
            $bodyitis = $resitis->getBody()->getContents();

            if (null != $bodyitis) {
                $v = ['[', ']'];
                $nitis = str_replace($v, '', $bodyitis);
                $n = htmlentities($nitis, ENT_QUOTES | ENT_IGNORE, 'UTF-8');
                $n1 = str_replace('&quot;', '', $n);
                //dump($n1);die;
                $object->setItisLink(
                    'https://www.itis.gov/servlet/SingleRpt/SingleRpt?search_topic=TSN&search_value=' . $n1 . '#null'
                );
            } else {
                //$n1 = null;
                $object->setItisLink(null);
            }

            //Eol Encyclopedia of Life (EoL) page identifier
            $reseol = $client->request(
                'GET',
                'http://www.marinespecies.org/rest/AphiaExternalIDByAphiaID/' . $Aphia . '?type=eol'
            );
            $codeeol = $reseol->getStatusCode();
            $bodyeol = $reseol->getBody()->getContents();

            $v1 = ['[', ']'];
            $neol = str_replace($v1, '', $bodyeol);
            $n2 = htmlentities($neol, ENT_QUOTES | ENT_IGNORE, 'UTF-8');
            $n3 = str_replace('&quot;', '', $n2);

            //col Catologue of life
            $rescol = $client->request(
                'GET',
                'http://webservice.catalogueoflife.org/col/webservice?name=' . $Species . '&format=json'
            );
            $codecol = $rescol->getStatusCode();
            $bodycol = $rescol->getBody()->getContents();
            $data4 = (array)json_decode($bodycol, true);

            if (0 != $data4['number_of_results_returned']) {
                $uui = $data4['results'][0]['url'];
                //dump($uui);die;
            } else {
                $uui = null;
            }
            foreach ($object->getSynonyms() as $Synonyms) {
                $Synonyms->setCatalogue($object);
            }
            $entitymanager = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
            $jobs = $entitymanager->getRepository(Synonym::class)->findBy(['Catalogue' => $object->getId()]);
            //dump($jobs);die;
            foreach ($jobs as $job) {
                $entitymanager->remove($job);
            }
            foreach ($object->getSynonyms() as $Synonyms) {
                $Synonyms->setCatalogue($object);
            }
            //$entitymanager->remove($jobs);
            $entitymanager->flush();

            foreach ($data3 as $a) {
                //try {
                $s = new Synonym();
                //$a->unacceptreason = null;
                if (null == $a->unacceptreason) {
                    $object->addSynonym($s->setSpeciesSynonym($a->scientificname . ' ' . $a->authority));
                } else {
                    $object->addSynonym(
                        $s->setSpeciesSynonym(
                            $a->scientificname . ' ' . $a->authority . ' (' . $a->unacceptreason . ')'
                        )
                    );
                }
                //} catch (UniqueConstraintViolationException $e) {
                //   continue;
                //}
            }

            //flushing
            $pos = strpos($Species, '%20');
            if (null != $Species[$pos + 3] && $Species[$pos + 4] && $Species[$pos + 5]) {
                $codespecies = $Species[0] . $Species[1] . $Species[2] . $Species[3] . $Species[$pos + 3] . $Species[$pos + 4] . $Species[$pos + 5];
            } else {
                $codespecies = $Species[0] . $Species[1] . $Species[2] . $Species[3];
            }
            //$codespecies = $Species[0].$Species[1].$Species[2].$Species[3].$Species[$pos + 3].$Species[$pos + 4].$Species[$pos + 5];
            //dump ( $S,$Species, $pos, $codespecies);
            //die;
            //$object->setAphia((int)$body);
            $object->setSpeciesCode(strtoupper($codespecies));
            //$object->setAphia($AphiaID);
            $object->setAuthority($data2['authority']);
            $object->setKingdom($data2['kingdom']);
            $object->setPhylum($data2['phylum']);
            $object->setClass($data2['class']);
            $object->setOrdersp($data2['order']);
            $object->setFamily($data2['family']);
            $object->setWormsUrl('http://www.marinespecies.org/aphia.php?p=taxdetails&id=' . $Aphia);
            //$object->setCoLlink($uui);
            //$object->setItisLink('https://www.itis.gov/servlet/SingleRpt/SingleRpt?search_topic=TSN&search_value='.$n1.'#null');
            //$object->setEoLlink('' . $n3 );
            $object->setGBIFlink(
                'https://www.gbif.org/species/search?q=' . $Species . '&rank=SPECIES&name_type=SCIENTIFIC&qField=SCIENTIFIC&status=ACCEPTED&advanced=1'
            );
            $href = 'http://www.marinespecies.org/aphia.php?p=taxlist&tName=' . $Aphia;

            //if ('200' == $code) {
            $object->setRefTax('WoRMS');
            $object->setStatus('Validated');
            $this->getRequest()->getSession()->getFlashBag()->add(
                'success',
                "The Classification of <i>$S</i> with the AphiaID ($Aphia)  is retrieved from WoRMS <br>"
            );
            //}
        }

        return new RedirectResponse($this->generateUrl('create'));
    }

    public function manualAction()
    {
        $entitymanager = $this->getDoctrine()->getManager();
        $Aphia = '';
        $object = $this->admin->getSubject();
        $Species = '';
        $Aphia = (int)$object->getAphia();
        $S = $object->getSpecies();
        $result = urlencode($S);
        $Species = str_replace('%C2%A0', '%20', $result);
        $client = new Client(['http_errors' => false]);

        //classification
        $res2 = $client->request('GET', 'http://www.marinespecies.org/rest/AphiaRecordByAphiaID/' . $Aphia);
        $body2 = $res2->getBody()->getContents();
        $data2 = json_decode($body2, true);
        $code = $res2->getStatusCode();

        $pos = strpos($Species, '%20');
        if (null != $Species[$pos + 3] && $Species[$pos + 4] && $Species[$pos + 5]) {
            //$codespecies = $Species[0] . $Species[1] . $Species[2] . $Species[3] . $Species[$pos + 3] . $Species[$pos + 4] . $Species[$pos + 5];
        } else {
            //$codespecies = $Species[0] . $Species[1] . $Species[2] . $Species[3];
        }

        //$object->setSpeciesCode (strtoupper ($codespecies));
        //$object->setAphia($Aphia);
        $object->setAuthority($data2['authority']);
        $object->setKingdom($data2['kingdom']);
        $object->setPhylum($data2['phylum']);
        $object->setClass($data2['class']);
        $object->setOrdersp($data2['order']);
        $object->setFamily($data2['family']);
        $object->setWormsUrl('http://www.marinespecies.org/aphia.php?p=taxdetails&id=' . $Aphia);
        $object->setGBIFlink(
            'https://www.gbif.org/species/search?q=' . $Species . '&rank=SPECIES&name_type=SCIENTIFIC&qField=SCIENTIFIC&status=ACCEPTED&advanced=1'
        );
        $href = 'http://www.marinespecies.org/aphia.php?p=taxlist&tName=' . $Aphia;
        $object->setRefTax('WoRMS');
        $object->setStatus('Validated');

        //synonyms
        $res3 = $client->request('GET', 'http://www.marinespecies.org/rest/AphiaSynonymsByAphiaID/' . $Aphia);
        $body3 = $res3->getBody()->getContents();
        $data3 = (array)json_decode($body3);

        //Itis ITIS Taxonomic Serial Number
        $resitis = $client->request(
            'GET',
            'http://www.marinespecies.org/rest/AphiaExternalIDByAphiaID/' . $Aphia . '?type=tsn'
        );
        $codeitis = $resitis->getStatusCode();
        //$bodyitis = $resitis->getBody()->getContents();
        $bodyitis = $resitis->getBody()->getContents();

        if (null != $bodyitis) {
            $v = ['[', ']'];
            $nitis = str_replace($v, '', $bodyitis);
            $n = htmlentities($nitis, ENT_QUOTES | ENT_IGNORE, 'UTF-8');
            $n1 = str_replace('&quot;', '', $n);
            //dump($n1);die;
            $object->setItisLink(
                'https://www.itis.gov/servlet/SingleRpt/SingleRpt?search_topic=TSN&search_value=' . $n1 . '#null'
            );
        } else {
            //$n1 = null;
            $object->setItisLink(null);
        }

        //Eol Encyclopedia of Life (EoL) page identifier
        $reseol = $client->request(
            'GET',
            'http://www.marinespecies.org/rest/AphiaExternalIDByAphiaID/' . $Aphia . '?type=eol'
        );
        $codeeol = $reseol->getStatusCode();
        $bodyeol = $reseol->getBody()->getContents();

        $v1 = ['[', ']'];
        $neol = str_replace($v1, '', $bodyeol);
        $n2 = htmlentities($neol, ENT_QUOTES | ENT_IGNORE, 'UTF-8');
        $n3 = str_replace('&quot;', '', $n2);

        //col Catologue of life
        $rescol = $client->request(
            'GET',
            'http://webservice.catalogueoflife.org/col/webservice?name=' . $Species . '&format=json'
        );
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
                    $object->addSynonym(
                        $s->setSpeciesSynonym(
                            $a->scientificname . ' ' . $a->authority . ' (' . $a->unacceptreason . ')'
                        )
                    );
                }
            } catch (UniqueConstraintViolationException $e) {
                continue;
            }

            $this->admin->preUpdate($object);

            $this->getRequest()->getSession()->getFlashBag()->add(
                'success',
                "The Classification of <i>$S</i> with the AphiaID ($Aphia)  is retrieved from WoRMS <br>"
            );

            //$entitymanager->persist($object);

            // Étape 2 : On « flush » tout ce qui a été persisté avant
            //$entitymanager->flush();
        }

        return new RedirectResponse(
            $this->admin->generateUrl('edit', ['id' => $object->getId()])
        );
    }

    public function getBatchActions()
    {
        // retrieve the default (currently only the delete action) actions
        $actions = parent::getBatchActions();
        // check user permissions
        if ($this->hasRoute('edit') && $this->isGranted('EDIT') && $this->hasRoute(
                'delete'
            ) && $this->isGranted('DELETE')) {
            $actions['worms'] = [
                'label' => 'Check',
                'ask_confirmation' => false, // If true, a confirmation will be asked before performing the action
            ];
        }

        return $actions;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('Species', null, ['label' => 'Species Name', 'show_filter' => true])
            ->add('kingdom', null, ['label' => 'Kingdom'])
            ->add('phylum', null, ['label' => 'Phylum'])
            ->add('class', null, ['label' => 'Class'])
            ->add('ordersp', null, ['label' => 'Order'])
            ->add('family', null, ['label' => 'Family'])
            ->add(
                'status',
                null,
                ['label' => 'Status in Catalogue ', 'show_filter' => true],
                ChoiceType::class,
                [
                    'choices' => [
                        'rejected' => 'Rejected',
                        'pending' => 'Pending',
                        'validated' => 'Validated',
                        'Not checked' => 'Not checked'
                    ],
                ]
            );
    }

    protected function configureListFields(ListMapper $listMapper): void
    {

        $listMapper
            ->add('id', IntegerType::class, ['label' => 'ID',
                'header_style' => 'width: 3%', 'template' => 'admin/catalogue/id.html.twig'])
            ->add('Species', null, ['label' => 'Species Name',
                'template' => 'admin/catalogue/species.html.twig'])
            //->add('speciesCode', null, array('label' => 'Species Code'))
            //->add('authority', null, ['label' => 'Authority'])
            //->add('Aphia',null, array('label' => 'Aphia Code'))
            ->add('WormsUrl', null, ['label' => 'WoRMS link',
                'template' => 'admin/catalogue/link.html.twig', 'header_style' => 'width: 3%'])
            ->add('kingdom', null, ['label' => 'Kingdom', 'header_style' => 'width: 3%'])
            ->add('phylum', null, ['label' => 'Phylum', 'header_style' => 'width: 3%'])
            ->add('class', null, ['label' => 'Class', 'header_style' => 'width: 3%'])
            ->add('ordersp', null, ['label' => 'Order', 'header_style' => 'width: 3%'])
            ->add('family', null, ['label' => 'Family', 'header_style' => 'width: 3%'])
            ->add(
                'status',
                null,
                ['label' => 'Catalogue Status',
                    'template' => 'admin/catalogue/status.html.twig', 'header_style' => 'width: 3%']
            )
            //->add('refTax',null, array('label' => 'Taxonomic Reference'))
            ->add(
                '_action',
                null,
                [
                    'actions' => [
                        'show' => [],
                        'edit' => [],
                        'delete' => [],

                    ],
                ]
            );
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $default = ['status' => 'Pending'];

        $formMapper
            ->tab('Non-Indigenous Species (NIS)')
            ->with('Species Details', ['class' => 'col-md-6'])->end()
            ->with('Taxonomy', ['class' => 'col-md-6'])->end()
            ->with('Synonyms', ['class' => 'col-md-6'])->end()
            //->with('Versionning', ['class' => 'col-md-12'])->end()
            ->end();

        $formMapper
            //->add('id')
            ->tab('Non-Indigenous Species (NIS)')
            ->with('Species Details', ['class' => 'col-md-5'])
            ->add('Species', null, ['label' => 'Species Name'])
            ->add('Aphia', null, ['label' => 'Aphia Code'])
            ->add('WormsUrl', null, ['label' => 'WoRMS link'])
            ->add(
                'status',
                ChoiceType::class,
                [
                    'choices' => [
                        '' => 'Choose an option',
                        'Rejected' => 'Rejected',
                        'Pending' => 'Pending',
                        'Validated' => 'Validated'
                    ],
                    'label' => 'Catalogue Status'
                ]
            )
            ->add('itisLink', null, ['label' => 'Itis Link'])
            ->add('CoLlink', null, ['label' => 'CoL Link'])
            ->add('EoLlink', null, ['label' => 'EoL Link'])
            ->add('GBIFlink', null, ['label' => 'GBIF Link'])
            ->add('catalogue_notes', TextareaType::class, ['label' => 'Notes', 'required' => false])
            ->end()
            ->with('Taxonomy', ['class' => 'col-md-2'])
            ->add('authority', null, ['label' => 'Authority'])
            ->add('kingdom', null, ['label' => 'Kingdom'])
            ->add('phylum', null, ['label' => 'Phylum'])
            ->add('class', null, ['label' => 'Class'])
            ->add('ordersp', null, ['label' => 'Order'])
            ->add('family', null, ['label' => 'Family'])
            ->end()
            ->with('Synonyms', ['class' => 'col-md-5'])
            ->add(
                'Synonyms',
                CollectionType::class,
                [
                    'label' => 'Synonymised names',
                    'required' => false,
                    'by_reference' => false,
                ],
                [
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'id',
                    'allow_add' => false,
                    'allow_delete' => true,
                ]
            )
            ->end()
            ->end()
            ->tab('logs')
            ->with('Versionning', ['class' => 'col-md-6'])
            ->add(
                'createdAt',
                DatePickerType::class,
                ['label' => 'Created At'],
                [
                    'dp_side_by_side' => true,
                    'dp_use_current' => true,
                    'dp_collapse' => true,
                    'dp_calendar_weeks' => false,
                    'dp_view_mode' => 'days',
                    'dp_min_view_mode' => 'days',
                ]
            )
            ->add(
                'updatedAt',
                DatePickerType::class,
                ['label' => 'update At'],
                [
                    'dp_side_by_side' => true,
                    'dp_use_current' => true,
                    'dp_collapse' => true,
                    'dp_calendar_weeks' => false,
                    'dp_view_mode' => 'days',
                    'dp_min_view_mode' => 'days',
                ]
            )
            ->end()
            ->end();
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->with('Species Details', ['class' => 'col-md-6'])
            ->add('Species', null, ['label' => 'Species Name'],
                ['template' => 'admin/catalogue/post.html.twig'])
            //->add ('speciesCode', null, ['label' => 'Species Code'])
            ->add('Aphia', null, ['label' => 'Aphia Code'])
            ->add('WormsUrl', null, ['label' => 'WoRMS link'])
            ->add('itislink', null, ['label' => 'ITIS link'])
            ->add('CoLlink', null, ['label' => 'CoL link'])
            ->add('GBIFlink', null, ['label' => 'GBIF link'])
            ->add('status', null, ['label' => 'Status'])
            ->add('refTax', null, ['label' => 'Taxonomic Reference'])
            ->add('createdAt', null, ['label' => 'Created At'])
            ->add('updatedAt', null, ['label' => 'update At'])
            ->end()
            ->with('Taxonomy', ['class' => 'col-md-6'])
            ->add('authority', null, ['label' => 'Authority'])
            ->add('kingdom', null, ['label' => 'Kingdom'])
            ->add('phylum', null, ['label' => 'Phylum'])
            ->add('class', null, ['label' => 'Class'])
            ->add('ordersp', null, ['label' => 'Order'])
            ->add('family', null, ['label' => 'Family'])
            //->add('Synonym', null, ['label' => 'Synonyms'])
            ->end();;
    }

}
