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

namespace Nucleos\UserBundle\Doctrine;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\Model\UserManager as BaseUserManager;
use Nucleos\UserBundle\Util\CanonicalFieldsUpdater;
use Nucleos\UserBundle\Util\PasswordUpdaterInterface;

final class UserManager extends BaseUserManager
{
    private ObjectManager $objectManager;

    /**
     * @phpstan-var class-string<UserInterface>
     */
    private string $class;

    /**
     * @phpstan-param class-string<UserInterface> $class
     */
    public function __construct(PasswordUpdaterInterface $passwordUpdater, CanonicalFieldsUpdater $canonicalFieldsUpdater, ObjectManager $om, string $class)
    {
        parent::__construct($passwordUpdater, $canonicalFieldsUpdater);

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
        if (false !== strpos($this->class, ':')) {
            $metadata    = $this->objectManager->getClassMetadata($this->class);
            $this->class = $metadata->getName();
        }

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
        $this->updateCanonicalFields($user);
        $this->updatePassword($user);

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
