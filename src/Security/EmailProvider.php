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

final class EmailProvider extends UserProvider
{
    protected function findUser(string $identifier): ?UserInterface
    {
        return $this->userManager->findUserByEmail($identifier);
    }
}
