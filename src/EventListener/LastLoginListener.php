<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\EventListener;

use DateTime;
use Nucleos\UserBundle\Event\UserEvent;
use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\Model\UserManager;
use Nucleos\UserBundle\NucleosUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

final class LastLoginListener implements EventSubscriberInterface
{
    private readonly UserManager $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NucleosUserEvents::SECURITY_IMPLICIT_LOGIN => 'onImplicitLogin',
            SecurityEvents::INTERACTIVE_LOGIN          => 'onSecurityInteractiveLogin',
        ];
    }

    public function onImplicitLogin(UserEvent $event): void
    {
        $user = $event->getUser();

        $user->setLastLogin(new DateTime());
        $this->userManager->updateUser($user);
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof UserInterface) {
            $user->setLastLogin(new DateTime());
            $this->userManager->updateUser($user);
        }
    }
}
