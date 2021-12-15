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

use Nucleos\UserBundle\Action\CheckLoginAction;
use Nucleos\UserBundle\Action\LoggedinAction;
use Nucleos\UserBundle\Action\LoginAction;
use Nucleos\UserBundle\Action\LogoutAction;

return static function (RoutingConfigurator $routes): void {
    $routes->add('nucleos_user_security_login', '/login')
        ->controller(LoginAction::class)
        ->methods(['GET', 'POST'])
    ;

    $routes->add('nucleos_user_security_check', '/login_check')
        ->controller(CheckLoginAction::class)
        ->methods(['POST'])
    ;

    $routes->add('nucleos_user_security_loggedin', '/loggedin')
        ->controller(LoggedinAction::class)
        ->methods(['GET', 'POST'])
    ;

    $routes->add('nucleos_user_security_logout', '/logout')
        ->controller(LogoutAction::class)
        ->methods(['GET', 'POST'])
    ;
};
