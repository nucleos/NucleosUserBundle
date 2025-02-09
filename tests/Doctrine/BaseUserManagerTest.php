<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\Doctrine;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Nucleos\UserBundle\Doctrine\UserManager;
use Nucleos\UserBundle\Tests\App\Entity\TestUser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class BaseUserManagerTest extends TestCase
{
    /**
     * @var string
     */
    private const USER_CLASS = TestUser::class;

    private readonly UserManager $userManager;

    /**
     * @var MockObject&ObjectManager
     */
    private $om;

    /**
     * @var MockObject&ObjectRepository<TestUser>
     */
    private $repository;

    protected function setUp(): void
    {
        $class            = $this->getMockBuilder(ClassMetadata::class)->getMock();
        $this->om         = $this->getMockBuilder(ObjectManager::class)->getMock();
        $this->repository = $this->getMockBuilder(ObjectRepository::class)->getMock();

        $this->om
            ->method('getRepository')
            ->with(self::equalTo(self::USER_CLASS))
            ->willReturn($this->repository)
        ;
        $this->om
            ->method('getClassMetadata')
            ->with(self::equalTo(self::USER_CLASS))
            ->willReturn($class)
        ;
        $class
            ->method('getName')
            ->willReturn(self::USER_CLASS)
        ;

        $this->userManager = new UserManager($this->om, self::USER_CLASS);
    }

    public function testDeleteUser(): void
    {
        $user = $this->getUser();
        $this->om->expects(self::once())->method('remove')->with(self::equalTo($user));
        $this->om->expects(self::once())->method('flush');

        $this->userManager->deleteUser($user);
    }

    public function testGetClass(): void
    {
        self::assertSame(self::USER_CLASS, $this->userManager->getClass());
    }

    public function testFindUserBy(): void
    {
        $crit = ['foo' => 'bar'];
        $this->repository->expects(self::once())->method('findOneBy')->with(self::equalTo($crit))
            ->willReturn(null)
        ;

        $this->userManager->findUserBy($crit);
    }

    public function testFindUsers(): void
    {
        $this->repository->expects(self::once())->method('findAll')->willReturn([]);

        $this->userManager->findUsers();
    }

    public function testUpdateUser(): void
    {
        $user = $this->getUser();
        $this->om->expects(self::once())->method('persist')->with(self::equalTo($user));
        $this->om->expects(self::once())->method('flush');

        $this->userManager->updateUser($user);
    }

    private function getUser(): TestUser
    {
        $userClass = self::USER_CLASS;

        return new $userClass();
    }
}
