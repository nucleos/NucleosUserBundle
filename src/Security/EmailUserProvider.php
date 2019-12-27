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

namespace Nucleos\UserBundle\Security;

use Nucleos\UserBundle\Model\UserInterface;

final class EmailUserProvider extends UserProvider
{
    protected function findUser(string $username): ?UserInterface
    {
        return $this->userManager->findUserByUsernameOrEmail($username);
    }
}
