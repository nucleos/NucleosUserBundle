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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

/**
 * @SuppressWarnings("PHPMD.TooManyPublicMethods")
 *
 * @phpstan-template GroupTemplate of GroupInterface
 *
 * @phpstan-implements GroupAwareUser<GroupTemplate>
 *
 * @SuppressWarnings("PHPMD.TooManyPublicMethods")
 */
abstract class User implements UserInterface, GroupAwareUser, LocaleAwareUser
{
    protected ?string $username = null;

    protected ?string $email = null;

    protected bool $enabled = false;

    protected ?string $password = null;

    /**
     * @deprecated
     */
    protected ?string $plainPassword = null;

    protected ?DateTime $lastLogin = null;

    protected ?string $confirmationToken = null;

    protected ?DateTime $passwordRequestedAt = null;

    /**
     * @phpstan-var Collection<array-key, GroupTemplate>
     */
    protected Collection $groups;

    /**
     * @var string[]
     */
    protected array $roles = [];

    protected ?string $locale = null;

    protected ?string $timezone = null;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getUsername();
    }

    public function addRole(string $role): void
    {
        $role = strtoupper($role);

        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUsername(): string
    {
        return $this->username ?? '';
    }

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function getEmail(): string
    {
        return $this->email ?? '';
    }

    public function getPassword(): string
    {
        return $this->password ?? '';
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getLastLogin(): ?DateTime
    {
        return $this->lastLogin;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_values(array_unique($roles));
    }

    public function hasRole(string $role): bool
    {
        return \in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function isAccountNonExpired(): bool
    {
        return true;
    }

    public function isAccountNonLocked(): bool
    {
        return true;
    }

    public function isCredentialsNonExpired(): bool
    {
        return true;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    public function removeRole(string $role): void
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    public function setUsername(string $username): void
    {
        $this->username = strtolower($username);
    }

    public function setEmail(string $email): void
    {
        $this->email = strtolower($email);
    }

    public function setEnabled(bool $boolean): void
    {
        $this->enabled = $boolean;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setSuperAdmin(bool $boolean): void
    {
        if ($boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }
    }

    public function setPlainPassword(?string $password): void
    {
        $this->plainPassword       = $password;
        $this->passwordRequestedAt = null;
    }

    public function setLastLogin(?DateTime $time = null): void
    {
        $this->lastLogin = $time;
    }

    public function setConfirmationToken(?string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function setPasswordRequestedAt(?DateTime $date = null): void
    {
        $this->passwordRequestedAt = $date;
    }

    public function getPasswordRequestedAt(): ?DateTime
    {
        return $this->passwordRequestedAt;
    }

    public function isPasswordRequestNonExpired(int $ttl): bool
    {
        return $this->getPasswordRequestedAt() instanceof DateTime
               && $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    public function setRoles(array $roles): void
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function getGroups(): Collection
    {
        if (!isset($this->groups)) {
            return new ArrayCollection();
        }

        return $this->groups;
    }

    public function getGroupNames(): array
    {
        $names = [];
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    public function hasGroup(string $name): bool
    {
        return \in_array($name, $this->getGroupNames(), true);
    }

    public function addGroup(GroupInterface $group): void
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }
    }

    public function removeGroup(GroupInterface $group): void
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setTimezone(?string $timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    /**
     * @SuppressWarnings("PHPMD.CyclomaticComplexity")
     */
    public function isEqualTo(BaseUserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}
