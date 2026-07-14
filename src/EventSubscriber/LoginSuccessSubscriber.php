<?php

namespace App\EventSubscriber;

use Symfony\Component\BrowserKit\Request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSuccessSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
        
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $request = $event->getRequest();
        
   
        if (str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }

        $user = $event->getUser();

        if (in_array('ROLE_CHEF', $user->getRoles(), true)) {
            $response = new RedirectResponse($this->urlGenerator->generate('app_chef_dashboard'));
            $event->setResponse($response);
        } else {
            $response = new RedirectResponse($this->urlGenerator->generate('app_home'));
            $event->setResponse($response);
        }
    }
}
