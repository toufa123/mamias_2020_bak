<?php

namespace App\service;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Class AfterLoginRedirection.
 */
class AfterLoginRedirection implements AuthenticationSuccessHandlerInterface
{
    private $router;

    /**
     * AfterLoginRedirection constructor.
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $roles = $token->getRoles();

        $rolesTab = array_map(
            function ($role) {
                return $role->getRole();
            },
            $roles
        );

        if (in_array(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_SPARAC', 'ROLE_S', 'ROLE_REGSITREDUSER'], $rolesTab, true)) {
            // c'est un aministrateur : on le rediriger vers l'espace admin
            $redirection = new RedirectResponse($this->router->generate('sonata_admin_redirect'));
        } elseif (in_array(['ROLE_REGSITREDUSER', 'ROLE_FOCALPOINT', 'ROLE_NATIONALEXPERT'], $rolesTab, true)) {
            $redirection = new RedirectResponse($this->router->generate('nfp'));
        } else {
            $redirection = new RedirectResponse($this->router->generate('declaration'));
        }

        return $redirection;
    }
}
