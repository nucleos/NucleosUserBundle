<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Loader\Configurator;

use Nucleos\UserBundle\Action\AccountDeletionAction;

return static function (RoutingConfigurator $routes): void {
    $routes->add('nucleos_user_delete_account', '/delete')
        ->controller(AccountDeletionAction::class)
        ->methods(['GET', 'POST'])
    ;
};
