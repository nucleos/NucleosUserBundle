<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class InjectUserCheckerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $firewallName = $container->getParameter('nucleos_user.firewall_name');
        $loginManager = $container->findDefinition('nucleos_user.security.login_manager');

        if ($container->has('security.user_checker.'.$firewallName)) {
            $loginManager->replaceArgument(1, new Reference('security.user_checker.'.$firewallName));
        }
    }
}
