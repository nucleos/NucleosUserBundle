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

namespace Nucleos\UserBundle\Tests\Security;

use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\Security\LoginManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

final class LoginManagerTest extends TestCase
{
    public function testLogInUserWithRequestStack(): void
    {
        $loginManager = $this->createLoginManager('main');
        $loginManager->logInUser('main', $this->mockUser());
    }

    public function testLogInUserWithRememberMeAndRequestStack(): void
    {
        $response = $this->getMockBuilder(Response::class)->getMock();

        $loginManager = $this->createLoginManager('main', $response);
        $loginManager->logInUser('main', $this->mockUser(), $response);
    }

    private function createLoginManager(string $firewallName, Response $response = null): LoginManager
    {
        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();

        $tokenStorage
            ->expects(static::once())
            ->method('setToken')
            ->with(static::isInstanceOf(TokenInterface::class))
        ;

        $userChecker = $this->getMockBuilder(UserCheckerInterface::class)->getMock();
        $userChecker
            ->expects(static::once())
            ->method('checkPreAuth')
            ->with(static::isInstanceOf(UserInterface::class))
        ;

        $request = $this->getMockBuilder(Request::class)->getMock();

        $sessionStrategy = $this->getMockBuilder(SessionAuthenticationStrategyInterface::class)->getMock();
        $sessionStrategy
            ->expects(static::once())
            ->method('onAuthentication')
            ->with($request, static::isInstanceOf(TokenInterface::class))
        ;

        $requestStack = $this->getMockBuilder(RequestStack::class)->getMock();
        $requestStack
            ->expects(static::once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;

        $rememberMe = null;
        if (null !== $response) {
            $rememberMe = $this->getMockBuilder(RememberMeServicesInterface::class)->getMock();
            $rememberMe
                ->expects(static::once())
                ->method('loginSuccess')
                ->with($request, $response, static::isInstanceOf(TokenInterface::class))
            ;
        }

        return new LoginManager($tokenStorage, $userChecker, $sessionStrategy, $requestStack, $rememberMe);
    }

    /**
     * @return UserInterface&MockObject
     */
    private function mockUser(): MockObject
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $user
            ->expects(static::once())
            ->method('getRoles')
            ->willReturn(['ROLE_USER'])
        ;

        return $user;
    }
}
