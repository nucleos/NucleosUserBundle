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

use Nucleos\UserBundle\Util\CanonicalFieldsUpdater;

abstract class UserManager implements UserManagerInterface
{
    private CanonicalFieldsUpdater $canonicalFieldsUpdater;

    public function __construct(CanonicalFieldsUpdater $canonicalFieldsUpdater)
    {
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
    }

    public function createUser(): UserInterface
    {
        $class = $this->getClass();

        return new $class();
    }

    public function findUserByEmail(string $email): ?UserInterface
    {
        return $this->findUserBy(['emailCanonical' => $this->canonicalFieldsUpdater->canonicalizeEmail($email)]);
    }

    public function findUserByUsername(string $username): ?UserInterface
    {
        return $this->findUserBy(['usernameCanonical' => $this->canonicalFieldsUpdater->canonicalizeUsername($username)]);
    }

    public function findUserByConfirmationToken(string $token): ?UserInterface
    {
        return $this->findUserBy(['confirmationToken' => $token]);
    }

    public function updateCanonicalFields(UserInterface $user): void
    {
        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
    }

    protected function getCanonicalFieldsUpdater(): CanonicalFieldsUpdater
    {
        return $this->canonicalFieldsUpdater;
    }
}
