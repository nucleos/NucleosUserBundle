<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Action;

use RuntimeException;

final class LogoutAction
{
    public function __invoke(): void
    {
        throw new RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
