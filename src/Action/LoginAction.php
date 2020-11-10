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

namespace Nucleos\UserBundle\Action;

use Nucleos\UserBundle\Event\GetResponseLoginEvent;
use Nucleos\UserBundle\NucleosUserEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

final class LoginAction
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var CsrfTokenManagerInterface|null
     */
    private $tokenManager;

    public function __construct(
        Environment $twig,
        EventDispatcherInterface $eventDispatcher,
        CsrfTokenManagerInterface $tokenManager = null
    ) {
        $this->twig            = $twig;
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenManager    = $tokenManager;
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __invoke(Request $request): Response
    {
        $event = new GetResponseLoginEvent($request);
        $this->eventDispatcher->dispatch($event, NucleosUserEvents::SECURITY_LOGIN_INITIALIZE);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $session = $this->getSession($request);

        $authErrorKey    = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        return new Response($this->twig->render('@NucleosUser/Security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'csrf_token'    => $this->getCsrfToken(),
        ]));
    }

    private function getCsrfToken(): ?string
    {
        return null !== $this->tokenManager ? $this->tokenManager->getToken('authenticate')->getValue() : null;
    }

    private function getSession(Request $request): ?SessionInterface
    {
        return $request->hasSession() ? $request->getSession() : null;
    }
}
