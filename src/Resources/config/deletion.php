<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nucleos\UserBundle\Action\AccountDeletionAction;
use Nucleos\UserBundle\Form\Model\AccountDeletion;
use Nucleos\UserBundle\Form\Type\AccountDeletionFormType;
use Nucleos\UserBundle\Model\UserManager;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(AccountDeletionAction::class)
        ->public()
        ->args([
            new Reference('twig'),
            new Reference('router'),
            new Reference(UserManager::class),
            new Reference('security.token_storage'),
            new Reference('form.factory'),
            new Reference(EventDispatcherInterface::class),
        ])
    ;

    $services->set(AccountDeletionFormType::class)
        ->tag('form.type')
        ->args([
            AccountDeletion::class,
        ])
    ;
};
