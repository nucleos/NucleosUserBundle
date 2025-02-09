<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Security;

use Nucleos\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Response;

interface LoginManager
{
    public function logInUser(string $firewallName, UserInterface $user, ?Response $response = null): void;
}
