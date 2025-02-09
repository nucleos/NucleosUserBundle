<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Security;

use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\Model\UserManager;
use Nucleos\UserBundle\Security\EmailProvider;
use Nucleos\UserBundle\Tests\App\Entity\TestUser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

final class EmailProviderTest extends TestCase
{
    /**
     * @var MockObject&UserManager
     */
    private readonly UserManager $userManager;

    private readonly EmailProvider $userProvider;

    protected function setUp(): void
    {
        $this->userManager  = $this->getMockBuilder(UserManager::class)->getMock();
        $this->userProvider = new EmailProvider($this->userManager);
    }

    public function testLoadUserByUsername(): void
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $this->userManager->expects(self::once())
            ->method('findUserByEmail')
            ->with('foobar')
            ->willReturn($user)
        ;

        self::assertSame($user, $this->userProvider->loadUserByUsername('foobar'));
    }

    public function testLoadUserByInvalidUsername(): void
    {
        $this->expectException(AuthenticationException::class);

        $this->userManager->expects(self::once())
            ->method('findUserByEmail')
            ->with('foobar')
            ->willReturn(null)
        ;

        $this->userProvider->loadUserByUsername('foobar');
    }

    public function testRefreshUserBy(): void
    {
        $user = $this->createUser();

        $refreshedUser = $this->getMockBuilder(UserInterface::class)->getMock();
        $this->userManager->expects(self::once())
            ->method('findUserByEmail')
            ->with('123')
            ->willReturn($refreshedUser)
        ;

        $this->userManager->expects(self::atLeastOnce())
            ->method('getClass')
            ->willReturn(\get_class($user))
        ;

        self::assertSame($refreshedUser, $this->userProvider->refreshUser($user));
    }

    public function testRefreshDeleted(): void
    {
        $this->expectException(AuthenticationException::class);

        $user = $this->createUser();
        $this->userManager->expects(self::once())
            ->method('findUserByEmail')
            ->willReturn(null)
        ;

        $this->userManager->expects(self::atLeastOnce())
            ->method('getClass')
            ->willReturn(\get_class($user))
        ;

        $this->userProvider->refreshUser($user);
    }

    public function testRefreshInvalidUser(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $user = $this->getMockBuilder(SymfonyUserInterface::class)->getMock();
        $this->userManager
            ->method('getClass')
            ->willReturn(\get_class($user))
        ;

        $this->userProvider->refreshUser($user);
    }

    public function testRefreshInvalidUserClass(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $user         = $this->createUser();
        $providedUser = $this->getMockBuilder(TestUser::class)->getMock();

        $this->userManager->expects(self::atLeastOnce())
            ->method('getClass')
            ->willReturn(\get_class($user))
        ;

        $this->userProvider->refreshUser($providedUser);
    }

    /**
     * @return MockObject&UserInterface
     */
    private function createUser(): UserInterface
    {
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('123');

        return $user;
    }
}
