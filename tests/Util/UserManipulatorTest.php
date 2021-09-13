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

namespace Nucleos\UserBundle\Tests\Util;

use InvalidArgumentException;
use Nucleos\UserBundle\Model\UserManagerInterface;
use Nucleos\UserBundle\NucleosUserEvents;
use Nucleos\UserBundle\Tests\App\Entity\TestUser;
use Nucleos\UserBundle\Util\UserManipulator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class UserManipulatorTest extends TestCase
{
    public function testCreate(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManagerInterface::class)->getMock();
        $user            = new TestUser();

        $username   = 'test_username';
        $password   = 'test_password';
        $email      = 'test@email.org';

        $userManagerMock->expects(static::once())
            ->method('createUser')
            ->willReturn($user)
        ;

        $userManagerMock->expects(static::once())
            ->method('updateUser')
            ->with(static::isInstanceOf(TestUser::class))
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_CREATED, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->create($username, $password, $email, true, false);

        static::assertSame($username, $user->getUsername());
        static::assertSame($password, $user->getPlainPassword());
        static::assertSame($email, $user->getEmail());
        static::assertTrue($user->isEnabled());
        static::assertFalse($user->isSuperAdmin());
    }

    public function testActivateWithValidUsername(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManagerInterface::class)->getMock();
        $username        = 'test_username';

        $user = new TestUser();
        $user->setUsername($username);
        $user->setEnabled(false);

        $userManagerMock->expects(static::once())
            ->method('findUserByUsername')
            ->willReturn($user)
            ->with(static::equalTo($username))
        ;

        $userManagerMock->expects(static::once())
            ->method('updateUser')
            ->with(static::isInstanceOf(TestUser::class))
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_ACTIVATED, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->activate($username);

        static::assertSame($username, $user->getUsername());
        static::assertTrue($user->isEnabled());
    }

    public function testActivateWithInvalidUsername(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $userManagerMock = $this->getMockBuilder(UserManagerInterface::class)->getMock();
        $invalidusername = 'invalid_username';

        $userManagerMock->expects(static::once())
            ->method('findUserByUsername')
            ->willReturn(null)
            ->with(static::equalTo($invalidusername))
        ;

        $userManagerMock->expects(static::never())
            ->method('updateUser')
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_ACTIVATED, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->activate($invalidusername);
    }

    public function testDeactivateWithValidUsername(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManagerInterface::class)->getMock();
        $username        = 'test_username';

        $user = new TestUser();
        $user->setUsername($username);
        $user->setEnabled(true);

        $userManagerMock->expects(static::once())
            ->method('findUserByUsername')
            ->willReturn($user)
            ->with(static::equalTo($username))
        ;

        $userManagerMock->expects(static::once())
            ->method('updateUser')
            ->with(static::isInstanceOf(TestUser::class))
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_DEACTIVATED, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->deactivate($username);

        static::assertSame($username, $user->getUsername());
        static::assertFalse($user->isEnabled());
    }

    public function testDeactivateWithInvalidUsername(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $userManagerMock = $this->getMockBuilder(UserManagerInterface::class)->getMock();
        $invalidusername = 'invalid_username';

        $userManagerMock->expects(static::once())
            ->method('findUserByUsername')
            ->willReturn(null)
            ->with(static::equalTo($invalidusername))
        ;

        $userManagerMock->expects(static::never())
            ->method('updateUser')
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_DEACTIVATED, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->deactivate($invalidusername);
    }

    public function testPromoteWithValidUsername(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManagerInterface::class)->getMock();
        $username        = 'test_username';

        $user = new TestUser();
        $user->setUsername($username);
        $user->setSuperAdmin(false);

        $userManagerMock->expects(static::once())
            ->method('findUserByUsername')
            ->willReturn($user)
            ->with(static::equalTo($username))
        ;

        $userManagerMock->expects(static::once())
            ->method('updateUser')
            ->with(static::isInstanceOf(TestUser::class))
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_PROMOTED, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->promote($username);

        static::assertSame($username, $user->getUsername());
        static::assertTrue($user->isSuperAdmin());
    }

    public function testPromoteWithInvalidUsername(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $userManagerMock = $this->getMockBuilder(UserManagerInterface::class)->getMock();
        $invalidusername = 'invalid_username';

        $userManagerMock->expects(static::once())
            ->method('findUserByUsername')
            ->willReturn(null)
            ->with(static::equalTo($invalidusername))
        ;

        $userManagerMock->expects(static::never())
            ->method('updateUser')
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_PROMOTED, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->promote($invalidusername);
    }

    public function testDemoteWithValidUsername(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManagerInterface::class)->getMock();
        $username        = 'test_username';

        $user = new TestUser();
        $user->setUsername($username);
        $user->setSuperAdmin(true);

        $userManagerMock->expects(static::once())
            ->method('findUserByUsername')
            ->willReturn($user)
            ->with(static::equalTo($username))
        ;

        $userManagerMock->expects(static::once())
            ->method('updateUser')
            ->with(static::isInstanceOf(TestUser::class))
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_DEMOTED, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->demote($username);

        static::assertSame($username, $user->getUsername());
        static::assertFalse($user->isSuperAdmin());
    }

    public function testDemoteWithInvalidUsername(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $userManagerMock = $this->getMockBuilder(UserManagerInterface::class)->getMock();
        $invalidusername = 'invalid_username';

        $userManagerMock->expects(static::once())
            ->method('findUserByUsername')
            ->willReturn(null)
            ->with(static::equalTo($invalidusername))
        ;

        $userManagerMock->expects(static::never())
            ->method('updateUser')
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_DEMOTED, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->demote($invalidusername);
    }

    public function testChangePasswordWithValidUsername(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManagerInterface::class)->getMock();

        $user        = new TestUser();
        $username    = 'test_username';
        $password    = 'test_password';
        $oldpassword = 'old_password';

        $user->setUsername($username);
        $user->setPlainPassword($oldpassword);

        $userManagerMock->expects(static::once())
            ->method('findUserByUsername')
            ->willReturn($user)
            ->with(static::equalTo($username))
        ;

        $userManagerMock->expects(static::once())
            ->method('updateUser')
            ->with(static::isInstanceOf(TestUser::class))
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_PASSWORD_CHANGED, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->changePassword($username, $password);

        static::assertSame($username, $user->getUsername());
        static::assertSame($password, $user->getPlainPassword());
    }

    public function testChangePasswordWithInvalidUsername(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $userManagerMock = $this->getMockBuilder(UserManagerInterface::class)->getMock();

        $invalidusername = 'invalid_username';
        $password        = 'test_password';

        $userManagerMock->expects(static::once())
            ->method('findUserByUsername')
            ->willReturn(null)
            ->with(static::equalTo($invalidusername))
        ;

        $userManagerMock->expects(static::never())
            ->method('updateUser')
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_PASSWORD_CHANGED, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->changePassword($invalidusername, $password);
    }

    public function testAddRole(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManagerInterface::class)->getMock();
        $username        = 'test_username';
        $userRole        = 'test_role';
        $user            = new TestUser();

        $userManagerMock->expects(static::exactly(2))
            ->method('findUserByUsername')
            ->willReturn($user)
            ->with(static::equalTo($username))
        ;

        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $requestStackMock    = $this->getRequestStackMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);

        static::assertTrue($manipulator->addRole($username, $userRole));
        static::assertFalse($manipulator->addRole($username, $userRole));
        static::assertTrue($user->hasRole($userRole));
    }

    public function testRemoveRole(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManagerInterface::class)->getMock();
        $username        = 'test_username';
        $userRole        = 'test_role';
        $user            = new TestUser();
        $user->addRole($userRole);

        $userManagerMock->expects(static::exactly(2))
            ->method('findUserByUsername')
            ->willReturn($user)
            ->with(static::equalTo($username))
        ;

        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $requestStackMock    = $this->getRequestStackMock(false);

        $manipulator = new UserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);

        static::assertTrue($manipulator->removeRole($username, $userRole));
        static::assertFalse($user->hasRole($userRole));
        static::assertFalse($manipulator->removeRole($username, $userRole));
    }

    /**
     * @return MockObject&EventDispatcherInterface
     */
    protected function getEventDispatcherMock(string $event, bool $once = true): MockObject
    {
        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $eventDispatcherMock->expects($once ? static::once() : static::never())
            ->method('dispatch')
            ->with(static::anything(), $event)
        ;

        return $eventDispatcherMock;
    }

    /**
     * @return MockObject&RequestStack
     */
    protected function getRequestStackMock(bool $once = true): MockObject
    {
        $requestStackMock = $this->getMockBuilder(RequestStack::class)->getMock();

        $requestStackMock->expects($once ? static::once() : static::never())
            ->method('getCurrentRequest')
            ->willReturn(null)
        ;

        return $requestStackMock;
    }
}
