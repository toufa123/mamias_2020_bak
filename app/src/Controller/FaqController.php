<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FaqController extends AbstractController
{
    /**
     * @Route("/faq", name="faq", options={"sitemap" = true})
     */
    public function index()
    {
        return $this->render('faq/index.html.twig');
    }
}
