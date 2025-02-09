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

final class EmailUserProvider extends UserProvider
{
    protected function findUser(string $identifier): ?UserInterface
    {
        $user = null;

        if (0 !== preg_match('/^.+\@\S+\.\S+$/', $identifier)) {
            $user = $this->userManager->findUserByEmail($identifier);
        }

        if (null === $user) {
            $user = parent::findUser($identifier);
        }

        return $user;
    }
}
