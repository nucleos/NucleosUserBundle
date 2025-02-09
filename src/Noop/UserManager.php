<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Noop;

use Nucleos\UserBundle\Model\BaseUserManager;
use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\Noop\Exception\NoDriverException;

final class UserManager extends BaseUserManager
{
    public function deleteUser(UserInterface $user): void
    {
        throw new NoDriverException();
    }

    public function findUserBy(array $criteria): ?UserInterface
    {
        throw new NoDriverException();
    }

    public function findUsers(): array
    {
        throw new NoDriverException();
    }

    public function getClass(): string
    {
        throw new NoDriverException();
    }

    public function reloadUser(UserInterface $user): void
    {
        throw new NoDriverException();
    }

    public function updateUser(UserInterface $user, bool $andFlush = true): void
    {
        throw new NoDriverException();
    }
}
