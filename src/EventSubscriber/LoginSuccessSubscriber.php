<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        $user = $event->getUser();

        // Check if the user has the ROLE_CHEF role
        if (in_array('ROLE_CHEF', $user->getRoles(), true)) {
            // Redirect chefs to their dashboard
            $response = new RedirectResponse($this->urlGenerator->generate('app_chef_deshbord'));
            $event->setResponse($response);
        } else {
            // Redirect normal users to the user dashboard
            $response = new RedirectResponse($this->urlGenerator->generate('app_user'));
            $event->setResponse($response);
        }
    }
}
