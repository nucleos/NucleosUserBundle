<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Doctrine;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Nucleos\UserBundle\Model\BaseUserManager;
use Nucleos\UserBundle\Model\UserInterface;

final class UserManager extends BaseUserManager
{
    private readonly ObjectManager $objectManager;

    /**
     * @phpstan-var class-string<UserInterface>
     */
    private readonly string $class;

    /**
     * @phpstan-param class-string<UserInterface> $class
     */
    public function __construct(ObjectManager $om, string $class)
    {
        $this->objectManager = $om;
        $this->class         = $class;
    }

    public function deleteUser(UserInterface $user): void
    {
        $this->objectManager->remove($user);
        $this->objectManager->flush();
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function findUserBy(array $criteria): ?UserInterface
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    public function findUsers(): array
    {
        return $this->getRepository()->findAll();
    }

    public function reloadUser(UserInterface $user): void
    {
        $this->objectManager->refresh($user);
    }

    public function updateUser(UserInterface $user, bool $andFlush = true): void
    {
        $this->objectManager->persist($user);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * @phpstan-return ObjectRepository<UserInterface>
     */
    protected function getRepository(): ObjectRepository
    {
        return $this->objectManager->getRepository($this->getClass());
    }
}
