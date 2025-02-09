<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Model;

use DateTime;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

interface UserInterface extends PasswordAuthenticatedUserInterface, BaseUserInterface, EquatableInterface
{
    public const ROLE_DEFAULT = 'ROLE_USER';

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * Returns the identifier for this user (e.g. its username or email address).
     */
    public function getUserIdentifier(): string;

    /**
     * Gets the username. This is no longer provided by the parent interface
     * (replaced by getUserIdentifier()) but users still have usernames.
     */
    public function getUsername(): string;

    /**
     * Sets the username.
     */
    public function setUsername(string $username): void;

    public function getEmail(): string;

    /**
     * Sets the email.
     */
    public function setEmail(string $email): void;

    /**
     * Gets the plain password.
     *
     * @deprecated
     */
    public function getPlainPassword(): ?string;

    /**
     * Sets the plain password.
     *
     * @deprecated
     */
    public function setPlainPassword(?string $password): void;

    /**
     * Sets the hashed password.
     */
    public function setPassword(string $password): void;

    /**
     * Tells if the the given user has the super admin role.
     */
    public function isSuperAdmin(): bool;

    public function setEnabled(bool $boolean): void;

    /**
     * Sets the super admin status.
     */
    public function setSuperAdmin(bool $boolean): void;

    /**
     * Gets the confirmation token.
     */
    public function getConfirmationToken(): ?string;

    /**
     * Sets the confirmation token.
     */
    public function setConfirmationToken(?string $confirmationToken): void;

    /**
     * Sets the timestamp that the user requested a password reset.
     */
    public function setPasswordRequestedAt(?DateTime $date = null): void;

    /**
     * Checks whether the password reset request has expired.
     *
     * @param int $ttl Requests older than this many seconds will be considered expired
     */
    public function isPasswordRequestNonExpired(int $ttl): bool;

    /**
     * Sets the last login time.
     */
    public function setLastLogin(?DateTime $time = null): void;

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the AuthorizationChecker, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $authorizationChecker->isGranted('ROLE_USER');
     */
    public function hasRole(string $role): bool;

    /**
     * Sets the roles of the user.
     *
     * This overwrites any previous roles.
     *
     * @param string[] $roles
     */
    public function setRoles(array $roles): void;

    /**
     * Adds a role to the user.
     */
    public function addRole(string $role): void;

    /**
     * Removes a role to the user.
     */
    public function removeRole(string $role): void;

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired(): bool;

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked(): bool;

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired(): bool;

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled(): bool;
}
