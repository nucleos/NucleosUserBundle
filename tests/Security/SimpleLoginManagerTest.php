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
use Nucleos\UserBundle\Security\SimpleLoginManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

final class SimpleLoginManagerTest extends TestCase
{
    public function testLogInUserWithRequestStack(): void
    {
        $loginManager = $this->createLoginManager();
        $loginManager->logInUser('main', $this->mockUser());
    }

    public function testLogInUserWithRememberMeAndRequestStack(): void
    {
        $response = $this->getMockBuilder(Response::class)->getMock();

        $loginManager = $this->createLoginManager($response);
        $loginManager->logInUser('main', $this->mockUser(), $response);
    }

    private function createLoginManager(?Response $response = null): SimpleLoginManager
    {
        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();

        $tokenStorage
            ->expects(self::once())
            ->method('setToken')
            ->with(self::isInstanceOf(TokenInterface::class))
        ;

        $userChecker = $this->getMockBuilder(UserCheckerInterface::class)->getMock();
        $userChecker
            ->expects(self::once())
            ->method('checkPreAuth')
            ->with(self::isInstanceOf(UserInterface::class))
        ;

        $request = new Request();

        $sessionStrategy = $this->getMockBuilder(SessionAuthenticationStrategyInterface::class)->getMock();
        $sessionStrategy
            ->expects(self::once())
            ->method('onAuthentication')
            ->with($request, self::isInstanceOf(TokenInterface::class))
        ;

        $requestStack = $this->getMockBuilder(RequestStack::class)->getMock();
        $requestStack
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;

        return new SimpleLoginManager($tokenStorage, $userChecker, $sessionStrategy, $requestStack);
    }

    /**
     * @return MockObject&UserInterface
     */
    private function mockUser(): MockObject
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $user
            ->expects(self::once())
            ->method('getRoles')
            ->willReturn(['ROLE_USER'])
        ;

        return $user;
    }
}
