<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nucleos\UserBundle\Model\UserManager;
use Nucleos\UserBundle\Util\CanonicalFieldsUpdater;
use Nucleos\UserBundle\Util\SimpleCanonicalFieldsUpdater;
use Nucleos\UserBundle\Util\SimpleCanonicalizer;
use Nucleos\UserBundle\Util\SimpleTokenGenerator;
use Nucleos\UserBundle\Util\SimpleUserManipulator;
use Nucleos\UserBundle\Util\TokenGenerator;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('nucleos_user.util.canonicalizer.simple', SimpleCanonicalizer::class)

        ->set('nucleos_user.util.user_manipulator.simple', SimpleUserManipulator::class)
            ->args([
                new Reference('nucleos_user.user_manager'),
                new Reference('event_dispatcher'),
                new Reference('request_stack'),
            ])

        ->alias('nucleos_user.util.user_manipulator', 'nucleos_user.util.user_manipulator.simple')

        ->set('nucleos_user.util.token_generator.simple', SimpleTokenGenerator::class)

        ->alias(TokenGenerator::class, 'nucleos_user.util.token_generator')

        ->set('nucleos_user.util.canonical_fields_updater', SimpleCanonicalFieldsUpdater::class)
            ->args([
                new Reference('nucleos_user.util.username_canonicalizer'),
                new Reference('nucleos_user.util.email_canonicalizer'),
            ])

        ->alias(CanonicalFieldsUpdater::class, 'nucleos_user.util.canonical_fields_updater')

        ->alias(UserManager::class, 'nucleos_user.user_manager')

    ;
};
