<?php

declare(strict_types=1);

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class InjectRememberMeServicesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $firewallName = $container->getParameter('nucleos_user.firewall_name');
        $loginManager = $container->getDefinition('nucleos_user.security.login_manager');

        if ($container->hasDefinition('security.authentication.rememberme.services.persistent.'.$firewallName)) {
            $loginManager->replaceArgument(4, new Reference('security.authentication.rememberme.services.persistent.'.$firewallName));
        } elseif ($container->hasDefinition('security.authentication.rememberme.services.simplehash.'.$firewallName)) {
            $loginManager->replaceArgument(4, new Reference('security.authentication.rememberme.services.simplehash.'.$firewallName));
        }
    }
}
