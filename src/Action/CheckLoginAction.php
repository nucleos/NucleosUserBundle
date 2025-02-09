<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Action;

use RuntimeException;

final class CheckLoginAction
{
    public function __invoke(): void
    {
        throw new RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }
}
