<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Noop;

use Nucleos\UserBundle\Model\TrustedDeviceInterface;
use Nucleos\UserBundle\Model\TrustedDeviceManager as BaseTrustedDeviceManager;
use Nucleos\UserBundle\Model\UserInterface;
use Nucleos\UserBundle\Noop\Exception\NoDriverException;

final class TrustedDeviceManager extends BaseTrustedDeviceManager
{
    public function findToken(UserInterface $user, string $token): TrustedDeviceInterface
    {
        throw new NoDriverException();
    }

    public function removeExpired(): void
    {
        throw new NoDriverException();
    }

    public function getClass(): string
    {
        throw new NoDriverException();
    }
}
