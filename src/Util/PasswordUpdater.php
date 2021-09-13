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

namespace Nucleos\UserBundle\Util;

use Nucleos\UserBundle\Model\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\LegacyPasswordHasherInterface;

final class PasswordUpdater implements PasswordUpdaterInterface
{
    private PasswordHasherFactoryInterface $passwordHasherFactory;

    public function __construct(PasswordHasherFactoryInterface $passwordHasherFactory)
    {
        $this->passwordHasherFactory = $passwordHasherFactory;
    }

    public function hashPassword(UserInterface $user): void
    {
        $plainPassword = $user->getPlainPassword();

        if (null === $plainPassword || '' === $plainPassword) {
            return;
        }

        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher($user);

        if ($passwordHasher instanceof LegacyPasswordHasherInterface) {
            $salt = rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '=');
            $user->setSalt($salt);
            $hashedPassword = $passwordHasher->hash($plainPassword, $salt);
        } else {
            $user->setSalt(null);
            $hashedPassword = $passwordHasher->hash($plainPassword);
        }

        $user->setPassword($hashedPassword);
        $user->eraseCredentials();
    }
}
