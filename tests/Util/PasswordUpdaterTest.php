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
use Nucleos\UserBundle\Tests\Fixtures\SelfSaltedEncoder;
use Nucleos\UserBundle\Util\PasswordUpdater;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

final class PasswordUpdaterTest extends TestCase
{
    /**
     * @var PasswordUpdater
     */
    private $updater;
    /**
     * @var MockObject&EncoderFactoryInterface
     */
    private $encoderFactory;

    protected function setUp(): void
    {
        $this->encoderFactory = $this->getMockBuilder(EncoderFactoryInterface::class)->getMock();

        $this->updater = new PasswordUpdater($this->encoderFactory);
    }

    public function testUpdatePassword(): void
    {
        $encoder = $this->getMockPasswordEncoder();
        $user    = new TestUser();
        $user->setPlainPassword('password');

        $this->encoderFactory->expects(static::once())
            ->method('getEncoder')
            ->with($user)
            ->willReturn($encoder)
        ;

        $encoder->expects(static::once())
            ->method('encodePassword')
            ->with('password', static::isType('string'))
            ->willReturn('encodedPassword')
        ;

        $this->updater->hashPassword($user);
        static::assertSame('encodedPassword', $user->getPassword(), '->updatePassword() sets encoded password');
        static::assertNotNull($user->getSalt());
        static::assertNull($user->getPlainPassword(), '->updatePassword() erases credentials');
    }

    public function testUpdatePasswordSelfSaltedEncoder(): void
    {
        $encoder = $this->createMock(SelfSaltedEncoder::class);
        $user    = new TestUser();
        $user->setPlainPassword('password');
        $user->setSalt('old_salt');

        $this->encoderFactory->expects(static::once())
            ->method('getEncoder')
            ->with($user)
            ->willReturn($encoder)
        ;

        $encoder->expects(static::once())
            ->method('encodePassword')
            ->with('password', static::isNull())
            ->willReturn('encodedPassword')
        ;

        $this->updater->hashPassword($user);
        static::assertSame('encodedPassword', $user->getPassword(), '->updatePassword() sets encoded password');
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
     * @return MockObject&PasswordEncoderInterface
     */
    private function getMockPasswordEncoder(): MockObject
    {
        return $this->getMockBuilder(PasswordEncoderInterface::class)->getMock();
    }
}
