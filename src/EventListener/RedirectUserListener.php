<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\User;
use Symfony\Component\Security\Core\User\User as SymfonyUser;

/**
 * Class RedirectUserListener
 * @package App\EventListener
 */
class RedirectUserListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * RedirectUserListener constructor.
     * @param TokenStorageInterface $t
     * @param RouterInterface $r
     */
    public function __construct(TokenStorageInterface $t, RouterInterface $r)
    {
        $this->tokenStorage = $t;
        $this->router = $r;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($this->isUserLoggedIn() && $event->isMasterRequest()) {
            $currentRoute = $event->getRequest()->attributes->get('_route');
            if ($this->isAuthenticatedUserOnAnonymousPage($currentRoute)) {
                $response = new RedirectResponse($this->router->generate('home'));
                $event->setResponse($response);
            }
        }
    }

    /**
     * @return bool
     */
    private function isUserLoggedIn(): bool
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return false;
        }

        $user = $token->getUser();

        return $user instanceof User || $user instanceof SymfonyUser;
    }

    /**
     * @param string $currentRoute
     * @return bool
     */
    private function isAuthenticatedUserOnAnonymousPage(string $currentRoute): bool
    {
        return in_array(
            $currentRoute,
            ['register', 'login']
        );
    }
}
