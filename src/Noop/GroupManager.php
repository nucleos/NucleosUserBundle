<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Noop;

use Nucleos\UserBundle\Model\BaseGroupManager;
use Nucleos\UserBundle\Model\GroupInterface;
use Nucleos\UserBundle\Noop\Exception\NoDriverException;

/**
 * @phpstan-template GroupTemplate of \Nucleos\UserBundle\Model\GroupInterface
 *
 * @phpstan-extends BaseGroupManager<GroupTemplate>
 */
final class GroupManager extends BaseGroupManager
{
    public function deleteGroup(GroupInterface $group): void
    {
        throw new NoDriverException();
    }

    public function findGroupBy(array $criteria): ?GroupInterface
    {
        throw new NoDriverException();
    }

    public function findGroups(): array
    {
        throw new NoDriverException();
    }

    public function getClass(): string
    {
        throw new NoDriverException();
    }

    public function updateGroup(GroupInterface $group, bool $andFlush = true): void
    {
        throw new NoDriverException();
    }
}
