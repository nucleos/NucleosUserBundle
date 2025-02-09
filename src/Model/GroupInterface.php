<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Model;

interface GroupInterface
{
    public function addRole(string $role): void;

    public function getName(): string;

    public function hasRole(string $role): bool;

    /**
     * @return string[]
     */
    public function getRoles(): array;

    public function removeRole(string $role): void;

    public function setName(string $name): void;

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): void;
}
