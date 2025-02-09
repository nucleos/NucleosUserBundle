<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nucleos\UserBundle\Noop\GroupManager;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('nucleos_user.group_manager.default', GroupManager::class)
    ;
};
