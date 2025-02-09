<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Model;

abstract class BaseUserManager implements UserManager
{
    public function createUser(): UserInterface
    {
        $class = $this->getClass();

        return new $class();
    }

    public function findUserByEmail(string $email): ?UserInterface
    {
        return $this->findUserBy(['email' => strtolower($email)]);
    }

    public function findUserByUsername(string $username): ?UserInterface
    {
        return $this->findUserBy(['username' => strtolower($username)]);
    }

    public function findUserByConfirmationToken(string $token): ?UserInterface
    {
        return $this->findUserBy(['confirmationToken' => $token]);
    }
}
