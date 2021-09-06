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

use Nucleos\UserBundle\Tests\App\Entity\TestUser;
use Nucleos\UserBundle\Tests\Fixtures\SaltedPasswordHasher;
use Nucleos\UserBundle\Util\PasswordUpdater;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

final class PasswordUpdaterTest extends TestCase
{
    /**
     * @var PasswordUpdater
     */
    private $updater;
    /**
     * @var MockObject&PasswordHasherFactoryInterface
     */
    private $passwordHasherFactory;

    protected function setUp(): void
    {
        $this->passwordHasherFactory = $this->getMockBuilder(PasswordHasherFactoryInterface::class)->getMock();

        $this->updater = new PasswordUpdater($this->passwordHasherFactory);
    }

    public function testUpdatePassword(): void
    {
        $passwordHasher = $this->createMock(SaltedPasswordHasher::class);
        $user           = new TestUser();
        $user->setPlainPassword('password');

        $this->passwordHasherFactory->expects(static::once())
            ->method('getPasswordHasher')
            ->with($user)
            ->willReturn($passwordHasher)
        ;

        $passwordHasher->expects(static::once())
            ->method('hash')
            ->with('password', static::isType('string'))
            ->willReturn('hashedPassword')
        ;

        $this->updater->hashPassword($user);
        static::assertSame('hashedPassword', $user->getPassword(), '->updatePassword() sets hashed password');
        static::assertNotNull($user->getSalt());
        static::assertNull($user->getPlainPassword(), '->updatePassword() erases credentials');
    }

    public function testUpdatePasswordSelfSaltedPasswordHasher(): void
    {
        $passwordHasher = $this->getMockPasswordHasher();
        $user           = new TestUser();
        $user->setPlainPassword('password');
        $user->setSalt('old_salt');

        $this->passwordHasherFactory->expects(static::once())
            ->method('getPasswordHasher')
            ->with($user)
            ->willReturn($passwordHasher)
        ;

        $passwordHasher->expects(static::once())
            ->method('hash')
            ->with('password')
            ->willReturn('hashedPassword')
        ;

        $this->updater->hashPassword($user);
        static::assertSame('hashedPassword', $user->getPassword(), '->updatePassword() sets hashed password');
        static::assertNull($user->getSalt());
        static::assertNull($user->getPlainPassword(), '->updatePassword() erases credentials');
    }

    public function testDoesNotUpdateWithoutPlainPassword(): void
    {
        $user = new TestUser();
        $user->setPassword('hash');

        $user->setPlainPassword('');

        $this->updater->hashPassword($user);
        static::assertSame('hash', $user->getPassword());
    }

    /**
     * @return MockObject&PasswordHasherInterface
     */
    private function getMockPasswordHasher(): MockObject
    {
        return $this->getMockBuilder(PasswordHasherInterface::class)->getMock();
    }
}
