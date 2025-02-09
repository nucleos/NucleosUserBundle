<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nucleos\UserBundle\Action\CheckEmailAction;
use Nucleos\UserBundle\Action\RequestResetAction;
use Nucleos\UserBundle\Action\ResetAction;
use Nucleos\UserBundle\EventListener\ResettingListener;
use Nucleos\UserBundle\Form\Model\Resetting;
use Nucleos\UserBundle\Form\Type\RequestPasswordFormType;
use Nucleos\UserBundle\Form\Type\ResettingFormType;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set(ResettingFormType::class)
            ->tag('form.type')
            ->args([
                Resetting::class,
            ])

        ->set(RequestPasswordFormType::class)
            ->tag('form.type')

        ->set(ResettingListener::class)
            ->tag('kernel.event_subscriber')
            ->args([
                new Reference('router'),
                new Parameter('nucleos_user.resetting.token_ttl'),
            ])

        ->set(RequestResetAction::class)
            ->public()
            ->args([
                new Reference('twig'),
                new Reference('form.factory'),
                new Reference('router'),
                new Reference('event_dispatcher'),
                new Reference('nucleos_user.user_manager'),
                new Reference('nucleos_user.util.token_generator'),
                new Reference('security.user_providers'),
                new Reference('nucleos_user.mailer'),
                new Parameter('nucleos_user.resetting.retry_ttl'),
                new Reference('translator'),
            ])

        ->set(ResetAction::class)
            ->public()
            ->args([
                new Reference('twig'),
                new Reference('router'),
                new Reference('event_dispatcher'),
                new Reference('form.factory'),
                new Reference('nucleos_user.user_manager'),
                '%nucleos_user.loggedin.route%',
                new Reference('nucleos_user.util.user_manipulator'),
            ])

        ->set(CheckEmailAction::class)
            ->public()
            ->args([
                new Reference('twig'),
                new Reference('router'),
                new Parameter('nucleos_user.resetting.retry_ttl'),
            ])
    ;
};
