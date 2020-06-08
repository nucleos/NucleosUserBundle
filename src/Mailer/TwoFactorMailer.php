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

namespace Nucleos\UserBundle\Mailer;

use Nucleos\UserBundle\Model\TrustedDeviceInterface;
use Nucleos\UserBundle\Model\UserInterface;

interface TwoFactorMailer
{
    public function sendTwoFactorMessage(UserInterface $user, TrustedDeviceInterface $token): void;
}
