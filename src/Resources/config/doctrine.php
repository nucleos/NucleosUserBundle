<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\Persistence\ObjectManager;
use Nucleos\UserBundle\Doctrine\UserListener;
use Nucleos\UserBundle\Doctrine\UserManager;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('nucleos_user.user_manager.default', UserManager::class)
            ->args([
                new Reference('nucleos_user.object_manager'),
                new Parameter('nucleos_user.model.user.class'),
            ])

        // The factory is configured in the DI extension class to support more Symfony versions
        ->set('nucleos_user.object_manager', ObjectManager::class)
            ->args([
                new Parameter('nucleos_user.model_manager_name'),
            ])

        ->set('nucleos_user.user_listener', UserListener::class)
            ->args([
                new Reference('security.password_hasher'),
            ])
    ;
};
