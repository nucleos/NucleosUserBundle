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

use Nucleos\UserBundle\Noop\UserListener;
use Nucleos\UserBundle\Noop\UserManager;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('nucleos_user.user_manager.default', UserManager::class)
            ->args([
                new Reference('nucleos_user.util.canonical_fields_updater'),
            ])

        ->set('nucleos_user.user_listener', UserListener::class)

    ;
};
