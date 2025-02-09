<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Util;

use Nucleos\UserBundle\Model\UserInterface;

interface UserManipulator
{
    public function create(string $username, string $password, string $email, bool $active, bool $superadmin): UserInterface;

    public function activate(string $username): void;

    public function deactivate(string $username): void;

    public function changePassword(string $username, string $password): void;

    public function promote(string $username): void;

    public function demote(string $username): void;

    /**
     * @return bool true if role was added, false if user already had the role
     */
    public function addRole(string $username, string $role): bool;

    /**
     * @return bool true if role was removed, false if user didn't have the role
     */
    public function removeRole(string $username, string $role): bool;
}
