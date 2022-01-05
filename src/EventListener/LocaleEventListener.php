<?php

declare(strict_types=1);

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\EventListener;

use Nucleos\UserBundle\Event\UserEvent;
use Nucleos\UserBundle\Model\LocaleAwareUser;
use Nucleos\UserBundle\NucleosUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Contracts\Translation\LocaleAwareInterface as LocaleAwareTranslator;

final class LocaleEventListener implements EventSubscriberInterface
{
    private LocaleAwareTranslator $translator;

    public function __construct(LocaleAwareTranslator $translator)
    {
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NucleosUserEvents::SECURITY_IMPLICIT_LOGIN => 'onImplicitLogin',
            SecurityEvents::INTERACTIVE_LOGIN          => 'onSecurityInteractiveLogin',
            KernelEvents::REQUEST                      => [['onKernelRequest', 20]],
            NucleosUserEvents::USER_LOCALE_CHANGED     => 'onLocaleChanged',
            NucleosUserEvents::USER_TIMEZONE_CHANGED   => 'onTimezoneChanged',
        ];
    }

    public function onImplicitLogin(UserEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof LocaleAwareUser || null === $event->getRequest()) {
            return;
        }

        $this->setLocale($event->getRequest(), $user);
        $this->setTimezone($event->getRequest(), $user);
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user instanceof LocaleAwareUser) {
            return;
        }

        $this->setLocale($event->getRequest(), $user);
        $this->setTimezone($event->getRequest(), $user);
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->hasPreviousSession()) {
            return;
        }

        $session = $request->getSession();

        if (null !== $locale = $session->get('_locale')) {
            $this->translator->setLocale($locale);
            $request->setLocale($locale);
        }
    }

    public function onTimezoneChanged(UserEvent $event): void
    {
        $user = $event->getUser();

        if ($user instanceof LocaleAwareUser && null !== $event->getRequest()) {
            $this->setTimezone($event->getRequest(), $user);
        }
    }

    public function onLocaleChanged(UserEvent $event): void
    {
        $user = $event->getUser();

        if ($user instanceof LocaleAwareUser && null !== $event->getRequest()) {
            $this->setLocale($event->getRequest(), $user);
        }
    }

    private function setLocale(Request $request, LocaleAwareUser $user): void
    {
        if (!$request->hasSession()) {
            return;
        }

        $session = $request->getSession();

        $locale = $user->getLocale();

        if ('' === $locale || null === $locale) {
            return;
        }

        $this->translator->setLocale($locale);
        $request->setLocale($locale);
        $session->set('_locale', $locale);
    }

    private function setTimezone(Request $request, LocaleAwareUser $user): void
    {
        if (!$request->hasSession()) {
            return;
        }

        $timezone = $user->getTimezone();

        if ('' === $timezone || null === $timezone) {
            return;
        }

        $session = $request->getSession();
        $session->set('_timezone', $timezone);
    }
}
