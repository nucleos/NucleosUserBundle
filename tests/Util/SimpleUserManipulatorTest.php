<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Util;

use InvalidArgumentException;
use Nucleos\UserBundle\Model\UserManager;
use Nucleos\UserBundle\NucleosUserEvents;
use Nucleos\UserBundle\Tests\App\Entity\TestUser;
use Nucleos\UserBundle\Util\SimpleUserManipulator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class SimpleUserManipulatorTest extends TestCase
{
    public function testCreate(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManager::class)->getMock();
        $user            = new TestUser();

        $username   = 'test_username';
        $password   = 'test_password';
        $email      = 'test@email.org';

        $userManagerMock->expects(self::once())
            ->method('createUser')
            ->willReturn($user)
        ;

        $userManagerMock->expects(self::once())
            ->method('updateUser')
            ->with(self::isInstanceOf(TestUser::class))
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_CREATED, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new SimpleUserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->create($username, $password, $email, true, false);

        self::assertSame($username, $user->getUsername());
        self::assertSame($password, $user->getPlainPassword());
        self::assertSame($email, $user->getEmail());
        self::assertTrue($user->isEnabled());
        self::assertFalse($user->isSuperAdmin());
    }

    public function testCreateWithHasher(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManager::class)->getMock();
        $user            = new TestUser();

        $username   = 'test_username';
        $password   = 'test_password';
        $email      = 'test@email.org';

        $userManagerMock->expects(self::once())
            ->method('createUser')
            ->willReturn($user)
        ;

        $userManagerMock->expects(self::once())
            ->method('updateUser')
            ->with(self::isInstanceOf(TestUser::class))
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_CREATED, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->method('hashPassword')->with($user, $password)->willReturn('hashed_password');

        $manipulator = new SimpleUserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock, $hasher);
        $manipulator->create($username, $password, $email, true, false);

        self::assertSame($username, $user->getUsername());
        self::assertSame($password, $user->getPlainPassword());
        self::assertSame('hashed_password', $user->getPassword());
        self::assertSame($email, $user->getEmail());
        self::assertTrue($user->isEnabled());
        self::assertFalse($user->isSuperAdmin());
    }

    public function testActivateWithValidUsername(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManager::class)->getMock();
        $username        = 'test_username';

        $user = new TestUser();
        $user->setUsername($username);
        $user->setEnabled(false);

        $userManagerMock->expects(self::once())
            ->method('findUserByUsername')
            ->willReturn($user)
            ->with(self::equalTo($username))
        ;

        $userManagerMock->expects(self::once())
            ->method('updateUser')
            ->with(self::isInstanceOf(TestUser::class))
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_ACTIVATED, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new SimpleUserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->activate($username);

        self::assertSame($username, $user->getUsername());
        self::assertTrue($user->isEnabled());
    }

    public function testActivateWithInvalidUsername(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $userManagerMock = $this->getMockBuilder(UserManager::class)->getMock();
        $invalidusername = 'invalid_username';

        $userManagerMock->expects(self::once())
            ->method('findUserByUsername')
            ->willReturn(null)
            ->with(self::equalTo($invalidusername))
        ;

        $userManagerMock->expects(self::never())
            ->method('updateUser')
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_ACTIVATED, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new SimpleUserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->activate($invalidusername);
    }

    public function testDeactivateWithValidUsername(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManager::class)->getMock();
        $username        = 'test_username';

        $user = new TestUser();
        $user->setUsername($username);
        $user->setEnabled(true);

        $userManagerMock->expects(self::once())
            ->method('findUserByUsername')
            ->willReturn($user)
            ->with(self::equalTo($username))
        ;

        $userManagerMock->expects(self::once())
            ->method('updateUser')
            ->with(self::isInstanceOf(TestUser::class))
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_DEACTIVATED, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new SimpleUserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->deactivate($username);

        self::assertSame($username, $user->getUsername());
        self::assertFalse($user->isEnabled());
    }

    public function testDeactivateWithInvalidUsername(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $userManagerMock = $this->getMockBuilder(UserManager::class)->getMock();
        $invalidusername = 'invalid_username';

        $userManagerMock->expects(self::once())
            ->method('findUserByUsername')
            ->willReturn(null)
            ->with(self::equalTo($invalidusername))
        ;

        $userManagerMock->expects(self::never())
            ->method('updateUser')
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_DEACTIVATED, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new SimpleUserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->deactivate($invalidusername);
    }

    public function testPromoteWithValidUsername(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManager::class)->getMock();
        $username        = 'test_username';

        $user = new TestUser();
        $user->setUsername($username);
        $user->setSuperAdmin(false);

        $userManagerMock->expects(self::once())
            ->method('findUserByUsername')
            ->willReturn($user)
            ->with(self::equalTo($username))
        ;

        $userManagerMock->expects(self::once())
            ->method('updateUser')
            ->with(self::isInstanceOf(TestUser::class))
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_PROMOTED, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new SimpleUserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->promote($username);

        self::assertSame($username, $user->getUsername());
        self::assertTrue($user->isSuperAdmin());
    }

    public function testPromoteWithInvalidUsername(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $userManagerMock = $this->getMockBuilder(UserManager::class)->getMock();
        $invalidusername = 'invalid_username';

        $userManagerMock->expects(self::once())
            ->method('findUserByUsername')
            ->willReturn(null)
            ->with(self::equalTo($invalidusername))
        ;

        $userManagerMock->expects(self::never())
            ->method('updateUser')
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_PROMOTED, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new SimpleUserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->promote($invalidusername);
    }

    public function testDemoteWithValidUsername(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManager::class)->getMock();
        $username        = 'test_username';

        $user = new TestUser();
        $user->setUsername($username);
        $user->setSuperAdmin(true);

        $userManagerMock->expects(self::once())
            ->method('findUserByUsername')
            ->willReturn($user)
            ->with(self::equalTo($username))
        ;

        $userManagerMock->expects(self::once())
            ->method('updateUser')
            ->with(self::isInstanceOf(TestUser::class))
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_DEMOTED, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new SimpleUserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->demote($username);

        self::assertSame($username, $user->getUsername());
        self::assertFalse($user->isSuperAdmin());
    }

    public function testDemoteWithInvalidUsername(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $userManagerMock = $this->getMockBuilder(UserManager::class)->getMock();
        $invalidusername = 'invalid_username';

        $userManagerMock->expects(self::once())
            ->method('findUserByUsername')
            ->willReturn(null)
            ->with(self::equalTo($invalidusername))
        ;

        $userManagerMock->expects(self::never())
            ->method('updateUser')
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_DEMOTED, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new SimpleUserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->demote($invalidusername);
    }

    public function testChangePasswordWithValidUsername(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManager::class)->getMock();

        $user        = new TestUser();
        $username    = 'test_username';
        $password    = 'test_password';
        $oldpassword = 'old_password';

        $user->setUsername($username);
        $user->setPlainPassword($oldpassword);

        $userManagerMock->expects(self::once())
            ->method('findUserByUsername')
            ->willReturn($user)
            ->with(self::equalTo($username))
        ;

        $userManagerMock->expects(self::once())
            ->method('updateUser')
            ->with(self::isInstanceOf(TestUser::class))
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_PASSWORD_CHANGED, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $manipulator = new SimpleUserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->changePassword($username, $password);

        self::assertSame($username, $user->getUsername());
        self::assertSame($password, $user->getPlainPassword());
    }

    public function testChangePasswordWithValidUsernameAndHasher(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManager::class)->getMock();

        $user        = new TestUser();
        $username    = 'test_username';
        $password    = 'test_password';
        $oldpassword = 'old_password';

        $user->setUsername($username);
        $user->setPlainPassword($oldpassword);

        $userManagerMock->expects(self::once())
            ->method('findUserByUsername')
            ->willReturn($user)
            ->with(self::equalTo($username))
        ;

        $userManagerMock->expects(self::once())
            ->method('updateUser')
            ->with(self::isInstanceOf(TestUser::class))
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_PASSWORD_CHANGED, true);

        $requestStackMock = $this->getRequestStackMock(true);

        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->method('hashPassword')->with($user, $password)->willReturn('hashed_password');

        $manipulator = new SimpleUserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock, $hasher);
        $manipulator->changePassword($username, $password);

        self::assertSame($username, $user->getUsername());
        self::assertSame($password, $user->getPlainPassword());
        self::assertSame('hashed_password', $user->getPassword());
    }

    public function testChangePasswordWithInvalidUsername(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $userManagerMock = $this->getMockBuilder(UserManager::class)->getMock();

        $invalidusername = 'invalid_username';
        $password        = 'test_password';

        $userManagerMock->expects(self::once())
            ->method('findUserByUsername')
            ->willReturn(null)
            ->with(self::equalTo($invalidusername))
        ;

        $userManagerMock->expects(self::never())
            ->method('updateUser')
        ;

        $eventDispatcherMock = $this->getEventDispatcherMock(NucleosUserEvents::USER_PASSWORD_CHANGED, false);

        $requestStackMock = $this->getRequestStackMock(false);

        $manipulator = new SimpleUserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);
        $manipulator->changePassword($invalidusername, $password);
    }

    public function testAddRole(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManager::class)->getMock();
        $username        = 'test_username';
        $userRole        = 'test_role';
        $user            = new TestUser();

        $userManagerMock->expects(self::exactly(2))
            ->method('findUserByUsername')
            ->willReturn($user)
            ->with(self::equalTo($username))
        ;

        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $requestStackMock    = $this->getRequestStackMock(false);

        $manipulator = new SimpleUserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);

        self::assertTrue($manipulator->addRole($username, $userRole));
        self::assertFalse($manipulator->addRole($username, $userRole));
        self::assertTrue($user->hasRole($userRole));
    }

    public function testRemoveRole(): void
    {
        $userManagerMock = $this->getMockBuilder(UserManager::class)->getMock();
        $username        = 'test_username';
        $userRole        = 'test_role';
        $user            = new TestUser();
        $user->addRole($userRole);

        $userManagerMock->expects(self::exactly(2))
            ->method('findUserByUsername')
            ->willReturn($user)
            ->with(self::equalTo($username))
        ;

        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $requestStackMock    = $this->getRequestStackMock(false);

        $manipulator = new SimpleUserManipulator($userManagerMock, $eventDispatcherMock, $requestStackMock);

        self::assertTrue($manipulator->removeRole($username, $userRole));
        self::assertFalse($user->hasRole($userRole));
        self::assertFalse($manipulator->removeRole($username, $userRole));
    }

    /**
     * @return EventDispatcherInterface&MockObject
     */
    protected function getEventDispatcherMock(string $event, bool $once = true): MockObject
    {
        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $eventDispatcherMock->expects($once ? self::once() : self::never())
            ->method('dispatch')
            ->with(self::anything(), $event)
        ;

        return $eventDispatcherMock;
    }

    /**
     * @return MockObject&RequestStack
     */
    protected function getRequestStackMock(bool $once = true): MockObject
    {
        $requestStackMock = $this->getMockBuilder(RequestStack::class)->getMock();

        $requestStackMock->expects($once ? self::once() : self::never())
            ->method('getCurrentRequest')
            ->willReturn(null)
        ;

        return $requestStackMock;
    }
}
