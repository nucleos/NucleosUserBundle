<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Noop;

use Doctrine\Common\EventSubscriber;

final class UserListener implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
        ];
    }
}
