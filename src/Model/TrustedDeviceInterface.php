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

use DateTime;

interface TrustedDeviceInterface
{
    public function getUser(): UserInterface;

    public function getToken(): string;

    public function getLastIp(): ?string;

    public function getLastSeen(): ?DateTime;

    public function updateLastSeen(DateTime $date, string $ip): void;
}
