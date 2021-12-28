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

namespace Nucleos\UserBundle\Security;

use Nucleos\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

final class SimpleLoginManager implements LoginManager
{
    private TokenStorageInterface $tokenStorage;

    private UserCheckerInterface $userChecker;

    private SessionAuthenticationStrategyInterface $sessionStrategy;

    private RequestStack $requestStack;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        UserCheckerInterface $userChecker,
        SessionAuthenticationStrategyInterface $sessionStrategy,
        RequestStack $requestStack
    ) {
        $this->tokenStorage      = $tokenStorage;
        $this->userChecker       = $userChecker;
        $this->sessionStrategy   = $sessionStrategy;
        $this->requestStack      = $requestStack;
    }

    public function logInUser(string $firewallName, UserInterface $user, Response $response = null): void
    {
        $this->userChecker->checkPreAuth($user);

        $token   = $this->createToken($firewallName, $user);
        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            $this->sessionStrategy->onAuthentication($request, $token);
        }

        $this->tokenStorage->setToken($token);
    }

    private function createToken(string $firewall, UserInterface $user): UsernamePasswordToken
    {
        return new UsernamePasswordToken($user, $firewall, $user->getRoles());
    }
}
