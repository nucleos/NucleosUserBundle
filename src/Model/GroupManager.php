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

/**
 * @phpstan-template GroupTemplate of \Nucleos\UserBundle\Model\GroupInterface
 * @phpstan-implements GroupManagerInterface<GroupTemplate>
 */
abstract class GroupManager implements GroupManagerInterface
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
