<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Security;

use Nucleos\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

final class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(BaseUserInterface $user): void
    {
        if (!$user instanceof UserInterface) {
            return;
        }

        $this->verifyAccountLocked($user);
        $this->verifyAccountEnabled($user);
        $this->verifyAccountExpired($user);
    }

    public function checkPostAuth(BaseUserInterface $user): void
    {
        if (!$user instanceof UserInterface) {
            return;
        }

        $this->verifyCredentialsExpired($user);
    }

    private function verifyAccountLocked(UserInterface $user): void
    {
        if (!$user->isAccountNonLocked()) {
            $ex = new LockedException('User account is locked.');
            $ex->setUser($user);

            throw $ex;
        }
    }

    private function verifyAccountEnabled(UserInterface $user): void
    {
        if (!$user->isEnabled()) {
            $ex = new DisabledException('User account is disabled.');
            $ex->setUser($user);

            throw $ex;
        }
    }

    private function verifyAccountExpired(UserInterface $user): void
    {
        if (!$user->isAccountNonExpired()) {
            $ex = new AccountExpiredException('User account has expired.');
            $ex->setUser($user);

            throw $ex;
        }
    }

    private function verifyCredentialsExpired(UserInterface $user): void
    {
        if (!$user->isCredentialsNonExpired()) {
            $ex = new CredentialsExpiredException('User credentials have expired.');
            $ex->setUser($user);

            throw $ex;
        }
    }
}
