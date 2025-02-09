<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Model;

use Doctrine\Common\Collections\Collection;

/**
 * @phpstan-template GroupTemplate of \Nucleos\UserBundle\Model\GroupInterface
 */
interface GroupAwareUser
{
    /**
     * Gets the groups granted to the user.
     *
     * @phpstan-return Collection<array-key, GroupTemplate>
     */
    public function getGroups(): Collection;

    /**
     * Gets the name of the groups which includes the user.
     *
     * @return string[]
     */
    public function getGroupNames(): array;

    /**
     * Indicates whether the user belongs to the specified group or not.
     *
     * @param string $name Name of the group
     */
    public function hasGroup(string $name): bool;

    /**
     * Add a group to the user groups.
     *
     * @phpstan-param GroupTemplate $group
     */
    public function addGroup(GroupInterface $group): void;

    /**
     * Remove a group from the user groups.
     *
     * @phpstan-param GroupTemplate $group
     */
    public function removeGroup(GroupInterface $group): void;
}
