<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Util;

use InvalidArgumentException;
use Nucleos\UserBundle\Event\UserEvent;
use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\Model\UserManager;
use Nucleos\UserBundle\NucleosUserEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SimpleUserManipulator implements UserManipulator
{
    private readonly UserManager $userManager;

    private readonly EventDispatcherInterface $dispatcher;

    private readonly RequestStack $requestStack;

    private readonly ?UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        UserManager $userManager,
        EventDispatcherInterface $dispatcher,
        RequestStack $requestStack,
        ?UserPasswordHasherInterface $userPasswordHasher = null
    ) {
        $this->userManager        = $userManager;
        $this->dispatcher         = $dispatcher;
        $this->requestStack       = $requestStack;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function create(string $username, string $password, string $email, bool $active, bool $superadmin): UserInterface
    {
        $user = $this->userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $this->setPassword($user, $password);
        $user->setEnabled($active);
        $user->setSuperAdmin($superadmin);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch($event, NucleosUserEvents::USER_CREATED);

        return $user;
    }

    public function activate(string $username): void
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        $user->setEnabled(true);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch($event, NucleosUserEvents::USER_ACTIVATED);
    }

    public function deactivate(string $username): void
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        $user->setEnabled(false);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch($event, NucleosUserEvents::USER_DEACTIVATED);
    }

    public function changePassword(string $username, string $password): void
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        $this->setPassword($user, $password);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch($event, NucleosUserEvents::USER_PASSWORD_CHANGED);
    }

    public function promote(string $username): void
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        $user->setSuperAdmin(true);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch($event, NucleosUserEvents::USER_PROMOTED);
    }

    public function demote(string $username): void
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        $user->setSuperAdmin(false);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch($event, NucleosUserEvents::USER_DEMOTED);
    }

    /**
     * @return bool true if role was added, false if user already had the role
     */
    public function addRole(string $username, string $role): bool
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        if ($user->hasRole($role)) {
            return false;
        }
        $user->addRole($role);
        $this->userManager->updateUser($user);

        return true;
    }

    /**
     * @return bool true if role was removed, false if user didn't have the role
     */
    public function removeRole(string $username, string $role): bool
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        if (!$user->hasRole($role)) {
            return false;
        }
        $user->removeRole($role);
        $this->userManager->updateUser($user);

        return true;
    }

    /**
     * @throws InvalidArgumentException When user does not exist
     */
    private function findUserByUsernameOrThrowException(string $username): UserInterface
    {
        $user = $this->userManager->findUserByUsername($username);

        if (null === $user) {
            throw new InvalidArgumentException(\sprintf('User identified by "%s" username does not exist.', $username));
        }

        return $user;
    }

    private function getRequest(): ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    private function setPassword(UserInterface $user, string $password): void
    {
        $user->setPlainPassword($password);

        if (null !== $this->userPasswordHasher) {
            $user->setPassword(
                $this->userPasswordHasher->hashPassword($user, $password)
            );
        }
    }
}
