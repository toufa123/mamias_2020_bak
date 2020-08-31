<?php

declare(strict_types=1);

namespace App\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CatalogueAdminController extends CRUDController
{

    public function importAction(Request $request)
    {

        return new Response('<html><body>Hello !</body></html>');

    }

}
