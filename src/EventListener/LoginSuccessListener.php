<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Routing\RouterInterface;

class LoginSuccessListener
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        // On récupère la session pour stocker la redirection
        $request = $event->getRequest();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $url = $this->router->generate('admin_dashboard');
        } else {
            $url = $this->router->generate('products_list');
        }

        $response = new RedirectResponse($url);
        $event->getRequest()->getSession()->set('_security.main.target_path', $url);
    }
}