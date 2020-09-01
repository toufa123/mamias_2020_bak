<?php

declare(strict_types=1);

namespace App\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CatalogueAdminController extends CRUDController
{
    /**
     * @Route("/catalogue/import", name="importcatalogue")
     */

    public function importAction(Request $request)
    {
        return $this->renderWithExtraParams('admin/catalogue/import.html.twig');
    }

}
