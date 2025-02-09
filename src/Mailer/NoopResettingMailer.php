<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Mailer;

use Nucleos\UserBundle\Model\UserInterface;

final class NoopResettingMailer implements ResettingMailer
{
    public function sendResettingEmailMessage(UserInterface $user): void {}
}
