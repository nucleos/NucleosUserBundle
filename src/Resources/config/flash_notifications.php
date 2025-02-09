<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nucleos\UserBundle\EventListener\FlashListener;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set(FlashListener::class)
            ->tag('kernel.event_subscriber')
            ->args([
                new Reference('request_stack'),
                new Reference('translator'),
            ])
    ;
};
