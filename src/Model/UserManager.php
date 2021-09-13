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
use Nucleos\UserBundle\Util\PasswordUpdaterInterface;

abstract class UserManager implements UserManagerInterface
{
    private PasswordUpdaterInterface $passwordUpdater;

    private CanonicalFieldsUpdater $canonicalFieldsUpdater;

    public function __construct(PasswordUpdaterInterface $passwordUpdater, CanonicalFieldsUpdater $canonicalFieldsUpdater)
    {
        $this->passwordUpdater        = $passwordUpdater;
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

    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?UserInterface
    {
        if (0 !== preg_match('/^.+\@\S+\.\S+$/', $usernameOrEmail)) {
            $user = $this->findUserByEmail($usernameOrEmail);
            if (null !== $user) {
                return $user;
            }
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    public function findUserByConfirmationToken(string $token): ?UserInterface
    {
        return $this->findUserBy(['confirmationToken' => $token]);
    }

    public function updateCanonicalFields(UserInterface $user): void
    {
        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
    }

    public function updatePassword(UserInterface $user): void
    {
        $this->passwordUpdater->hashPassword($user);
    }

    protected function getPasswordUpdater(): PasswordUpdaterInterface
    {
        return $this->passwordUpdater;
    }

    protected function getCanonicalFieldsUpdater(): CanonicalFieldsUpdater
    {
        return $this->canonicalFieldsUpdater;
    }
}
