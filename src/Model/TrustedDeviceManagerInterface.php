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

interface TrustedDeviceManagerInterface
{
    public function findToken(UserInterface $user, string $token): ?TrustedDeviceInterface;

    public function createToken(UserInterface $user, string $code, DateTimeInterface $expiryDate): ?TrustedDeviceInterface;

    public function removeExpired(): void;

    /**
     * @phpstan-return string-class<TrustedDeviceInterface>
     */
    public function getClass(): string;
}
