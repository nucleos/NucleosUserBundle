<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Loader\Configurator;

use Nucleos\UserBundle\Action\UpdateSecurityAction;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;

return static function (RoutingConfigurator $routes): void {
    $routes->add('nucleos_user_update_security', '/update-security')
        ->controller(UpdateSecurityAction::class)
        ->methods(['GET', 'POST'])
    ;

    $routes->add('nucleos_user_update_security_legacy', '/change-password')
        ->controller(RedirectController::class)
        ->methods(['GET', 'POST'])
        ->defaults([
            'route'     => 'nucleos_user_update_security',
            'permanent' => true,
        ])
    ;
};
