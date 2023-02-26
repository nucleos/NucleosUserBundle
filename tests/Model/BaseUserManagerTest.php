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

use Nucleos\UserBundle\Model\BaseUserManager;
use Nucleos\UserBundle\Model\GroupInterface;
use Nucleos\UserBundle\Model\User;
use Nucleos\UserBundle\Util\CanonicalFieldsUpdater;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class BaseUserManagerTest extends TestCase
{
    /**
     * @var BaseUserManager&MockObject
     */
    private $manager;

    /**
     * @var CanonicalFieldsUpdater&MockObject
     */
    private $fieldsUpdater;

    protected function setUp(): void
    {
        $this->fieldsUpdater = $this->createMock(CanonicalFieldsUpdater::class);

        $this->manager = $this->getUserManager([
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
     * @return BaseUserManager&MockObject
     */
    private function getUserManager(array $args): MockObject
    {
        return $this->getMockBuilder(BaseUserManager::class)
            ->setConstructorArgs($args)
            ->getMockForAbstractClass()
        ;
    }
}
