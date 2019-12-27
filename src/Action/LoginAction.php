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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment;

final class LoginAction
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var CsrfTokenManagerInterface|null
     */
    private $tokenManager;

    public function __construct(Environment $twig, CsrfTokenManagerInterface $tokenManager = null)
    {
        $this->twig         = $twig;
        $this->tokenManager = $tokenManager;
    }

    public function __invoke(Request $request): Response
    {
        $session = $request->hasSession() ? $request->getSession() : null;

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

        $csrfToken = null !== $this->tokenManager
            ? $this->tokenManager->getToken('authenticate')->getValue()
            : null;

        return new Response($this->twig->render('@NucleosUser/Security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'csrf_token'    => $csrfToken,
        ]));
    }
}
