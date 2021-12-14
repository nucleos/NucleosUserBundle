<?php

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
 * @deprecated since symfony 5.4
 */
interface LegacyUserInterface
{
    public function setSalt(?string $salt): void;

    public function getSalt(): ?string;
}
