<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Model;

/**
 * @phpstan-template GroupTemplate of GroupInterface
 */
interface GroupManager
{
    /**
     * Returns an empty group instance.
     *
     * @phpstan-return GroupTemplate
     */
    public function createGroup(string $name): GroupInterface;

    /**
     * Deletes a group.
     */
    public function deleteGroup(GroupInterface $group): void;

    /**
     * Finds one group by the given criteria.
     *
     * @param array<string, mixed> $criteria
     *
     * @phpstan-return GroupTemplate|null
     */
    public function findGroupBy(array $criteria): ?GroupInterface;

    /**
     * Finds a group by name.
     *
     * @phpstan-return GroupTemplate|null
     */
    public function findGroupByName(string $name): ?GroupInterface;

    /**
     * Returns a collection with all group instances.
     *
     * @phpstan-return GroupTemplate[]
     */
    public function findGroups(): array;

    /**
     * Returns the group's fully qualified class name.
     *
     * @phpstan-return class-string<GroupTemplate>
     */
    public function getClass(): string;

    /**
     * Updates a group.
     */
    public function updateGroup(GroupInterface $group, bool $andFlush = true): void;
}
