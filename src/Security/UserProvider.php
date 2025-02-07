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

namespace Nucleos\UserBundle\Security;

use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\Model\UserManager;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @phpstan-implements UserProviderInterface<UserInterface>
 */
class UserProvider implements UserProviderInterface
{
    protected UserManager $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->loadUserByUsername($identifier);
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        $user = $this->findUser($username);

        if (null === $user) {
            throw new UserNotFoundException(\sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    public function refreshUser(SecurityUserInterface $user): UserInterface
    {
        if (!$user instanceof UserInterface) {
            throw new UnsupportedUserException(\sprintf('Expected an instance of Nucleos\UserBundle\Model\UserInterface, but got "%s".', \get_class($user)));
        }

        if (!$this->supportsClass(\get_class($user))) {
            throw new UnsupportedUserException(\sprintf('Expected an instance of %s, but got "%s".', $this->userManager->getClass(), \get_class($user)));
        }

        if (null === $reloadedUser = $this->findUser($user->getUserIdentifier())) {
            throw new AuthenticationException(\sprintf('User with ID "%s" could not be reloaded.', $user->getUserIdentifier()));
        }

        return $reloadedUser;
    }

    /**
     * @param mixed $class
     */
    public function supportsClass($class): bool
    {
        $userClass = $this->userManager->getClass();

        return $userClass === $class || is_subclass_of($class, $userClass);
    }

    protected function findUser(string $identifier): ?UserInterface
    {
        return $this->userManager->findUserByUsername($identifier);
    }
}
