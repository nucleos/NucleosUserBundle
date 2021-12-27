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

use Nucleos\UserBundle\Model\UserManagerInterface;
use Nucleos\UserBundle\Util\CanonicalFieldsUpdater;
use Nucleos\UserBundle\Util\Canonicalizer;
use Nucleos\UserBundle\Util\TokenGenerator;
use Nucleos\UserBundle\Util\TokenGeneratorInterface;
use Nucleos\UserBundle\Util\UserManipulator;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('nucleos_user.util.canonicalizer.default', Canonicalizer::class)

        ->set('nucleos_user.util.user_manipulator', UserManipulator::class)
            ->args([
                new Reference('nucleos_user.user_manager'),
                new Reference('event_dispatcher'),
                new Reference('request_stack'),
            ])

        ->set('nucleos_user.util.token_generator.default', TokenGenerator::class)

        ->alias(TokenGeneratorInterface::class, 'nucleos_user.util.token_generator')

        ->set('nucleos_user.util.canonical_fields_updater', CanonicalFieldsUpdater::class)
            ->args([
                new Reference('nucleos_user.util.username_canonicalizer'),
                new Reference('nucleos_user.util.email_canonicalizer'),
            ])

        ->alias(CanonicalFieldsUpdater::class, 'nucleos_user.util.canonical_fields_updater')

        ->alias(UserManagerInterface::class, 'nucleos_user.user_manager')

    ;
};
