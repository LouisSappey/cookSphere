<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BannedUserSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $user = $this->security->getUser();

        // Allow access to home and login pages
        if (in_array($request->getPathInfo(), ['/', '/login', '/logout'])) {
            return;
        }

        if ($user && in_array('ROLE_BANNED', $user->getRoles())) {
            $response = new RedirectResponse($this->urlGenerator->generate('page_homepage'));
            $event->setResponse($response);
        }
    }
} 