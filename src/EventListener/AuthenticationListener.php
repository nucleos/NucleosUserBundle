<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\EventListener;

use Nucleos\UserBundle\Event\FilterUserResponseEvent;
use Nucleos\UserBundle\Event\UserEvent;
use Nucleos\UserBundle\NucleosUserEvents;
use Nucleos\UserBundle\Security\LoginManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class AuthenticationListener implements EventSubscriberInterface
{
    private readonly LoginManager $loginManager;

    private readonly string $firewallName;

    public function __construct(LoginManager $loginManager, string $firewallName)
    {
        $this->loginManager = $loginManager;
        $this->firewallName = $firewallName;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NucleosUserEvents::RESETTING_RESET_COMPLETED => 'authenticate',
        ];
    }

    public function authenticate(FilterUserResponseEvent $event, string $eventName, EventDispatcherInterface $eventDispatcher): void
    {
        try {
            $this->loginManager->logInUser($this->firewallName, $event->getUser(), $event->getResponse());

            $eventDispatcher->dispatch(new UserEvent($event->getUser(), $event->getRequest()), NucleosUserEvents::SECURITY_IMPLICIT_LOGIN);
        } catch (AccountStatusException $ex) {
            // We simply do not authenticate users which do not pass the user
            // checker (not enabled, expired, etc.).
        }
    }
}
