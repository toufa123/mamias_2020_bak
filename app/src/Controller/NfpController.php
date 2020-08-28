<?php

namespace App\Controller;

use App\Application\Sonata\UserBundle\Entity\User;
use App\Entity\Mamias;
use App\Form\BaselineType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class NfpController extends AbstractController
{
    /**
     * @Route("/nfp", name="nfp")
     * @IsGranted("ROLE_FOCALPOINT")
     */
    public function index()
    {
        $n1 = null;
        $n2 = null;
        $n3 = null;
        $co = null;
        $userCountry = null;

        $em = $this->getDoctrine()->getManager();
        $a = $this->getUser();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ('' != $user) {
            $username = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
            $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
            $Country = $this->get('security.token_storage')->getToken()->getUser()->getCountry();
            $userCountry = $this->get('security.token_storage')->getToken()->getUser()->getCountry()->getId();
            //dump($userCountry);die;

            $n1 = $em->getRepository(Mamias::class)->findnumbersBycountry($userCountry);
            $n2 = $em->getRepository(Mamias::class)->findnumbersByestablished($userCountry);
            $n3 = $em->getRepository(Mamias::class)->findnumbersByInvasive($userCountry);

            $species = $em->getRepository(Mamias::class)->findSpeciesByCountry($userCountry);
            //$userManager = $this->get('fos_user.user_manager');
            // $national_expert = $this->container->get('fos_user.user_manager')->findUserBy(array('country'=>$userCountry));
            //dump($national_expert);die;

            $query = $this->getDoctrine()->getEntityManager()
                ->createQuery('SELECT u FROM App\Application\Sonata\UserBundle\Entity\User u WHERE u.country = :country'
                )->setParameter('country', $userCountry);

            $national_expert = $query->getResult();
            //dump($national_expert);die;

            return $this->render(
                'nfp/index.html.twig',
                [
                    'country' => $Country,
                    'n1' => $n1,
                    'n2' => $n2,
                    'n3' => $n3,
                    'species' => $species,
                    'national_expert' => $national_expert
                ]
            );
        } else {
            return $this->forward($this->generateUrl('sonata_user_admin_security_login'));
        }


    }


    /*
     * @Route("/user/{id}", name="fos_user_profile_show_user")
     * @IsGranted("ROLE_FOCALPOINT")
     */
    public function showAction($id)
    {
        $userID = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager()
            ->createQuery('SELECT u FROM App\Application\Sonata\UserBundle\Entity\User u WHERE u.id = :id'
            )->setParameter('id', $userID);

        $national_expert = $em->getResult();


        return $this->render('FOSUserBundle:Profile:show_content.html.twig', array(
            'entity' => $national_expert,
        ));
    }

    /*
     * @Route("/upload/", name="upload")
     * @IsGranted("ROLE_FOCALPOINT")
     */
    public function uploadAction(Request $request)
    {
        $User = $this->get('security.token_storage')->getToken()->getUser();
        $baseline = new baseline();
        $form = $this->createForm(BaselineType::class, $baseline);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $country = $form['country']->getData();
            $date_reporting = $form['date_reporting']->getData();
            $filename = $form['filename']->getData();
            $baseline->setCountry($country);
            $baseline->setDateReporting($date_reporting);
            $baseline->setBaselineFile($filename);
            $baseline->setUser($User);


            // finally add data in database
            $bl = $this->getDoctrine()->getManager();
            $bl->persist($baseline);
            $bl->flush();
        }
        return $this->render('nfp/index.html.twig', array('form' => $form->createView()

        ));
    }
}
