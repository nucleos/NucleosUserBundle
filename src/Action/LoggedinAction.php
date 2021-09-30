<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Action;

use Nucleos\UserBundle\Event\GetResponseUserEvent;
use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\NucleosUserEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

final class LoggedinAction
{
    private Environment $twig;

    private EventDispatcherInterface $eventDispatcher;

    private Security $security;

    public function __construct(Environment $twig, EventDispatcherInterface $eventDispatcher, Security $security)
    {
        $this->twig            = $twig;
        $this->eventDispatcher = $eventDispatcher;
        $this->security        = $security;
    }

    public function __invoke(Request $request): Response
    {
        $user  = $this->getUser();

        if (null === $user) {
            throw new AccessDeniedHttpException();
        }

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch($event, NucleosUserEvents::SECURITY_LOGIN_COMPLETED);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        return new Response($this->twig->render('@NucleosUser/Security/loggedin.html.twig'));
    }

    private function getUser(): ?UserInterface
    {
        $user = $this->security->getUser();

        if ($user instanceof UserInterface) {
            return $user;
        }

        return null;
    }
}
