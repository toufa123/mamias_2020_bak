<?php

namespace App\Controller;

use App\Entity\GeoOccurence;
use App\Entity\Mamias;
use App\Entity\Country;
use App\Form\GeoOccurenceType;
use CrEOF\Geo\WKT\Parser;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Xlsxwriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Column;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\Session\Session;

class SpeciesDeclarationController extends Controller
{
    private $mailer;
    private $templating;

    public function __construct(Swift_Mailer $mailer, EngineInterface $templating, FlashBagInterface $flash)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->flash = $flash;
    }

    /**
     * @Route("/species/declaration", name="declaration")
     */
    public function reports()
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();
        //dump($user);die;
        if ('anon.' != $user) {
            $userId = $user->getId();
            $species = $this->getDoctrine()->getRepository(GeoOccurence::class)->findBy(
                array('user' => $userId)
            );

            //dump($species);die;
            return $this->render(
                'declaration/ListOccurences.html.twig',
                [
                    'controller_name' => 'SpeciesDeclarationController',
                    'species' => $species,
                ]
            );
        } else {
            return new RedirectResponse($this->generateUrl('sonata_user_admin_security_login'));
            //return $this->forward ($this->generateUrl ('sonata_user_admin_security_login'));
        }
    }

    /**
     * @Route("/species/declare", name="species_declaration")
     */
    public function declare(Request $request, Swift_Mailer $mailer)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('sonata_user_admin_security_login');
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();
        $useremail = $user->getEmail();
        //dump($a);die;
        $declaration = new GeoOccurence();
        $form = $this->createForm(GeoOccurenceType::class, $declaration);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $data = $form->getData();
            $dec = $data->getMamias();
            $declaration->setMamias($data->getMamias());
            $declaration->setCountry($data->getCountry());
            $declaration->setDepth($data->getDepth());
            $declaration->setPlantsAnimals($data->getPlantsAnimals());
            $declaration->setNvalues($data->getNvalues());
            $declaration->setEstimatedMeasured($data->getEstimatedMeasured());
            $parser = new Parser('Point(' . $data->getLocation() . ')');
            $geo = $parser->parse();
            $g = new Point($geo['value'], '4326');
            $declaration->setLocation($g);
            $declaration->setDateOccurence($data->getDateOccurence());
            $declaration->setNoteOccurence($data->getNoteOccurence());
            $declaration->setCreatedAt($data->getCreatedAt());
            $declaration->setUpdatedAt($data->getUpdatedAt());
            $declaration->setUser($this->getUser());
            $declaration->setImageFile($data->getImageFile());
            $declaration->setStatus('Submitted');
            $em->persist($declaration);
            $em->flush();
            //$this->get('session')->getFlashBag()->add(
            //  'notice',
            //'Customer Added!'
            //);
            //$this->addFlash('success', 'Article Created! Knowledge is power!');

            $message = (new Swift_Message())
                ->embed(\Swift_Image::fromPath('public/resources/logo/Logo-Mamias-web.png'))
                //$message = \Swift_Message::newInstance()
                ->setSubject('NIS Occurence reporting')
                ->setFrom(['mamias@no-reply.com' => 'MAMIAS Team'])
                ->setTo($useremail)
                ->setBcc(['atef.ouerghi@spa-rac.org', 'atef.ouerghi@gmail.com'])
                ->setBody(
                    $this->renderView('declaration/sendemail.html.twig', ['User' => $user, 'dec' => $dec]),
                    'text/html'
                )
                ->addPart(
                    'Hi ' . $user . ' ! Your report of the occurencce of ' . $dec . ' is successfully
                        Submitted on' . $data->getCreatedAt()->format('d-m-Y') .
                    '<br>We will get back to you soon!<br>Thanks!',
                    'text/plain'
                );

            //$transporter = new Swift_SmtpTransport('smtp.gmail.com', 587, 'tls');
            //$transporter->setAuthMode ('login')
            //->setUsername ('mamias2020@gmail.com')
            //->setPassword ('MAMIAS2019')
            //->setStreamOptions (
            //  [
            //    'ssl' => [
            //      'allow_self_signed' => true,
            //     'verify_peer' => false,
            // ],
            // ]
            //);
            //$transporter->setLocalDomain ('[127.0.0.1]');

            //$mailer = new Swift_Mailer($transporter);

            $email = (new Swift_Message('NIS Occurence reporting'))
                //$message = \Swift_Message::newInstance()
                //->setSubject ($subject)
                ->setFrom(['no-reply@mamias.org' => 'MAMIAS team'])
                //->setFrom ('no-reply@mamias.org')
                ->setTo('atef.ouerghi@spa-rac.org')
                //->setBcc (['mamias2020@gmail.com' => 'MAMIAS team'])
                ->setBody(
                    $this->renderView('contact/sendReportingnotification.html.twig'),
                    'text/html'
                )
                ->addPart(
                    'Hi a NIS Occurence reporting was added', 'text/plain');

            $mailer->send($message);
            //$this->container->get('mailer')->send($message);
            //$this->get('mailer')->send($message);

            //$this->flash->add('success', 'Occurence reported! Knowledge is power!');
            return $this->redirectToRoute('declaration');
        }

        return $this->render('declaration/declare.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/species/import", name="import")
     */
    public function Import(Request $request)
    {

        $session = $request->getSession();
        $tmp_name = $_FILES['file']['tmp_name'];
        $destination = $this->getParameter('kernel.project_dir') . '/public/upload/import/';
        $uploadfile = $destination . basename($_FILES['file']['name']);
        $arr_file = explode('.', $_FILES['file']['name']);
        $extension = end($arr_file);

        if ('xls' == $extension) {
            $reader = new Xls();
        } elseif ('xlsx' == $extension) {
            $reader = new XlsxReader();
        } else {
            return $this->redirect($this->generateUrl('import'));

        }
        if (move_uploaded_file($tmp_name, $uploadfile)) {

            $spreadsheet = $reader->load($uploadfile);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();
            dump($sheetData);
            die;

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'File is valid, and was successfully uploaded.!');
            return $this->redirect($this->generateUrl('declaration'));

        } else {
            $request->getSession()
                ->getFlashBag()
                ->add('error', 'File is not valid.!');
            return $this->redirect($this->generateUrl('import'));
        }

        //dump($_FILES);die;


    }

    /**
     * @Route("/species/template", name="template")
     */
    public function Template(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('MAMIAS web App')
            ->setLastModifiedBy('MAMIAS Team')
            ->setTitle('MAMIAS GeoOccurences Template')
            ->setSubject('MAMIAS GeoOccurences Template')
            ->setDescription('MAMIAS GeoOccurences Template')
            ->setKeywords('MAMIAS, GeoOccurences, Template');


        /* @var $sheet1 \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $sheet1 = $spreadsheet->getActiveSheet();
        //$sheet1->setCellValue('A1', 'ID');
        //$sheet1->getColumnDimension('A');
        $sheet1->setCellValue('A1', 'Country');
        $sheet1->getColumnDimension('A')
            ->setAutoSize(false)->setWidth(30);
        $sheet1->setCellValue('B1', 'NIS Species');
        $sheet1->getColumnDimension('B')
            ->setAutoSize(false);
        //->setWidth(30);
        $sheet1->setCellValue('C1', 'Reporting Date');
        $sheet1->getColumnDimension('C')
            ->setAutoSize(true);
        $sheet1->getStyle('C:C')
            ->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

        $sheet1->setCellValue('D1', 'Latitude');
        $sheet1->getColumnDimension('D')
            ->setAutoSize(true);
        $sheet1->getStyle('D:D')
            ->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
        $sheet1->setCellValue('E1', 'Longitude');
        $sheet1->getColumnDimension('E')
            ->setAutoSize(true);
        $sheet1->getStyle('E:E')
            ->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
        $sheet1->setCellValue('G1', 'Coverage/Nb of Inviduals');
        $sheet1->getColumnDimension('G')->setAutoSize(true);
        $sheet1->setCellValue('F1', 'Values');
        $sheet1->getColumnDimension('F')->setAutoSize(true);
        $sheet1->setCellValue('H1', 'Depth(m)');
        $sheet1->getColumnDimension('H')->setAutoSize(true);

        $sheet1->setCellValue('I1', 'Accuracy');
        $sheet1->getColumnDimension('I')
            ->setAutoSize(true);
        $sheet1->setCellValue('J1', 'Note');
        $sheet1->getColumnDimension('J')
            ->setAutoSize(false)->setWidth(50);
        $sheet1->setTitle("GeoOccurences");
        $sheet1->getActiveCell('A2');
        $sheet1->getStyle('c:c')
            ->getNumberFormat()->setFormatCode('dd/mm/yyyy');

        $sheet1->setSelectedCell('A2');


        $em = $this->getDoctrine()->getManager();
        $species = $em->getRepository(Mamias::class)->findBy(array(), array('relation' => 'ASC'));
        $country = $em->getRepository(Country::class)->findBy(array(), array('country' => 'ASC'));

        // NIS List sheet
        //$spreadsheet->createSheet();
        // Zero based, so set the second tab as active sheet
        //$sheet2= $spreadsheet->setActiveSheetIndex(1);
        //$spreadsheet->getActiveSheet()
        //    ->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
        //$spreadsheet->setActiveSheetIndex(1);
        //$spreadsheet->getActiveSheet()->setTitle('NIS');
        $sheet2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'NIS List');
        $spreadsheet->addSheet($sheet2, 2);

        $sheet2->setCellValue('A1', 'ID');
        $sheet2->getColumnDimension('A')->setAutoSize(true);
        //$sheet2->getColumnDimension ('A')->setVisible (false);
        $sheet2->setCellValue('B1', 'NIS');
        $sheet2->getColumnDimension('B')
            ->setAutoSize(true);
        //$sheet2->getColumnDimension ('B')->setVisible (false);


        $row = 2;
        foreach ($species as $e) {
            //$spreadsheet->setActiveSheetIndex(1)
            $sheet2
                ->setCellValue('A' . $row, $e->getRelation()->getId())
                ->setCellValue('B' . $row, $e->getRelation()->getSpecies());
            $row++;
        }


        // Country List sheet
        //$spreadsheet->createSheet();
        // Zero based, so set the second tab as active sheet
        //    ->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
        //$spreadsheet->setActiveSheetIndex(2);
        //$spreadsheet->getActiveSheet()->setTitle('Countries');


        $sheet3 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'country');
        $spreadsheet->addSheet($sheet3, 3);

        $sheet3->setCellValue('A1', 'ID');
        //$sheet3->getColumnDimension ('A')->setVisible (false);
        $sheet3->getColumnDimension('A')->setAutoSize(true);
        $sheet3->setCellValue('B1', 'Country');
        $sheet3->getColumnDimension('B')->setAutoSize(true);
        //$sheet3->getColumnDimension ('B')->setVisible (false);
        $row2 = 2;
        foreach ($country as $c) {
            $sheet3
                ->setCellValue('A' . $row2, $c->getId())
                ->setCellValue('B' . $row2, $c->getCountry());
            $row2++;
        }

        $spreadsheet->setActiveSheetIndex(0);

        $col_count = 10000;
        for ($i = 2; $i <= $col_count; $i++) {
            //Country List
            $validation = $spreadsheet->getActiveSheet()->getCell('A' . $i)->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Input error');
            $validation->setError('Value is not in list.');
            $validation->setPromptTitle('Pick from list');
            $validation->setPrompt('Please pick a value from the drop-down list.');
            $validation->setShowDropDown('true');
            $validation->setFormula1('\'country\'!$B$2:$B$' . $row2);
            $sheet1->getColumnDimension('B')->setWidth(20);

            //NIS List
            $validation1 = $spreadsheet->getActiveSheet()->getCell('B' . $i)->getDataValidation();
            $validation1->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation1->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $validation1->setAllowBlank(false);
            $validation1->setShowInputMessage(true);
            $validation1->setShowErrorMessage(true);
            $validation1->setShowDropDown(true);
            $validation1->setErrorTitle('Input error');
            $validation1->setError('Value is not in list.');
            $validation1->setPromptTitle('Pick from list');
            $validation1->setPrompt('Please pick a value from the drop-down list.');
            $validation1->setShowDropDown('true');

            $validation1->setFormula1('\'NIS List\'!$B$2:$B$' . $row);
            $sheet1->getColumnDimension('B')->setWidth(30);

            $validation2 = $spreadsheet->getActiveSheet()->getCell('G' . $i)
                ->getDataValidation();
            $validation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $validation2->setAllowBlank(false);
            $validation2->setShowInputMessage(true);
            $validation2->setShowErrorMessage(true);
            $validation2->setShowDropDown(true);
            $validation2->setErrorTitle('Input error');
            $validation2->setError('Value is not in list.');
            $validation2->setPromptTitle('Pick from list');
            $validation2->setPrompt('Please pick a value from the drop-down list.');
            $validation2->setFormula1('"Individuals, m2"');

            $validation3 = $spreadsheet->getActiveSheet()->getCell('I' . $i)
                ->getDataValidation();
            $validation3->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation3->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $validation3->setAllowBlank(false);
            $validation3->setShowInputMessage(true);
            $validation3->setShowErrorMessage(true);
            $validation3->setShowDropDown(true);
            $validation3->setErrorTitle('Input error');
            $validation3->setError('Value is not in list.');
            $validation3->setPromptTitle('Pick from list');
            $validation3->setPrompt('Please pick a value from the drop-down list.');
            $validation3->setFormula1('"Estimated, Measured"');

        }

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsxwriter($spreadsheet);
        $date = new \DateTime('now');
        $name = $date->format('d-m-yy');

        // Create a Temporary file in the system
        $fileName = 'Template_geo_MAMIAS_' . $name . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);

        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

    }
}
