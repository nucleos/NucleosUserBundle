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

namespace Nucleos\UserBundle\Tests\Model;

use Nucleos\UserBundle\Model\GroupInterface;
use Nucleos\UserBundle\Model\User;
use Nucleos\UserBundle\Model\UserManager;
use Nucleos\UserBundle\Util\CanonicalFieldsUpdater;
use Nucleos\UserBundle\Util\PasswordUpdaterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UserManagerTest extends TestCase
{
    /**
     * @var MockObject&UserManager
     */
    private $manager;

    /**
     * @var MockObject&PasswordUpdaterInterface
     */
    private $passwordUpdater;

    /**
     * @var MockObject&CanonicalFieldsUpdater
     */
    private $fieldsUpdater;

    protected function setUp(): void
    {
        $this->passwordUpdater = $this->getMockBuilder(PasswordUpdaterInterface::class)->getMock();
        $this->fieldsUpdater   = $this->getMockBuilder(CanonicalFieldsUpdater::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->manager = $this->getUserManager([
            $this->passwordUpdater,
            $this->fieldsUpdater,
        ]);
    }

    public function testUpdateCanonicalFields(): void
    {
        $user = $this->getUser();

        $this->fieldsUpdater->expects(static::once())
            ->method('updateCanonicalFields')
            ->with(static::identicalTo($user))
        ;

        $this->manager->updateCanonicalFields($user);
    }

    public function testUpdatePassword(): void
    {
        $user = $this->getUser();

        $this->passwordUpdater->expects(static::once())
            ->method('hashPassword')
            ->with(static::identicalTo($user))
        ;

        $this->manager->updatePassword($user);
    }

    public function testFindUserByUsername(): void
    {
        $this->manager->expects(static::once())
            ->method('findUserBy')
            ->with(static::equalTo(['usernameCanonical' => 'jack']))
        ;
        $this->fieldsUpdater->expects(static::once())
            ->method('canonicalizeUsername')
            ->with('jack')
            ->willReturn('jack')
        ;

        $this->manager->findUserByUsername('jack');
    }

    public function testFindUserByUsernameLowercasesTheUsername(): void
    {
        $this->manager->expects(static::once())
            ->method('findUserBy')
            ->with(static::equalTo(['usernameCanonical' => 'jack']))
        ;
        $this->fieldsUpdater->expects(static::once())
            ->method('canonicalizeUsername')
            ->with('JaCk')
            ->willReturn('jack')
        ;

        $this->manager->findUserByUsername('JaCk');
    }

    public function testFindUserByEmail(): void
    {
        $this->manager->expects(static::once())
            ->method('findUserBy')
            ->with(static::equalTo(['emailCanonical' => 'jack@email.org']))
        ;
        $this->fieldsUpdater->expects(static::once())
            ->method('canonicalizeEmail')
            ->with('jack@email.org')
            ->willReturn('jack@email.org')
        ;

        $this->manager->findUserByEmail('jack@email.org');
    }

    public function testFindUserByEmailLowercasesTheEmail(): void
    {
        $this->manager->expects(static::once())
            ->method('findUserBy')
            ->with(static::equalTo(['emailCanonical' => 'jack@email.org']))
        ;
        $this->fieldsUpdater->expects(static::once())
            ->method('canonicalizeEmail')
            ->with('JaCk@EmAiL.oRg')
            ->willReturn('jack@email.org')
        ;

        $this->manager->findUserByEmail('JaCk@EmAiL.oRg');
    }

    public function testFindUserByUsernameOrEmailWithUsername(): void
    {
        $this->manager->expects(static::once())
            ->method('findUserBy')
            ->with(static::equalTo(['usernameCanonical' => 'jack']))
        ;
        $this->fieldsUpdater->expects(static::once())
            ->method('canonicalizeUsername')
            ->with('JaCk')
            ->willReturn('jack')
        ;

        $this->manager->findUserByUsernameOrEmail('JaCk');
    }

    public function testFindUserByUsernameOrEmailWithEmail(): void
    {
        $this->manager->expects(static::once())
            ->method('findUserBy')
            ->with(static::equalTo(['emailCanonical' => 'jack@email.org']))
            ->willReturn($this->getUser())
        ;
        $this->fieldsUpdater->expects(static::once())
            ->method('canonicalizeEmail')
            ->with('JaCk@EmAiL.oRg')
            ->willReturn('jack@email.org')
        ;

        $this->manager->findUserByUsernameOrEmail('JaCk@EmAiL.oRg');
    }

    public function testFindUserByUsernameOrEmailWithUsernameThatLooksLikeEmail(): void
    {
        $usernameThatLooksLikeEmail = 'bob@example.com';
        $user                       = $this->getUser();

        $this->manager->expects(static::exactly(2))
            ->method('findUserBy')
            ->withConsecutive(
                [static::equalTo(['emailCanonical' => $usernameThatLooksLikeEmail])],
                [static::equalTo(['usernameCanonical' => $usernameThatLooksLikeEmail])],
            )
            ->willReturn(
                null,
                $user,
            )
        ;
        $this->fieldsUpdater->expects(static::once())
            ->method('canonicalizeEmail')
            ->with($usernameThatLooksLikeEmail)
            ->willReturn($usernameThatLooksLikeEmail)
        ;

        $this->fieldsUpdater->expects(static::once())
            ->method('canonicalizeUsername')
            ->with($usernameThatLooksLikeEmail)
            ->willReturn($usernameThatLooksLikeEmail)
        ;

        $actualUser = $this->manager->findUserByUsernameOrEmail($usernameThatLooksLikeEmail);

        static::assertSame($user, $actualUser);
    }

    /**
     * @return MockObject&User<GroupInterface>
     */
    private function getUser(): MockObject
    {
        return $this->getMockBuilder(User::class)
            ->getMockForAbstractClass()
        ;
    }

    /**
     * @return MockObject&UserManager
     */
    private function getUserManager(array $args): MockObject
    {
        return $this->getMockBuilder(UserManager::class)
            ->setConstructorArgs($args)
            ->getMockForAbstractClass()
        ;
    }
}
