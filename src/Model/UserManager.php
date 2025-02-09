<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Model;

interface UserManager
{
    /**
     * Creates an empty user instance.
     */
    public function createUser(): UserInterface;

    /**
     * Deletes a user.
     */
    public function deleteUser(UserInterface $user): void;

    /**
     * Finds one user by the given criteria.
     *
     * @param array<string, mixed> $criteria
     */
    public function findUserBy(array $criteria): ?UserInterface;

    /**
     * Find a user by its username.
     */
    public function findUserByUsername(string $username): ?UserInterface;

    /**
     * Finds a user by its email.
     */
    public function findUserByEmail(string $email): ?UserInterface;

    /**
     * Finds a user by its confirmationToken.
     */
    public function findUserByConfirmationToken(string $token): ?UserInterface;

    /**
     * Returns a collection with all user instances.
     *
     * @return UserInterface[]
     */
    public function findUsers(): array;

    /**
     * Returns the user's fully qualified class name.
     *
     * @phpstan-return class-string<UserInterface>
     */
    public function getClass(): string;

    /**
     * Reloads a user.
     */
    public function reloadUser(UserInterface $user): void;

    /**
     * Updates a user.
     */
    public function updateUser(UserInterface $user, bool $andFlush = true): void;
}
