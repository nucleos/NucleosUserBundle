<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Model;

use DateTime;
use Nucleos\UserBundle\Model\GroupInterface;
use Nucleos\UserBundle\Model\User;
use Nucleos\UserBundle\Model\UserInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testUsername(): void
    {
        $user = $this->getUser();
        $user->setUsername('tony');
        self::assertSame('tony', $user->getUsername());
    }

    public function testEmail(): void
    {
        $user = $this->getUser();

        $user->setEmail('tony@mail.org');
        self::assertSame('tony@mail.org', $user->getEmail());
    }

    public function testIsPasswordRequestNonExpired(): void
    {
        $user                = $this->getUser();
        $passwordRequestedAt = new DateTime('-10 seconds');

        $user->setPasswordRequestedAt($passwordRequestedAt);

        self::assertSame($passwordRequestedAt, $user->getPasswordRequestedAt());
        self::assertTrue($user->isPasswordRequestNonExpired(15));
        self::assertFalse($user->isPasswordRequestNonExpired(5));
    }

    public function testIsPasswordRequestAtCleared(): void
    {
        $user                = $this->getUser();
        $passwordRequestedAt = new DateTime('-10 seconds');

        $user->setPasswordRequestedAt($passwordRequestedAt);
        $user->setPasswordRequestedAt(null);

        self::assertFalse($user->isPasswordRequestNonExpired(15));
        self::assertFalse($user->isPasswordRequestNonExpired(5));
    }

    public function testTrueHasRole(): void
    {
        $user        = $this->getUser();
        $defaultrole = User::ROLE_DEFAULT;
        $newrole     = 'ROLE_X';
        self::assertTrue($user->hasRole($defaultrole));
        $user->addRole($defaultrole);
        self::assertTrue($user->hasRole($defaultrole));
        $user->addRole($newrole);
        self::assertTrue($user->hasRole($newrole));
    }

    public function testFalseHasRole(): void
    {
        $user    = $this->getUser();
        $newrole = 'ROLE_X';
        self::assertFalse($user->hasRole($newrole));
        $user->addRole($newrole);
        self::assertTrue($user->hasRole($newrole));
    }

    public function testIsEqualTo(): void
    {
        $user = $this->getUser();
        self::assertTrue($user->isEqualTo($user));
        self::assertFalse($user->isEqualTo($this->getMockBuilder(UserInterface::class)->getMock()));

        $user2 = $this->getUser();
        $user2->setPassword('secret');
        self::assertFalse($user->isEqualTo($user2));

        $user3 = $this->getUser();
        $user3->setPassword('secret');
        self::assertFalse($user->isEqualTo($user3));

        $user4 = $this->getUser();
        $user4->setPassword('secret');
        $user4->setUsername('f00b4r');
        self::assertFalse($user->isEqualTo($user4));
    }

    /**
     * @return MockObject&User<GroupInterface>
     */
    private function getUser(): MockObject
    {
        $user =  $this->getMockForAbstractClass(User::class);
        $user->setUsername('username');
        $user->setPassword('password');

        return $user;
    }
}
