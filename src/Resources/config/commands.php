<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nucleos\UserBundle\Command\ActivateUserCommand;
use Nucleos\UserBundle\Command\ChangePasswordCommand;
use Nucleos\UserBundle\Command\CreateUserCommand;
use Nucleos\UserBundle\Command\DeactivateUserCommand;
use Nucleos\UserBundle\Command\DemoteUserCommand;
use Nucleos\UserBundle\Command\PromoteUserCommand;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set(ActivateUserCommand::class)
            ->tag('console.command')
            ->args([
                new Reference('nucleos_user.util.user_manipulator'),
            ])

        ->set(ChangePasswordCommand::class)
            ->tag('console.command')
            ->args([
                new Reference('nucleos_user.util.user_manipulator'),
            ])

        ->set(CreateUserCommand::class)
            ->tag('console.command')
            ->args([
                new Reference('nucleos_user.util.user_manipulator'),
            ])

        ->set(DeactivateUserCommand::class)
            ->tag('console.command')
            ->args([
                new Reference('nucleos_user.util.user_manipulator'),
            ])

        ->set(DemoteUserCommand::class)
            ->tag('console.command')
            ->args([
                new Reference('nucleos_user.util.user_manipulator'),
            ])

        ->set(PromoteUserCommand::class)
            ->tag('console.command')
            ->args([
                new Reference('nucleos_user.util.user_manipulator'),
            ])
    ;
};
