<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Model;

use Nucleos\UserBundle\Model\BaseUserManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class BaseUserManagerTest extends TestCase
{
    /**
     * @var BaseUserManager&MockObject
     */
    private $manager;

    protected function setUp(): void
    {
        $this->manager = $this->getUserManager([]);
    }

    public function testFindUserByUsername(): void
    {
        $this->manager->expects(self::once())
            ->method('findUserBy')
            ->with(self::equalTo(['username' => 'jack']))
        ;

        $this->manager->findUserByUsername('jack');
    }

    public function testFindUserByUsernameLowercasesTheUsername(): void
    {
        $this->manager->expects(self::once())
            ->method('findUserBy')
            ->with(self::equalTo(['username' => 'jack']))
        ;

        $this->manager->findUserByUsername('JaCk');
    }

    public function testFindUserByEmail(): void
    {
        $this->manager->expects(self::once())
            ->method('findUserBy')
            ->with(self::equalTo(['email' => 'jack@email.org']))
        ;

        $this->manager->findUserByEmail('jack@email.org');
    }

    public function testFindUserByEmailLowercasesTheEmail(): void
    {
        $this->manager->expects(self::once())
            ->method('findUserBy')
            ->with(self::equalTo(['email' => 'jack@email.org']))
        ;

        $this->manager->findUserByEmail('JaCk@EmAiL.oRg');
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
