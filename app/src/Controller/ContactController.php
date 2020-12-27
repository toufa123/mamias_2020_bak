<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\NotificationEmail;


class ContactController extends AbstractController
{
    private $mailer;
    private $templating;

    public function __construct(Swift_Mailer $mailer, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * @Route("/contact", name="contact",  options={"sitemap" = true})
     */
    public function index(Request $request, Swift_Mailer $mailer)
    {
        $pagetitle = 'Contact US';
        $contact = new Contact();

        // Add form fields

        $form = $this->createForm(ContactType::class);
        // Handle form and recaptcha response
        $form->handleRequest($request);

        // check if form is submitted and Recaptcha response is success
        if ($form->isSubmitted() && $form->isValid()) {
            //$this->addFlash('success', 'Article Created! Knowledge is power!');
            $FirstName = $form['FirstName']->getData();
            $LastName = $form['LastName']->getData();
            $email = $form['email']->getData();
            $subject = $form['subject']->getData();
            $message = $form['message']->getData();

            // set form data
            $contact->setFirstName($FirstName);
            $contact->setLastName($LastName);
            $contact->setEmail($email);
            $contact->setSubject($subject);
            $contact->setMessage($message);

            // finally add data in database
            $sn = $this->getDoctrine()->getManager();
            $sn->persist($contact);
            $sn->flush();


            //$mailer = new Swift_Mailer($transport);
            try {
                //$img = $message->embed(\Swift_Image::fromPath('resources/logo/Logo-Mamias-web.png'));
                $message = (new Swift_Message())
                    //$message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    //->setFrom(['no-reply@mamias.org' => 'MAMIAS team'])
                    ->setFrom('no-reply@mamias.org')
                    ->setTo($email)
                    //->setBcc (['mamias2020@gmail.com' => 'MAMIAS team'])
                    ->setBody(
                        $this->renderView('contact/sendemail.html.twig', ['LastName' => $LastName]),
                        'text/html'
                    )
                    ->addPart(
                        'Hi ' . $LastName . '! Your Message is successfully Submitted.We will get back to you soon!
                            
Thanks.

MAMIAS Team',
                        'text/plain'
                    );


                //$transport = (new Swift_SmtpTransport('127.0.0.1', 25))
                //   ->setUsername('NULL')
                //    ->setPassword('NULL')
                //;
                //$mailer = new Swift_Mailer($transport);

                //$transporter = new Swift_SmtpTransport('smtp.gmail.com', 587, 'tls');
                //$transporter->setAuthMode ('login')
                //->setUsername ('mamias2020@gmail.com')
                //->setPassword ('MAMIAS2019')
                //->setStreamOptions (['ssl' => ['allow_self_signed' => true, 'verify_peer' => false]]);
                //$transporter->setLocalDomain ('[127.0.0.1]');
                //$mailer = new Swift_Mailer($transporter);
                $email = (new Swift_Message('Contact Message'))
                    //$message = \Swift_Message::newInstance()
                    //->setSubject ($subject)
                    ->setFrom(['no-reply@mamias.org' => 'MAMIAS team'])
                    //->setFrom ('no-reply@mamias.org')
                    ->setTo('atef.ouerghi@spa-rac.org')
                    //->setBcc (['mamias2020@gmail.com' => 'MAMIAS team'])
                    ->setBody(
                        $this->renderView('contact/sendnotification.html.twig'),
                        'text/html'
                    )
                    ->addPart(
                        'Hi a Contact Messsage was sent', 'text/plain');


                $mailer->send($message);
                $mailer->send($email);


            } catch (Exception $e) {
                echo $e->getMessage();
            }


            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Your Message is sent. A confirmation Email was sent to your email adress !');
            return $this->redirectToRoute('contact');
        }

        return $this->render(
            'contact/index.html.twig',
            ['form' => $form->createView(), 'pagetitle' => $pagetitle,]
        );
    }
}
