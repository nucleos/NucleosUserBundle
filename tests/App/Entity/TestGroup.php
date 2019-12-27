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

namespace Nucleos\UserBundle\Tests\App\Entity;

use Nucleos\UserBundle\Model\Group;

class TestGroup extends Group
{
    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
