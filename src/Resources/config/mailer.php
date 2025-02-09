<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nucleos\UserBundle\Mailer\NoopResettingMailer;
use Nucleos\UserBundle\Mailer\SimpleResettingMailer;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('nucleos_user.mailer.simple', SimpleResettingMailer::class)
            ->args([
                new Reference('mailer.mailer'),
                new Reference('translator'),
                new Reference('router'),
                new Parameter('nucleos_user.resetting.from_email'),
            ])

        ->set('nucleos_user.mailer.noop', NoopResettingMailer::class)
    ;
};
