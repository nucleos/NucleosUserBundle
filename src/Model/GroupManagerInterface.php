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

namespace Nucleos\UserBundle\Model;

/**
 * @phpstan-template GroupTemplate of \Nucleos\UserBundle\Model\GroupInterface
 */
interface GroupManagerInterface
{
    /**
     * Returns an empty group instance.
     *
     * @return GroupTemplate
     */
    public function createGroup(string $name): GroupInterface;

    /**
     * Deletes a group.
     */
    public function deleteGroup(GroupInterface $group): void;

    /**
     * Finds one group by the given criteria.
     *
     * @return GroupTemplate
     */
    public function findGroupBy(array $criteria): GroupInterface;

    /**
     * Finds a group by name.
     *
     * @return GroupTemplate
     */
    public function findGroupByName(string $name): GroupInterface;

    /**
     * Returns a collection with all group instances.
     *
     * @return UserInterface[]&GroupTemplate[]
     */
    public function findGroups(): array;

    /**
     * Returns the group's fully qualified class name.
     */
    public function getClass(): string;

    /**
     * Updates a group.
     */
    public function updateGroup(GroupInterface $group, bool $andFlush = true): void;
}
