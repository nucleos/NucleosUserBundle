<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Model;

/**
 * @phpstan-template GroupTemplate of \Nucleos\UserBundle\Model\GroupInterface
 *
 * @phpstan-implements GroupManager<GroupTemplate>
 */
abstract class BaseGroupManager implements GroupManager
{
    public function createGroup(string $name): GroupInterface
    {
        $class = $this->getClass();

        return new $class($name);
    }

    public function findGroupByName(string $name): ?GroupInterface
    {
        return $this->findGroupBy(['name' => $name]);
    }
}
