<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nucleos\UserBundle\Action\UpdateSecurityAction;
use Nucleos\UserBundle\Form\Type\UpdateSecurityFormType;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set(UpdateSecurityFormType::class)
            ->tag('form.type')
            ->args([
                '%nucleos_user.model.user.class%',
            ])

        ->set(UpdateSecurityAction::class)
            ->public()
            ->args([
                new Reference('twig'),
                new Reference('router'),
                new Reference('security.helper'),
                new Reference('event_dispatcher'),
                new Reference('form.factory'),
                new Reference('nucleos_user.util.user_manipulator'),
                new Reference('nucleos_user.user_manager'),
            ])
    ;
};
