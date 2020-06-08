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

use DateTimeInterface;

abstract class TrustedDeviceManager implements TrustedDeviceManagerInterface
{
    public function createToken(UserInterface $user, string $code, DateTimeInterface $expiryDate): ?TrustedDeviceInterface
    {
        $class = $this->getClass();

        return new $class($user, $code, $expiryDate);
    }
}
