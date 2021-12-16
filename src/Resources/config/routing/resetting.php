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

namespace Symfony\Component\Routing\Loader\Configurator;

use Nucleos\UserBundle\Action\CheckEmailAction;
use Nucleos\UserBundle\Action\RequestResetAction;
use Nucleos\UserBundle\Action\ResetAction;
use Nucleos\UserBundle\Action\SendEmailAction;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;

return static function (RoutingConfigurator $routes): void {
    $routes->add('nucleos_user_resetting_request', '/request')
        ->controller(RequestResetAction::class)
        ->methods(['GET'])
    ;

    $routes->add('nucleos_user_resetting_send_email', '/send-email')
        ->controller(SendEmailAction::class)
        ->methods(['POST'])
    ;

    $routes->add('nucleos_user_resetting_check_email', '/check-email')
        ->controller(CheckEmailAction::class)
        ->methods(['GET'])
    ;

    $routes->add('nucleos_user_resetting_reset', '/reset/{token}')
        ->controller(ResetAction::class)
        ->methods(['GET', 'POST'])
    ;

    $routes->add('nucleos_user_resetting', '/')
        ->controller(RedirectController::class)
        ->methods(['GET'])
        ->defaults([
            'route'     => 'nucleos_user_resetting_request',
            'permanent' => true,
        ])
    ;
};
