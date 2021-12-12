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

use Nucleos\UserBundle\Action\CheckLoginAction;
use Nucleos\UserBundle\Action\LoggedinAction;
use Nucleos\UserBundle\Action\LoginAction;
use Nucleos\UserBundle\Action\LogoutAction;
use Nucleos\UserBundle\EventListener\LastLoginListener;
use Nucleos\UserBundle\Form\Type\LoginFormType;
use Nucleos\UserBundle\Security\EmailProvider;
use Nucleos\UserBundle\Security\EmailUserProvider;
use Nucleos\UserBundle\Security\LoginManager;
use Nucleos\UserBundle\Security\LoginManagerInterface;
use Nucleos\UserBundle\Security\UserChecker;
use Nucleos\UserBundle\Security\UserProvider;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set(LastLoginListener::class)
            ->tag('kernel.event_subscriber')
            ->args([
                new Reference('nucleos_user.user_manager'),
            ])

        ->set('nucleos_user.security.login_manager', LoginManager::class)
            ->args([
                new Reference('security.token_storage'),
                new Reference('security.user_checker'),
                new Reference('security.authentication.session_strategy'),
                new Reference('request_stack'),
            ])

        ->alias(LoginManagerInterface::class, 'nucleos_user.security.login_manager')

        ->set('nucleos_user.user_provider.username', UserProvider::class)
            ->args([
                new Reference('nucleos_user.user_manager'),
            ])

        ->set('nucleos_user.user_provider.username_email', EmailUserProvider::class)
            ->args([
                new Reference('nucleos_user.user_manager'),
            ])

        ->set('nucleos_user.user_provider.email', EmailProvider::class)
            ->args([
                new Reference('nucleos_user.user_manager'),
            ])

        ->set(LoginFormType::class)
            ->tag('form.type')
            ->args([
                new Reference('security.authentication_utils'),
                new Reference('translator'),
            ])

        ->set(LoginAction::class)
            ->public()
            ->args([
                new Reference('twig'),
                new Reference('event_dispatcher'),
                new Reference('form.factory'),
                new Reference('router'),
                new Reference('security.csrf.token_manager'),
                new Reference('security.authentication_utils'),
            ])

        ->set(LogoutAction::class)
            ->public()

        ->set(LoggedinAction::class)
            ->public()
            ->args([
                new Reference('twig'),
                new Reference('event_dispatcher'),
                new Reference('security.helper'),
            ])

        ->set(CheckLoginAction::class)
            ->public()

        ->set(UserChecker::class)

    ;
};
