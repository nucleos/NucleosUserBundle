<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Security;

use Exception;
use Nucleos\UserBundle\Model\Group;
use Nucleos\UserBundle\Model\User;
use Nucleos\UserBundle\Security\UserChecker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;

final class UserCheckerTest extends TestCase
{
    public function testCheckPreAuthFailsLockedOut(): void
    {
        $this->expectException(LockedException::class);
        $this->expectExceptionMessage('User account is locked.');

        $userMock = $this->getUser(false, false, false, false);
        $checker  = new UserChecker();
        $checker->checkPreAuth($userMock);
    }

    public function testCheckPreAuthFailsIsEnabled(): void
    {
        $this->expectException(DisabledException::class);
        $this->expectExceptionMessage('User account is disabled.');

        $userMock = $this->getUser(true, false, false, false);
        $checker  = new UserChecker();
        $checker->checkPreAuth($userMock);
    }

    public function testCheckPreAuthFailsIsAccountNonExpired(): void
    {
        $this->expectException(AccountExpiredException::class);
        $this->expectExceptionMessage('User account has expired.');

        $userMock = $this->getUser(true, true, false, false);
        $checker  = new UserChecker();
        $checker->checkPreAuth($userMock);
    }

    public function testCheckPreAuthSuccess(): void
    {
        $this->expectNotToPerformAssertions();

        $userMock = $this->getUser(true, true, true, false);
        $checker  = new UserChecker();

        try {
            $checker->checkPreAuth($userMock);
        } catch (Exception $exception) {
            self::fail();
        }
    }

    public function testCheckPostAuthFailsIsCredentialsNonExpired(): void
    {
        $this->expectException(CredentialsExpiredException::class);
        $this->expectExceptionMessage('User credentials have expired.');

        $userMock = $this->getUser(true, true, true, false);
        $checker  = new UserChecker();
        $checker->checkPostAuth($userMock);
    }

    public function testCheckPostAuthSuccess(): void
    {
        $this->expectNotToPerformAssertions();

        $userMock = $this->getUser(true, true, true, true);
        $checker  = new UserChecker();

        try {
            $checker->checkPostAuth($userMock);
        } catch (Exception $exception) {
            self::fail();
        }
    }

    /**
     * @return MockObject&User<Group>
     */
    private function getUser(bool $isAccountNonLocked, bool $isEnabled, bool $isAccountNonExpired, bool $isCredentialsNonExpired): MockObject
    {
        $userMock = $this->createMock(User::class);
        $userMock
            ->method('isAccountNonLocked')
            ->willReturn($isAccountNonLocked)
        ;
        $userMock
            ->method('isEnabled')
            ->willReturn($isEnabled)
        ;
        $userMock
            ->method('isAccountNonExpired')
            ->willReturn($isAccountNonExpired)
        ;
        $userMock
            ->method('isCredentialsNonExpired')
            ->willReturn($isCredentialsNonExpired)
        ;

        return $userMock;
    }
}
