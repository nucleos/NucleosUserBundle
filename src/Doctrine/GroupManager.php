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
use Nucleos\UserBundle\Model\GroupInterface;
use Nucleos\UserBundle\Model\GroupManager as BaseGroupManager;

/**
 * @phpstan-template GroupTemplate of \Nucleos\UserBundle\Model\GroupInterface
 * @phpstan-extends \Nucleos\UserBundle\Model\GroupManager<GroupTemplate>
 */
final class GroupManager extends BaseGroupManager
{
    private ObjectManager $objectManager;

    /**
     * @phpstan-var class-string<GroupTemplate>
     */
    private string $class;

    /**
     * @phpstan-var ObjectRepository<GroupTemplate>
     */
    private ObjectRepository $repository;

    /**
     * @phpstan-param class-string<GroupTemplate> $class
     */
    public function __construct(ObjectManager $om, string $class)
    {
        $this->objectManager = $om;
        $this->repository    = $om->getRepository($class);

        $metadata    = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    public function deleteGroup(GroupInterface $group): void
    {
        $this->objectManager->remove($group);
        $this->objectManager->flush();
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function findGroupBy(array $criteria): ?GroupInterface
    {
        return $this->repository->findOneBy($criteria);
    }

    public function findGroups(): array
    {
        // @phpstan-ignore-next-line
        return $this->repository->findAll();
    }

    public function updateGroup(GroupInterface $group, bool $andFlush = true): void
    {
        $this->objectManager->persist($group);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }
}
